<?php
require_once(dirname(__FILE__) . '/session_manager.php');
if(isSessionValid() == false)
	die("Access denied");
//-------------------------------------------------------
date_default_timezone_set('UTC');
$entry_time = (new DateTime())->format('d-m-Y h:i A');
header('Content-Type: application/json');

if (isset($_POST)) {
	$POSTJ = json_decode(file_get_contents('php://input'),true);

	if(isset($POSTJ['action_type'])){
		if($POSTJ['action_type'] == "get_SP_base_URL")
			getSPBaseURL($conn);
		if($POSTJ['action_type'] == "save_web_tracker")
			saveWebTracker($conn, $POSTJ);
	    if($POSTJ['action_type'] == "get_web_tracker_list")
			getWebTrackerList($conn);
	    if($POSTJ['action_type'] == "get_web_tracker_from_id")
			getWebTrackerFromId($conn, $POSTJ['tracker_id']);
	    if($POSTJ['action_type'] == "delete_web_tracker")
			deleteWebTracker($conn, $POSTJ['tracker_id']);
		if($POSTJ['action_type'] == "make_copy_web_tracker")
			makeCopyWebTracker($conn, $POSTJ['tracker_id'], $POSTJ['new_tracker_id'], $POSTJ['new_tracker_name']);
		if($POSTJ['action_type'] == "get_web_tracker_list_for_modal")
			getWebTrackerListForModal($conn);
		if($POSTJ['action_type'] == "pause_stop_web_tracker_tracking")
			pauseStopWebTrackerTracking($conn, $POSTJ['active'], $POSTJ['tracker_id'],false);
		if($POSTJ['action_type'] == "get_html_content")
			getHTMLContent($POSTJ['url']);
		if($POSTJ['action_type'] == "delete_web_tracker_data")
			deleteWebTrackerData($conn, $POSTJ['tracker_id']);

		if($POSTJ['action_type'] == "get_link_to_web_tracker")		//from mail template
			getLinktoWebTracker($conn);
	}
}

//-----------------------------
function getSPBaseURL(&$conn){
	echo json_encode(['baseurl' => getServerVariable($conn)['baseurl']]);
}
function saveWebTracker($conn, &$POSTJ) { 
	$tracker_step_data_string = base64_decode($POSTJ['tracker_step_data']);
	$tracker_step_data = json_decode($tracker_step_data_string,true);
	$tracker_code_output = json_decode(base64_decode($POSTJ['tracker_code_output']),true);
	$tracker_name = $tracker_step_data['start']['tb_tracker_name'];
	$active = $tracker_step_data['start']['cb_auto_ativate'] == true ? 1 : 0;
	$tracker_id = $POSTJ['tracker_id'];
	$content_html = json_encode($tracker_code_output["web_forms_code"]);
	$content_js = $tracker_code_output["js_tracker"];
	$client_id = getCurrentClientId();

	// Verificar se existe o tracker para este cliente
	$stmt_check = $conn->prepare("SELECT COUNT(*) FROM tb_core_web_tracker_list WHERE tracker_id = ? AND client_id = ?");
	$stmt_check->bind_param('ss', $tracker_id, $client_id);
	$stmt_check->execute();
	$result = $stmt_check->get_result();
	$exists = $result->fetch_row()[0] > 0;
	$stmt_check->close();

	if($exists){
		$stmt = $conn->prepare("UPDATE tb_core_web_tracker_list SET tracker_name=?, content_html=?, content_js=?, tracker_step_data=?, active=? WHERE tracker_id=? AND client_id=?");
		$stmt->bind_param('sssssss', $tracker_name,$content_html,$content_js,$tracker_step_data_string,$active,$tracker_id,$client_id);
	}
	else{
		$stmt = $conn->prepare("INSERT INTO tb_core_web_tracker_list(tracker_id,tracker_name,content_html,content_js,tracker_step_data,active,date,client_id) VALUES(?,?,?,?,?,?,?,?)");
		$stmt->bind_param('ssssssss', $tracker_id,$tracker_name,$content_html,$content_js,$tracker_step_data_string,$active,$GLOBALS['entry_time'],$client_id);
	}

	if ($stmt->execute() === TRUE){
		// Save training configuration if enabled
		if (isset($tracker_step_data['training']) && $tracker_step_data['training']['training_enabled']) {
			saveTrainingConfiguration($conn, $tracker_id, $tracker_step_data['training']);
		}
		
		echo json_encode(['result' => 'success']);	
		pauseStopWebTrackerTracking($conn,$active,$tracker_id,true);
	}
	else 
		echo json_encode(['result' => 'failed', 'error' => 'Error saving data']);	
	$stmt->close();
}

function getWebTrackerList($conn){	
	$resp=[];
	$DTime_info = getTimeInfo($conn);
	$client_id = getCurrentClientId();

	$stmt = $conn->prepare("SELECT tracker_id,tracker_name,tracker_step_data,date,start_time,stop_time,active FROM tb_core_web_tracker_list WHERE client_id = ?");
	$stmt->bind_param('s', $client_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if($result->num_rows > 0){
		foreach ($result->fetch_all(MYSQLI_ASSOC) as $row){
			$row['tracker_step_data'] = json_decode($row["tracker_step_data"]);	
			$row['date'] = getInClientTime_FD($DTime_info,$row['date'],null,'d-m-Y h:i A');
			$row['start_time'] = getInClientTime_FD($DTime_info,$row['start_time'],null,'d-m-Y h:i A');
			$row['stop_time'] = getInClientTime_FD($DTime_info,$row['stop_time'],null,'d-m-Y h:i A');
        	array_push($resp,$row);
		}
		echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);
	}
	else
		echo json_encode(['error' => 'No data']);
	$stmt->close();
}

function getWebTrackerFromId($conn, $tracker_id){	
	$DTime_info = getTimeInfo($conn);
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT * FROM tb_core_web_tracker_list where tracker_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $tracker_id, $client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows != 0){
		$row = $result->fetch_assoc();
		$row['tracker_step_data'] = json_decode($row["tracker_step_data"]);	
		$row['date'] = getInClientTime_FD($DTime_info,$row['date']);
		$row['start_time'] = getInClientTime_FD($DTime_info,$row['start_time']);
		$row['stop_time'] = getInClientTime_FD($DTime_info,$row['stop_time']);
		echo json_encode($row, JSON_INVALID_UTF8_IGNORE);
	}
	else
		echo json_encode(['error' => 'No data']);				
	$stmt->close();
}

function deleteWebTracker($conn, $tracker_id){	
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("DELETE FROM tb_core_web_tracker_list WHERE tracker_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $tracker_id, $client_id);
	$stmt->execute();
	if($stmt->affected_rows != 0)
		deleteWebTrackerData($conn,$tracker_id);
	else
		echo json_encode(['result' => 'failed', 'error' => 'Error deleting tracker!']);	
	$stmt->close();
}

function makeCopyWebTracker($conn, $old_tracker_id, $new_tracker_id, $new_tracker_name){
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("INSERT INTO tb_core_web_tracker_list (tracker_id,tracker_name,content_html,content_js,tracker_step_data,date,active,client_id) SELECT ?, ?, content_html,content_js,tracker_step_data,?,0,client_id FROM tb_core_web_tracker_list WHERE tracker_id=? AND client_id=?");
	$stmt->bind_param("sssss", $new_tracker_id, $new_tracker_name, $GLOBALS['entry_time'], $old_tracker_id, $client_id);
	
	if($stmt->execute() === TRUE){
		echo(json_encode(['result' => 'success']));	
	}
	else 
		echo(json_encode(['result' => 'failed', 'error' => 'Error making copy!']));	
	$stmt->close();
}

function getWebTrackerListForModal($conn){	
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT tracker_id,tracker_name,date FROM tb_core_web_tracker_list WHERE client_id = ?");
	$stmt->bind_param('s', $client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0)
		echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC), JSON_INVALID_UTF8_IGNORE);
	else
		echo json_encode(['error' => 'No data']);
	$stmt->close();
}

function pauseStopWebTrackerTracking($conn,$active,$tracker_id,$quite){
	$client_id = getCurrentClientId();
	
	if($active == false){ //stopping
		$stmt = $conn->prepare("UPDATE tb_core_web_tracker_list SET active=?, stop_time=? WHERE tracker_id=? AND client_id=?");
		$stmt->bind_param('ssss', $active,$GLOBALS['entry_time'],$tracker_id, $client_id);
	}
	else
		if(checkTrackerStartedPreviously($conn,$tracker_id) == true){
			$stmt = $conn->prepare("UPDATE tb_core_web_tracker_list SET active=? WHERE tracker_id=? AND client_id=?");
			$stmt->bind_param('sss', $active,$tracker_id, $client_id);
		}
		else{
			$stmt = $conn->prepare("UPDATE tb_core_web_tracker_list SET active=?, start_time=? WHERE tracker_id=? AND client_id=?");
			$stmt->bind_param('ssss', $active,$GLOBALS['entry_time'],$tracker_id, $client_id);
		}

	if($quite)
		$stmt->execute();
	else{
		if ($stmt->execute() === TRUE){
			echo(json_encode(['result' => 'success']));	
		}
		else 
			echo(json_encode(['result' => 'failed', 'error' => 'Error changing status']));	
		}
	$stmt->close();	
}

function getHTMLContent($url){
	$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:79.0) Gecko/20100101 Firefox/79.0');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // ignore SSL errors
    $result=curl_exec($ch);
    curl_close($ch);
    if($result)
    	echo json_encode($result);
    else
    	echo json_encode(['result' => 'failed', 'error' => 'Failed to fetch content']);
}

//-------------------------------------------------------------------------

function checkTrackerStartedPreviously($conn,$tracker_id){
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT start_time FROM tb_core_web_tracker_list WHERE tracker_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $tracker_id, $client_id);
	$stmt->execute();
	$row = $stmt->get_result()->fetch_assoc();
	$stmt->close();
	if($row['start_time'] == "")
		return false;
	else
		return true;
}

function deleteWebTrackerData($conn, $tracker_id){
	$stmt = $conn->prepare("DELETE FROM tb_data_webpage_visit WHERE tracker_id = ?");
	$stmt->bind_param("s", $tracker_id);
	$stmt->execute();

	$stmt = $conn->prepare("DELETE FROM tb_data_webform_submit WHERE tracker_id = ?");
	$stmt->bind_param("s", $tracker_id);
	$stmt->execute();
	$stmt->close();
	
	echo(json_encode(['result' => 'success']));	
}

//-----------------------------------------------------------------------------
function getLinktoWebTracker($conn){
	$resp = [];
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT tracker_id,tracker_name,tracker_step_data FROM tb_core_web_tracker_list WHERE client_id = ?");
	$stmt->bind_param('s', $client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
			$first_page = json_decode($row['tracker_step_data'],true)['web_forms']['data'][0]['page_url'];
		    array_push($resp, array('tracker_id' => $row['tracker_id'], 'tracker_name' => $row['tracker_name'], 'first_page' => $first_page));
		}
		echo json_encode($resp);
	}
}

/**
 * Save training configuration for a tracker
 */
function saveTrainingConfiguration($conn, $tracker_id, $training_config) {
    try {
        // Get campaign ID from tracker (we'll need to link to campaigns)
        $stmt = $conn->prepare("SELECT campaign_id FROM tb_core_web_tracker_list WHERE tracker_id = ?");
        $stmt->bind_param('s', $tracker_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $campaign_id = $row['campaign_id'] ?? null;
            
            // If no campaign_id exists, we'll create a virtual campaign record for tracking
            if (!$campaign_id) {
                $campaign_id = createVirtualCampaignForTracker($conn, $tracker_id);
            }
            
            if ($campaign_id) {
                // Delete existing training assignments for this campaign
                $stmt = $conn->prepare("DELETE FROM tb_campaign_training_assignments WHERE campaign_id = ?");
                $stmt->bind_param('i', $campaign_id);
                $stmt->execute();
                
                // Insert new training assignment
                $stmt = $conn->prepare("
                    INSERT INTO tb_campaign_training_assignments 
                    (campaign_id, module_id, trigger_type, redirect_url, is_active, created_at) 
                    VALUES (?, ?, ?, ?, 1, NOW())
                ");
                
                $stmt->bind_param('iiss', 
                    $campaign_id,
                    $training_config['training_module_id'],
                    $training_config['training_trigger_condition'],
                    $training_config['training_completion_redirect']
                );
                
                $stmt->execute();
                
                // Update tracker with campaign_id if it was created
                if ($row['campaign_id'] === null) {
                    $stmt = $conn->prepare("UPDATE tb_core_web_tracker_list SET campaign_id = ? WHERE tracker_id = ?");
                    $stmt->bind_param('is', $campaign_id, $tracker_id);
                    $stmt->execute();
                }
            }
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        error_log("Error saving training configuration: " . $e->getMessage());
    }
}

/**
 * Create a virtual campaign for tracker-only training
 */
function createVirtualCampaignForTracker($conn, $tracker_id) {
    try {
        $campaign_name = "Auto Campaign for Tracker: " . $tracker_id;
        $client_id = getCurrentClientId();
        
        $stmt = $conn->prepare("
            INSERT INTO tb_campaigns 
            (campaign_name, client_id, created_at, is_virtual) 
            VALUES (?, ?, NOW(), 1)
        ");
        
        $stmt->bind_param('ss', $campaign_name, $client_id);
        
        if ($stmt->execute()) {
            return $conn->insert_id;
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Error creating virtual campaign: " . $e->getMessage());
        return null;
    }
}
?>
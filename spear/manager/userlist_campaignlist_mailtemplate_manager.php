<?php
require_once(dirname(__FILE__) . '/session_manager.php');
require_once(dirname(__FILE__) . '/common_functions.php');
require_once(dirname(__FILE__,2) . '/libs/symfony/autoload.php');
require_once(dirname(__FILE__,2) . '/libs/qr_barcode/qrcode.php');
require_once(dirname(__FILE__,2) . '/libs/qr_barcode/barcode.php');
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
if(isSessionValid() == false)
	die("Access denied");
//-------------------------------------------------------
date_default_timezone_set('UTC');
$entry_time = (new DateTime())->format('d-m-Y h:i A');
header('Content-Type: application/json');

if (isset($_POST)) {
	$POSTJ = json_decode(file_get_contents('php://input'),true);

	if(isset($POSTJ['action_type'])){
		if($POSTJ['action_type'] == "add_user_to_table")
			addUserToTable($conn, $POSTJ);
		if($POSTJ['action_type'] == "save_user_group")
			saveUserGroup($conn, $POSTJ['user_group_id'], $POSTJ['user_group_name']);
		if($POSTJ['action_type'] == "update_user")
			updateUser($conn,$POSTJ);
		if($POSTJ['action_type'] == "delete_user")
			deleteUser($conn, $POSTJ['user_group_id'], $POSTJ['uid']);
		if($POSTJ['action_type'] == "download_user")
			downloadUser($conn,$POSTJ['user_group_id']);
		if($POSTJ['action_type'] == "get_user_group_list")
			getUserGroupList($conn);
		if($POSTJ['action_type'] == "upload_user")
			uploadUserCVS($conn,$POSTJ);
		if($POSTJ['action_type'] == "get_user_group_from_group_Id_table")
			getUserGroupFromGroupIdTable($conn,$POSTJ);
		if($POSTJ['action_type'] == "delete_user_group_from_group_id")
			deleteUserGroupFromGroupId($conn,$POSTJ['user_group_id']);
		if($POSTJ['action_type'] == "make_copy_user_group")
			makeCopyUserGroup($conn, $POSTJ['user_group_id'], $POSTJ['new_user_group_id'], $POSTJ['new_user_group_name']);

		if($POSTJ['action_type'] == "save_mail_template")
			saveMailTemplate($conn,$POSTJ);
		if($POSTJ['action_type'] == "get_mail_template_list")
			getMailTemplateList($conn);
		if($POSTJ['action_type'] == "get_mail_template_from_template_id")
			getMailTemplateFromTemplateId($conn,$POSTJ['mail_template_id']);
		if($POSTJ['action_type'] == "delete_mail_template_from_template_id")
			deleteMailTemplateFromTemplateId($conn,$POSTJ['mail_template_id']);
		if($POSTJ['action_type'] == "make_copy_mail_template")
			makeCopyMailTemplate($conn, $POSTJ['mail_template_id'], $POSTJ['new_mail_template_id'], $POSTJ['new_mail_template_name']);
		if($POSTJ['action_type'] == "upload_tracker_image")
			uploadTrackerImage($conn,$POSTJ);
		if($POSTJ['action_type'] == "upload_attachments")
			uploadAttachment($conn,$POSTJ);
		if($POSTJ['action_type'] == "upload_mail_body_files")
			uploadMailBodyFiles($conn,$POSTJ);

		if($POSTJ['action_type'] == "save_sender_list")
			saveSenderList($conn, $POSTJ);
		if($POSTJ['action_type'] == "get_sender_list")
			getSenderList($conn);	
		if($POSTJ['action_type'] == "get_sender_from_sender_list_id")
			getSenderFromSenderListId($conn,$POSTJ['sender_list_id']);	
		if($POSTJ['action_type'] == "delete_mail_sender_list_from_list_id")
			deleteMailSenderListFromSenderId($conn,$POSTJ['sender_list_id']);
		if($POSTJ['action_type'] == "make_copy_sender_list")
			makeCopyMailSenderList($conn,$POSTJ['sender_list_id'],$POSTJ['new_sender_list_id'],$POSTJ['new_sender_list_name']);
		if($POSTJ['action_type'] == "verify_mailbox_access")
			verifyMailboxAccess($conn,$POSTJ);

		if($POSTJ['action_type'] == "send_test_mail_verification")
			sendTestMailVerification($conn,$POSTJ);
		if($POSTJ['action_type'] == "send_test_mail_sample")
			sendTestMailSample($conn,$POSTJ);
	}
}

//-----------------------------
function addUserToTable($conn, &$POSTJ){
	$user_group_id = $POSTJ['user_group_id'];
	$user_group_name = $POSTJ['user_group_name'];
	if(empty($user_group_name))
		die(json_encode(['result' => 'failed', 'error' => 'Error adding user!']));			

	$row = getUserGroupFromGroupId($conn, $user_group_id);
	if(empty($row) || empty($row["user_data"]))
		$user_data =[];
	else
		$user_data = json_decode($row["user_data"],true);

	$uid = getRandomStr(10);
	array_push($user_data,['uid'=>$uid, 'fname'=>$POSTJ['fname'], 'lname'=>$POSTJ['lname'], 'email'=>$POSTJ['email'], 'notes'=>$POSTJ['notes']]);
	// remove duplicate entries and reindex to ensure JSON encodes as array
	$user_data = array_values(array_unique($user_data, SORT_REGULAR));
	$user_data = json_encode($user_data);

	if(checkAnIDExist($conn,$user_group_id,'user_group_id','tb_core_mailcamp_user_group')){
		$stmt = $conn->prepare("UPDATE tb_core_mailcamp_user_group SET user_group_name=?, user_data=? WHERE user_group_id=?");
		$stmt->bind_param('sss', $user_group_name,$user_data,$user_group_id);
	}
	else{
		$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_user_group(user_group_id,user_group_name,user_data,date) VALUES(?,?,?,?)");
		$stmt->bind_param('ssss', $user_group_id,$user_group_name,$user_data,$GLOBALS['entry_time']);
	}

	if($stmt->execute() === TRUE){
		echo(json_encode(['result' => 'success']));	
	}
	else 
		echo(json_encode(['result' => 'failed', 'error' => 'Error adding user!']));			
}

function saveUserGroup($conn, $user_group_id, $user_group_name){
	$client_id = getCurrentClientId();
	
	// Verificar se existe o user group para este cliente
	$stmt_check = $conn->prepare("SELECT COUNT(*) FROM tb_core_mailcamp_user_group WHERE user_group_id = ? AND client_id = ?");
	$stmt_check->bind_param('ss', $user_group_id, $client_id);
	$stmt_check->execute();
	$result = $stmt_check->get_result();
	$exists = $result->fetch_row()[0] > 0;
	$stmt_check->close();

	if($exists){
		$stmt = $conn->prepare("UPDATE tb_core_mailcamp_user_group SET user_group_name=? WHERE user_group_id=? AND client_id=?");
		$stmt->bind_param('sss', $user_group_name,$user_group_id,$client_id);
	}
	else{
		$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_user_group(user_group_id,user_group_name,date,client_id) VALUES(?,?,?,?)");
		$stmt->bind_param('ssss', $user_group_id,$user_group_name,$GLOBALS['entry_time'],$client_id);
	}
	
	if ($stmt->execute() === TRUE)
		echo(json_encode(['result' => 'success']));	
	else 
		echo(json_encode(['result' => 'failed', 'error' => 'Error saving data!']));	
	$stmt->close();
}

function updateUser($conn, &$POSTJ){
	$user_group_id = $POSTJ['user_group_id'];
	$uid = $POSTJ['uid'];

	$row = getUserGroupFromGroupId($conn, $user_group_id);

	if(!empty($row)){
		$user_data = json_decode($row["user_data"],true);

		$index = array_search($uid, array_column($user_data, 'uid'));
		if($index !== false ){	//returns false if not found
			$user_data[$index]= ['uid'=>$uid, 'fname'=>$POSTJ['fname'], 'lname'=>$POSTJ['lname'], 'email'=>$POSTJ['email'], 'notes'=>$POSTJ['notes']];
			$user_data = json_encode($user_data);
			$stmt = $conn->prepare("UPDATE tb_core_mailcamp_user_group SET user_data=? WHERE user_group_id=?");
			$stmt->bind_param('ss', $user_data,$user_group_id);
			if($stmt->execute() === TRUE)
				echo(json_encode(['result' => 'success']));				
			else 
				echo(json_encode(['result' => 'failed', 'error' => 'Error updating row!']));		
		}
		else
			echo(json_encode(['result' => 'failed', 'error' => 'Error updating row. User not found!']));
	}
	else
		echo(json_encode(['result' => 'failed', 'error' => 'Error updating row. User group not found!']));	
}

function deleteUser($conn, $user_group_id, $uid){
	$stmt = $conn->prepare("SELECT user_data FROM tb_core_mailcamp_user_group WHERE user_group_id = ?");
	$stmt->bind_param("s", $user_group_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows != 0){
		$row = $result->fetch_assoc();
		$user_data = json_decode($row["user_data"],true);

		$index = array_search($uid, array_column($user_data, 'uid'));
		if($index !== false ){	//returns false if not found
				unset($user_data[$index]);
				// reindex array after removal so JSON remains a proper array
				$user_data = array_values($user_data);
				$user_data = json_encode($user_data);
			$stmt = $conn->prepare("UPDATE tb_core_mailcamp_user_group SET user_data=? WHERE user_group_id=?");
			$stmt->bind_param('ss', $user_data,$user_group_id);
			if($stmt->execute() === TRUE)
				echo(json_encode(['result' => 'success']));				
			else 
				echo(json_encode(['result' => 'failed', 'error' => 'Error deleting row!']));		
		}else
			echo(json_encode(['result' => 'failed', 'error' => 'Error deleting row. User not found!']));
	}
	else
		echo(json_encode(['result' => 'failed', 'error' => 'Error deleting row. User group not found!']));	
}

function downloadUser($conn, $user_group_id){
	$stmt = $conn->prepare("SELECT user_data,user_group_name FROM tb_core_mailcamp_user_group WHERE user_group_id = ?");
	$stmt->bind_param("s", $user_group_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows != 0){
		$row = $result->fetch_assoc();
		$user_data = json_decode($row["user_data"],true);

		$f = fopen('php://memory', 'w'); 
		fputcsv($f, ['First Name', 'Last Name', 'Email', 'Notes'], ','); 

	    foreach ($user_data as $line) {
	    	unset($line['uid']);	//remove uid field
	        fputcsv($f, $line, ','); 
	    }

	    fseek($f, 0);
	    header('Content-Type: text/csv');
	    header('Content-Disposition: attachment; filename='.$row['user_group_name']);
	    fpassthru($f);
	}
	else
		echo(json_encode(['result' => 'failed', 'error' => 'Error updating row. User group not found!']));	
}

function getUserGroupList($conn){
	$resp = [];
	$DTime_info = getTimeInfo($conn);
	$client_id = getCurrentClientId();
	
	$stmt = $conn->prepare("SELECT user_group_id,user_group_name,JSON_LENGTH(user_data) as user_count,date FROM tb_core_mailcamp_user_group WHERE client_id = ?");
	$stmt->bind_param('s', $client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result->num_rows > 0){
		foreach ($result->fetch_all(MYSQLI_ASSOC) as $row){
			$row["user_data"] = json_decode($row["user_data"]);	//avoid double json encoding
			$row["date"] = getInClientTime_FD($DTime_info,$row['date'],null,'d-m-Y h:i A');
        	array_push($resp,$row);
		}
		echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);
	}
	else
		echo json_encode(['error' => 'No data']);
	$stmt->close();
}

function uploadUserCVS($conn, &$POSTJ){
	$user_group_id = isset($POSTJ['user_group_id']) ? $POSTJ['user_group_id'] : null;
	$user_group_name = isset($POSTJ['user_group_name']) ? $POSTJ['user_group_name'] : null;
	$raw_data = isset($POSTJ['user_data']) ? $POSTJ['user_data'] : null;

	if(empty($user_group_id) || empty($user_group_name) || empty($raw_data))
		die(json_encode(['result' => 'failed', 'error' => 'Invalid input']));

	// Normalize newlines and remove BOM if exists
	$raw_data = preg_replace("/\r\n|\r/", "\n", $raw_data);
	$raw_data = preg_replace('/^\xEF\xBB\xBF/', '', $raw_data);

	$lines = array_filter(array_map('trim', explode("\n", $raw_data)));
	if(count($lines) == 0)
		die(json_encode(['result' => 'failed', 'error' => 'Empty file or invalid format']));

	// Detect header: check first line for common column names
	$first_line = array_shift($lines);
	$first_cols = str_getcsv($first_line);
	$lower_cols = array_map(function($c){ return strtolower(trim($c)); }, $first_cols);
	$has_header = false;
	$header_map = [];
	$known_keys = ['email','e-mail','first name','firstname','fname','last name','lastname','lname','notes','note'];
	foreach($lower_cols as $idx => $col){
		foreach($known_keys as $k){
			if(strpos($col,$k) !== false){
				$has_header = true;
				$header_map[$k] = $idx; // store index for partial keys
			}
		}
	}

	// If no header, put the first line back as data
	if(!$has_header)
		array_unshift($lines, $first_line);

	$arr_users = [];
	$errors = [];

	foreach($lines as $lineno => $line){
		if(trim($line) === '') continue;
		$cols = str_getcsv($line);
		// trim all cols
		$cols = array_map('trim', $cols);

		// find email column by heuristics
		$email = null;
		if($has_header){
			// try header_map for 'email' like keys
			foreach($header_map as $k => $idx){
				if(strpos($k,'email') !== false && isset($cols[$idx])){ $email = $cols[$idx]; break; }
			}
			// fallback: search any column for valid email
			if(empty($email)){
				foreach($cols as $c){ if(isValidEmail($c)){ $email = $c; break; } }
			}
			// names and notes using header positions if available
			$fname = null; $lname = null; $notes = null;
			foreach($header_map as $k => $idx){
				if((strpos($k,'first')!==false || strpos($k,'fname')!==false) && isset($cols[$idx])) $fname = $cols[$idx];
				if((strpos($k,'last')!==false || strpos($k,'lname')!==false) && isset($cols[$idx])) $lname = $cols[$idx];
				if(strpos($k,'note')!==false && isset($cols[$idx])) $notes = $cols[$idx];
			}
			// best-effort fallback for names if header didn't provide
			if(empty($fname) && !empty($cols)) $fname = $cols[0];
			if(empty($lname) && count($cols) > 1) $lname = $cols[1];
			if(empty($notes) && count($cols) > 2) $notes = end($cols);
		}
		else{
			// No header: try to locate email and assign adjacent columns to names/notes
			foreach($cols as $idx => $c){ if(isValidEmail($c)){ $email = $c; $email_idx = $idx; break; } }
			if($email !== null){
				// assume firstname is before email if exists
				$fname = isset($cols[0]) && $email_idx != 0 ? $cols[0] : null;
				$lname = ($email_idx > 1) ? $cols[1] : null;
				// notes can be after email
				$notes = isset($cols[$email_idx+1]) ? $cols[$email_idx+1] : null;
			} else {
				$errors[] = 'Invalid or missing email at line ' . ($lineno+2);
				continue;
			}
		}

		if(empty($email) || !isValidEmail($email)){
			$errors[] = 'Invalid email at line ' . ($lineno+2) . ' -> ' . ($email ?? '');
			continue;
		}

		$uid = getRandomStr(10);
		$arr_users[] = ['uid'=>$uid, 'fname'=>$fname ?? 'Empty', 'lname'=>$lname ?? 'Empty', 'email'=>$email, 'notes'=>$notes ?? ''];
	}

	// merge with existing users (avoid duplicates by email)
	$row = getUserGroupFromGroupId($conn, $user_group_id);
	$old_user_data = [];
	if(!empty($row) && !empty($row['user_data'])) {
		$old_user_data = json_decode($row['user_data'], true);
		if(!is_array($old_user_data)) $old_user_data = [];
	}

	$existing_emails = array_map(function($u){ return strtolower($u['email']); }, $old_user_data);
	$added = 0;
	foreach($arr_users as $u){
		if(!in_array(strtolower($u['email']), $existing_emails)){
			$old_user_data[] = $u;
			$existing_emails[] = strtolower($u['email']);
			$added++;
		}
	}

	$user_data_json = json_encode(array_values($old_user_data), JSON_INVALID_UTF8_IGNORE);

	if(checkAnIDExist($conn,$user_group_id,'user_group_id','tb_core_mailcamp_user_group')){
		$stmt = $conn->prepare("UPDATE tb_core_mailcamp_user_group SET user_group_name=?, user_data=? WHERE user_group_id=?");
		$stmt->bind_param('sss', $user_group_name,$user_data_json,$user_group_id);
	}
	else{
		$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_user_group(user_group_id,user_group_name,user_data,date) VALUES(?,?,?,?)");
		$stmt->bind_param('ssss', $user_group_id,$user_group_name,$user_data_json,$GLOBALS['entry_time']);
	}

	if($stmt->execute() === TRUE){
		$resp = ['result' => 'success', 'imported' => $added, 'errors' => $errors];
		echo(json_encode($resp));
	}
	else 
		echo(json_encode(['result' => 'failed', 'error' => 'Error importing user data! '.$stmt->error]));
}

function getUserGroupFromGroupIdTable($conn,&$POSTJ){
	$offset = htmlspecialchars($POSTJ['start']);
	$limit = htmlspecialchars($POSTJ['length']);
	$draw = htmlspecialchars($POSTJ['draw']);
	$search_value = htmlspecialchars($POSTJ['search']['value']);
	$data = array();
	$columnSortOrder = $POSTJ['order'][0]['dir'] == 'asc'?'asc':'desc'; // asc or desc
	$totalRecords = 0;
	$user_group_id = $POSTJ['user_group_id'];

	if(empty($search_value))
		$totalRecords_with_filter = $totalRecords;
	else
		$totalRecords_with_filter = 0;	//will be updated from below

	$arr_filtered=[];
	$row = getUserGroupFromGroupId($conn, $user_group_id);

	if(!empty($row)){
		$user_data = json_decode($row["user_data"],true);
		if(!empty($user_data) && is_array($user_data)){
			foreach ($user_data as $item){
			    $m_array = preg_grep('/.*'.$search_value.'.*/', $item);
			    if(!empty($m_array))
			    	array_push($arr_filtered, $item);
			}
			$totalRecords = sizeof($user_data);
		} else {
			$totalRecords = 0;
			$arr_filtered = [];
		}

		$totalRecords_with_filter = sizeof($arr_filtered);
		$resp = array(
		  "draw" => intval($draw),
		  "recordsTotal" => intval($totalRecords),
		  "recordsFiltered" => intval($totalRecords_with_filter),
		  "data" => array_slice($arr_filtered, $offset, $limit)
		);

		$resp['user_group_name'] = $row['user_group_name'];
		echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);
	}		
	else {
		// Retornar estrutura válida mesmo quando não há dados
		$resp = array(
		  "draw" => intval($draw),
		  "recordsTotal" => 0,
		  "recordsFiltered" => 0,
		  "data" => [],
		  "user_group_name" => ""
		);
		echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);
	}
}

function deleteUserGroupFromGroupId($conn,$user_group_id){	
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("DELETE FROM tb_core_mailcamp_user_group WHERE user_group_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $user_group_id, $client_id);
	$stmt->execute();
	if($stmt->affected_rows != 0)
		echo json_encode(['result' => 'success']);	
	else
		echo json_encode(['result' => 'failed', 'error' => 'User group does not exist']);	
	$stmt->close();
}

function makeCopyUserGroup($conn, $old_user_group_id, $new_user_group_id, $new_user_group_name){
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_user_group (user_group_id,user_group_name,user_data,date,client_id) SELECT ?, ?,user_data,?,client_id FROM tb_core_mailcamp_user_group WHERE user_group_id=? AND client_id=?");
	$stmt->bind_param("sssss", $new_user_group_id, $new_user_group_name, $GLOBALS['entry_time'], $old_user_group_id, $client_id);
	
	if($stmt->execute() === TRUE){
		echo(json_encode(['result' => 'success']));	
	}
	else 
		echo(json_encode(['result' => 'failed', 'error' => 'Error making copy!']));	
	$stmt->close();
}

function getUserGroupFromGroupId($conn, $user_group_id){
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT * FROM tb_core_mailcamp_user_group WHERE user_group_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $user_group_id, $client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	if($result->num_rows != 0)
		return $result->fetch_assoc();
	return [];
}
//---------------------------------------Email Template Section --------------------------------

function saveMailTemplate($conn,&$POSTJ){
	$mail_template_id = $POSTJ['mail_template_id'];
	if($mail_template_id == '')
		$mail_template_id = null;

	$mail_template_name = $POSTJ['mail_template_name'];
	$mail_template_subject = $POSTJ['mail_template_subject'];
	$mail_template_content = $POSTJ['mail_template_content'];
	$timage_type = $POSTJ['timage_type'];
	$attachments = json_encode($POSTJ['attachments']);
	$mail_content_type = $POSTJ['mail_content_type'];
	$current_client_id = getCurrentClientId();

	if(checkAnIDExist($conn,$mail_template_id,'mail_template_id','tb_core_mailcamp_template_list')){
		$stmt = $conn->prepare("UPDATE tb_core_mailcamp_template_list SET mail_template_name=?, mail_template_subject=?, mail_template_content=?, timage_type=?, mail_content_type=?, attachment=? WHERE mail_template_id=? AND client_id=?");
		$stmt->bind_param('ssssssss', $mail_template_name,$mail_template_subject, $mail_template_content,$timage_type,$mail_content_type,$attachments,$mail_template_id,$current_client_id);
	}
	else{
		$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_template_list(mail_template_id, client_id, mail_template_name, mail_template_subject, mail_template_content, timage_type, mail_content_type, attachment, date) VALUES(?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param('sssssssss', $mail_template_id,$current_client_id,$mail_template_name,$mail_template_subject,$mail_template_content,$timage_type,$mail_content_type,$attachments,$GLOBALS['entry_time']);
	}
	
	if ($stmt->execute() === TRUE){
		echo(json_encode(['result' => 'success']));	
	}
	else 
		echo(json_encode(['result' => 'failed', 'error' => $stmt->error]));	
}

function getMailTemplateList($conn){
	$resp = [];
	$DTime_info = getTimeInfo($conn);
	$current_client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT mail_template_id, mail_template_name, LEFT(mail_template_subject , 50) mail_template_subject, LEFT(mail_template_content , 50) mail_template_content,attachment,date FROM tb_core_mailcamp_template_list WHERE client_id = ?");
	$stmt->bind_param('s', $current_client_id);
	$stmt->execute();
	$result = $stmt->get_result();

	if($result && $result->num_rows > 0){
		foreach ($result->fetch_all(MYSQLI_ASSOC) as $row){
			$row["attachment"] = json_decode($row["attachment"]);	//avoid double json encoding
			$row["date"] = getInClientTime_FD($DTime_info,$row['date'],null,'d-m-Y h:i A');
        	array_push($resp,$row);
		}
		echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);
	}
	else
		echo json_encode(['error' => 'No data']);	
	$result->close();
}

function getMailTemplateFromTemplateId($conn, $mail_template_id){
	$current_client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT * FROM tb_core_mailcamp_template_list WHERE mail_template_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $mail_template_id, $current_client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows != 0){
		$row = $result->fetch_assoc() ;
		$row['attachment'] = json_decode($row['attachment']);
		echo json_encode($row, JSON_INVALID_UTF8_IGNORE) ;
	}
	else
		echo json_encode(['error' => 'No data']);				
	$stmt->close();
}

function deleteMailTemplateFromTemplateId($conn,$mail_template_id){	
	$current_client_id = getCurrentClientId();
	$stmt = $conn->prepare("DELETE FROM tb_core_mailcamp_template_list WHERE mail_template_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $mail_template_id, $current_client_id);
	$stmt->bind_param("s", $mail_template_id);
	$stmt->execute();
	if($stmt->affected_rows != 0)
		echo json_encode(['result' => 'success']);	
	else
		echo json_encode(['result' => 'failed', 'error' => 'Mail template does not exist']);	
	$stmt->close();
}

function makeCopyMailTemplate($conn, $old_mail_template_id, $new_mail_template_id, $new_mail_template_name){
	$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_template_list (mail_template_id,mail_template_name,mail_template_subject,mail_template_content,timage_type,mail_content_type,attachment,date) SELECT ?, ?, mail_template_subject,mail_template_content,timage_type,mail_content_type,attachment,? FROM tb_core_mailcamp_template_list WHERE mail_template_id=?");
	$stmt->bind_param("ssss", $new_mail_template_id, $new_mail_template_name, $GLOBALS['entry_time'], $old_mail_template_id);
	
	if ($stmt->execute() === TRUE)
		echo json_encode(['result' => 'success']);	
	else
		echo json_encode(['result' => 'failed', 'error' => $stmt->error]);	
	$stmt->close();
}

function uploadTrackerImage($conn,&$POSTJ){
	$mail_template_id = $POSTJ['mail_template_id'];
	$file_name = filter_var($POSTJ['file_name'], FILTER_SANITIZE_STRING);
	$file_b64 = explode(',', $POSTJ['file_b64'])[1];
	$binary_data = base64_decode($file_b64);

	$target_file = dirname(__FILE__,2).'/uploads/timages/'.$mail_template_id.'.timg';
	if(getimagesizefromstring($binary_data)){
        try{
        	file_put_contents($target_file,$binary_data);
        	echo(json_encode(['result' => 'success']));	
        }catch(Exception $e) {
			echo(json_encode(['result' => 'failed', 'error' => $e->getMessage()]));	
		}        	
    }
    else
    	echo(json_encode(['result' => 'failed', 'error' => 'Invalid file']));	
}

function uploadAttachment($conn,&$POSTJ){
	$mail_template_id = $POSTJ['mail_template_id'];
	$file_name = filter_var($POSTJ['file_name'], FILTER_SANITIZE_STRING);
	$file_b64 = explode(',', $POSTJ['file_b64'])[1];
	$binary_data = base64_decode($file_b64);
	$file_id = $mail_template_id.'_'.time();

	$target_file = dirname(__FILE__,2).'/uploads/attachments/'.$file_id.'.att';

	if (!is_dir(dirname(__FILE__,2).'/uploads/attachments/')) 
		die(json_encode(['result' => 'failed', 'error' => 'Directory spear/uploads/attachments/ does not exist']));
	if (!is_writable(dirname(__FILE__,2).'/uploads/attachments/')) 
		die(json_encode(['result' => 'failed', 'error' => 'Directory spear/uploads/attachments/ has no write permission']));

	try{
    	if(file_put_contents($target_file,$binary_data) || file_exists($target_file))	//if 0 size file failed, check if they exist (written)
    		echo(json_encode(['result' => 'success', 'file_id' => $file_id]));	
    	else
			echo(json_encode(['result' => 'failed', 'error' => 'File upload failed!']));	
    }catch(Exception $e) {
		echo(json_encode(['result' => 'failed', 'error' => $e->getMessage()]));	
	}       
}

function uploadMailBodyFiles($conn,&$POSTJ){
	$mail_template_id = $POSTJ['mail_template_id'];
	$file_name = filter_var($POSTJ['file_name'], FILTER_SANITIZE_STRING);
	$file_b64 = explode(',', $POSTJ['file_b64'])[1];
	$binary_data = base64_decode($file_b64);
	$file_id_part = time();
	$file_id = $mail_template_id.'_'.$file_id_part;

	$target_file = dirname(__FILE__,2).'/uploads/attachments/'.$file_id.'.mbf';

	if (!is_dir(dirname(__FILE__,2).'/uploads/attachments/')) 
		die(json_encode(['result' => 'failed', 'error' => 'Directory spear/uploads/attachments/ does not exist']));
	if (!is_writable(dirname(__FILE__,2).'/uploads/attachments/')) 
		die(json_encode(['result' => 'failed', 'error' => 'Directory spear/uploads/attachments/ has no write permission']));

    try{
    	if(file_put_contents($target_file,$binary_data) || file_exists($target_file))	//if 0 size file failed, check if they exist (written)
    		echo(json_encode(['result' => 'success', 'file_id' => $file_id, "mbf" => $file_id_part]));
    	else
    		echo(json_encode(['result' => 'failed', 'error' => 'File upload failed!']));
    }catch(Exception $e) {
    	echo(json_encode(['result' => 'failed', 'error' =>'File upload failed!']));
    }       
}

//---------------------------------------Sender List Section --------------------------------
function saveSenderList($conn, &$POSTJ){
	$sender_list_id = $POSTJ['sender_list_id'];
	$sender_list_mail_sender_name = $POSTJ['sender_list_mail_sender_name'];
	$sender_list_mail_sender_SMTP_server = $POSTJ['sender_list_mail_sender_SMTP_server'];
	$sender_list_mail_sender_from = $POSTJ['sender_list_mail_sender_from'];
	$sender_list_mail_sender_acc_username = $POSTJ['sender_list_mail_sender_acc_username'];
	$sender_list_mail_sender_acc_pwd = $POSTJ['sender_list_mail_sender_acc_pwd'];
	$auto_mailbox = $POSTJ['cb_auto_mailbox'];
	$mail_sender_mailbox = $POSTJ['mail_sender_mailbox'];
	$sender_list_cust_headers = json_encode($POSTJ['sender_list_cust_headers']); 
	$dsn_type = $POSTJ['dsn_type'];
	$client_id = getCurrentClientId();

	// Verificar se existe o sender para este cliente
	$stmt_check = $conn->prepare("SELECT COUNT(*) FROM tb_core_mailcamp_sender_list WHERE sender_list_id = ? AND client_id = ?");
	$stmt_check->bind_param('ss', $sender_list_id, $client_id);
	$stmt_check->execute();
	$result = $stmt_check->get_result();
	$exists = $result->fetch_row()[0] > 0;
	$stmt_check->close();

	if($exists){
		if($sender_list_mail_sender_acc_pwd != ''){	//new sender acc pwd
			$stmt = $conn->prepare("UPDATE tb_core_mailcamp_sender_list SET sender_name=?, sender_SMTP_server=?, sender_from=?, sender_acc_username=?, sender_acc_pwd=?, auto_mailbox=?, sender_mailbox=?, cust_headers=?, dsn_type=? WHERE sender_list_id=? AND client_id=?");
			$stmt->bind_param('sssssssssss', $sender_list_mail_sender_name,$sender_list_mail_sender_SMTP_server,$sender_list_mail_sender_from,$sender_list_mail_sender_acc_username,$sender_list_mail_sender_acc_pwd,$auto_mailbox,$mail_sender_mailbox,$sender_list_cust_headers,$dsn_type,$sender_list_id,$client_id);
		}
		else{	//sender acc pwd has no change
			$stmt = $conn->prepare("UPDATE tb_core_mailcamp_sender_list SET sender_name=?, sender_SMTP_server=?, sender_from=?, sender_acc_username=?, auto_mailbox=?, sender_mailbox=?, cust_headers=?, dsn_type=? WHERE sender_list_id=? AND client_id=?");
			$stmt->bind_param('ssssssssss', $sender_list_mail_sender_name,$sender_list_mail_sender_SMTP_server,$sender_list_mail_sender_from,$sender_list_mail_sender_acc_username,$auto_mailbox,$mail_sender_mailbox,$sender_list_cust_headers,$dsn_type,$sender_list_id,$client_id);
		}
	}
	else{
		$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_sender_list(sender_list_id,sender_name,sender_SMTP_server,sender_from,sender_acc_username,sender_acc_pwd,auto_mailbox,sender_mailbox,cust_headers,dsn_type,date,client_id) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param('ssssssssssss', $sender_list_id,$sender_list_mail_sender_name,$sender_list_mail_sender_SMTP_server,$sender_list_mail_sender_from,$sender_list_mail_sender_acc_username,$sender_list_mail_sender_acc_pwd,$auto_mailbox,$mail_sender_mailbox,$sender_list_cust_headers,$dsn_type,$GLOBALS['entry_time'],$client_id);
	}
	
	if ($stmt->execute() === TRUE)
		echo json_encode(['result' => 'success']);
	else 
		echo json_encode(['result' => 'failed']);
	$stmt->close();
}

function getSenderList($conn){
	$resp = [];
	$DTime_info = getTimeInfo($conn);
	$client_id = getCurrentClientId();
	
	$stmt = $conn->prepare("SELECT sender_list_id,sender_name,sender_SMTP_server,sender_from,sender_acc_username,sender_mailbox,cust_headers,dsn_type,date FROM tb_core_mailcamp_sender_list WHERE client_id = ?");
	$stmt->bind_param('s', $client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	
	if($result->num_rows > 0){
		foreach ($result->fetch_all(MYSQLI_ASSOC) as $row){
			$row["cust_headers"] = json_decode($row["cust_headers"]);	//avoid double json encoding
			$row["date"] = getInClientTime_FD($DTime_info,$row['date'],null,'d-m-Y h:i A');
        	array_push($resp,$row);
		}
		echo json_encode($resp, JSON_INVALID_UTF8_IGNORE);
	}
	else
		echo json_encode(['error' => 'No data']);
	$stmt->close();
}

function getSenderFromSenderListId($conn, $sender_list_id){
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("SELECT sender_name,sender_SMTP_server,sender_from,sender_acc_username,auto_mailbox,sender_mailbox,cust_headers,dsn_type FROM tb_core_mailcamp_sender_list WHERE sender_list_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $sender_list_id, $client_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($result->num_rows > 0){
		$row = $result->fetch_assoc() ;
		$row["cust_headers"] = json_decode($row["cust_headers"]);	//avoid double json encoding
		echo json_encode($row, JSON_INVALID_UTF8_IGNORE) ;
	}			
	else
		echo json_encode(['error' => 'No data']);	
	$stmt->close();
}

function deleteMailSenderListFromSenderId($conn, $sender_list_id){	
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("DELETE FROM tb_core_mailcamp_sender_list WHERE sender_list_id = ? AND client_id = ?");
	$stmt->bind_param("ss", $sender_list_id, $client_id);
	$stmt->execute();
	if($stmt->affected_rows != 0)
		echo json_encode(['result' => 'success']);	
	else
		echo json_encode(['result' => 'failed', 'error' => 'Error deleting sender!']);	
	$stmt->close();
}

function makeCopyMailSenderList($conn, $old_sender_list_id, $new_sender_list_id, $new_sender_list_name){
	$client_id = getCurrentClientId();
	$stmt = $conn->prepare("INSERT INTO tb_core_mailcamp_sender_list (sender_list_id,sender_name,sender_SMTP_server,sender_from,sender_acc_username,sender_acc_pwd,auto_mailbox,sender_mailbox,cust_headers,dsn_type,date,client_id) SELECT ?, ?, sender_SMTP_server,sender_from,sender_acc_username,sender_acc_pwd,auto_mailbox,sender_mailbox,cust_headers,dsn_type,?,client_id FROM tb_core_mailcamp_sender_list WHERE sender_list_id=? AND client_id=?");
	$stmt->bind_param("sssss", $new_sender_list_id, $new_sender_list_name, $GLOBALS['entry_time'], $old_sender_list_id, $client_id);
	
	if ($stmt->execute() === TRUE)
		echo json_encode(['result' => 'success']);	
	else
		echo json_encode(['result' => 'failed', 'error' => $stmt->error]);	
	$stmt->close();
}

function verifyMailboxAccess($conn, $POSTJ){
	$sender_list_id = $POSTJ['sender_list_id'];
	$sender_username = $POSTJ['mail_sender_acc_username'];
	$sender_pwd = $POSTJ['mail_sender_acc_pwd'];
	$sender_mailbox = $POSTJ['mail_sender_mailbox'];

	if(empty($sender_pwd))
		$sender_pwd = getSenderPwd($conn, $sender_list_id);

	if(empty($sender_pwd))
		die(json_encode(['result' => 'failed', 'error' => "Sender list does not exist. Please fill the password field"]));	
	else{
		try{
			$imap_obj = imap_open($sender_mailbox,$sender_username,$sender_pwd);		
	    	$resp = ['result' => 'success', 'total_msg_count' => imap_num_msg($imap_obj)];
		} catch (Exception $e) {
	  		$resp = ['result' => 'failed', 'error' =>$e->getMessage()];
		}

		$imap_err = imap_errors(); //required to capture imap errors
		if(!empty($imap_err))
			$resp = ['result' => 'failed', 'error' => $imap_err];	
	}	

	echo json_encode($resp);
}

//---------------------------------------End Sender List Section --------------------------------
//====================================================================================================
function sendTestMailVerification($conn,$POSTJ){
	$sender_list_id = $POSTJ['sender_list_id'];
	$smtp_server = $POSTJ['sender_list_mail_sender_SMTP_server'];
	$sender_from = $POSTJ['sender_list_mail_sender_from'];
	$sender_username = $POSTJ['sender_list_mail_sender_acc_username'];
	$sender_pwd = $POSTJ['sender_list_mail_sender_acc_pwd'];
	$cust_headers = $POSTJ['sender_list_cust_headers'];
	$test_to_address = $POSTJ['test_to_address'];
	$mail_subject = "SniperPhish Test Mail";
	$mail_body = "Success. Here is the test message body";
	$mail_content_type = "text/plain";
	$dsn_type = $POSTJ['dsn_type'];
	$message = (new Email());

	//-----------------------------------
	if(empty($sender_pwd))
		$sender_pwd = getSenderPwd($conn, $sender_list_id);

	if(empty($sender_pwd))
		die(json_encode(['result' => 'failed', 'error' => "Sender list does not exist. Please fill the password field"]));	
	else
		shootMail($message,$smtp_server,$sender_username,$sender_pwd,$sender_from,$test_to_address,$cust_headers,$mail_subject,$mail_body,$mail_content_type,$dsn_type);
}

function sendTestMailSample($conn,$POSTJ){
	$sender_list_id = $POSTJ['sender_list_id'];
	$smtp_server = $POSTJ['smtp_server'];
	$sender_from = $POSTJ['sender_from'];
	$sender_username = $POSTJ['sender_username'];
	$sender_pwd = $POSTJ['sender_pwd'];
	$cust_headers = $POSTJ['cust_headers'];
	$test_to_address = $POSTJ['test_to_address'];
	$mail_subject = $POSTJ['mail_subject'];
	$mail_body = $POSTJ['mail_body'];
	$mail_content_type = $POSTJ['mail_content_type'];
	$mail_attachment = $POSTJ['attachments'];


	$keyword_vals = array();
	$serv_variables = getServerVariable($conn);
	$RID = getRandomStr(10);

    $keyword_vals['{{RID}}'] = $RID;
    $keyword_vals['{{MID}}'] = "MailCampaign_id";
    $keyword_vals['{{NAME}}'] = "ABC XYZ";
    $keyword_vals['{{FNAME}}'] = "ABC";
    $keyword_vals['{{LNAME}}'] = "XYZ";
    $keyword_vals['{{NOTES}}'] = "Note_content";
    $keyword_vals['{{EMAIL}}'] = $test_to_address;
    $keyword_vals['{{FROM}}'] = $sender_from;
    $keyword_vals['{{TRACKINGURL}}'] = $serv_variables['baseurl'].'/tmail?mid='."MailCampaign_id".'&rid='.$RID;
    $keyword_vals['{{TRACKER}}'] = '<img src="'.$keyword_vals['{{TRACKINGURL}}'].'"/>';
    $keyword_vals['{{BASEURL}}'] = $serv_variables['baseurl'];
	$keyword_vals['{{MUSERNAME}}'] = explode('@', $test_to_address)[0];
	$keyword_vals['{{MDOMAIN}}'] = explode('@', $test_to_address)[1];

	if(empty($sender_pwd)){
		$stmt = $conn->prepare("SELECT sender_acc_pwd FROM tb_core_mailcamp_sender_list WHERE sender_list_id = ?");
		$stmt->bind_param("s", $sender_list_id);
		$stmt->execute();
		$result = $stmt->get_result();
		if($row = $result->fetch_assoc())
			$sender_pwd = $row['sender_acc_pwd'];
		else
			die(json_encode(['result' => 'failed', 'error' => "Sender list does not exist. Please fill the password field"]));	
	}

	$message = (new Email());
	$mail_subject = filterKeywords($mail_subject,$keyword_vals);
	$mail_body = filterKeywords($mail_body,$keyword_vals);  	
	$mail_body = filterQRBarCode($mail_body,$keyword_vals,$message);

	foreach ($mail_attachment as $attachment) {
		$file_path = dirname(__FILE__,2).'/uploads/attachments/'.$attachment['file_id'].'.att';
		$file_disp_name = filterKeywords($attachment['file_disp_name'],$keyword_vals);

		if($attachment['inline'])
	    	$message->embedFromPath($file_path, $file_disp_name);
	    else
	    	$message->attachFromPath($file_path, $file_disp_name);
	}

	//---------------------------
	shootMail($message,$smtp_server,$sender_username,$sender_pwd,$sender_from,$test_to_address,$cust_headers,$mail_subject,$mail_body,$mail_content_type);  
}
//===================================================================================================
function getSenderPwd(&$conn, &$sender_list_id){
	$stmt = $conn->prepare("SELECT sender_acc_pwd FROM tb_core_mailcamp_sender_list WHERE sender_list_id = ?");
	$stmt->bind_param("s", $sender_list_id);
	$stmt->execute();
	$result = $stmt->get_result();
	if($row = $result->fetch_assoc())
		return $row['sender_acc_pwd'];
	else
		return "";
}
?>
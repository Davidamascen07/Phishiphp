<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
require_once(dirname(__FILE__) . '/spear/config/db.php');
require_once(dirname(__FILE__) . '/spear/manager/common_functions.php');
require_once(dirname(__FILE__) . '/spear/libs/browser_detect/BrowserDetection.php');
date_default_timezone_set('UTC');

// LOG DEBUG FUNCTION
function debug_log($message) {
    $timestamp = date('Y-m-d H:i:s');
    error_log("[$timestamp] TRACK DEBUG: $message");
    // Também adicionar ao output se em modo debug
    if (isset($_GET['debug'])) {
        echo "DEBUG: $message\n";
    }
}

debug_log("=== TRACK.PHP INICIADO ===");
debug_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
debug_log("CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'não definido'));

//-------------------------------------
if (isset($_POST)) {
    $input = file_get_contents('php://input');
    debug_log("Raw input length: " . strlen($input));
    debug_log("Raw input content: " . substr($input, 0, 500) . (strlen($input) > 500 ? '...' : ''));
    
    $POSTJ = json_decode($input, true);
    debug_log("JSON decode result: " . ($POSTJ ? 'SUCCESS' : 'FAILED'));
    debug_log("JSON decode error: " . json_last_error_msg());
} else {
    debug_log("ERROR: _POST not set");
    die();
}

//---SP verification req----
if(isset($POSTJ['sp_ver'])) {
    debug_log("SP verification request detected");
    die("success");
}
//--------------------------

// RID handling - allow empty RID (fix for tracker without rid parameter)
debug_log("Checking RID in received data...");
debug_log("POSTJ keys: " . implode(', ', array_keys($POSTJ)));

if(isset($POSTJ['rid'])) {
    $rid_raw = $POSTJ['rid'];
    debug_log("RID raw value: '" . $rid_raw . "' (length: " . strlen($rid_raw) . ")");
    $rid = doFilter($POSTJ['rid'],'ALPHA_NUM');
    debug_log("RID after filter: '" . $rid . "' (length: " . strlen($rid) . ")");
} else {
    debug_log("RID not present in data, using empty string");
    $rid = ''; // Allow empty RID instead of dying
}

if(isset($POSTJ['sess_id'])) {
    $session_id = doFilter($POSTJ['sess_id'],'ALPHA_NUM');
    debug_log("Session ID: $session_id");
} else {
    $session_id = 'Failed';
    debug_log("Session ID not found, using 'Failed'");
}

if(isset($POSTJ['trackerId'])) {
    $trackerId = doFilter($POSTJ['trackerId'],'ALPHA_NUM');
    debug_log("Tracker ID: $trackerId");
} else {
    $trackerId = 'Failed';
    debug_log("Tracker ID not found, using 'Failed'");
}

$ua_info = new Wolfcast\BrowserDetection();
$public_ip = getPublicIP();
debug_log("Public IP: $public_ip");

$user_agent = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);    

$date_time = round(microtime(true) * 1000);
$user_browser = $ua_info->getName().' '.($ua_info->getVersion() == "unknown"?"":$ua_info->getVersion());
$user_os = $ua_info->getPlatformVersion();
$device_type = $ua_info->isMobile()?"Mobile":"Desktop";

debug_log("Browser: $user_browser");
debug_log("OS: $user_os");
debug_log("Device: $device_type");

if(empty($POSTJ['ip_info']))
    $ip_info = getIPInfo($conn, $public_ip);
else
    $ip_info = json_encode(craftIPInfoArr($POSTJ['ip_info']));

//-----------------------------------
if(isset($POSTJ['screen_res']))
    $screen_res = htmlspecialchars($POSTJ['screen_res']);
else
    $screen_res = 'Failed'; 

//Check tracker stopped/paused
debug_log("Checking if tracker is active...");
$stmt = $conn->prepare("SELECT active FROM tb_core_web_tracker_list WHERE tracker_id = ?");
$stmt->bind_param("s", $trackerId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
if(!$result) {
    debug_log("ERROR: Tracker not found in database");
    die("Tracker not found");
}
if($result["active"] == 0) {
    debug_log("WARNING: Tracker is inactive");
    return;
}
debug_log("Tracker is active, proceeding...");

$page = $POSTJ['page'];
debug_log("Page value: $page");

if($page == 0) {  //page visit
    debug_log("Processing PAGE VISIT...");
    debug_log("Preparing INSERT with RID: '$rid'");
    
    $stmt = $conn->prepare("INSERT INTO tb_data_webpage_visit(tracker_id,session_id,rid,public_ip,ip_info,user_agent,screen_res,time,browser,platform,device_type) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssssss', $trackerId,$session_id,$rid,$public_ip,$ip_info,$user_agent,$screen_res,$date_time,$user_browser,$user_os,$device_type);
    
    debug_log("SQL prepared, executing...");
    if ($stmt->execute() === TRUE) {
        debug_log("SUCCESS: Page visit inserted with RID: '$rid'");
        die('success'); 
    } else {
        debug_log("ERROR: Failed to insert page visit: " . $stmt->error);
        die("failed"); 
    }
}  
elseif(is_numeric($page)) {
    debug_log("Processing FORM SUBMIT for page $page...");
    
    foreach ($POSTJ['form_field_data'] as $i => $field_data) {
        $POSTJ['form_field_data'][$i] = htmlspecialchars($POSTJ['form_field_data'][$i]);
    }
    $form_field_data = json_encode($POSTJ['form_field_data']);
    debug_log("Form data: $form_field_data");
    debug_log("Preparing INSERT with RID: '$rid'");
    
    $stmt = $conn->prepare("INSERT INTO tb_data_webform_submit(tracker_id,session_id,rid,public_ip,ip_info,user_agent,screen_res,time,browser,platform,device_type,page,form_field_data) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssssssss', $trackerId,$session_id,$rid,$public_ip,$ip_info,$user_agent,$screen_res,$date_time,$user_browser,$user_os,$device_type,$page,$form_field_data);
    
    debug_log("SQL prepared, executing...");
    if ($stmt->execute() === TRUE) {
        debug_log("SUCCESS: Form submit inserted with RID: '$rid'");
        die('success'); 
    } else {
        debug_log("ERROR: Failed to insert form submit: " . $stmt->error);
        die("failed"); 
    }
}

debug_log("=== TRACK.PHP FINALIZADO ===");
?>
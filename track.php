<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
require_once(dirname(__FILE__) . '/spear/config/db.php');
require_once(dirname(__FILE__) . '/spear/manager/common_functions.php');
require_once(dirname(__FILE__) . '/spear/manager/user_campaign_hooks.php');
require_once(dirname(__FILE__) . '/spear/libs/browser_detect/BrowserDetection.php');
date_default_timezone_set('UTC');
//-------------------------------------
if (isset($_POST))
    $POSTJ = json_decode(file_get_contents('php://input'),true);
else
    die();

//---SP verification req----
if(isset($POSTJ['sp_ver']))
    die("success");
//--------------------------

// RID handling - allow empty RID and preserve common characters (fix for tracker without rid parameter)
if(isset($POSTJ['rid'])) {
    // More permissive filter for RID - allow alphanumeric, underscore, hyphen, dot
    $rid = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $POSTJ['rid']);
    // Limit length for security
    $rid = substr($rid, 0, 100);
} else {
    $rid = ''; // Allow empty RID instead of dying
}
    
if(isset($POSTJ['sess_id']))
    $session_id = doFilter($POSTJ['sess_id'],'ALPHA_NUM');
else
    $session_id = 'Failed';

if(isset($POSTJ['trackerId']))
    $trackerId = doFilter($POSTJ['trackerId'],'ALPHA_NUM');
else
    $trackerId = 'Failed';

$ua_info = new Wolfcast\BrowserDetection();
$public_ip = getPublicIP();

$user_agent = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);    

$date_time = round(microtime(true) * 1000);
$user_browser = $ua_info->getName().' '.($ua_info->getVersion() == "unknown"?"":$ua_info->getVersion());
$user_os = $ua_info->getPlatformVersion();
$device_type = $ua_info->isMobile()?"Mobile":"Desktop";
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
$stmt = $conn->prepare("SELECT active FROM tb_core_web_tracker_list WHERE tracker_id = ?");
$stmt->bind_param("s", $trackerId);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc() ;
if($result["active"] == 0)
  return;
  
$page = $POSTJ['page'];
if($page == 0){  //page visit
	$stmt = $conn->prepare("INSERT INTO tb_data_webpage_visit(tracker_id,session_id,rid,public_ip,ip_info,user_agent,screen_res,time,browser,platform,device_type) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param('sssssssssss', $trackerId,$session_id,$rid,$public_ip,$ip_info,$user_agent,$screen_res,$date_time,$user_browser,$user_os,$device_type);
	if ($stmt->execute() === TRUE) {
		// Hook: Registrar visita da página web
		if (!empty($rid) && filter_var($rid, FILTER_VALIDATE_EMAIL)) {
			$page_info = [
				'user_agent' => $user_browser,
				'platform' => $user_os,
				'device_type' => $device_type,
				'ip' => $public_ip,
				'screen_res' => $screen_res
			];
			onWebPageVisited($conn, $rid, $trackerId, $page_info);
		}
		die('success'); 
	} else {
		die("failed"); 
	}
}  
elseif(is_numeric($page)){
    foreach ($POSTJ['form_field_data'] as $i => $field_data) {
        $POSTJ['form_field_data'][$i] = htmlspecialchars($POSTJ['form_field_data'][$i]);
    }
    $form_field_data = json_encode($POSTJ['form_field_data']);
	
	$stmt = $conn->prepare("INSERT INTO tb_data_webform_submit(tracker_id,session_id,rid,public_ip,ip_info,user_agent,screen_res,time,browser,platform,device_type,page,form_field_data) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param('sssssssssssss', $trackerId,$session_id,$rid,$public_ip,$ip_info,$user_agent,$screen_res,$date_time,$user_browser,$user_os,$device_type,$page,$form_field_data);
	if ($stmt->execute() === TRUE) {
		// Hook: Registrar submissão de formulário web
		if (!empty($rid) && filter_var($rid, FILTER_VALIDATE_EMAIL)) {
			onWebFormSubmitted($conn, $rid, $trackerId, $POSTJ['form_field_data']);
		}
		die('success'); 
	} else {
		die("failed"); 
	}
}

//-----------------------------------------


?>
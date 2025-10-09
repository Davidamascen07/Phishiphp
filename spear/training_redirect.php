<?php
/**
 * Training Redirect Handler
 * Endpoint para redirecionamento automático baseado em triggers de campanha
 */

require_once 'manager/common_functions.php';
require_once 'manager/training_manager.php';

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    // Get and decode JSON input
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    
    // Fallback to POST/GET data if JSON is not provided
    if (!$data) {
        $data = array_merge($_POST, $_GET);
    }
    
    $tracker_id = $data['tracker_id'] ?? '';
    $user_id = $data['user_id'] ?? '';
    $session_id = $data['session_id'] ?? '';
    $trigger_type = $data['trigger_type'] ?? 'on_interaction';
    $interaction_type = $data['interaction_type'] ?? 'click';
    $campaign_id = $data['campaign_id'] ?? '';
    
    // Log the redirect request for analytics
    logTrainingRedirectRequest($data);
    
    // Determine training configuration based on context
    $training_config = null;
    
    if (!empty($campaign_id)) {
        // Check if campaign has training configured
        $training_config = getTrainingConfigForCampaign($campaign_id);
    } elseif (!empty($tracker_id)) {
        // Check if tracker has training configured
        $training_config = getTrainingConfigForTracker($tracker_id);
    }
    
    if (!$training_config || !$training_config['is_active']) {
        // No training configured or not active
        $response = array(
            'result' => 'success',
            'training_redirect' => false,
            'message' => 'No training configured for this trigger'
        );
    } else {
        // Build training URL
        $training_url = buildTrainingRedirectUrl($training_config, $data);
        
        $response = array(
            'result' => 'success',
            'training_redirect' => true,
            'training_url' => $training_url,
            'delay_seconds' => $training_config['delay_seconds'] ?? 2,
            'module_id' => $training_config['module_id'],
            'trigger_event' => $training_config['trigger_event'] ?? 'click'
        );
    }
    
} catch (Exception $e) {
    $response = array(
        'result' => 'error',
        'error' => $e->getMessage(),
        'training_redirect' => false
    );
    http_response_code(400);
}

// Output JSON response
echo json_encode($response);

/**
 * Get training configuration for campaign
 */
function getTrainingConfigForCampaign($campaign_id) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT cta.*, tm.module_name, tm.content_data
                FROM tb_campaign_training_association cta
                JOIN tb_training_modules tm ON cta.module_id = tm.module_id
                WHERE cta.campaign_id = ? 
                AND cta.campaign_type = 'mail' 
                AND cta.is_active = 1
                AND tm.status = 1
                ORDER BY cta.created_date DESC
                LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $campaign_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
        
    } catch (Exception $e) {
        error_log("Error getting training config for campaign: " . $e->getMessage());
        return null;
    } finally {
        $db->close();
    }
}

/**
 * Get training configuration for web tracker
 */
function getTrainingConfigForTracker($tracker_id) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT cta.*, tm.module_name, tm.content_data
                FROM tb_campaign_training_association cta
                JOIN tb_training_modules tm ON cta.module_id = tm.module_id
                WHERE cta.campaign_id = ? 
                AND cta.campaign_type = 'web' 
                AND cta.is_active = 1
                AND tm.status = 1
                ORDER BY cta.created_date DESC
                LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $tracker_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
        
    } catch (Exception $e) {
        error_log("Error getting training config for tracker: " . $e->getMessage());
        return null;
    } finally {
        $db->close();
    }
}

/**
 * Build training redirect URL
 */
function buildTrainingRedirectUrl($training_config, $request_data) {
    $base_url = getBaseUrlForTraining();
    
    // Extract module content to get video URL or redirect to appropriate training page
    $content_data = json_decode($training_config['content_data'] ?? '{}', true);
    $video_url = $content_data['video_url'] ?? '';
    
    $params = array(
        'user_id' => $request_data['user_id'] ?? generateGuestUserId(),
        'module_id' => $training_config['module_id'],
        'tracker_id' => $request_data['tracker_id'] ?? '',
        'trigger_type' => $request_data['trigger_type'] ?? 'redirect',
        'session_id' => $request_data['session_id'] ?? ''
    );
    
    // If video URL is available, redirect to training player
    if (!empty($video_url)) {
        $params['video'] = $video_url;
        return $base_url . '/training_player.php?' . http_build_query($params);
    } else {
        // Redirect to training management page or quiz
        return $base_url . '/TrainingManagement.php?' . http_build_query($params);
    }
}

/**
 * Generate guest user ID for anonymous users
 */
function generateGuestUserId() {
    return 'guest_' . uniqid() . '_' . time();
}

/**
 * Log training redirect request for analytics
 */
function logTrainingRedirectRequest($request_data) {
    global $conn; $db = $conn;
    
    try {
        $log_data = array(
            'timestamp' => time(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
            'request_data' => $request_data
        );
        
        // Store in a simple log table or file
        $sql = "INSERT INTO tb_training_redirect_logs 
                (tracker_id, user_id, trigger_type, request_data, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssssss", 
                $request_data['tracker_id'] ?? '',
                $request_data['user_id'] ?? '',
                $request_data['trigger_type'] ?? '',
                json_encode($request_data),
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            );
            $stmt->execute();
        }
        
    } catch (Exception $e) {
        // Silent fail - logging shouldn't break the redirect
        error_log("Error logging training redirect: " . $e->getMessage());
    } finally {
        if (isset($db)) $db->close();
    }
}

/**
 * Get base URL for training pages
 */
function getBaseUrlForTraining() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    
    // Remove 'spear' from path if present
    $path = str_replace('/spear', '', $path);
    
    return $protocol . $host . $path . '/spear';
}

/**
 * Create training redirect logs table if it doesn't exist
 */
function createTrainingRedirectLogsTable() {
    global $conn; $db = $conn;
    
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `tb_training_redirect_logs` (
            `log_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `tracker_id` varchar(50) DEFAULT NULL,
            `user_id` varchar(100) DEFAULT NULL,
            `trigger_type` varchar(50) DEFAULT NULL,
            `request_data` text DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` text DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_tracker_user` (`tracker_id`, `user_id`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $db->query($sql);
        
    } catch (Exception $e) {
        error_log("Error creating training redirect logs table: " . $e->getMessage());
    } finally {
        $db->close();
    }
}

// Auto-create logs table on first use
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    createTrainingRedirectLogsTable();
}
?>
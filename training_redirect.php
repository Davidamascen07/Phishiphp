<?php
/**
 * Training Redirect Handler
 * Handles automatic redirection to training modules after phishing interactions
 */

require_once 'spear/config/db.php';
require_once 'spear/manager/common_functions.php';

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
    // Get parameters
    $tracker_id = $_GET['tracker_id'] ?? $_POST['tracker_id'] ?? '';
    $user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? '';
    $session_id = $_GET['session_id'] ?? $_POST['session_id'] ?? '';
    $trigger_type = $_GET['trigger_type'] ?? $_POST['trigger_type'] ?? 'on_interaction';
    
    // Validate required parameters
    if (empty($tracker_id)) {
        throw new Exception('Tracker ID is required');
    }
    
    // Check if training is configured for this tracker
    $training_config = getTrainingConfigForTracker($tracker_id, $trigger_type);
    
    if (!$training_config) {
        // No training configured, return normal response
        echo json_encode(array(
            'result' => 'success',
            'training_redirect' => false,
            'message' => 'No training configured'
        ));
        exit;
    }
    
    // Generate unique user ID if not provided
    if (empty($user_id)) {
        $user_id = generateUserIdentifier($session_id, $tracker_id);
    }
    
    // Log the interaction
    logTrainingTrigger($user_id, $tracker_id, $training_config['module_id'], $trigger_type);
    
    // Build training URL
    $training_url = buildTrainingURL($training_config, $user_id, $tracker_id, $trigger_type);
    
    // Return redirect response
    echo json_encode(array(
        'result' => 'success',
        'training_redirect' => true,
        'training_url' => $training_url,
        'module_name' => $training_config['module_name'],
        'delay_seconds' => $training_config['redirect_delay'] ?? 2
    ));
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(array(
        'result' => 'error',
        'error' => $e->getMessage(),
        'training_redirect' => false
    ));
}

/**
 * Get training configuration for a tracker
 */
function getTrainingConfigForTracker($tracker_id, $trigger_type) {
    global $conn;
    
    try {
        // Get campaign ID from tracker
        $stmt = $conn->prepare("SELECT campaign_id FROM tb_core_web_tracker_list WHERE tracker_id = ?");
        $stmt->bind_param("s", $tracker_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            return null;
        }
        
        $tracker_data = $result->fetch_assoc();
        $campaign_id = $tracker_data['campaign_id'];
        
        // Get training assignment for this campaign and trigger type
        $stmt = $conn->prepare("
            SELECT cta.*, tm.module_name, tm.video_url, tm.landing_url, tm.redirect_delay
            FROM tb_campaign_training_assignments cta
            LEFT JOIN tb_training_modules tm ON cta.module_id = tm.module_id
            WHERE cta.campaign_id = ? AND cta.trigger_type = ? AND cta.is_active = 1
            LIMIT 1
        ");
        
        $stmt->bind_param("is", $campaign_id, $trigger_type);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            return null;
        }
        
        return $result->fetch_assoc();
        
    } catch (Exception $e) {
        error_log("Error getting training config: " . $e->getMessage());
        return null;
    }
}

/**
 * Generate unique user identifier
 */
function generateUserIdentifier($session_id, $tracker_id) {
    return 'user_' . md5($session_id . $tracker_id . time());
}

/**
 * Log training trigger event
 */
function logTrainingTrigger($user_id, $tracker_id, $module_id, $trigger_type) {
    global $conn;
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO tb_training_progress 
            (user_id, module_id, tracker_id, trigger_type, started_at, status) 
            VALUES (?, ?, ?, ?, NOW(), 'triggered')
            ON DUPLICATE KEY UPDATE trigger_type = VALUES(trigger_type), started_at = NOW()
        ");
        
        $stmt->bind_param("siss", $user_id, $module_id, $tracker_id, $trigger_type);
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error logging training trigger: " . $e->getMessage());
    }
}

/**
 * Build training URL with parameters
 */
function buildTrainingURL($config, $user_id, $tracker_id, $trigger_type) {
    // Check for custom redirect URL first
    if (!empty($config['redirect_url'])) {
        $url = $config['redirect_url'];
        $separator = strpos($url, '?') !== false ? '&' : '?';
        return $url . $separator . "user_id=" . urlencode($user_id) . "&module_id=" . $config['module_id'] . "&tracker_id=" . urlencode($tracker_id);
    }
    
    // Use landing URL if available
    if (!empty($config['landing_url'])) {
        $url = $config['landing_url'];
        $separator = strpos($url, '?') !== false ? '&' : '?';
        return $url . $separator . "user_id=" . urlencode($user_id) . "&module_id=" . $config['module_id'] . "&tracker_id=" . urlencode($tracker_id);
    }
    
    // Default to training player
    return "spear/training_player.php?video=" . urlencode($config['video_url']) . 
           "&user_id=" . urlencode($user_id) . 
           "&module_id=" . $config['module_id'] . 
           "&tracker_id=" . urlencode($tracker_id) . 
           "&trigger_type=" . urlencode($trigger_type);
}
?>
<?php
/**
 * Training Analytics Manager
 * Endpoint para logging e análise de eventos de training
 */

require_once 'common_functions.php';

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
    
    // Fallback to POST data if JSON is not provided
    if (!$data) {
        $data = $_POST;
    }
    
    // Validate required fields
    if (!isset($data['tracker_id']) || !isset($data['session_id'])) {
        throw new Exception('Tracker ID and session ID are required');
    }
    
    $tracker_id = $data['tracker_id'];
    $session_id = $data['session_id'];
    $log_data = $data['log_data'] ?? array();
    
    // Store the analytics data
    $result = storeTrainingAnalytics($tracker_id, $session_id, $log_data);
    
    if ($result) {
        $response = array(
            'result' => 'success',
            'message' => 'Analytics data stored successfully'
        );
    } else {
        $response = array(
            'result' => 'error',
            'error' => 'Failed to store analytics data'
        );
    }
    
} catch (Exception $e) {
    $response = array(
        'result' => 'error',
        'error' => $e->getMessage()
    );
    http_response_code(400);
}

// Output JSON response
echo json_encode($response);

/**
 * Store training analytics data
 */
function storeTrainingAnalytics($tracker_id, $session_id, $log_data) {
    global $conn; $db = $conn;
    
    try {
        // Ensure analytics table exists
        createTrainingAnalyticsTable($db);
        
        $sql = "INSERT INTO tb_training_analytics 
                (tracker_id, session_id, event_type, event_data, user_agent, ip_address, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $db->prepare($sql);
        
        $event_type = $log_data['interaction_type'] ?? 'unknown';
        $event_data = json_encode($log_data);
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
        
        $stmt->bind_param("ssssss", 
            $tracker_id, 
            $session_id, 
            $event_type, 
            $event_data, 
            $user_agent, 
            $ip_address
        );
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error storing training analytics: " . $e->getMessage());
        return false;
    } finally {
        $db->close();
    }
}

/**
 * Create training analytics table if it doesn't exist
 */
function createTrainingAnalyticsTable($db) {
    try {
        $sql = "CREATE TABLE IF NOT EXISTS `tb_training_analytics` (
            `analytics_id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `tracker_id` varchar(50) NOT NULL,
            `session_id` varchar(100) NOT NULL,
            `event_type` varchar(50) NOT NULL,
            `event_data` text DEFAULT NULL,
            `user_agent` text DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_tracker_session` (`tracker_id`, `session_id`),
            INDEX `idx_event_type` (`event_type`),
            INDEX `idx_created_at` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        $db->query($sql);
        
    } catch (Exception $e) {
        error_log("Error creating training analytics table: " . $e->getMessage());
    }
}

/**
 * Get training analytics for specific tracker/session
 */
function getTrainingAnalytics($tracker_id = null, $session_id = null, $start_date = null, $end_date = null) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT * FROM tb_training_analytics WHERE 1=1";
        $params = array();
        $types = '';
        
        if ($tracker_id) {
            $sql .= " AND tracker_id = ?";
            $params[] = $tracker_id;
            $types .= 's';
        }
        
        if ($session_id) {
            $sql .= " AND session_id = ?";
            $params[] = $session_id;
            $types .= 's';
        }
        
        if ($start_date) {
            $sql .= " AND created_at >= ?";
            $params[] = $start_date;
            $types .= 's';
        }
        
        if ($end_date) {
            $sql .= " AND created_at <= ?";
            $params[] = $end_date;
            $types .= 's';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $analytics = array();
        while ($row = $result->fetch_assoc()) {
            $row['event_data'] = json_decode($row['event_data'], true);
            $analytics[] = $row;
        }
        
        return $analytics;
        
    } catch (Exception $e) {
        error_log("Error getting training analytics: " . $e->getMessage());
        return array();
    } finally {
        $db->close();
    }
}

/**
 * Get analytics summary for dashboard
 */
function getAnalyticsSummary($tracker_id = null, $days = 30) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT 
                    event_type,
                    COUNT(*) as event_count,
                    COUNT(DISTINCT session_id) as unique_sessions,
                    DATE(created_at) as event_date
                FROM tb_training_analytics 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
        
        $params = array($days);
        $types = 'i';
        
        if ($tracker_id) {
            $sql .= " AND tracker_id = ?";
            $params[] = $tracker_id;
            $types .= 's';
        }
        
        $sql .= " GROUP BY event_type, DATE(created_at) ORDER BY event_date DESC, event_count DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $summary = array();
        
        while ($row = $result->fetch_assoc()) {
            $summary[] = $row;
        }
        
        return $summary;
        
    } catch (Exception $e) {
        error_log("Error getting analytics summary: " . $e->getMessage());
        return array();
    } finally {
        $db->close();
    }
}

/**
 * Get user behavior patterns
 */
function getUserBehaviorPatterns($session_id) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT 
                    event_type,
                    event_data,
                    created_at,
                    UNIX_TIMESTAMP(created_at) as timestamp
                FROM tb_training_analytics 
                WHERE session_id = ?
                ORDER BY created_at ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $events = array();
        $patterns = array();
        
        $start_time = null;
        $last_time = null;
        
        while ($row = $result->fetch_assoc()) {
            $row['event_data'] = json_decode($row['event_data'], true);
            $events[] = $row;
            
            if ($start_time === null) {
                $start_time = $row['timestamp'];
            }
            $last_time = $row['timestamp'];
        }
        
        // Calculate patterns
        $patterns['total_events'] = count($events);
        $patterns['session_duration'] = $last_time - $start_time;
        $patterns['events_by_type'] = array();
        
        foreach ($events as $event) {
            $type = $event['event_type'];
            if (!isset($patterns['events_by_type'][$type])) {
                $patterns['events_by_type'][$type] = 0;
            }
            $patterns['events_by_type'][$type]++;
        }
        
        return array(
            'events' => $events,
            'patterns' => $patterns
        );
        
    } catch (Exception $e) {
        error_log("Error getting user behavior patterns: " . $e->getMessage());
        return array('events' => array(), 'patterns' => array());
    } finally {
        $db->close();
    }
}

// Handle GET requests for analytics data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'summary':
            $tracker_id = $_GET['tracker_id'] ?? null;
            $days = intval($_GET['days'] ?? 30);
            $data = getAnalyticsSummary($tracker_id, $days);
            echo json_encode(array('result' => 'success', 'data' => $data));
            break;
            
        case 'patterns':
            $session_id = $_GET['session_id'] ?? '';
            if (empty($session_id)) {
                echo json_encode(array('result' => 'error', 'error' => 'Session ID required'));
            } else {
                $data = getUserBehaviorPatterns($session_id);
                echo json_encode(array('result' => 'success', 'data' => $data));
            }
            break;
            
        case 'events':
            $tracker_id = $_GET['tracker_id'] ?? null;
            $session_id = $_GET['session_id'] ?? null;
            $start_date = $_GET['start_date'] ?? null;
            $end_date = $_GET['end_date'] ?? null;
            
            $data = getTrainingAnalytics($tracker_id, $session_id, $start_date, $end_date);
            echo json_encode(array('result' => 'success', 'data' => $data));
            break;
            
        default:
            echo json_encode(array('result' => 'error', 'error' => 'Invalid action'));
            break;
    }
    exit;
}
?>
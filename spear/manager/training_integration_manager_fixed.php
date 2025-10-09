<?php
/**
 * Training Integration Manager - Versão Corrigida
 * Manages the integration between phishing campaigns and training modules
 */

// Clear any output and set headers
if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include required files
require_once '../config/db.php';
require_once 'training_manager.php';

try {
    // Get and decode JSON input
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    
    // Fallback to POST data if JSON is not provided
    if (!$data && !empty($_POST)) {
        $data = $_POST;
    }
    
    // Validate action type
    if (!isset($data['action_type'])) {
        throw new Exception('Action type is required');
    }
    
    $action_type = $data['action_type'];
    $response = array();
    
    switch ($action_type) {
        
        case 'get_training_modules':
            // Get all available training modules for campaign integration
            try {
                $training_manager = new TrainingManager();
                $modules = $training_manager->getAllTrainingModules();
                
                if ($modules !== false && is_array($modules)) {
                    $response = array(
                        'result' => 'success',
                        'modules' => $modules,
                        'total' => count($modules)
                    );
                } else {
                    // Try direct database query as fallback
                    $query = "SELECT module_id, module_name, module_type, status 
                             FROM tb_training_modules 
                             WHERE status = 1 
                             ORDER BY created_date DESC";
                    $result = mysqli_query($conn, $query);
                    
                    if ($result) {
                        $modules = array();
                        while ($row = mysqli_fetch_assoc($result)) {
                            $modules[] = $row;
                        }
                        
                        $response = array(
                            'result' => 'success',
                            'modules' => $modules,
                            'total' => count($modules)
                        );
                    } else {
                        throw new Exception('Failed to retrieve training modules from database');
                    }
                }
            } catch (Exception $e) {
                $response = array(
                    'result' => 'error',
                    'error' => 'Failed to retrieve training modules: ' . $e->getMessage()
                );
            }
            break;
            
        case 'get_module_details':
            // Get details of a specific training module
            if (!isset($data['module_id'])) {
                throw new Exception('Module ID is required');
            }
            
            $module_id = $data['module_id'];
            $query = "SELECT * FROM tb_training_modules WHERE module_id = ? AND status = 1";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $module_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $module = $result->fetch_assoc();
                $response = array(
                    'result' => 'success',
                    'module' => $module
                );
            } else {
                $response = array(
                    'result' => 'error',
                    'error' => 'Module not found'
                );
            }
            break;
            
        default:
            throw new Exception('Invalid action type: ' . $action_type);
    }
    
} catch (Exception $e) {
    $response = array(
        'result' => 'error',
        'error' => $e->getMessage()
    );
    http_response_code(400);
}

// Output JSON response
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit();
?>
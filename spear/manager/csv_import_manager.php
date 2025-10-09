<?php
require_once(dirname(__FILE__) . '/session_manager.php');
require_once(dirname(__FILE__) . '/common_functions.php');

if(isSessionValid() == false)
    die("Access denied");

header('Content-Type: application/json');
$entry_time = (new DateTime())->format('Y-m-d H:i:s');

if (isset($_POST)) {
    $POSTJ = json_decode(file_get_contents('php://input'), true);
    
    if(isset($POSTJ['action_type'])){
        switch($POSTJ['action_type']) {
            case "import_users_with_departments":
                importUsersWithDepartments($conn, $POSTJ);
                break;
            case "get_import_preview":
                getImportPreview($conn, $POSTJ);
                break;
            case "get_departments_summary":
                getDepartmentsSummary($conn);
                break;
            case "get_users_by_department":
                getUsersByDepartment($conn, $POSTJ);
                break;
        }
    }
}

/**
 * Função principal para importação de usuários com criação automática de departamentos
 */
function importUsersWithDepartments($conn, &$POSTJ) {
    $client_id = getCurrentClientId();
    $raw_data = isset($POSTJ['csv_data']) ? $POSTJ['csv_data'] : null;
    $create_departments = isset($POSTJ['create_departments']) ? $POSTJ['create_departments'] : true;
    $create_users = isset($POSTJ['create_users']) ? $POSTJ['create_users'] : true;
    $update_user_group = isset($POSTJ['update_user_group']) ? $POSTJ['update_user_group'] : false;
    $user_group_id = isset($POSTJ['user_group_id']) ? $POSTJ['user_group_id'] : null;
    
    if(empty($raw_data)) {
        die(json_encode(['result' => 'failed', 'error' => 'Dados CSV não fornecidos']));
    }
    
    // Parse CSV data
    $parsed_data = parseCSVData($raw_data);
    if(isset($parsed_data['error'])) {
        die(json_encode(['result' => 'failed', 'error' => $parsed_data['error']]));
    }
    
    $results = [
        'departments_created' => 0,
        'departments_existing' => 0,
        'users_created' => 0,
        'users_updated' => 0,
        'users_skipped' => 0,
        'errors' => [],
        'department_summary' => []
    ];
    
    // Begin transaction
    mysqli_autocommit($conn, false);
    
    try {
        // Step 1: Process departments
        if($create_departments) {
            $dept_results = processDepartments($conn, $parsed_data['users'], $client_id);
            $results['departments_created'] = $dept_results['created'];
            $results['departments_existing'] = $dept_results['existing'];
            $results['department_summary'] = $dept_results['summary'];
        }
        
        // Step 2: Process users
        if($create_users) {
            $user_results = processUsers($conn, $parsed_data['users'], $client_id);
            $results['users_created'] = $user_results['created'];
            $results['users_updated'] = $user_results['updated'];
            $results['users_skipped'] = $user_results['skipped'];
            $results['errors'] = array_merge($results['errors'], $user_results['errors']);
        }
        
        // Step 3: Update user group if requested
        if($update_user_group && $user_group_id) {
            $group_results = updateUserGroup($conn, $parsed_data['users'], $user_group_id);
            $results['user_group_updated'] = $group_results['success'];
        }
        
        // Commit transaction
        mysqli_commit($conn);
        $results['result'] = 'success';
        
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $results['result'] = 'failed';
        $results['error'] = $e->getMessage();
    }
    
    mysqli_autocommit($conn, true);
    echo json_encode($results);
}

/**
 * Preview dos dados que serão importados
 */
function getImportPreview($conn, &$POSTJ) {
    $raw_data = isset($POSTJ['csv_data']) ? $POSTJ['csv_data'] : null;
    
    if(empty($raw_data)) {
        die(json_encode(['result' => 'failed', 'error' => 'Dados CSV não fornecidos']));
    }
    
    $parsed_data = parseCSVData($raw_data);
    if(isset($parsed_data['error'])) {
        die(json_encode(['result' => 'failed', 'error' => $parsed_data['error']]));
    }
    
    // Analyze departments
    $departments = [];
    $existing_depts = getExistingDepartments($conn, getCurrentClientId());
    
    foreach($parsed_data['users'] as $user) {
        $dept_name = $user['notes'];
        if(!empty($dept_name) && !isset($departments[$dept_name])) {
            $departments[$dept_name] = [
                'name' => $dept_name,
                'exists' => in_array($dept_name, $existing_depts),
                'user_count' => 0
            ];
        }
        if(!empty($dept_name)) {
            $departments[$dept_name]['user_count']++;
        }
    }
    
    echo json_encode([
        'result' => 'success',
        'total_users' => count($parsed_data['users']),
        'departments' => array_values($departments),
        'sample_data' => array_slice($parsed_data['users'], 0, 5) // First 5 rows as sample
    ]);
}

/**
 * Parse CSV data into structured format
 */
function parseCSVData($raw_data) {
    // Normalize newlines and remove BOM
    $raw_data = preg_replace("/\r\n|\r/", "\n", $raw_data);
    $raw_data = preg_replace('/^\xEF\xBB\xBF/', '', $raw_data);
    
    $lines = array_filter(array_map('trim', explode("\n", $raw_data)));
    if(count($lines) == 0) {
        return ['error' => 'Arquivo vazio ou formato inválido'];
    }
    
    // Detect header
    $first_line = array_shift($lines);
    $first_cols = str_getcsv($first_line);
    $lower_cols = array_map(function($c){ return strtolower(trim($c)); }, $first_cols);
    
    // Check for header
    $has_header = false;
    foreach($lower_cols as $col) {
        if(in_array($col, ['first name', 'firstname', 'fname', 'last name', 'lastname', 'lname', 'email', 'e-mail', 'notes', 'note'])) {
            $has_header = true;
            break;
        }
    }
    
    // If no header detected, put first line back
    if(!$has_header) {
        array_unshift($lines, $first_line);
    }
    
    $users = [];
    $errors = [];
    
    foreach($lines as $lineno => $line) {
        if(trim($line) === '') continue;
        
        $cols = str_getcsv($line);
        $cols = array_map('trim', $cols);
        
        // Expected format: First Name, Last Name, Email, Notes (Department)
        if(count($cols) < 3) {
            $errors[] = "Linha " . ($lineno + 1) . ": Número insuficiente de colunas";
            continue;
        }
        
        $fname = isset($cols[0]) ? $cols[0] : '';
        $lname = isset($cols[1]) ? $cols[1] : '';
        $email = isset($cols[2]) ? $cols[2] : '';
        $notes = isset($cols[3]) ? $cols[3] : '';
        
        // Validate email
        if(!isValidEmail($email)) {
            $errors[] = "Linha " . ($lineno + 1) . ": Email inválido - $email";
            continue;
        }
        
        $users[] = [
            'fname' => !empty($fname) ? $fname : 'Nome',
            'lname' => !empty($lname) ? $lname : 'Sobrenome',
            'email' => $email,
            'notes' => $notes,
            'department' => $notes // Department name from notes field
        ];
    }
    
    return [
        'users' => $users,
        'errors' => $errors,
        'has_header' => $has_header
    ];
}

/**
 * Process and create departments
 */
function processDepartments($conn, $users, $client_id) {
    $departments = [];
    $existing_depts = getExistingDepartments($conn, $client_id);
    $created = 0;
    $existing = 0;
    $summary = [];
    
    // Collect unique departments
    foreach($users as $user) {
        $dept_name = trim($user['department']);
        if(!empty($dept_name) && !in_array($dept_name, $departments)) {
            $departments[] = $dept_name;
        }
    }
    
    foreach($departments as $dept_name) {
        if(in_array($dept_name, $existing_depts)) {
            $existing++;
            $dept_id = getDepartmentIdByName($conn, $dept_name, $client_id);
        } else {
            // Create new department
            $dept_id = 'dept_' . $dept_name . '_' . strtoupper(getRandomStr(4));
            $color = getRandomDepartmentColor();
            $description = "Departamento criado automaticamente via importação CSV";
            
            $stmt = $conn->prepare("INSERT INTO tb_departments (client_id, department_id, department_name, description, color, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, ?, ?)");
            $stmt->bind_param('sssssss', $client_id, $dept_id, $dept_name, $description, $color, $GLOBALS['entry_time'], $GLOBALS['entry_time']);
            
            if($stmt->execute()) {
                $created++;
            }
            $stmt->close();
        }
        
        $summary[] = [
            'name' => $dept_name,
            'id' => $dept_id,
            'created' => !in_array($dept_name, $existing_depts)
        ];
    }
    
    return [
        'created' => $created,
        'existing' => $existing,
        'summary' => $summary
    ];
}

/**
 * Process and create/update users
 */
function processUsers($conn, $users, $client_id) {
    $created = 0;
    $updated = 0;
    $skipped = 0;
    $errors = [];
    
    foreach($users as $user) {
        $email = $user['email'];
        $fname = $user['fname'];
        $lname = $user['lname'];
        $dept_name = $user['department'];
        
        // Get department ID
        $dept_id = null;
        if(!empty($dept_name)) {
            $dept_id = getDepartmentIdByName($conn, $dept_name, $client_id);
        }
        
        // Check if user exists
        $existing_user = getUserByEmail($conn, $email, $client_id);
        
        if($existing_user) {
            // Update existing user
            $stmt = $conn->prepare("UPDATE tb_client_users SET first_name=?, last_name=?, department=?, department_id=?, last_updated=? WHERE user_email=? AND client_id=?");
            $stmt->bind_param('sssssss', $fname, $lname, $dept_name, $dept_id, $GLOBALS['entry_time'], $email, $client_id);
            
            if($stmt->execute()) {
                $updated++;
            } else {
                $errors[] = "Erro ao atualizar usuário: $email";
            }
            $stmt->close();
        } else {
            // Create new user
            $user_name = $fname . ' ' . $lname;
            $stmt = $conn->prepare("INSERT INTO tb_client_users (user_email, user_name, first_name, last_name, client_id, department, department_id, added_date, last_updated, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param('sssssssss', $email, $user_name, $fname, $lname, $client_id, $dept_name, $dept_id, $GLOBALS['entry_time'], $GLOBALS['entry_time']);
            
            if($stmt->execute()) {
                $created++;
            } else {
                $errors[] = "Erro ao criar usuário: $email - " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    return [
        'created' => $created,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors' => $errors
    ];
}

/**
 * Update user group with imported users
 */
function updateUserGroup($conn, $users, $user_group_id) {
    // Get existing user group data
    $stmt = $conn->prepare("SELECT user_data FROM tb_core_mailcamp_user_group WHERE user_group_id = ?");
    $stmt->bind_param('s', $user_group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    $existing_data = [];
    if($row && !empty($row['user_data'])) {
        $existing_data = json_decode($row['user_data'], true);
        if(!is_array($existing_data)) $existing_data = [];
    }
    
    // Add new users to group
    $existing_emails = array_map(function($u) { return strtolower($u['email']); }, $existing_data);
    
    foreach($users as $user) {
        $email = strtolower($user['email']);
        if(!in_array($email, $existing_emails)) {
            $uid = getRandomStr(10);
            $existing_data[] = [
                'uid' => $uid,
                'fname' => $user['fname'],
                'lname' => $user['lname'],
                'email' => $user['email'],
                'notes' => $user['notes']
            ];
            $existing_emails[] = $email;
        }
    }
    
    $user_data_json = json_encode(array_values($existing_data));
    
    $stmt = $conn->prepare("UPDATE tb_core_mailcamp_user_group SET user_data = ? WHERE user_group_id = ?");
    $stmt->bind_param('ss', $user_data_json, $user_group_id);
    $success = $stmt->execute();
    $stmt->close();
    
    return ['success' => $success];
}

/**
 * Helper functions
 */
function getExistingDepartments($conn, $client_id) {
    $departments = [];
    $stmt = $conn->prepare("SELECT department_name FROM tb_departments WHERE client_id = ? AND status = 1");
    $stmt->bind_param('s', $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $departments[] = $row['department_name'];
    }
    $stmt->close();
    
    return $departments;
}

function getDepartmentIdByName($conn, $dept_name, $client_id) {
    $stmt = $conn->prepare("SELECT department_id FROM tb_departments WHERE department_name = ? AND client_id = ? AND status = 1");
    $stmt->bind_param('ss', $dept_name, $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row ? $row['department_id'] : null;
}

function getUserByEmail($conn, $email, $client_id) {
    $stmt = $conn->prepare("SELECT * FROM tb_client_users WHERE user_email = ? AND client_id = ?");
    $stmt->bind_param('ss', $email, $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row;
}

function getRandomDepartmentColor() {
    $colors = ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1', '#fd7e14', '#20c997', '#6c757d'];
    return $colors[array_rand($colors)];
}

function getDepartmentsSummary($conn) {
    $client_id = getCurrentClientId();
    
    $stmt = $conn->prepare("
        SELECT 
            d.department_id,
            d.department_name,
            d.color,
            d.created_at,
            COUNT(u.id) as user_count
        FROM tb_departments d
        LEFT JOIN tb_client_users u ON d.department_id = u.department_id AND u.client_id = d.client_id
        WHERE d.client_id = ? AND d.status = 1
        GROUP BY d.department_id
        ORDER BY d.department_name
    ");
    $stmt->bind_param('s', $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $departments = [];
    while($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['result' => 'success', 'departments' => $departments]);
}

function getUsersByDepartment($conn, &$POSTJ) {
    $client_id = getCurrentClientId();
    $department_id = isset($POSTJ['department_id']) ? $POSTJ['department_id'] : null;
    
    if(empty($department_id)) {
        die(json_encode(['result' => 'failed', 'error' => 'ID do departamento não fornecido']));
    }
    
    $stmt = $conn->prepare("
        SELECT 
            user_email,
            user_name,
            first_name,
            last_name,
            department,
            added_date,
            status
        FROM tb_client_users 
        WHERE department_id = ? AND client_id = ?
        ORDER BY first_name, last_name
    ");
    $stmt->bind_param('ss', $department_id, $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    
    echo json_encode(['result' => 'success', 'users' => $users]);
}
?>
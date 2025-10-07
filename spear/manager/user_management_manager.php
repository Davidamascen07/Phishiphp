<?php
require_once "session_manager.php";
require_once "../config/db.php";

header('Content-Type: application/json; charset=utf-8');

$POSTJ = json_decode(file_get_contents('php://input'), true);

if (!empty($POSTJ)) {
    global $conn;
    if (!$conn) {
        die('{"success": false, "error": "Database connection failed"}');
    }
    mysqli_set_charset($conn, "utf8");
    
    if (isset($POSTJ['action_type'])) {
        switch ($POSTJ['action_type']) {
            case "import_users_from_csv":
                importUsersFromCSV($conn, $POSTJ['csv_data']);
                break;
            case "get_user_list":
                getUserList($conn);
                break;
            case "get_user_campaign_history":
                getUserCampaignHistory($conn, $POSTJ['user_email']);
                break;
            case "update_user_campaign_participation":
                updateUserCampaignParticipation($conn, $POSTJ);
                break;
            case "get_department_list":
                getDepartmentList($conn);
                break;
            case "create_department":
                createDepartment($conn, $POSTJ['department_name'], $POSTJ['description'], $POSTJ['color']);
                break;
            case "get_user_stats":
                getUserStats($conn);
                break;
        }
    }
} else {
    die('{"error": "Invalid request"}');
}

/**
 * Importa usuários de dados CSV
 */
function importUsersFromCSV($conn, $csv_data) {
    $current_client_id = getCurrentClientId();
    
    if (empty($current_client_id)) {
        echo json_encode(['success' => false, 'error' => 'Client ID não encontrado']);
        return;
    }
    
    if (empty($csv_data) || !is_array($csv_data)) {
        echo json_encode(['success' => false, 'error' => 'Dados CSV inválidos ou vazios']);
        return;
    }
    
    $import_results = [
        'total_processed' => 0,
        'users_created' => 0,
        'users_updated' => 0,
        'departments_created' => 0,
        'errors' => []
    ];
    
    try {
        $conn->begin_transaction();
        
        foreach ($csv_data as $row) {
            $import_results['total_processed']++;
            
            $first_name = trim($row['First Name'] ?? '');
            $last_name = trim($row['Last Name'] ?? '');
            $email = trim(strtolower($row['Email'] ?? ''));
            $department_name = trim($row['Notes'] ?? '');
            
            // Validações básicas
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $import_results['errors'][] = "Email inválido: " . $email;
                continue;
            }
            
            if (empty($first_name)) {
                $import_results['errors'][] = "Nome obrigatório para email: " . $email;
                continue;
            }
            
            // Criar/buscar departamento
            $department_id = null;
            if (!empty($department_name)) {
                $department_id = createOrGetDepartment($conn, $department_name, $current_client_id);
                if ($department_id === false) {
                    $import_results['errors'][] = "Erro ao criar departamento: " . $department_name;
                    continue;
                }
            }
            
            // Verificar se usuário já existe
            $stmt = $conn->prepare("SELECT id, user_name, department FROM tb_client_users WHERE user_email = ? AND client_id = ?");
            $stmt->bind_param("ss", $email, $current_client_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $full_name = trim($first_name . ' ' . $last_name);
            $current_time = date('d-m-Y h:i A');
            
            if ($result->num_rows > 0) {
                // Atualizar usuário existente
                $stmt = $conn->prepare("UPDATE tb_client_users SET 
                    user_name = ?, first_name = ?, last_name = ?, 
                    department = ?, department_id = ?, last_updated = ? 
                    WHERE user_email = ? AND client_id = ?");
                $stmt->bind_param("ssssssss", $full_name, $first_name, $last_name, 
                    $department_name, $department_id, $current_time, $email, $current_client_id);
                
                if ($stmt->execute()) {
                    $import_results['users_updated']++;
                } else {
                    $import_results['errors'][] = "Erro ao atualizar usuário: " . $email;
                }
            } else {
                // Criar novo usuário
                $stmt = $conn->prepare("INSERT INTO tb_client_users 
                    (user_email, user_name, first_name, last_name, client_id, department, department_id, added_date, last_updated, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
                $stmt->bind_param("sssssssss", $email, $full_name, $first_name, $last_name, 
                    $current_client_id, $department_name, $department_id, $current_time, $current_time);
                
                if ($stmt->execute()) {
                    $import_results['users_created']++;
                } else {
                    $import_results['errors'][] = "Erro ao criar usuário: " . $email;
                }
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'results' => $import_results]);
        
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

/**
 * Cria ou busca departamento existente
 */
function createOrGetDepartment($conn, $department_name, $client_id) {
    // Verificar se departamento já existe
    $stmt = $conn->prepare("SELECT department_id FROM tb_departments WHERE department_name = ? AND client_id = ?");
    $stmt->bind_param("ss", $department_name, $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['department_id'];
    }
    
    // Criar novo departamento
    $department_id = 'dept_' . strtoupper($department_name) . '_' . generateRandomString(4);
    $current_time = date('Y-m-d H:i:s');
    
    $stmt = $conn->prepare("INSERT INTO tb_departments 
        (client_id, department_id, department_name, description, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $description = "Departamento criado automaticamente via importação CSV";
    $stmt->bind_param("ssssss", $client_id, $department_id, $department_name, $description, $current_time, $current_time);
    
    if ($stmt->execute()) {
        return $department_id;
    }
    
    return false;
}

/**
 * Lista usuários do cliente atual
 */
function getUserList($conn) {
    $current_client_id = getCurrentClientId();
    
    $stmt = $conn->prepare("SELECT u.*, d.department_name as dept_name, d.color as dept_color 
        FROM tb_client_users u 
        LEFT JOIN tb_departments d ON u.department_id = d.department_id AND u.client_id = d.client_id 
        WHERE u.client_id = ? 
        ORDER BY u.user_name");
    $stmt->bind_param("s", $current_client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    echo json_encode(['success' => true, 'users' => $users]);
}

/**
 * Busca histórico de campanhas de um usuário
 */
function getUserCampaignHistory($conn, $user_email) {
    $current_client_id = getCurrentClientId();
    
    $stmt = $conn->prepare("SELECT h.*, 
        CASE 
            WHEN h.campaign_type = 'mail' THEN (SELECT campaign_name FROM tb_core_mailcamp_list WHERE campaign_id = h.campaign_id)
            WHEN h.campaign_type = 'web' THEN (SELECT tracker_name FROM tb_core_web_tracker_list WHERE tracker_id = h.campaign_id)
        END as campaign_name
        FROM tb_user_campaign_history h 
        WHERE h.user_email = ? AND h.client_id = ? 
        ORDER BY h.participation_date DESC");
    $stmt->bind_param("ss", $user_email, $current_client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    echo json_encode(['success' => true, 'history' => $history]);
}

/**
 * Atualiza participação do usuário em campanha
 */
function updateUserCampaignParticipation($conn, $data) {
    $current_client_id = getCurrentClientId();
    
    $stmt = $conn->prepare("INSERT INTO tb_user_campaign_history 
        (user_email, client_id, campaign_id, campaign_type, clicked, submitted_data, completed_training, last_activity, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)
        ON DUPLICATE KEY UPDATE 
        clicked = VALUES(clicked), 
        submitted_data = VALUES(submitted_data), 
        completed_training = VALUES(completed_training), 
        last_activity = VALUES(last_activity),
        notes = VALUES(notes)");
    
    $stmt->bind_param("ssssiiis", 
        $data['user_email'], 
        $current_client_id, 
        $data['campaign_id'], 
        $data['campaign_type'], 
        $data['clicked'], 
        $data['submitted_data'], 
        $data['completed_training'], 
        $data['notes']
    );
    
    if ($stmt->execute()) {
        // Atualizar contador de campanhas do usuário
        updateUserCampaignCount($conn, $data['user_email'], $current_client_id);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
}

/**
 * Atualiza contador de campanhas do usuário
 */
function updateUserCampaignCount($conn, $user_email, $client_id) {
    $stmt = $conn->prepare("UPDATE tb_client_users SET 
        campaign_count = (SELECT COUNT(*) FROM tb_user_campaign_history WHERE user_email = ? AND client_id = ?),
        last_campaign_date = (SELECT MAX(participation_date) FROM tb_user_campaign_history WHERE user_email = ? AND client_id = ?)
        WHERE user_email = ? AND client_id = ?");
    $stmt->bind_param("ssssss", $user_email, $client_id, $user_email, $client_id, $user_email, $client_id);
    $stmt->execute();
}

/**
 * Lista departamentos do cliente
 */
function getDepartmentList($conn) {
    $current_client_id = getCurrentClientId();
    
    $stmt = $conn->prepare("SELECT d.*, 
        (SELECT COUNT(*) FROM tb_client_users WHERE department_id = d.department_id AND client_id = d.client_id) as user_count
        FROM tb_departments d 
        WHERE d.client_id = ? 
        ORDER BY d.department_name");
    $stmt->bind_param("s", $current_client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $departments = [];
    while ($row = $result->fetch_assoc()) {
        $departments[] = $row;
    }
    
    echo json_encode(['success' => true, 'departments' => $departments]);
}

/**
 * Cria novo departamento
 */
function createDepartment($conn, $department_name, $description = '', $color = '#007bff') {
    try {
        $current_client_id = getCurrentClientId();
        
        if (empty($current_client_id)) {
            echo json_encode(['success' => false, 'error' => 'Client ID não encontrado']);
            return;
        }
        
        $department_id = 'dept_' . strtoupper(str_replace(' ', '_', $department_name)) . '_' . generateRandomString(4);
        $current_time = date('Y-m-d H:i:s');
        
        // Verificar se já existe departamento com esse nome
        $check_stmt = $conn->prepare("SELECT department_id FROM tb_departments WHERE department_name = ? AND client_id = ?");
        $check_stmt->bind_param("ss", $department_name, $current_client_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Já existe um departamento com este nome']);
            return;
        }
        
        $stmt = $conn->prepare("INSERT INTO tb_departments 
            (client_id, department_id, department_name, description, color, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $current_client_id, $department_id, $department_name, $description, $color, $current_time, $current_time);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'department_id' => $department_id, 'message' => 'Departamento criado com sucesso']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Erro ao criar departamento: ' . $stmt->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => 'Erro interno: ' . $e->getMessage()]);
    }
}

/**
 * Estatísticas gerais dos usuários
 */
function getUserStats($conn) {
    $current_client_id = getCurrentClientId();
    
    // Total de usuários
    $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM tb_client_users WHERE client_id = ?");
    $stmt->bind_param("s", $current_client_id);
    $stmt->execute();
    $total_users = $stmt->get_result()->fetch_assoc()['total_users'];
    
    // Total de departamentos
    $stmt = $conn->prepare("SELECT COUNT(*) as total_departments FROM tb_departments WHERE client_id = ?");
    $stmt->bind_param("s", $current_client_id);
    $stmt->execute();
    $total_departments = $stmt->get_result()->fetch_assoc()['total_departments'];
    
    // Usuários por departamento
    $stmt = $conn->prepare("SELECT d.department_name, COUNT(u.id) as user_count 
        FROM tb_departments d 
        LEFT JOIN tb_client_users u ON d.department_id = u.department_id 
        WHERE d.client_id = ? 
        GROUP BY d.department_id, d.department_name 
        ORDER BY user_count DESC");
    $stmt->bind_param("s", $current_client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $departments_stats = [];
    while ($row = $result->fetch_assoc()) {
        $departments_stats[] = $row;
    }
    
    // Top usuários em campanhas
    $stmt = $conn->prepare("SELECT u.user_name, u.user_email, u.campaign_count, u.last_campaign_date, d.department_name
        FROM tb_client_users u 
        LEFT JOIN tb_departments d ON u.department_id = d.department_id 
        WHERE u.client_id = ? AND u.campaign_count > 0
        ORDER BY u.campaign_count DESC, u.last_campaign_date DESC 
        LIMIT 10");
    $stmt->bind_param("s", $current_client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $top_users = [];
    while ($row = $result->fetch_assoc()) {
        $top_users[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'stats' => [
            'total_users' => $total_users,
            'total_departments' => $total_departments,
            'departments_stats' => $departments_stats,
            'top_users' => $top_users
        ]
    ]);
}

/**
 * Gera string aleatória
 */
function generateRandomString($length = 6) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>
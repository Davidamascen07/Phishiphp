<?php
/**
 * Loophish - Client Management System
 * Gerenciador de Clientes Multi-tenant
 * Fase 1 - Implementação do Sistema de Clientes
 */

require_once(dirname(__FILE__, 2) . '/config/db.php');
require_once(dirname(__FILE__) . '/session_manager.php');
require_once(dirname(__FILE__) . '/common_functions.php');

// Verificar se é requisição POST
if (isset($_POST)) {
    $POSTJ = json_decode(file_get_contents('php://input'), true);
    
    if (isset($POSTJ['action_type'])) {
        header('Content-Type: application/json');
        
        switch ($POSTJ['action_type']) {
            case 'get_client_list':
                getClientList($conn);
                break;
            case 'save_client':
                saveClient($conn, $POSTJ);
                break;
            case 'get_client_details':
                getClientDetails($conn, $POSTJ['client_id']);
                break;
            case 'delete_client':
                deleteClient($conn, $POSTJ['client_id']);
                break;
            case 'get_client_users':
                getClientUsers($conn, $POSTJ['client_id']);
                break;
            case 'save_client_user':
                saveClientUser($conn, $POSTJ);
                break;
            case 'delete_client_user':
                deleteClientUser($conn, $POSTJ['user_id']);
                break;
            case 'import_client_users':
                importClientUsers($conn, $POSTJ);
                break;
            case 'get_client_stats':
                getClientStats($conn, $POSTJ['client_id']);
                break;
            case 'update_client_settings':
                updateClientSettings($conn, $POSTJ);
                break;
            case 'getClients':
                getAvailableClients($conn, $POSTJ['search'] ?? '');
                break;
            case 'setActiveClient':
                setActiveClient($conn, $POSTJ);
                break;
            case 'getActiveClient':
                getActiveClient($conn);
                break;
            default:
                echo json_encode(['result' => 'error', 'message' => 'Ação não reconhecida']);
        }
    }
}

/**
 * Listar todos os clientes
 */
function getClientList($conn) {
    try {
        // Verificar se a tabela tb_client_users existe
        $table_exists = mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE 'tb_client_users'")) > 0;
        
        if ($table_exists) {
            $query = "SELECT c.client_id, c.client_name, c.client_domain, c.contact_email, 
                             c.created_date, c.status, c.brand_colors,
                             (SELECT COUNT(*) FROM tb_client_users WHERE client_id = c.client_id AND status = 1) as user_count,
                             (SELECT COUNT(*) FROM tb_core_mailcamp_list WHERE client_id = c.client_id) as campaign_count
                      FROM tb_clients c 
                      WHERE c.status = 1
                      ORDER BY c.client_name ASC";
        } else {
            $query = "SELECT c.client_id, c.client_name, c.client_domain, c.contact_email, 
                             c.created_date, c.status, c.brand_colors,
                             0 as user_count,
                             (SELECT COUNT(*) FROM tb_core_mailcamp_list WHERE client_id = c.client_id) as campaign_count
                      FROM tb_clients c 
                      WHERE c.status = 1
                      ORDER BY c.client_name ASC";
        }
        
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            throw new Exception('Erro na consulta: ' . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($result) > 0) {
            $clients = [];
            while ($row = mysqli_fetch_assoc($result)) {
                // Decodificar brand_colors se existir
                if (!empty($row['brand_colors'])) {
                    $row['brand_colors'] = json_decode($row['brand_colors'], true);
                }
                $clients[] = $row;
            }
            echo json_encode($clients);
        } else {
            echo json_encode([]);
        }
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Salvar/Atualizar cliente
 */
function saveClient($conn, $data) {
    try {
        // Verificar se é atualização baseado na existência e preenchimento do client_id
        $is_update = !empty($data['client_id']) && $data['is_update'] === 'true';
        $client_id = $is_update ? $data['client_id'] : generateUniqueClientId($conn);
        
        $client_name = mysqli_real_escape_string($conn, $data['client_name']);
        $client_domain = mysqli_real_escape_string($conn, $data['client_domain'] ?? '');
        $contact_email = mysqli_real_escape_string($conn, $data['contact_email'] ?? '');
        $contact_phone = mysqli_real_escape_string($conn, $data['contact_phone'] ?? '');
        $address = mysqli_real_escape_string($conn, $data['address'] ?? '');
        $brand_colors = json_encode($data['brand_colors'] ?? ['primary' => '#4361ee', 'secondary' => '#6366f1']);
        $settings = json_encode($data['settings'] ?? []);
        $status = intval($data['status'] ?? 1);
        
        if ($is_update) {
            // Verificar se cliente existe antes de atualizar
            $check_stmt = $conn->prepare("SELECT client_id FROM tb_clients WHERE client_id = ?");
            $check_stmt->bind_param('s', $client_id);
            $check_stmt->execute();
            
            if ($check_stmt->get_result()->num_rows === 0) {
                echo json_encode(['result' => 'error', 'message' => 'Cliente não encontrado']);
                return;
            }
            // Atualizar cliente existente
            $stmt = $conn->prepare("UPDATE tb_clients SET 
                client_name = ?, client_domain = ?, contact_email = ?, 
                contact_phone = ?, address = ?, brand_colors = ?, 
                settings = ?, status = ?, last_modified = ?
                WHERE client_id = ?");
            
            $current_date = (new DateTime())->format('d-m-Y h:i A');
            $stmt->bind_param('sssssssiis', 
                $client_name, $client_domain, $contact_email, 
                $contact_phone, $address, $brand_colors, 
                $settings, $status, $current_date, $client_id
            );
        } else {
            // Criar novo cliente
            $stmt = $conn->prepare("INSERT INTO tb_clients 
                (client_id, client_name, client_domain, contact_email, 
                 contact_phone, address, brand_colors, settings, 
                 created_by, created_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $current_date = (new DateTime())->format('d-m-Y h:i A');
            $created_by = $_SESSION['username'] ?? 'admin';
            
            $stmt->bind_param('ssssssssssi', 
                $client_id, $client_name, $client_domain, $contact_email, 
                $contact_phone, $address, $brand_colors, $settings, 
                $created_by, $current_date, $status
            );
        }
        
        if ($stmt->execute()) {
            logIt($is_update ? 'Cliente atualizado' : 'Cliente criado', $client_name);
            echo json_encode(['result' => 'success', 'client_id' => $client_id]);
        } else {
            echo json_encode(['result' => 'error', 'message' => $stmt->error]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Obter detalhes de um cliente
 */
function getClientDetails($conn, $client_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM tb_clients WHERE client_id = ?");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $client = $result->fetch_assoc();
            
            // Decodificar JSON fields
            $client['brand_colors'] = json_decode($client['brand_colors'], true);
            $client['settings'] = json_decode($client['settings'], true);
            
            echo json_encode($client);
        } else {
            echo json_encode(['result' => 'error', 'message' => 'Cliente não encontrado']);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Excluir cliente (soft delete)
 */
function deleteClient($conn, $client_id) {
    try {
        // Verificar se cliente tem serviços vinculados
        $dependencies = checkClientDependencies($conn, $client_id);
        
        if (!empty($dependencies)) {
            $message = 'Cliente possui serviços vinculados: ' . implode(', ', $dependencies);
            echo json_encode(['result' => 'error', 'message' => $message]);
            return;
        }
        
        // Soft delete
        $stmt = $conn->prepare("UPDATE tb_clients SET status = 0, last_modified = ? WHERE client_id = ?");
        $current_date = (new DateTime())->format('d-m-Y h:i A');
        $stmt->bind_param('ss', $current_date, $client_id);
        
        if ($stmt->execute()) {
            logIt('Cliente desativado', $client_id);
            echo json_encode(['result' => 'success']);
        } else {
            echo json_encode(['result' => 'error', 'message' => $stmt->error]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Verificar dependências do cliente
 */
function checkClientDependencies($conn, $client_id) {
    $dependencies = [];
    
    // Verificar campanhas de email
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tb_core_mailcamp_list WHERE client_id = ?");
    $stmt->bind_param('s', $client_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result['count'] > 0) {
        $dependencies[] = $result['count'] . ' campanha(s) de email';
    }
    $stmt->close();
    
    // Verificar rastreadores web
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tb_core_web_tracker_list WHERE client_id = ?");
    $stmt->bind_param('s', $client_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result['count'] > 0) {
        $dependencies[] = $result['count'] . ' rastreador(es) web';
    }
    $stmt->close();
    
    // Verificar quick trackers
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tb_core_quick_tracker_list WHERE client_id = ?");
    $stmt->bind_param('s', $client_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    if ($result['count'] > 0) {
        $dependencies[] = $result['count'] . ' rastreador(es) rápido(s)';
    }
    $stmt->close();
    
    // Verificar usuários do cliente
    if (mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE 'tb_client_users'")) > 0) {
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tb_client_users WHERE client_id = ?");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result['count'] > 0) {
            $dependencies[] = $result['count'] . ' usuário(s)';
        }
        $stmt->close();
    }
    
    return $dependencies;
}

/**
 * Obter usuários de um cliente
 */
function getClientUsers($conn, $client_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM tb_client_users 
                               WHERE client_id = ? AND status = 1 
                               ORDER BY user_name ASC");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $users = mysqli_fetch_all($result, MYSQLI_ASSOC);
            echo json_encode($users);
        } else {
            echo json_encode([]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Salvar/Atualizar usuário do cliente
 */
function saveClientUser($conn, $data) {
    try {
        $user_email = mysqli_real_escape_string($conn, $data['user_email']);
        $user_name = mysqli_real_escape_string($conn, $data['user_name']);
        $client_id = mysqli_real_escape_string($conn, $data['client_id']);
        $department = mysqli_real_escape_string($conn, $data['department'] ?? '');
        $position = mysqli_real_escape_string($conn, $data['position'] ?? '');
        $phone = mysqli_real_escape_string($conn, $data['phone'] ?? '');
        $user_data = json_encode($data['user_data'] ?? []);
        $current_date = (new DateTime())->format('d-m-Y h:i A');
        
        $is_update = isset($data['id']) && !empty($data['id']);
        
        if ($is_update) {
            $stmt = $conn->prepare("UPDATE tb_client_users SET 
                user_email = ?, user_name = ?, department = ?, 
                position = ?, phone = ?, user_data = ?, last_updated = ?
                WHERE id = ?");
            $stmt->bind_param('sssssssi', 
                $user_email, $user_name, $department, 
                $position, $phone, $user_data, $current_date, $data['id']
            );
        } else {
            $stmt = $conn->prepare("INSERT INTO tb_client_users 
                (user_email, user_name, client_id, department, position, 
                 phone, user_data, added_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
            $stmt->bind_param('ssssssss', 
                $user_email, $user_name, $client_id, $department, 
                $position, $phone, $user_data, $current_date
            );
        }
        
        if ($stmt->execute()) {
            echo json_encode(['result' => 'success']);
        } else {
            echo json_encode(['result' => 'error', 'message' => $stmt->error]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Excluir usuário do cliente
 */
function deleteClientUser($conn, $user_id) {
    try {
        $stmt = $conn->prepare("UPDATE tb_client_users SET status = 0 WHERE id = ?");
        $stmt->bind_param('i', $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['result' => 'success']);
        } else {
            echo json_encode(['result' => 'error', 'message' => $stmt->error]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Importar usuários via CSV
 */
function importClientUsers($conn, $data) {
    try {
        $client_id = $data['client_id'];
        $users_data = $data['users_data']; // Array de usuários
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        foreach ($users_data as $user) {
            try {
                $user_email = mysqli_real_escape_string($conn, $user['email']);
                $user_name = mysqli_real_escape_string($conn, $user['name'] ?? '');
                $department = mysqli_real_escape_string($conn, $user['department'] ?? '');
                $position = mysqli_real_escape_string($conn, $user['position'] ?? '');
                $phone = mysqli_real_escape_string($conn, $user['phone'] ?? '');
                $current_date = (new DateTime())->format('d-m-Y h:i A');
                
                $stmt = $conn->prepare("INSERT INTO tb_client_users 
                    (user_email, user_name, client_id, department, position, 
                     phone, added_date, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 1)
                    ON DUPLICATE KEY UPDATE 
                    user_name = VALUES(user_name),
                    department = VALUES(department),
                    position = VALUES(position),
                    phone = VALUES(phone),
                    last_updated = VALUES(added_date)");
                    
                $stmt->bind_param('sssssss', 
                    $user_email, $user_name, $client_id, 
                    $department, $position, $phone, $current_date
                );
                
                if ($stmt->execute()) {
                    $success_count++;
                } else {
                    $error_count++;
                    $errors[] = "Erro ao importar {$user_email}: " . $stmt->error;
                }
                
                $stmt->close();
            } catch (Exception $e) {
                $error_count++;
                $errors[] = "Erro ao processar {$user['email']}: " . $e->getMessage();
            }
        }
        
        echo json_encode([
            'result' => 'success',
            'imported' => $success_count,
            'errors' => $error_count,
            'error_details' => $errors
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Obter estatísticas do cliente
 */
function getClientStats($conn, $client_id) {
    try {
        $stats = [];
        
        // Total de usuários
        $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM tb_client_users WHERE client_id = ? AND status = 1");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $stats['total_users'] = $stmt->get_result()->fetch_assoc()['total_users'];
        $stmt->close();
        
        // Total de campanhas
        $stmt = $conn->prepare("SELECT COUNT(*) as total_campaigns FROM tb_core_mailcamp_list WHERE client_id = ?");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $stats['total_campaigns'] = $stmt->get_result()->fetch_assoc()['total_campaigns'];
        $stmt->close();
        
        // Campanhas ativas
        $stmt = $conn->prepare("SELECT COUNT(*) as active_campaigns FROM tb_core_mailcamp_list WHERE client_id = ? AND camp_status IN (1,2)");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $stats['active_campaigns'] = $stmt->get_result()->fetch_assoc()['active_campaigns'];
        $stmt->close();
        
        // Top departamentos
        $stmt = $conn->prepare("SELECT department, COUNT(*) as count FROM tb_client_users WHERE client_id = ? AND status = 1 AND department != '' GROUP BY department ORDER BY count DESC LIMIT 5");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['top_departments'] = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $stmt->close();
        
        echo json_encode($stats);
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Atualizar configurações do cliente
 */
function updateClientSettings($conn, $data) {
    try {
        $client_id = $data['client_id'];
        $settings = $data['settings'];
        
        foreach ($settings as $key => $value) {
            $setting_id = $client_id . '_' . $key;
            $setting_type = is_bool($value) ? 'boolean' : (is_numeric($value) ? 'number' : 'string');
            $setting_value = is_bool($value) ? ($value ? 'true' : 'false') : $value;
            $current_date = (new DateTime())->format('d-m-Y h:i A');
            
            $stmt = $conn->prepare("INSERT INTO tb_client_settings 
                (setting_id, client_id, setting_key, setting_value, setting_type, created_date, updated_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                setting_type = VALUES(setting_type),
                updated_date = VALUES(updated_date)");
                
            $stmt->bind_param('sssssss', 
                $setting_id, $client_id, $key, $setting_value, 
                $setting_type, $current_date, $current_date
            );
            
            $stmt->execute();
            $stmt->close();
        }
        
        echo json_encode(['result' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Gerar ID único para cliente
 */
function generateUniqueClientId($conn, $maxAttempts = 10) {
    for ($i = 0; $i < $maxAttempts; $i++) {
        $client_id = 'client_' . generateRandomId(8);
        
        // Verificar se já existe
        $check_stmt = $conn->prepare("SELECT client_id FROM tb_clients WHERE client_id = ?");
        $check_stmt->bind_param('s', $client_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows === 0) {
            $check_stmt->close();
            return $client_id;
        }
        $check_stmt->close();
    }
    
    // Fallback com timestamp se não conseguir gerar ID único
    return 'client_' . time() . '_' . generateRandomId(4);
}

/**
 * Gerar ID aleatório
 */
function generateRandomId($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
}

/**
 * Buscar clientes disponíveis para o seletor
 */
function getAvailableClients($conn, $search = '') {
    try {
        $whereClause = "WHERE status = 1";
        $params = [];
        $types = "";
        
        if (!empty($search)) {
            $whereClause .= " AND (client_name LIKE ? OR client_domain LIKE ? OR contact_email LIKE ?)";
            $searchParam = '%' . $search . '%';
            $params = [$searchParam, $searchParam, $searchParam];
            $types = "sss";
        }
        
        $query = "
            SELECT 
                client_id as id,
                client_name as text,
                client_domain,
                contact_email,
                industry_sector,
                company_size
            FROM tb_clients 
            {$whereClause}
            ORDER BY client_name ASC
            LIMIT 50
        ";
        
        if (!empty($params)) {
            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = mysqli_query($conn, $query);
        }
        
        $clients = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $clients[] = [
                    'id' => $row['id'],
                    'text' => $row['text'],
                    'domain' => $row['client_domain'],
                    'email' => $row['contact_email'],
                    'industry' => $row['industry_sector'],
                    'size' => $row['company_size']
                ];
            }
        }
        
        echo json_encode(['result' => 'success', 'data' => $clients]);
        
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Definir cliente ativo
 */
function setActiveClient($conn, $data) {
    try {
        $clientId = $data['client_id'] ?? '';
        if (empty($clientId)) {
            throw new Exception('ID do cliente é obrigatório');
        }
        
        // Verificar se o cliente existe
        $stmt = $conn->prepare("SELECT client_id FROM tb_clients WHERE client_id = ? AND status = 1");
        $stmt->bind_param('s', $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Cliente não encontrado');
        }
        
        // Definir contexto na sessão
        setClientContext($clientId);
        
        echo json_encode(['result' => 'success', 'message' => 'Cliente ativo alterado']);
        
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Obter cliente ativo atual
 */
function getActiveClient($conn) {
    try {
        $currentClientId = getCurrentClientId();
        
        if (empty($currentClientId) || $currentClientId === 'default_org') {
            echo json_encode([
                'result' => 'success',
                'data' => [
                    'client_id' => 'default_org',
                    'client_name' => 'Organização Padrão'
                ]
            ]);
            return;
        }
        
        $stmt = $conn->prepare("SELECT client_id, client_name FROM tb_clients WHERE client_id = ? AND status = 1");
        $stmt->bind_param('s', $currentClientId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            echo json_encode([
                'result' => 'success',
                'data' => [
                    'client_id' => $row['client_id'],
                    'client_name' => $row['client_name']
                ]
            ]);
        } else {
            echo json_encode([
                'result' => 'success',
                'data' => [
                    'client_id' => $currentClientId,
                    'client_name' => 'Cliente ' . $currentClientId
                ]
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
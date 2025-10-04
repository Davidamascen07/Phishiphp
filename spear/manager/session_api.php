<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(dirname(__FILE__) . '/session_manager.php');
require_once(dirname(__FILE__,2) . '/config/db.php');

// Verify session is valid (simplified check)
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Sessão inválida ou expirada']);
    exit;
}

// Get POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ação não especificada']);
    exit;
}

try {
    switch ($data['action']) {
        case 'getUserAccessibleClients':
            handleGetUserAccessibleClients();
            break;
            
        case 'setClientContext':
            handleSetClientContext($data);
            break;
            
        case 'getCurrentClientContext':
            handleGetCurrentClientContext();
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
}

function handleGetUserAccessibleClients() {
    global $conn;
    
    try {
        // Get current user from session
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
        
        if (!$username) {
            echo json_encode(['success' => false, 'message' => 'Usuário não logado']);
            return;
        }
        
    // Get accessible clients (usar colunas corretas e status numérico)
    $query = "SELECT client_id, client_name, contact_email, status FROM tb_clients WHERE status = 1 ORDER BY client_name";
    $result = mysqli_query($conn, $query);
        
        $clients = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $clients[] = $row;
            }
        }
        
        // Get current client context
        $currentClientId = getCurrentClientId();
        $currentClientName = null;
        
        if ($currentClientId) {
            foreach ($clients as $client) {
                if (isset($client['client_id']) && $client['client_id'] == $currentClientId) {
                    $currentClientName = isset($client['client_name']) ? $client['client_name'] : null;
                    break;
                }
            }
        }
        
        // If no current client, set first available as default
        if (!$currentClientId && count($clients) > 0) {
            $currentClientId = $clients[0]['client_id'];
            $currentClientName = $clients[0]['client_name'];
            setClientContext($currentClientId);
        }
        
        echo json_encode([
            'success' => true,
            'clients' => $clients,
            'currentClientId' => $currentClientId,
            'currentClientName' => $currentClientName
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao carregar clientes: ' . $e->getMessage()]);
    }
}

function handleSetClientContext($data) {
    try {
        if (!isset($data['clientId'])) {
            echo json_encode(['success' => false, 'message' => 'ID do cliente não fornecido']);
            return;
        }
        
        $clientId = $data['clientId'];
        
        // Validate client exists
        global $conn;
        $query = "SELECT client_name FROM tb_clients WHERE client_id = ? AND status = 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 's', $clientId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            echo json_encode(['success' => false, 'message' => 'Cliente não encontrado ou inativo']);
            return;
        }
        
        $client = mysqli_fetch_assoc($result);
        
        // Set client context
        setClientContext($clientId);
        
        echo json_encode([
            'success' => true,
            'message' => 'Contexto alterado com sucesso',
            'clientId' => $clientId,
            'clientName' => $client['client_name']
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao alterar contexto: ' . $e->getMessage()]);
    }
}

function handleGetCurrentClientContext() {
    try {
        $currentClientId = getCurrentClientId();
        $currentClientName = null;
        
        if ($currentClientId) {
            global $conn;
            $query = "SELECT client_name FROM tb_clients WHERE client_id = ? AND status = 1";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 's', $currentClientId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $client = mysqli_fetch_assoc($result);
                $currentClientName = $client['client_name'];
            }
        }
        
        echo json_encode([
            'success' => true,
            'clientId' => $currentClientId,
            'clientName' => $currentClientName,
            'currentClientId' => $currentClientId, // backward compatibility
            'currentClientName' => $currentClientName // backward compatibility
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao obter contexto: ' . $e->getMessage()]);
    }
}
?>
<?php
/**
 * Link Click Handler para Campanhas de Email
 * Processa cliques em links dentro de emails de phishing
 */

require_once 'spear/config/db.php';
require_once 'spear/manager/common_functions.php';
require_once 'spear/manager/user_campaign_hooks.php';
require_once 'spear/libs/browser_detect/BrowserDetection.php';

// Parâmetros de entrada
$rid = $_GET['rid'] ?? '';
$mid = $_GET['mid'] ?? '';
$url = $_GET['url'] ?? '';

// Validar parâmetros obrigatórios
if (empty($rid) || empty($mid)) {
    // Redirecionar para página padrão se parâmetros inválidos
    header('Location: https://www.google.com');
    exit;
}

// Verificar se campanha está ativa
$user_details = verifyMailCampaignUser($conn, $mid, $rid);
if (verifyMailCampaign($conn, $mid) == true && $user_details != 'empty') {
    
    // Hook: Registrar clique no link
    if (!empty($user_details['user_email'])) {
        onLinkClicked($conn, $user_details['user_email'], $mid);
    }
    
    // Registrar dados do clique
    $ua_info = new Wolfcast\BrowserDetection();
    $public_ip = getPublicIP();
    $user_agent = htmlspecialchars($_SERVER['HTTP_USER_AGENT']);
    $date_time = round(microtime(true) * 1000);
    $user_os = $ua_info->getPlatformVersion();
    $device_type = $ua_info->isMobile() ? "Mobile" : "Desktop";
    $mail_client = getMailClient($user_agent);
    
    if ($mail_client == "unknown") {
        $mail_client = $ua_info->getName() . ' ' . ($ua_info->getVersion() == "unknown" ? "" : $ua_info->getVersion());
    }
    
    // Atualizar dados de clique na tabela tb_data_mailcamp_live
    $click_times = '';
    if (empty($user_details['click_times'])) {
        $click_times = json_encode(array($date_time));
    } else {
        $tmp = json_decode($user_details['click_times']);
        array_push($tmp, $date_time);
        $click_times = json_encode($tmp);
    }
    
    // Atualizar registro com dados do clique
    $stmt = $conn->prepare("UPDATE tb_data_mailcamp_live SET click_times=?, last_click_time=? WHERE campaign_id=? AND rid=?");
    $stmt->bind_param('ssss', $click_times, $date_time, $mid, $rid);
    $stmt->execute();
    
    // Verificar se há treinamento configurado para esta campanha
    $training_config = getTrainingConfigForCampaign($mid);
    
    if ($training_config && $training_config['is_active']) {
        // Redirecionar para página de treinamento
        $training_url = buildTrainingUrl($rid, $mid, $training_config);
        header('Location: ' . $training_url);
        exit;
    }
    
    // Se há URL específica para redirecionar, usar ela
    if (!empty($url)) {
        header('Location: ' . urldecode($url));
        exit;
    }
    
    // Verificar se há landing page configurada na campanha
    $landing_page = getCampaignLandingPage($conn, $mid);
    if (!empty($landing_page)) {
        header('Location: ' . $landing_page);
        exit;
    }
    
    // Redirecionar para página padrão
    header('Location: https://www.microsoft.com');
    exit;
} else {
    // Campanha inativa ou inválida - redirecionar para página padrão
    header('Location: https://www.google.com');
    exit;
}

/**
 * Verifica se campanha de email está ativa
 */
function verifyMailCampaign($conn, $campaign_id) {
    $stmt = $conn->prepare("SELECT scheduled_time,camp_status FROM tb_core_mailcamp_list where campaign_id = ?");
    $stmt->bind_param("s", $campaign_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        if ($row['camp_status'] == 2 || $row['camp_status'] == 4) { // If in-progress
            return true;
        }
    }
    return false;
}

/**
 * Verifica usuário da campanha de email
 */
function verifyMailCampaignUser($conn, $campaign_id, $id) {
    $stmt = $conn->prepare("SELECT * FROM tb_data_mailcamp_live WHERE campaign_id = ? AND rid=?");
    $stmt->bind_param("ss", $campaign_id, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row;
    } else {
        return 'empty';
    }
}

/**
 * Busca configuração de treinamento para campanha
 */
function getTrainingConfigForCampaign($campaign_id) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM tb_campaign_training_association WHERE campaign_id = ? AND campaign_type = 'mail' AND is_active = 1");
    $stmt->bind_param("s", $campaign_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return null;
}

/**
 * Constrói URL para treinamento
 */
function buildTrainingUrl($rid, $campaign_id, $training_config) {
    $base_url = getBaseUrl();
    
    return $base_url . '/spear/training_player.php?' . http_build_query([
        'rid' => $rid,
        'campaign_id' => $campaign_id,
        'module_id' => $training_config['module_id'],
        'trigger' => 'email_click'
    ]);
}

/**
 * Busca landing page da campanha
 */
function getCampaignLandingPage($conn, $campaign_id) {
    $stmt = $conn->prepare("SELECT campaign_data FROM tb_core_mailcamp_list WHERE campaign_id = ?");
    $stmt->bind_param("s", $campaign_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $campaign_data = json_decode($row['campaign_data'], true);
        
        if (isset($campaign_data['landing_page_url']) && !empty($campaign_data['landing_page_url'])) {
            return $campaign_data['landing_page_url'];
        }
    }
    
    return null;
}

/**
 * Obtém URL base
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'];
}
?>
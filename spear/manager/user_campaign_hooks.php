<?php
/**
 * Hooks para integração automática com sistema de usuários
 * Este arquivo deve ser incluído nos managers de campanhas
 */

/**
 * Registra participação do usuário em campanha de email
 */
function registerUserEmailCampaignActivity($conn, $user_email, $campaign_id, $activity_type, $notes = '') {
    $current_client_id = getCurrentClientId();
    
    // Verificar se usuário existe no sistema
    ensureUserExists($conn, $user_email, $current_client_id);
    
    // Registrar atividade
    $clicked = ($activity_type === 'clicked') ? 1 : 0;
    $submitted = ($activity_type === 'submitted') ? 1 : 0;
    $training = ($activity_type === 'training') ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO tb_user_campaign_history 
        (user_email, client_id, campaign_id, campaign_type, clicked, submitted_data, completed_training, last_activity, notes) 
        VALUES (?, ?, ?, 'mail', ?, ?, ?, NOW(), ?)
        ON DUPLICATE KEY UPDATE 
        clicked = GREATEST(clicked, VALUES(clicked)), 
        submitted_data = GREATEST(submitted_data, VALUES(submitted_data)), 
        completed_training = GREATEST(completed_training, VALUES(completed_training)), 
        last_activity = VALUES(last_activity),
        notes = CONCAT(IFNULL(notes, ''), IF(notes IS NULL OR notes = '', '', '; '), VALUES(notes))");
    
    $stmt->bind_param("sssiiis", $user_email, $current_client_id, $campaign_id, $clicked, $submitted, $training, $notes);
    
    if ($stmt->execute()) {
        // Atualizar contador de campanhas do usuário
        updateUserCampaignStats($conn, $user_email, $current_client_id);
        return true;
    }
    
    return false;
}

/**
 * Registra participação do usuário em campanha web
 */
function registerUserWebCampaignActivity($conn, $user_email, $tracker_id, $activity_type, $notes = '') {
    $current_client_id = getCurrentClientId();
    
    // Verificar se usuário existe no sistema
    ensureUserExists($conn, $user_email, $current_client_id);
    
    // Registrar atividade
    $clicked = ($activity_type === 'clicked' || $activity_type === 'visited') ? 1 : 0;
    $submitted = ($activity_type === 'submitted') ? 1 : 0;
    $training = ($activity_type === 'training') ? 1 : 0;
    
    $stmt = $conn->prepare("INSERT INTO tb_user_campaign_history 
        (user_email, client_id, campaign_id, campaign_type, clicked, submitted_data, completed_training, last_activity, notes) 
        VALUES (?, ?, ?, 'web', ?, ?, ?, NOW(), ?)
        ON DUPLICATE KEY UPDATE 
        clicked = GREATEST(clicked, VALUES(clicked)), 
        submitted_data = GREATEST(submitted_data, VALUES(submitted_data)), 
        completed_training = GREATEST(completed_training, VALUES(completed_training)), 
        last_activity = VALUES(last_activity),
        notes = CONCAT(IFNULL(notes, ''), IF(notes IS NULL OR notes = '', '', '; '), VALUES(notes))");
    
    $stmt->bind_param("sssiiis", $user_email, $current_client_id, $tracker_id, $clicked, $submitted, $training, $notes);
    
    if ($stmt->execute()) {
        // Atualizar contador de campanhas do usuário
        updateUserCampaignStats($conn, $user_email, $current_client_id);
        return true;
    }
    
    return false;
}

/**
 * Garante que o usuário existe no sistema
 */
function ensureUserExists($conn, $user_email, $client_id) {
    // Verificar se usuário já existe
    $stmt = $conn->prepare("SELECT id FROM tb_client_users WHERE user_email = ? AND client_id = ?");
    $stmt->bind_param("ss", $user_email, $client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        // Criar usuário básico
        $current_time = date('d-m-Y h:i A');
        $user_name = extractNameFromEmail($user_email);
        
        $stmt = $conn->prepare("INSERT INTO tb_client_users 
            (user_email, user_name, client_id, added_date, last_updated, status) 
            VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("sssss", $user_email, $user_name, $client_id, $current_time, $current_time);
        $stmt->execute();
    }
}

/**
 * Extrai nome do email para criar usuário básico
 */
function extractNameFromEmail($email) {
    $parts = explode('@', $email);
    $username = $parts[0];
    
    // Remover números e caracteres especiais
    $name = preg_replace('/[^a-zA-Z\s]/', ' ', $username);
    $name = ucwords(trim($name));
    
    return !empty($name) ? $name : 'Usuário';
}

/**
 * Atualiza estatísticas de campanhas do usuário
 */
function updateUserCampaignStats($conn, $user_email, $client_id) {
    $stmt = $conn->prepare("UPDATE tb_client_users SET 
        campaign_count = (SELECT COUNT(DISTINCT campaign_id) FROM tb_user_campaign_history WHERE user_email = ? AND client_id = ?),
        last_campaign_date = (SELECT MAX(participation_date) FROM tb_user_campaign_history WHERE user_email = ? AND client_id = ?)
        WHERE user_email = ? AND client_id = ?");
    $stmt->bind_param("ssssss", $user_email, $client_id, $user_email, $client_id, $user_email, $client_id);
    $stmt->execute();
}

/**
 * Busca usuários por email para campanhas
 */
function getUsersForCampaign($conn, $user_group_id) {
    $current_client_id = getCurrentClientId();
    
    // Buscar emails do grupo de usuários
    $stmt = $conn->prepare("SELECT user_data FROM tb_core_mailcamp_user_group WHERE user_group_id = ? AND client_id = ?");
    $stmt->bind_param("ss", $user_group_id, $current_client_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        return [];
    }
    
    $row = $result->fetch_assoc();
    $user_data = json_decode($row['user_data'], true);
    
    $emails = [];
    if (is_array($user_data)) {
        foreach ($user_data as $user) {
            if (isset($user['email']) && !empty($user['email'])) {
                $emails[] = strtolower(trim($user['email']));
            }
        }
    }
    
    return $emails;
}

/**
 * Sincroniza usuários de grupo com sistema de usuários
 */
function syncUserGroupToUserSystem($conn, $user_group_id) {
    $current_client_id = getCurrentClientId();
    $emails = getUsersForCampaign($conn, $user_group_id);
    
    foreach ($emails as $email) {
        ensureUserExists($conn, $email, $current_client_id);
    }
    
    return count($emails);
}

/**
 * Hook para ser chamado quando email é aberto
 */
function onEmailOpened($conn, $user_email, $campaign_id) {
    registerUserEmailCampaignActivity($conn, $user_email, $campaign_id, 'clicked', 'Email aberto');
}

/**
 * Hook para ser chamado quando link é clicado
 */
function onLinkClicked($conn, $user_email, $campaign_id) {
    registerUserEmailCampaignActivity($conn, $user_email, $campaign_id, 'clicked', 'Link clicado');
}

/**
 * Hook para ser chamado quando dados são submetidos
 */
function onDataSubmitted($conn, $user_email, $campaign_id, $submitted_data = []) {
    $notes = 'Dados submetidos';
    if (!empty($submitted_data)) {
        $notes .= ': ' . json_encode($submitted_data);
    }
    registerUserEmailCampaignActivity($conn, $user_email, $campaign_id, 'submitted', $notes);
}

/**
 * Hook para ser chamado quando página web é visitada
 */
function onWebPageVisited($conn, $user_email, $tracker_id, $page_info = []) {
    $notes = 'Página visitada';
    if (!empty($page_info)) {
        $notes .= ': ' . json_encode($page_info);
    }
    registerUserWebCampaignActivity($conn, $user_email, $tracker_id, 'visited', $notes);
}

/**
 * Hook para ser chamado quando formulário web é submetido
 */
function onWebFormSubmitted($conn, $user_email, $tracker_id, $form_data = []) {
    $notes = 'Formulário web submetido';
    if (!empty($form_data)) {
        $notes .= ': ' . json_encode($form_data);
    }
    registerUserWebCampaignActivity($conn, $user_email, $tracker_id, 'submitted', $notes);
}

/**
 * Hook para ser chamado quando treinamento é completado
 */
function onTrainingCompleted($conn, $user_email, $campaign_id, $campaign_type = 'mail') {
    if ($campaign_type === 'mail') {
        registerUserEmailCampaignActivity($conn, $user_email, $campaign_id, 'training', 'Treinamento completado');
    } else {
        registerUserWebCampaignActivity($conn, $user_email, $campaign_id, 'training', 'Treinamento completado');
    }
}
?>
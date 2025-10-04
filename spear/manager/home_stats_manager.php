<?php
/**
 * Home Stats Manager - Loophish
 * Gerenciador de Estatísticas do Dashboard com Suporte Multi-tenant
 * Fase 1 - Sistema de Clientes
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
            case 'get_email_campaigns_stats':
                getEmailCampaignsStats($conn, $POSTJ['client_id'] ?? 'default_org');
                break;
            case 'get_web_trackers_stats':
                getWebTrackersStats($conn, $POSTJ['client_id'] ?? 'default_org');
                break;
            case 'get_quick_trackers_stats':
                getQuickTrackersStats($conn, $POSTJ['client_id'] ?? 'default_org');
                break;
            case 'get_dashboard_overview':
                getDashboardOverview($conn, $POSTJ['client_id'] ?? 'default_org');
                break;
            case 'set_client_context':
                setClientContextSession($POSTJ['client_id']);
                break;
            default:
                echo json_encode(['result' => 'error', 'message' => 'Ação não reconhecida']);
        }
    }
}

/**
 * Obter estatísticas de campanhas de email por cliente
 */
function getEmailCampaignsStats($conn, $client_id) {
    try {
        $stats = [];
        
        // Verificar se as tabelas possuem client_id
        $check_column = $conn->query("SHOW COLUMNS FROM tb_core_mailcamp_list LIKE 'client_id'");
        $has_client_id = $check_column && $check_column->num_rows > 0;
        
        if (!$has_client_id) {
            // Se não tem client_id, retorna todas as campanhas (versão compatível)
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_core_mailcamp_list");
            $stmt->execute();
            $stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM tb_core_mailcamp_list WHERE camp_status IN (1,2)");
            $stmt->execute();
            $stats['active'] = $stmt->get_result()->fetch_assoc()['active'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM tb_core_mailcamp_list WHERE camp_status = 3");
            $stmt->execute();
            $stats['completed'] = $stmt->get_result()->fetch_assoc()['completed'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as sent_emails FROM tb_data_mailcamp_live WHERE sending_status >= 1");
            $stmt->execute();
            $stats['sent_emails'] = $stmt->get_result()->fetch_assoc()['sent_emails'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as opened_emails FROM tb_data_mailcamp_live WHERE mail_open_times IS NOT NULL AND mail_open_times != ''");
            $stmt->execute();
            $stats['opened_emails'] = $stmt->get_result()->fetch_assoc()['opened_emails'];
            $stmt->close();
        } else {
            // Se tem client_id, usa filtro por cliente
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_core_mailcamp_list WHERE client_id = ?");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM tb_core_mailcamp_list WHERE client_id = ? AND camp_status IN (1,2)");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['active'] = $stmt->get_result()->fetch_assoc()['active'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as completed FROM tb_core_mailcamp_list WHERE client_id = ? AND camp_status = 3");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['completed'] = $stmt->get_result()->fetch_assoc()['completed'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as sent_emails FROM tb_data_mailcamp_live mcl 
                                   JOIN tb_core_mailcamp_list cml ON mcl.campaign_id = cml.campaign_id 
                                   WHERE cml.client_id = ? AND mcl.sending_status >= 1");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['sent_emails'] = $stmt->get_result()->fetch_assoc()['sent_emails'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as opened_emails FROM tb_data_mailcamp_live mcl 
                                   JOIN tb_core_mailcamp_list cml ON mcl.campaign_id = cml.campaign_id 
                                   WHERE cml.client_id = ? AND mcl.mail_open_times IS NOT NULL AND mcl.mail_open_times != ''");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['opened_emails'] = $stmt->get_result()->fetch_assoc()['opened_emails'];
            $stmt->close();
        }
        
        echo json_encode($stats);
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Obter estatísticas de rastreadores web por cliente
 */
function getWebTrackersStats($conn, $client_id) {
    try {
        $stats = [];
        
        // Verificar se as tabelas possuem client_id
        $check_column = $conn->query("SHOW COLUMNS FROM tb_core_web_tracker_list LIKE 'client_id'");
        $has_client_id = $check_column && $check_column->num_rows > 0;
        
        if (!$has_client_id) {
            // Se não tem client_id, retorna todos os rastreadores (versão compatível)
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_core_web_tracker_list");
            $stmt->execute();
            $stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM tb_core_web_tracker_list WHERE active = 1");
            $stmt->execute();
            $stats['active'] = $stmt->get_result()->fetch_assoc()['active'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as visits FROM tb_data_webpage_visit");
            $stmt->execute();
            $stats['visits'] = $stmt->get_result()->fetch_assoc()['visits'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as submissions FROM tb_data_webform_submit");
            $stmt->execute();
            $stats['submissions'] = $stmt->get_result()->fetch_assoc()['submissions'];
            $stmt->close();
        } else {
            // Se tem client_id, usa filtro por cliente
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_core_web_tracker_list WHERE client_id = ?");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM tb_core_web_tracker_list WHERE client_id = ? AND active = 1");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['active'] = $stmt->get_result()->fetch_assoc()['active'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as visits FROM tb_data_webpage_visit wpv 
                                   JOIN tb_core_web_tracker_list wtl ON wpv.tracker_id = wtl.tracker_id 
                                   WHERE wtl.client_id = ?");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['visits'] = $stmt->get_result()->fetch_assoc()['visits'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as submissions FROM tb_data_webform_submit wfs 
                                   JOIN tb_core_web_tracker_list wtl ON wfs.tracker_id = wtl.tracker_id 
                                   WHERE wtl.client_id = ?");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['submissions'] = $stmt->get_result()->fetch_assoc()['submissions'];
            $stmt->close();
        }
        
        echo json_encode($stats);
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Obter estatísticas de trackers rápidos por cliente
 */
function getQuickTrackersStats($conn, $client_id) {
    try {
        $stats = [];
        
        // Verificar se as tabelas possuem client_id
        $check_column = $conn->query("SHOW COLUMNS FROM tb_core_quick_tracker_list LIKE 'client_id'");
        $has_client_id = $check_column && $check_column->num_rows > 0;
        
        if (!$has_client_id) {
            // Se não tem client_id, retorna todos os trackers (versão compatível)
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_core_quick_tracker_list");
            $stmt->execute();
            $stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM tb_core_quick_tracker_list WHERE active = 1");
            $stmt->execute();
            $stats['active'] = $stmt->get_result()->fetch_assoc()['active'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as clicks FROM tb_data_quick_tracker_live");
            $stmt->execute();
            $stats['clicks'] = $stmt->get_result()->fetch_assoc()['clicks'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT public_ip) as unique_clicks FROM tb_data_quick_tracker_live");
            $stmt->execute();
            $stats['unique_clicks'] = $stmt->get_result()->fetch_assoc()['unique_clicks'];
            $stmt->close();
        } else {
            // Se tem client_id, usa filtro por cliente
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_core_quick_tracker_list WHERE client_id = ?");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as active FROM tb_core_quick_tracker_list WHERE client_id = ? AND active = 1");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['active'] = $stmt->get_result()->fetch_assoc()['active'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(*) as clicks FROM tb_data_quick_tracker_live qtl 
                                   JOIN tb_core_quick_tracker_list qtr ON qtl.tracker_id = qtr.tracker_id 
                                   WHERE qtr.client_id = ?");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['clicks'] = $stmt->get_result()->fetch_assoc()['clicks'];
            $stmt->close();
            
            $stmt = $conn->prepare("SELECT COUNT(DISTINCT qtl.public_ip) as unique_clicks FROM tb_data_quick_tracker_live qtl 
                                   JOIN tb_core_quick_tracker_list qtr ON qtl.tracker_id = qtr.tracker_id 
                                   WHERE qtr.client_id = ?");
            $stmt->bind_param('s', $client_id);
            $stmt->execute();
            $stats['unique_clicks'] = $stmt->get_result()->fetch_assoc()['unique_clicks'];
            $stmt->close();
        }
        
        echo json_encode($stats);
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Obter visão geral completa do dashboard
 */
function getDashboardOverview($conn, $client_id) {
    try {
        $overview = [];
        
        // Campanhas por status
        $stmt = $conn->prepare("SELECT camp_status, COUNT(*) as count FROM tb_core_mailcamp_list 
                               WHERE client_id = ? GROUP BY camp_status");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $campaigns_by_status = [];
        while ($row = $result->fetch_assoc()) {
            $status_labels = [
                0 => 'Inativo',
                1 => 'Executando', 
                2 => 'Agendado',
                3 => 'Completado'
            ];
            $campaigns_by_status[] = [
                'status' => $status_labels[$row['camp_status']] ?? 'Desconhecido',
                'count' => intval($row['count'])
            ];
        }
        $overview['campaigns_by_status'] = $campaigns_by_status;
        $stmt->close();
        
        // Atividade recente (últimos 30 dias)
        $stmt = $conn->prepare("SELECT 
                                   DATE(STR_TO_DATE(date, '%d-%m-%Y %h:%i %p')) as activity_date,
                                   COUNT(*) as count,
                                   'campaign' as type
                               FROM tb_core_mailcamp_list 
                               WHERE client_id = ? 
                                   AND STR_TO_DATE(date, '%d-%m-%Y %h:%i %p') >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                               GROUP BY DATE(STR_TO_DATE(date, '%d-%m-%Y %h:%i %p'))
                               ORDER BY activity_date DESC
                               LIMIT 10");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $recent_activity = [];
        while ($row = $result->fetch_assoc()) {
            $recent_activity[] = [
                'date' => $row['activity_date'],
                'count' => intval($row['count']),
                'type' => $row['type']
            ];
        }
        $overview['recent_activity'] = $recent_activity;
        $stmt->close();
        
        // Top 5 campanhas por clicks/abertura
        $stmt = $conn->prepare("SELECT 
                                   cml.campaign_name,
                                   COUNT(mcl.rid) as interactions,
                                   SUM(CASE WHEN mcl.mail_open_times IS NOT NULL AND mcl.mail_open_times != '' THEN 1 ELSE 0 END) as opens
                               FROM tb_core_mailcamp_list cml
                               LEFT JOIN tb_data_mailcamp_live mcl ON cml.campaign_id = mcl.campaign_id
                               WHERE cml.client_id = ?
                               GROUP BY cml.campaign_id, cml.campaign_name
                               HAVING interactions > 0
                               ORDER BY interactions DESC
                               LIMIT 5");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $top_campaigns = [];
        while ($row = $result->fetch_assoc()) {
            $top_campaigns[] = [
                'name' => $row['campaign_name'],
                'interactions' => intval($row['interactions']),
                'opens' => intval($row['opens'])
            ];
        }
        $overview['top_campaigns'] = $top_campaigns;
        $stmt->close();
        
        echo json_encode($overview);
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Definir contexto do cliente na sessão
 */
function setClientContextSession($client_id) {
    try {
        // Verificar se o cliente existe
        global $conn;
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tb_clients WHERE client_id = ? AND status = 1");
        $stmt->bind_param('s', $client_id);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc()['count'] > 0;
        $stmt->close();
        
        if (!$exists) {
            echo json_encode(['result' => 'error', 'message' => 'Cliente não encontrado']);
            return;
        }
        
        // Definir contexto na sessão
        setClientContext($client_id);
        
        echo json_encode(['result' => 'success']);
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}

/**
 * Obter dados para gráficos do dashboard
 */
function getDashboardChartData($conn, $client_id, $chart_type, $period = '30') {
    try {
        $data = [];
        
        switch ($chart_type) {
            case 'campaigns_timeline':
                // Timeline de campanhas nos últimos X dias
                $stmt = $conn->prepare("SELECT 
                                           DATE(STR_TO_DATE(date, '%d-%m-%Y %h:%i %p')) as chart_date,
                                           COUNT(*) as count
                                       FROM tb_core_mailcamp_list 
                                       WHERE client_id = ? 
                                           AND STR_TO_DATE(date, '%d-%m-%Y %h:%i %p') >= DATE_SUB(NOW(), INTERVAL ? DAY)
                                       GROUP BY DATE(STR_TO_DATE(date, '%d-%m-%Y %h:%i %p'))
                                       ORDER BY chart_date ASC");
                $stmt->bind_param('si', $client_id, $period);
                break;
                
            case 'email_interactions':
                // Interações com emails ao longo do tempo
                $stmt = $conn->prepare("SELECT 
                                           DATE(STR_TO_DATE(mcl.send_time, '%d-%m-%Y %h:%i %p')) as chart_date,
                                           COUNT(*) as sent,
                                           SUM(CASE WHEN mcl.mail_open_times IS NOT NULL AND mcl.mail_open_times != '' THEN 1 ELSE 0 END) as opened
                                       FROM tb_data_mailcamp_live mcl
                                       JOIN tb_core_mailcamp_list cml ON mcl.campaign_id = cml.campaign_id
                                       WHERE cml.client_id = ? 
                                           AND STR_TO_DATE(mcl.send_time, '%d-%m-%Y %h:%i %p') >= DATE_SUB(NOW(), INTERVAL ? DAY)
                                       GROUP BY DATE(STR_TO_DATE(mcl.send_time, '%d-%m-%Y %h:%i %p'))
                                       ORDER BY chart_date ASC");
                $stmt->bind_param('si', $client_id, $period);
                break;
                
            default:
                echo json_encode(['result' => 'error', 'message' => 'Tipo de gráfico não reconhecido']);
                return;
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $stmt->close();
        echo json_encode($data);
        
    } catch (Exception $e) {
        echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
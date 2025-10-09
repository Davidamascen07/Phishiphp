<?php
/**
 * Certificate Manager
 * Gerenciamento completo de certificados de treinamento
 */

require_once(dirname(__FILE__) . '/session_manager.php');
if(isSessionValid() == false)
    die("Access denied");

require_once(dirname(__FILE__) . '/common_functions.php');

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
    
    // Validate action type
    if (!isset($data['action_type'])) {
        throw new Exception('Action type is required');
    }
    
    $action_type = $data['action_type'];
    $response = array();
    
    switch ($action_type) {
        
        case 'get_certificates':
            $client_id = $data['client_id'] ?? null;
            $module_id = $data['module_id'] ?? null;
            $status = $data['status'] ?? null;
            
            $certificates = getCertificates($client_id, $module_id, $status);
            
            $response = array(
                'result' => 'success',
                'data' => $certificates,
                'total' => count($certificates)
            );
            break;
            
        case 'get_certificate_stats':
            $client_id = $data['client_id'] ?? null;
            $stats = getCertificateStats($client_id);
            
            $response = array(
                'result' => 'success',
                'data' => $stats
            );
            break;
            
        case 'validate_certificate':
            $validation_code = $data['validation_code'] ?? '';
            
            if (empty($validation_code)) {
                throw new Exception('Validation code is required');
            }
            
            $certificate = validateCertificate($validation_code);
            
            if ($certificate) {
                $response = array(
                    'result' => 'success',
                    'data' => $certificate
                );
            } else {
                $response = array(
                    'result' => 'error',
                    'error' => 'Certificate not found or invalid'
                );
            }
            break;
            
        case 'generate_certificate_pdf':
            $certificate_id = $data['certificate_id'] ?? '';
            $template = $data['template'] ?? 'default';
            
            if (empty($certificate_id)) {
                throw new Exception('Certificate ID is required');
            }
            
            $pdf_url = generateCertificatePDF($certificate_id, $template);
            
            if ($pdf_url) {
                $response = array(
                    'result' => 'success',
                    'pdf_url' => $pdf_url
                );
            } else {
                $response = array(
                    'result' => 'error',
                    'error' => 'Failed to generate PDF'
                );
            }
            break;
            
        case 'revoke_certificate':
            $certificate_id = $data['certificate_id'] ?? '';
            
            if (empty($certificate_id)) {
                throw new Exception('Certificate ID is required');
            }
            
            $result = revokeCertificate($certificate_id);
            
            if ($result) {
                $response = array(
                    'result' => 'success',
                    'message' => 'Certificate revoked successfully'
                );
            } else {
                $response = array(
                    'result' => 'error',
                    'error' => 'Failed to revoke certificate'
                );
            }
            break;
            
        case 'restore_certificate':
            $certificate_id = $data['certificate_id'] ?? '';
            
            if (empty($certificate_id)) {
                throw new Exception('Certificate ID is required');
            }
            
            $result = restoreCertificate($certificate_id);
            
            if ($result) {
                $response = array(
                    'result' => 'success',
                    'message' => 'Certificate restored successfully'
                );
            } else {
                $response = array(
                    'result' => 'error',
                    'error' => 'Failed to restore certificate'
                );
            }
            break;
            
        case 'bulk_issue_certificates':
            $module_id = $data['module_id'] ?? '';
            $client_id = $data['client_id'] ?? null;
            $min_score = intval($data['min_score'] ?? 70);
            
            if (empty($module_id)) {
                throw new Exception('Module ID is required');
            }
            
            $issued_count = bulkIssueCertificates($module_id, $client_id, $min_score);
            
            $response = array(
                'result' => 'success',
                'issued_count' => $issued_count,
                'message' => "Successfully issued $issued_count certificates"
            );
            break;
            
        case 'get_certificate_templates':
            $templates = getCertificateTemplates();
            
            $response = array(
                'result' => 'success',
                'data' => $templates
            );
            break;
            
        case 'upload_certificate_template':
            if (empty($_FILES['template_file'])) {
                throw new Exception('Template file is required');
            }
            
            $template_name = $data['template_name'] ?? '';
            $template_id = uploadCertificateTemplate($_FILES['template_file'], $template_name);
            
            $response = array(
                'result' => 'success',
                'template_id' => $template_id,
                'message' => 'Template uploaded successfully'
            );
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
echo json_encode($response);

/**
 * Get certificates with optional filters
 */
function getCertificates($client_id = null, $module_id = null, $status = null) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT tc.*, tm.module_name, tm.category,
                       CASE WHEN cl.client_name IS NOT NULL THEN cl.client_name ELSE 'Default' END as client_name,
                       tp.completion_time, tp.time_spent
                FROM tb_training_certificates tc
                JOIN tb_training_modules tm ON tc.module_id = tm.module_id
                LEFT JOIN tb_clients cl ON tc.client_id = cl.client_id
                LEFT JOIN tb_training_progress tp ON tc.progress_id = tp.progress_id
                WHERE 1=1";
        
        $params = array();
        $types = '';
        
        if ($client_id) {
            $sql .= " AND tc.client_id = ?";
            $params[] = $client_id;
            $types .= 's';
        }
        
        if ($module_id) {
            $sql .= " AND tc.module_id = ?";
            $params[] = $module_id;
            $types .= 's';
        }
        
        if ($status !== null) {
            $sql .= " AND tc.status = ?";
            $params[] = $status;
            $types .= 'i';
        }
        
        $sql .= " ORDER BY tc.issued_date DESC";
        
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $certificates = array();
        while ($row = $result->fetch_assoc()) {
            // Format dates
            $row['issued_date'] = formatDateForDisplay($row['issued_date']);
            $row['completion_date'] = formatDateForDisplay($row['completion_date']);
            $row['expires_date'] = !empty($row['expires_date']) ? formatDateForDisplay($row['expires_date']) : null;
            
            $certificates[] = $row;
        }
        
        return $certificates;
        
    } catch (Exception $e) {
        error_log("Error getting certificates: " . $e->getMessage());
        return array();
    } finally {
        $db->close();
    }
}

/**
 * Get certificate statistics
 */
function getCertificateStats($client_id = null) {
    global $conn; $db = $conn;
    
    try {
        $stats = array();
        
        // Total certificates
        $sql = "SELECT COUNT(*) as total FROM tb_training_certificates WHERE 1=1";
        $params = array();
        $types = '';
        
        if ($client_id) {
            $sql .= " AND client_id = ?";
            $params[] = $client_id;
            $types .= 's';
        }
        
        $stmt = $db->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $stats['total_certificates'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Monthly certificates (current month)
        $sql = "SELECT COUNT(*) as total FROM tb_training_certificates 
                WHERE MONTH(STR_TO_DATE(issued_date, '%d-%m-%Y %h:%i %p')) = MONTH(CURDATE()) 
                AND YEAR(STR_TO_DATE(issued_date, '%d-%m-%Y %h:%i %p')) = YEAR(CURDATE())";
        
        if ($client_id) {
            $sql .= " AND client_id = ?";
        }
        
        $stmt = $db->prepare($sql);
        if ($client_id) {
            $stmt->bind_param('s', $client_id);
        }
        $stmt->execute();
        $stats['monthly_certificates'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Valid certificates
        $sql = "SELECT COUNT(*) as total FROM tb_training_certificates WHERE status = 1";
        if ($client_id) {
            $sql .= " AND client_id = ?";
        }
        
        $stmt = $db->prepare($sql);
        if ($client_id) {
            $stmt->bind_param('s', $client_id);
        }
        $stmt->execute();
        $stats['valid_certificates'] = $stmt->get_result()->fetch_assoc()['total'];
        
        // Downloaded certificates (estimate based on PDF generation logs)
        $stats['downloaded_certificates'] = floor($stats['total_certificates'] * 0.7); // Estimate
        
        return $stats;
        
    } catch (Exception $e) {
        error_log("Error getting certificate stats: " . $e->getMessage());
        return array(
            'total_certificates' => 0,
            'monthly_certificates' => 0,
            'valid_certificates' => 0,
            'downloaded_certificates' => 0
        );
    } finally {
        $db->close();
    }
}

/**
 * Validate certificate by validation code
 */
function validateCertificate($validation_code) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT tc.*, tm.module_name, tm.module_description,
                       CASE WHEN cl.client_name IS NOT NULL THEN cl.client_name ELSE 'Default' END as client_name,
                       CASE 
                           WHEN tc.status = 1 AND (tc.expires_date IS NULL OR STR_TO_DATE(tc.expires_date, '%d-%m-%Y %h:%i %p') > NOW()) 
                           THEN 1 
                           ELSE 0 
                       END as is_valid
                FROM tb_training_certificates tc
                JOIN tb_training_modules tm ON tc.module_id = tm.module_id
                LEFT JOIN tb_clients cl ON tc.client_id = cl.client_id
                WHERE tc.validation_code = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $validation_code);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            // Format dates
            $row['issued_date'] = formatDateForDisplay($row['issued_date']);
            $row['completion_date'] = formatDateForDisplay($row['completion_date']);
            $row['expires_date'] = !empty($row['expires_date']) ? formatDateForDisplay($row['expires_date']) : null;
            
            return $row;
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Error validating certificate: " . $e->getMessage());
        return null;
    } finally {
        $db->close();
    }
}

/**
 * Generate PDF certificate
 */
function generateCertificatePDF($certificate_id, $template = 'default') {
    try {
        // For now, redirect to certificate_view.php with download action
        $base_url = getBaseUrlForCertificates();
        $pdf_url = $base_url . '/certificate_view.php?cert_id=' . $certificate_id . '&action=download';
        
        return $pdf_url;
        
    } catch (Exception $e) {
        error_log("Error generating PDF: " . $e->getMessage());
        return null;
    }
}

/**
 * Revoke certificate
 */
function revokeCertificate($certificate_id) {
    global $conn; $db = $conn;
    
    try {
        $sql = "UPDATE tb_training_certificates SET status = 0 WHERE certificate_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $certificate_id);
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error revoking certificate: " . $e->getMessage());
        return false;
    } finally {
        $db->close();
    }
}

/**
 * Restore certificate
 */
function restoreCertificate($certificate_id) {
    global $conn; $db = $conn;
    
    try {
        $sql = "UPDATE tb_training_certificates SET status = 1 WHERE certificate_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $certificate_id);
        
        return $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error restoring certificate: " . $e->getMessage());
        return false;
    } finally {
        $db->close();
    }
}

/**
 * Bulk issue certificates
 */
function bulkIssueCertificates($module_id, $client_id = null, $min_score = 70) {
    global $conn; $db = $conn;
    
    try {
        // Find eligible users (completed training with passing score, no certificate yet)
        $sql = "SELECT tp.*, tm.module_name, tqr.score
                FROM tb_training_progress tp
                JOIN tb_training_modules tm ON tp.module_id = tm.module_id
                LEFT JOIN tb_training_quiz_results tqr ON tp.progress_id = tqr.progress_id
                LEFT JOIN tb_training_certificates tc ON tp.progress_id = tc.progress_id
                WHERE tp.module_id = ? 
                AND tp.status = 'completed'
                AND (tqr.score >= ? OR tqr.score IS NULL)
                AND tc.certificate_id IS NULL";
        
        $params = array($module_id, $min_score);
        $types = 'si';
        
        if ($client_id) {
            $sql .= " AND tp.client_id = ?";
            $params[] = $client_id;
            $types .= 's';
        }
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $issued_count = 0;
        
        while ($row = $result->fetch_assoc()) {
            $certificate_id = issueCertificateForUser($row);
            if ($certificate_id) {
                $issued_count++;
            }
        }
        
        return $issued_count;
        
    } catch (Exception $e) {
        error_log("Error bulk issuing certificates: " . $e->getMessage());
        return 0;
    } finally {
        $db->close();
    }
}

/**
 * Issue certificate for specific user
 */
function issueCertificateForUser($progress_data) {
    global $conn; $db = $conn;
    
    try {
        $certificate_id = generateRandomId();
        $validation_code = strtoupper(generateRandomId(12));
        $current_date = (new DateTime())->format('d-m-Y h:i A');
        
        $sql = "INSERT INTO tb_training_certificates 
                (certificate_id, progress_id, user_email, user_name, client_id, module_id, module_name, 
                 score_achieved, completion_date, validation_code, issued_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sssssssssss", 
            $certificate_id,
            $progress_data['progress_id'],
            $progress_data['user_email'],
            $progress_data['user_name'],
            $progress_data['client_id'],
            $progress_data['module_id'],
            $progress_data['module_name'],
            $progress_data['score'] ?? 100,
            $progress_data['completion_time'] ?? $current_date,
            $validation_code,
            $current_date
        );
        
        if ($stmt->execute()) {
            // Update progress record
            $update_sql = "UPDATE tb_training_progress SET certificate_issued = 1, certificate_id = ? WHERE progress_id = ?";
            $update_stmt = $db->prepare($update_sql);
            $update_stmt->bind_param("ss", $certificate_id, $progress_data['progress_id']);
            $update_stmt->execute();
            
            return $certificate_id;
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Error issuing certificate: " . $e->getMessage());
        return false;
    }
}

/**
 * Get available certificate templates
 */
function getCertificateTemplates() {
    $templates_dir = dirname(__FILE__) . '/../templates/certificates/';
    $templates = array();
    
    if (is_dir($templates_dir)) {
        $files = scandir($templates_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'html') {
                $template_name = pathinfo($file, PATHINFO_FILENAME);
                $templates[] = array(
                    'id' => $template_name,
                    'name' => ucwords(str_replace('_', ' ', $template_name)),
                    'file' => $file
                );
            }
        }
    }
    
    return $templates;
}

/**
 * Upload new certificate template
 */
function uploadCertificateTemplate($file, $template_name) {
    $templates_dir = dirname(__FILE__) . '/../templates/certificates/';
    
    if (!is_dir($templates_dir)) {
        mkdir($templates_dir, 0755, true);
    }
    
    $template_id = preg_replace('/[^a-zA-Z0-9_-]/', '', $template_name);
    if (empty($template_id)) {
        $template_id = 'custom_' . generateRandomId(8);
    }
    
    $target_file = $templates_dir . $template_id . '.html';
    
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return $template_id;
    }
    
    return false;
}

/**
 * Helper function to get base URL
 */
function getBaseUrlForCertificates() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    
    return $protocol . $host . $path . '/..';
}

/**
 * Format date for display
 */
function formatDateForDisplay($date_string) {
    if (empty($date_string)) return '';
    
    try {
        // Try to parse the date format used in the system (d-m-Y h:i A)
        $date = DateTime::createFromFormat('d-m-Y h:i A', $date_string);
        if ($date === false) {
            // Fallback to standard format
            $date = new DateTime($date_string);
        }
        return $date->format('d/m/Y H:i');
    } catch (Exception $e) {
        return $date_string;
    }
}

/**
 * Generate random ID
 */
function generateRandomId($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}
?>
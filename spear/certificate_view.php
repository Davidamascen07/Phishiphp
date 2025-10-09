<?php
/**
 * Certificate View & Download
 * Página para visualização e download de certificados com templates customizáveis
 */

session_start();
require_once 'manager/common_functions.php';
require_once 'manager/training_manager.php';

// Get parameters
$cert_id = $_GET['cert_id'] ?? $_GET['id'] ?? '';
$action = $_GET['action'] ?? 'view'; // view, download, validate

if (empty($cert_id)) {
    die('Certificate ID is required');
}

// Get certificate details
$certificate = getCertificateDetails($cert_id);

if (!$certificate) {
    die('Certificate not found or invalid');
}

// Handle different actions
switch ($action) {
    case 'download':
        generateCertificatePDF($certificate);
        break;
    case 'validate':
        validateCertificate($certificate);
        break;
    case 'view':
    default:
        displayCertificate($certificate);
        break;
}

/**
 * Get certificate details from database
 */
function getCertificateDetails($cert_id) {
    global $conn; $db = $conn;
    
    try {
        $sql = "SELECT tc.*, tm.module_name, tm.module_description, tm.category,
                       tp.completion_time, tp.time_spent
                FROM tb_training_certificates tc
                JOIN tb_training_modules tm ON tc.module_id = tm.module_id
                LEFT JOIN tb_training_progress tp ON tc.progress_id = tp.progress_id
                WHERE tc.certificate_id = ? AND tc.status = 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $cert_id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
        
    } catch (Exception $e) {
        error_log("Error getting certificate: " . $e->getMessage());
        return null;
    } finally {
        $db->close();
    }
}

/**
 * Display certificate in HTML format
 */
function displayCertificate($certificate) {
    $template_file = getTemplate($certificate['certificate_template'] ?? 'default');
    
    // Replace template variables
    $html_content = processTemplate($template_file, $certificate);
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Security Training Certificate</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <link rel="stylesheet" href="css/certificate.css">
        <style>
            body {
                font-family: 'Georgia', serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                padding: 20px;
            }
            
            .certificate-container {
                max-width: 900px;
                margin: 0 auto;
                background: white;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                overflow: hidden;
            }
            
            .certificate-header {
                background: linear-gradient(45deg, #2c3e50, #3498db);
                color: white;
                padding: 30px;
                text-align: center;
            }
            
            .certificate-body {
                padding: 50px;
                text-align: center;
            }
            
            .certificate-title {
                font-size: 2.5rem;
                font-weight: bold;
                color: #2c3e50;
                margin-bottom: 30px;
                text-transform: uppercase;
                letter-spacing: 2px;
            }
            
            .certificate-subtitle {
                font-size: 1.2rem;
                color: #7f8c8d;
                margin-bottom: 40px;
            }
            
            .recipient-name {
                font-size: 2rem;
                color: #2980b9;
                font-weight: bold;
                margin: 30px 0;
                padding: 20px;
                border: 3px solid #3498db;
                border-radius: 10px;
                display: inline-block;
            }
            
            .certificate-content {
                font-size: 1.1rem;
                line-height: 1.8;
                color: #34495e;
                margin: 30px 0;
            }
            
            .certificate-details {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin: 40px 0;
            }
            
            .detail-item {
                background: #ecf0f1;
                padding: 15px;
                border-radius: 8px;
                text-align: center;
            }
            
            .detail-label {
                font-weight: bold;
                color: #2c3e50;
                font-size: 0.9rem;
                text-transform: uppercase;
            }
            
            .detail-value {
                color: #3498db;
                font-size: 1.1rem;
                font-weight: bold;
                margin-top: 5px;
            }
            
            .validation-section {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 10px;
                margin: 30px 0;
            }
            
            .download-buttons {
                margin: 30px 0;
            }
            
            .btn-certificate {
                background: linear-gradient(45deg, #3498db, #2980b9);
                border: none;
                color: white;
                padding: 12px 30px;
                border-radius: 25px;
                font-size: 1.1rem;
                margin: 10px;
                transition: all 0.3s ease;
            }
            
            .btn-certificate:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
                color: white;
            }
            
            @media print {
                .download-buttons, .validation-section { display: none; }
                body { background: white; }
                .certificate-container { box-shadow: none; }
            }
        </style>
    </head>
    <body>
        <div class="certificate-container">
            <div class="certificate-header">
                <h1><i class="fas fa-shield-alt"></i> Cybersecurity Training Certificate</h1>
                <p>This certificate validates the completion of security awareness training</p>
            </div>
            
            <div class="certificate-body">
                <?php echo $html_content; ?>
                
                <div class="validation-section">
                    <h5><i class="fas fa-certificate"></i> Certificate Validation</h5>
                    <p><strong>Validation Code:</strong> <code><?php echo $certificate['validation_code']; ?></code></p>
                    <p><strong>Certificate ID:</strong> <code><?php echo $certificate['certificate_id']; ?></code></p>
                    <p>Verify this certificate at: <a href="certificate_view.php?action=validate&cert_id=<?php echo $certificate['certificate_id']; ?>">Validation Link</a></p>
                </div>
                
                <div class="download-buttons">
                    <a href="certificate_view.php?cert_id=<?php echo $certificate['certificate_id']; ?>&action=download" 
                       class="btn btn-certificate">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-certificate">
                        <i class="fas fa-print"></i> Print Certificate
                    </button>
                </div>
            </div>
        </div>
        
        <script src="js/libs/jquery.min.js"></script>
        <script src="js/libs/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    </body>
    </html>
    <?php
}

/**
 * Generate and download PDF certificate
 */
function generateCertificatePDF($certificate) {
    // Check if TCPDF is available
    if (file_exists(__DIR__ . '/libs/tcpdf/tcpdf.php')) {
        require_once(__DIR__ . '/libs/tcpdf/tcpdf.php');
        generateTCPDFCertificate($certificate);
    } else {
        // Fallback to HTML to PDF conversion or simple HTML download
        generateHTMLCertificate($certificate);
    }
}

/**
 * Generate PDF using TCPDF
 */
function generateTCPDFCertificate($certificate) {
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('LoophishX Training System');
    $pdf->SetAuthor('LoophishX');
    $pdf->SetTitle('Security Training Certificate');
    $pdf->SetSubject('Cybersecurity Training Completion Certificate');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Add a page
    $pdf->AddPage();
    
    // Get template content
    $template_file = getTemplate($certificate['certificate_template'] ?? 'default');
    $html_content = processTemplate($template_file, $certificate);
    
    // Write HTML content
    $pdf->writeHTML($html_content, true, false, true, false, '');
    
    // Output PDF
    $filename = 'certificate_' . $certificate['certificate_id'] . '.pdf';
    $pdf->Output($filename, 'D');
}

/**
 * Generate HTML certificate for download
 */
function generateHTMLCertificate($certificate) {
    $template_file = getTemplate($certificate['certificate_template'] ?? 'default');
    $html_content = processTemplate($template_file, $certificate);
    
    $filename = 'certificate_' . $certificate['certificate_id'] . '.html';
    
    header('Content-Type: text/html');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Certificate</title>';
    echo '<style>' . getCertificateCSS() . '</style></head><body>';
    echo $html_content;
    echo '</body></html>';
}

/**
 * Get certificate template
 */
function getTemplate($template_name = 'default') {
    $template_path = __DIR__ . '/templates/certificates/' . $template_name . '.html';
    
    if (file_exists($template_path)) {
        return file_get_contents($template_path);
    }
    
    // Return default template
    return getDefaultTemplate();
}

/**
 * Default certificate template
 */
function getDefaultTemplate() {
    return '
    <div class="certificate-title">Certificate of Completion</div>
    <div class="certificate-subtitle">Cybersecurity Awareness Training</div>
    
    <p class="certificate-content">This is to certify that</p>
    
    <div class="recipient-name">{user_name}</div>
    
    <p class="certificate-content">
        has successfully completed the <strong>{module_name}</strong> training module
        and demonstrated proficiency in cybersecurity awareness with a score of <strong>{score_achieved}%</strong>.
    </p>
    
    <div class="certificate-details">
        <div class="detail-item">
            <div class="detail-label">Module</div>
            <div class="detail-value">{module_name}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Score</div>
            <div class="detail-value">{score_achieved}%</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Completion Date</div>
            <div class="detail-value">{completion_date}</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Certificate ID</div>
            <div class="detail-value">{certificate_id}</div>
        </div>
    </div>
    
    <p class="certificate-content">
        This certificate validates the holder\'s understanding of cybersecurity principles
        and their commitment to maintaining secure practices in the digital workplace.
    </p>
    ';
}

/**
 * Process template with variable replacement
 */
function processTemplate($template, $certificate) {
    $variables = [
        '{user_name}' => $certificate['user_name'],
        '{module_name}' => $certificate['module_name'],
        '{module_description}' => $certificate['module_description'] ?? '',
        '{score_achieved}' => $certificate['score_achieved'],
        '{completion_date}' => formatDate($certificate['completion_date']),
        '{issued_date}' => formatDate($certificate['issued_date']),
        '{certificate_id}' => $certificate['certificate_id'],
        '{validation_code}' => $certificate['validation_code'],
        '{category}' => $certificate['category'] ?? 'Security Awareness',
        '{time_spent}' => formatDuration($certificate['time_spent'] ?? 0),
        '{current_date}' => date('F j, Y'),
        '{current_year}' => date('Y')
    ];
    
    return str_replace(array_keys($variables), array_values($variables), $template);
}

/**
 * Format date for display
 */
function formatDate($date_string) {
    if (empty($date_string)) return '';
    
    try {
        $date = new DateTime($date_string);
        return $date->format('F j, Y');
    } catch (Exception $e) {
        return $date_string;
    }
}

/**
 * Format duration in seconds to readable format
 */
function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . ' seconds';
    } elseif ($seconds < 3600) {
        return floor($seconds / 60) . ' minutes';
    } else {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return $hours . 'h ' . $minutes . 'm';
    }
}

/**
 * Get certificate CSS styles
 */
function getCertificateCSS() {
    return '
        body { font-family: Georgia, serif; margin: 0; padding: 20px; }
        .certificate-title { font-size: 2.5rem; font-weight: bold; text-align: center; margin: 20px 0; }
        .certificate-subtitle { font-size: 1.2rem; text-align: center; margin: 10px 0; }
        .recipient-name { font-size: 2rem; font-weight: bold; text-align: center; margin: 30px 0; padding: 20px; border: 3px solid #3498db; }
        .certificate-content { font-size: 1.1rem; text-align: center; margin: 20px 0; }
        .certificate-details { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: 30px 0; }
        .detail-item { text-align: center; padding: 15px; background: #f8f9fa; }
        .detail-label { font-weight: bold; font-size: 0.9rem; }
        .detail-value { font-size: 1.1rem; font-weight: bold; margin-top: 5px; }
    ';
}

/**
 * Validate certificate and show validation page
 */
function validateCertificate($certificate) {
    $is_valid = ($certificate['status'] == 1);
    $expires = !empty($certificate['expires_date']) ? new DateTime($certificate['expires_date']) : null;
    
    if ($expires && $expires < new DateTime()) {
        $is_valid = false;
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Certificate Validation</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; padding: 20px; }
            .validation-container { max-width: 600px; margin: 0 auto; background: white; border-radius: 15px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
            .validation-result { text-align: center; }
            .status-valid { color: #27ae60; }
            .status-invalid { color: #e74c3c; }
        </style>
    </head>
    <body>
        <div class="validation-container">
            <div class="validation-result">
                <h2><i class="fas fa-certificate"></i> Certificate Validation</h2>
                
                <?php if ($is_valid): ?>
                    <div class="alert alert-success">
                        <h4 class="status-valid"><i class="fas fa-check-circle"></i> VALID CERTIFICATE</h4>
                        <p>This certificate is authentic and valid.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger">
                        <h4 class="status-invalid"><i class="fas fa-times-circle"></i> INVALID/EXPIRED CERTIFICATE</h4>
                        <p>This certificate is not valid or has expired.</p>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <div class="certificate-info">
                    <p><strong>Holder:</strong> <?php echo htmlspecialchars($certificate['user_name']); ?></p>
                    <p><strong>Training Module:</strong> <?php echo htmlspecialchars($certificate['module_name']); ?></p>
                    <p><strong>Score:</strong> <?php echo $certificate['score_achieved']; ?>%</p>
                    <p><strong>Issued:</strong> <?php echo formatDate($certificate['issued_date']); ?></p>
                    <?php if (!empty($certificate['expires_date'])): ?>
                        <p><strong>Expires:</strong> <?php echo formatDate($certificate['expires_date']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mt-4">
                    <a href="certificate_view.php?cert_id=<?php echo $certificate['certificate_id']; ?>" 
                       class="btn btn-primary">View Full Certificate</a>
                </div>
            </div>
        </div>
        
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    </body>
    </html>
    <?php
}
?>
<?php
/**
 * Training Completion Page
 * Shows completion status and next steps after finishing a training module
 */

session_start();
require_once 'manager/common_functions.php';
require_once 'manager/training_manager.php';

// Get parameters
$user_id = $_GET['user_id'] ?? '';
$module_id = $_GET['module_id'] ?? '';

// Validate required parameters
if (empty($user_id) || empty($module_id)) {
    die('Missing required parameters');
}

// Get module and completion details
$training_manager = new TrainingManager();
$module = $training_manager->getTrainingModuleById($module_id);
$completion_data = getTrainingCompletion($user_id, $module_id);

if (!$module) {
    die('Training module not found');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Completed - <?php echo htmlspecialchars($module['module_name']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/loophish-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .completion-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            margin: 30px auto;
            max-width: 800px;
        }
        
        .completion-header {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .completion-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: bounceIn 1s ease-out;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .completion-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        
        .completion-content {
            padding: 40px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #4CAF50;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #4CAF50;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        
        .achievement-section {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
            text-align: center;
        }
        
        .certificate-preview {
            border: 3px solid #4CAF50;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            background: #f8fff8;
        }
        
        .next-steps {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 20px;
            border-radius: 0 10px 10px 0;
            margin: 30px 0;
        }
        
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary-custom {
            background: linear-gradient(45deg, #2196F3, #21CBF3);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover, .btn-secondary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
            text-decoration: none;
        }
        
        .security-tips {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 30px 0;
        }
        
        .tip-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .tip-icon {
            color: #f39c12;
            margin-right: 15px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="completion-container">
            <!-- Header -->
            <div class="completion-header">
                <div class="completion-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h1>Congratulations!</h1>
                <p>You have successfully completed the security training</p>
                <h3><?php echo htmlspecialchars($module['module_name']); ?></h3>
            </div>
            
            <!-- Content -->
            <div class="completion-content">
                
                <!-- Statistics -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo round($completion_data['completion_percentage'] ?? 100); ?>%</div>
                        <div class="stat-label">Completion Rate</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo formatDuration($completion_data['time_watched'] ?? 0); ?></div>
                        <div class="stat-label">Time Invested</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $completion_data['points_earned'] ?? 100; ?></div>
                        <div class="stat-label">Points Earned</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo date('M j, Y'); ?></div>
                        <div class="stat-label">Completed On</div>
                    </div>
                </div>
                
                <!-- Achievement Badge -->
                <?php if ($completion_data['certificate_id']): ?>
                <div class="achievement-section">
                    <i class="fas fa-medal fa-2x mb-3"></i>
                    <h4>🏆 Achievement Unlocked!</h4>
                    <p>You've earned a digital certificate for completing this training module.</p>
                    <div class="certificate-preview">
                        <h5>Security Training Certificate</h5>
                        <p><strong>Module:</strong> <?php echo htmlspecialchars($module['module_name']); ?></p>
                        <p><strong>Certificate ID:</strong> <?php echo htmlspecialchars($completion_data['certificate_number'] ?? 'CERT-' . strtoupper(uniqid())); ?></p>
                        <p><strong>Issued:</strong> <?php echo date('F j, Y'); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Security Tips Reminder -->
                <div class="security-tips">
                    <h4><i class="fas fa-shield-alt"></i> Remember These Key Points:</h4>
                    
                    <div class="tip-item">
                        <i class="fas fa-check-circle tip-icon"></i>
                        <span>Always verify the sender's identity before clicking on links in emails</span>
                    </div>
                    
                    <div class="tip-item">
                        <i class="fas fa-check-circle tip-icon"></i>
                        <span>Look for HTTPS and check the URL carefully for any suspicious characters</span>
                    </div>
                    
                    <div class="tip-item">
                        <i class="fas fa-check-circle tip-icon"></i>
                        <span>Be wary of urgent or threatening language designed to pressure you</span>
                    </div>
                    
                    <div class="tip-item">
                        <i class="fas fa-check-circle tip-icon"></i>
                        <span>When in doubt, contact your IT department or security team</span>
                    </div>
                    
                    <div class="tip-item">
                        <i class="fas fa-check-circle tip-icon"></i>
                        <span>Keep your software and security tools up to date</span>
                    </div>
                </div>
                
                <!-- Next Steps -->
                <div class="next-steps">
                    <h4><i class="fas fa-arrow-right"></i> What's Next?</h4>
                    <p>Continue building your security awareness with these recommended actions:</p>
                    <ul>
                        <li>Share what you learned with your colleagues</li>
                        <li>Apply these principles in your daily work</li>
                        <li>Stay alert for real phishing attempts</li>
                        <li>Complete additional training modules if available</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if ($completion_data['certificate_id']): ?>
                    <a href="certificate_view.php?cert_id=<?php echo $completion_data['certificate_id']; ?>" 
                       class="btn-primary-custom" target="_blank">
                        <i class="fas fa-download"></i> Download Certificate
                    </a>
                    <?php endif; ?>
                    
                    <a href="TrainingManagement.php" class="btn-secondary-custom">
                        <i class="fas fa-graduation-cap"></i> More Training
                    </a>
                    
                    <a href="Home.php" class="btn-secondary-custom">
                        <i class="fas fa-home"></i> Back to Dashboard
                    </a>
                </div>
                
                <!-- Feedback Section -->
                <div class="mt-4 text-center">
                    <h5>Help Us Improve</h5>
                    <p>How would you rate this training module?</p>
                    <div class="rating-buttons" id="rating-section">
                        <button class="btn btn-outline-warning" onclick="submitRating(1)">⭐</button>
                        <button class="btn btn-outline-warning" onclick="submitRating(2)">⭐⭐</button>
                        <button class="btn btn-outline-warning" onclick="submitRating(3)">⭐⭐⭐</button>
                        <button class="btn btn-outline-warning" onclick="submitRating(4)">⭐⭐⭐⭐</button>
                        <button class="btn btn-outline-warning" onclick="submitRating(5)">⭐⭐⭐⭐⭐</button>
                    </div>
                    <div id="rating-thanks" style="display: none;" class="mt-3">
                        <p class="text-success">Thank you for your feedback!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="js/libs/jquery.min.js"></script>
    <script src="js/libs/bootstrap.min.js"></script>
    
    <script>
        // Configuration
        const config = {
            userId: <?php echo json_encode($user_id); ?>,
            moduleId: <?php echo json_encode($module_id); ?>
        };
        
        // Rating submission
        function submitRating(rating) {
            $.post('manager/training_integration_manager.php', {
                action_type: 'submit_rating',
                user_id: config.userId,
                module_id: config.moduleId,
                rating: rating
            }, function(response) {
                $('#rating-section').hide();
                $('#rating-thanks').show();
            }, 'json').fail(function() {
                console.log('Failed to submit rating');
            });
        }
        
        // Auto-redirect after 2 minutes if no interaction
        let redirectTimer = setTimeout(function() {
            if (confirm('Would you like to return to the dashboard?')) {
                window.location.href = 'Home.php';
            }
        }, 120000); // 2 minutes
        
        // Clear timer on any user interaction
        $(document).on('click mousemove keypress', function() {
            clearTimeout(redirectTimer);
        });
        
        // Track completion page view
        $.post('manager/training_integration_manager.php', {
            action_type: 'log_completion_view',
            user_id: config.userId,
            module_id: config.moduleId
        }, function(response) {
            console.log('Completion view logged');
        }, 'json');
    </script>
</body>
</html>

<?php
/**
 * Helper Functions
 */

function getTrainingCompletion($user_id, $module_id) {
    $db = getDatabaseConnection();
    
    try {
        $sql = "SELECT tp.*, tc.certificate_id, tc.certificate_number, tm.points_value
                FROM tb_training_progress tp
                LEFT JOIN tb_training_certificates tc ON tp.user_id = tc.user_id AND tp.module_id = tc.module_id
                LEFT JOIN tb_training_modules tm ON tp.module_id = tm.module_id
                WHERE tp.user_id = ? AND tp.module_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("ii", $user_id, $module_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if ($data) {
            $data['points_earned'] = $data['points_value'] ?? 100;
        }
        
        return $data;
        
    } catch (Exception $e) {
        error_log("Error getting training completion: " . $e->getMessage());
        return array();
    } finally {
        $db->close();
    }
}

function formatDuration($seconds) {
    if ($seconds < 60) {
        return $seconds . 's';
    } elseif ($seconds < 3600) {
        return floor($seconds / 60) . 'm ' . ($seconds % 60) . 's';
    } else {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        return $hours . 'h ' . $minutes . 'm';
    }
}
?>
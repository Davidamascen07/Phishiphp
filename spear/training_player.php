<?php
/**
 * Training Player
 * Displays training videos with progress tracking for phishing campaign integration
 */

session_start();
require_once 'manager/common_functions.php';
require_once 'manager/training_manager.php';

// Get parameters
$video_url = $_GET['video'] ?? '';
$user_id = $_GET['user_id'] ?? '';
$module_id = $_GET['module_id'] ?? '';
$tracker_id = $_GET['tracker_id'] ?? '';
$trigger_type = $_GET['trigger_type'] ?? 'immediate';

// Validate required parameters
if (empty($video_url) || empty($user_id) || empty($module_id)) {
    die('Missing required parameters');
}

// Get module details
$training_manager = new TrainingManager();
$module = $training_manager->getTrainingModuleById($module_id);

if (!$module) {
    die('Training module not found');
}

// Log training start
logTrainingStart($user_id, $module_id, $tracker_id, $trigger_type);

// Detect video type and format URL
$video_type = detectVideoType($video_url);
$embed_url = formatVideoURL($video_url, $video_type);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Training - <?php echo htmlspecialchars($module['module_name']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/loophish-theme.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .training-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            margin: 30px auto;
            max-width: 1000px;
        }
        
        .training-header {
            background: linear-gradient(45deg, #2196F3, #21CBF3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .training-header h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 300;
        }
        
        .training-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .video-container {
            position: relative;
            width: 100%;
            height: 500px;
            background: #000;
        }
        
        .video-player {
            width: 100%;
            height: 100%;
        }
        
        .training-content {
            padding: 30px;
        }
        
        .progress-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .progress-bar-custom {
            height: 10px;
            border-radius: 5px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(45deg, #4CAF50, #45a049);
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .completion-message {
            display: none;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        
        .btn-primary-custom {
            background: linear-gradient(45deg, #2196F3, #21CBF3);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-size: 1.1rem;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .warning-banner {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .warning-banner .icon {
            color: #f39c12;
            margin-right: 10px;
        }
        
        .timer-display {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="training-container">
            <!-- Header -->
            <div class="training-header">
                <h1><?php echo htmlspecialchars($module['module_name']); ?></h1>
                <p><?php echo htmlspecialchars($module['module_description']); ?></p>
            </div>
            
            <!-- Warning Banner -->
            <div class="warning-banner">
                <i class="fas fa-exclamation-triangle icon"></i>
                <strong>Important:</strong> You clicked on a simulated phishing link. This training will help you identify and avoid such threats in the future.
            </div>
            
            <!-- Video Container -->
            <div class="video-container">
                <div class="timer-display" id="timer">00:00</div>
                
                <?php if ($video_type === 'youtube'): ?>
                    <iframe class="video-player" 
                            id="video-player"
                            src="<?php echo $embed_url; ?>" 
                            frameborder="0" 
                            allowfullscreen
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                    </iframe>
                <?php elseif ($video_type === 'vimeo'): ?>
                    <iframe class="video-player" 
                            id="video-player"
                            src="<?php echo $embed_url; ?>" 
                            frameborder="0" 
                            allowfullscreen>
                    </iframe>
                <?php else: ?>
                    <video class="video-player" 
                           id="video-player" 
                           controls 
                           preload="metadata">
                        <source src="<?php echo htmlspecialchars($video_url); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            
            <!-- Training Content -->
            <div class="training-content">
                <!-- Progress Section -->
                <div class="progress-section">
                    <h4>Training Progress</h4>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <p class="mt-2 mb-0">
                        <span id="progress-text">0%</span> completed • 
                        <span id="time-watched">0:00</span> watched
                    </p>
                </div>
                
                <!-- Completion Message -->
                <div class="completion-message" id="completion-message">
                    <h4>✅ Training Completed!</h4>
                    <p>Congratulations! You have successfully completed this security training module. Remember these key points to stay safe from phishing attacks.</p>
                </div>
                
                <!-- Key Learning Points -->
                <div class="mt-4">
                    <h4>Key Learning Points:</h4>
                    <ul class="list-unstyled">
                        <li>✓ Always verify sender identity before clicking links</li>
                        <li>✓ Check URL legitimacy and look for HTTPS encryption</li>
                        <li>✓ Be suspicious of urgent or threatening language</li>
                        <li>✓ When in doubt, contact IT support</li>
                    </ul>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <?php if ($module['quiz_enabled']): ?>
                    <a href="training_quiz.php?module_id=<?php echo $module_id; ?>&user_id=<?php echo $user_id; ?>&tracker_id=<?php echo $tracker_id; ?>&video_completed=1" 
                       class="btn-primary-custom" id="quiz-btn" style="display: none;">
                        Take Knowledge Quiz
                    </a>
                    <?php endif; ?>
                    
                    <a href="#" class="btn-primary-custom" id="continue-btn" style="display: none;">
                        Continue to Next Module
                    </a>
                    <a href="#" class="btn-primary-custom" id="complete-btn" style="display: none;">
                        Complete Training
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="js/libs/jquery.min.js"></script>
    <script src="js/libs/bootstrap.min.js"></script>
    
    <script>
        let startTime = Date.now();
        let videoWatched = 0;
        let totalDuration = 0;
        let progressTimer;
        let isCompleted = false;
        
        // Training configuration
        const config = {
            userId: <?php echo json_encode($user_id); ?>,
            moduleId: <?php echo json_encode($module_id); ?>,
            trackerId: <?php echo json_encode($tracker_id); ?>,
            videoType: <?php echo json_encode($video_type); ?>,
            minCompletionPercentage: 80,
            hasQuiz: <?php echo json_encode($module['quiz_enabled'] == 1); ?>
        };
        
        // Initialize training
        $(document).ready(function() {
            initializeTraining();
            startProgressTracking();
        });
        
        function initializeTraining() {
            const player = document.getElementById('video-player');
            
            if (config.videoType === 'html5') {
                // HTML5 video events
                player.addEventListener('loadedmetadata', function() {
                    totalDuration = player.duration;
                });
                
                player.addEventListener('timeupdate', function() {
                    updateProgress(player.currentTime);
                });
                
                player.addEventListener('ended', function() {
                    completeTraining();
                });
                
            } else if (config.videoType === 'youtube') {
                // YouTube API would go here
                // For now, we'll simulate progress
                simulateVideoProgress();
                
            } else if (config.videoType === 'vimeo') {
                // Vimeo API would go here
                // For now, we'll simulate progress
                simulateVideoProgress();
            }
        }
        
        function simulateVideoProgress() {
            // Simulate 5-minute video for demo purposes
            totalDuration = 300; // 5 minutes
            let currentTime = 0;
            
            const interval = setInterval(function() {
                if (currentTime < totalDuration && !isCompleted) {
                    currentTime += 1;
                    updateProgress(currentTime);
                } else {
                    clearInterval(interval);
                    if (!isCompleted) {
                        completeTraining();
                    }
                }
            }, 1000);
        }
        
        function updateProgress(currentTime) {
            videoWatched = currentTime;
            const percentage = totalDuration > 0 ? (currentTime / totalDuration) * 100 : 0;
            
            // Update progress bar
            $('#progress-fill').css('width', percentage + '%');
            $('#progress-text').text(Math.round(percentage) + '%');
            
            // Update time display
            $('#time-watched').text(formatTime(currentTime));
            $('#timer').text(formatTime(currentTime));
            
            // Check for completion
            if (percentage >= config.minCompletionPercentage && !isCompleted) {
                showCompletionOptions();
            }
            
            // Send progress update every 30 seconds
            if (Math.floor(currentTime) % 30 === 0) {
                sendProgressUpdate(percentage);
            }
        }
        
        function showCompletionOptions() {
            $('#completion-message').slideDown();
            
            if (config.hasQuiz) {
                $('#quiz-btn').show();
            } else {
                $('#complete-btn').show();
            }
            
            // Check if there are more modules
            checkForNextModule();
        }
        
        function completeTraining() {
            if (isCompleted) return;
            
            isCompleted = true;
            const finalPercentage = totalDuration > 0 ? (videoWatched / totalDuration) * 100 : 100;
            
            // Final progress update
            sendProgressUpdate(100, true);
            
            // Show completion message
            showCompletionOptions();
            
            // Log completion
            console.log('Training completed:', {
                userId: config.userId,
                moduleId: config.moduleId,
                percentage: finalPercentage,
                timeWatched: videoWatched
            });
        }
        
        function checkForNextModule() {
            // Check if there's a next module in the sequence
            $.post('manager/training_integration_manager.php', {
                action_type: 'get_next_module',
                current_module_id: config.moduleId,
                user_id: config.userId
            }, function(response) {
                if (response.result === 'success' && response.next_module) {
                    $('#continue-btn').show().attr('href', 
                        'training_player.php?video=' + encodeURIComponent(response.next_module.video_url) + 
                        '&user_id=' + config.userId + 
                        '&module_id=' + response.next_module.module_id
                    );
                }
            }, 'json');
        }
        
        function sendProgressUpdate(percentage, isComplete = false) {
            $.post('manager/training_integration_manager.php', {
                action_type: 'update_training_progress',
                user_id: config.userId,
                module_id: config.moduleId,
                tracker_id: config.trackerId,
                percentage: percentage,
                time_watched: videoWatched,
                is_completed: isComplete
            }, function(response) {
                console.log('Progress updated:', response);
            }, 'json').fail(function() {
                console.log('Failed to update progress');
            });
        }
        
        function startProgressTracking() {
            // Track time spent on page
            progressTimer = setInterval(function() {
                const timeSpent = Math.floor((Date.now() - startTime) / 1000);
                // Additional tracking can be added here
            }, 1000);
        }
        
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = Math.floor(seconds % 60);
            return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
        }
        
        // Handle page unload
        $(window).on('beforeunload', function() {
            if (!isCompleted && videoWatched > 0) {
                const percentage = totalDuration > 0 ? (videoWatched / totalDuration) * 100 : 0;
                sendProgressUpdate(percentage);
            }
        });
        
        // Button handlers
        $('#complete-btn').click(function(e) {
            e.preventDefault();
            
            // Send completion and redirect
            $.post('manager/training_integration_manager.php', {
                action_type: 'complete_training',
                user_id: config.userId,
                module_id: config.moduleId,
                tracker_id: config.trackerId
            }, function(response) {
                if (response.result === 'success') {
                    if (response.redirect_url) {
                        window.location.href = response.redirect_url;
                    } else {
                        alert('Training completed successfully!');
                        window.close();
                    }
                }
            }, 'json');
        });
    </script>
</body>
</html>

<?php
/**
 * Helper Functions
 */

function detectVideoType($url) {
    if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        return 'youtube';
    } elseif (strpos($url, 'vimeo.com') !== false) {
        return 'vimeo';
    } else {
        return 'html5';
    }
}

function formatVideoURL($url, $type) {
    switch ($type) {
        case 'youtube':
            // Extract video ID and create embed URL
            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
            return isset($matches[1]) ? 'https://www.youtube.com/embed/' . $matches[1] . '?autoplay=1&rel=0' : $url;
            
        case 'vimeo':
            // Extract video ID and create embed URL
            preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
            return isset($matches[1]) ? 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1' : $url;
            
        default:
            return $url;
    }
}

function logTrainingStart($user_id, $module_id, $tracker_id, $trigger_type) {
    $db = getDatabaseConnection();
    
    try {
        $sql = "INSERT INTO tb_training_progress 
                (user_id, module_id, tracker_id, trigger_type, started_at, status) 
                VALUES (?, ?, ?, ?, NOW(), 'started')
                ON DUPLICATE KEY UPDATE started_at = NOW(), status = 'started'";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("iiss", $user_id, $module_id, $tracker_id, $trigger_type);
        $stmt->execute();
        
    } catch (Exception $e) {
        error_log("Error logging training start: " . $e->getMessage());
    } finally {
        $db->close();
    }
}
?>
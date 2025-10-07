<?php
/**
 * Training Quiz Player
 * Integrated quiz system for training modules with scoring and certification
 */

session_start();
require_once 'manager/common_functions.php';
require_once 'manager/training_manager.php';

// Get parameters
$module_id = $_GET['module_id'] ?? '';
$user_id = $_GET['user_id'] ?? '';
$tracker_id = $_GET['tracker_id'] ?? '';
$video_completed = $_GET['video_completed'] ?? false;

// Validate required parameters
if (empty($module_id) || empty($user_id)) {
    die('Missing required parameters');
}

// Get module and quiz details
$training_manager = new TrainingManager();
$module = $training_manager->getTrainingModuleById($module_id);

if (!$module || !$module['quiz_enabled']) {
    die('Quiz not available for this module');
}

// Get quiz questions
$quiz_questions = getQuizQuestions($module_id);

if (empty($quiz_questions)) {
    die('No quiz questions found');
}

// Check if user already completed this quiz
$previous_attempt = getPreviousQuizAttempt($user_id, $module_id);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Quiz - <?php echo htmlspecialchars($module['module_name']); ?></title>
    
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
        
        .quiz-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            margin: 30px auto;
            max-width: 900px;
        }
        
        .quiz-header {
            background: linear-gradient(45deg, #2196F3, #21CBF3);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .quiz-progress {
            background: rgba(255,255,255,0.2);
            height: 8px;
            border-radius: 4px;
            margin: 20px 0 10px 0;
            overflow: hidden;
        }
        
        .quiz-progress-fill {
            background: white;
            height: 100%;
            width: 0%;
            transition: width 0.3s ease;
        }
        
        .quiz-content {
            padding: 40px;
        }
        
        .question-container {
            display: none;
            animation: fadeIn 0.5s ease-in;
        }
        
        .question-container.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .question-header {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #2196F3;
        }
        
        .question-number {
            color: #2196F3;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .question-text {
            font-size: 1.2rem;
            line-height: 1.6;
            margin: 0;
        }
        
        .answer-options {
            margin: 25px 0;
        }
        
        .answer-option {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin: 15px 0;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .answer-option:hover {
            border-color: #2196F3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.2);
        }
        
        .answer-option.selected {
            border-color: #2196F3;
            background: #e3f2fd;
        }
        
        .answer-option.correct {
            border-color: #4CAF50;
            background: #e8f5e8;
        }
        
        .answer-option.incorrect {
            border-color: #f44336;
            background: #ffebee;
        }
        
        .answer-option .option-letter {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #2196F3;
            color: white;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 15px;
        }
        
        .answer-option.correct .option-letter {
            background: #4CAF50;
        }
        
        .answer-option.incorrect .option-letter {
            background: #f44336;
        }
        
        .question-explanation {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            display: none;
        }
        
        .question-explanation.show {
            display: block;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; max-height: 0; }
            to { opacity: 1; max-height: 200px; }
        }
        
        .quiz-navigation {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
            padding: 20px 0;
            border-top: 1px solid #e9ecef;
        }
        
        .btn-quiz {
            padding: 12px 25px;
            border-radius: 25px;
            border: none;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary-quiz {
            background: linear-gradient(45deg, #2196F3, #21CBF3);
            color: white;
        }
        
        .btn-secondary-quiz {
            background: #6c757d;
            color: white;
        }
        
        .btn-success-quiz {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            color: white;
        }
        
        .btn-quiz:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-quiz:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .quiz-timer {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 1.1rem;
            font-weight: bold;
        }
        
        .quiz-timer.warning {
            background: rgba(255, 152, 0, 0.9);
            animation: pulse 1s infinite;
        }
        
        .quiz-timer.danger {
            background: rgba(244, 67, 54, 0.9);
            animation: pulse 0.5s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .quiz-summary {
            display: none;
            text-align: center;
            padding: 40px;
        }
        
        .score-circle {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px auto;
            font-size: 2.5rem;
            font-weight: bold;
            color: white;
        }
        
        .score-excellent {
            background: linear-gradient(45deg, #4CAF50, #45a049);
        }
        
        .score-good {
            background: linear-gradient(45deg, #2196F3, #21CBF3);
        }
        
        .score-fair {
            background: linear-gradient(45deg, #FF9800, #F57C00);
        }
        
        .score-poor {
            background: linear-gradient(45deg, #f44336, #d32f2f);
        }
        
        .quiz-results {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .result-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .result-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2196F3;
        }
        
        .certificate-earned {
            background: linear-gradient(45deg, #FFD700, #FFA500);
            color: white;
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Timer -->
        <div class="quiz-timer" id="quiz-timer">⏱️ <span id="timer-display">10:00</span></div>
        
        <div class="quiz-container">
            <!-- Header -->
            <div class="quiz-header">
                <h1>Security Knowledge Quiz</h1>
                <p><?php echo htmlspecialchars($module['module_name']); ?></p>
                <div class="quiz-progress">
                    <div class="quiz-progress-fill" id="quiz-progress"></div>
                </div>
                <p id="progress-text">Question 1 of <?php echo count($quiz_questions); ?></p>
            </div>
            
            <!-- Quiz Content -->
            <div class="quiz-content">
                
                <!-- Previous Attempt Warning -->
                <?php if ($previous_attempt && $previous_attempt['score'] >= 70): ?>
                <div class="alert alert-info">
                    <strong>Note:</strong> You previously completed this quiz with a score of <?php echo $previous_attempt['score']; ?>%. 
                    Taking it again will replace your previous score.
                </div>
                <?php endif; ?>
                
                <!-- Quiz Instructions -->
                <div id="quiz-instructions" class="text-center">
                    <div class="mb-4">
                        <i class="fas fa-clipboard-check fa-3x text-primary mb-3"></i>
                        <h3>Ready to Test Your Knowledge?</h3>
                        <p class="lead">This quiz will test what you learned about cybersecurity and phishing prevention.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="result-card">
                                <div class="result-number"><?php echo count($quiz_questions); ?></div>
                                <div>Questions</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card">
                                <div class="result-number">10</div>
                                <div>Minutes</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="result-card">
                                <div class="result-number">70%</div>
                                <div>Pass Score</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button class="btn-quiz btn-primary-quiz" onclick="startQuiz()">
                            <i class="fas fa-play"></i> Start Quiz
                        </button>
                    </div>
                </div>
                
                <!-- Quiz Questions -->
                <div id="quiz-questions" style="display: none;">
                    <?php foreach ($quiz_questions as $index => $question): ?>
                    <div class="question-container" data-question="<?php echo $index; ?>">
                        <div class="question-header">
                            <div class="question-number">Question <?php echo $index + 1; ?> of <?php echo count($quiz_questions); ?></div>
                            <div class="question-text"><?php echo htmlspecialchars($question['question_text']); ?></div>
                        </div>
                        
                        <div class="answer-options">
                            <?php 
                            $answers = json_decode($question['answer_options'], true);
                            $letters = ['A', 'B', 'C', 'D'];
                            foreach ($answers as $answerIndex => $answer): 
                            ?>
                            <div class="answer-option" onclick="selectAnswer(<?php echo $index; ?>, <?php echo $answerIndex; ?>)">
                                <span class="option-letter"><?php echo $letters[$answerIndex]; ?></span>
                                <?php echo htmlspecialchars($answer); ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="question-explanation">
                            <strong>Explanation:</strong><br>
                            <?php echo htmlspecialchars($question['explanation'] ?? 'No explanation provided.'); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Quiz Summary -->
                <div id="quiz-summary" class="quiz-summary">
                    <div class="score-circle" id="score-circle">
                        <span id="final-score">0%</span>
                    </div>
                    
                    <h2 id="result-title">Quiz Complete!</h2>
                    <p id="result-message" class="lead"></p>
                    
                    <div class="quiz-results">
                        <div class="result-card">
                            <div class="result-number" id="correct-answers">0</div>
                            <div>Correct Answers</div>
                        </div>
                        <div class="result-card">
                            <div class="result-number" id="total-questions"><?php echo count($quiz_questions); ?></div>
                            <div>Total Questions</div>
                        </div>
                        <div class="result-card">
                            <div class="result-number" id="time-taken">0:00</div>
                            <div>Time Taken</div>
                        </div>
                        <div class="result-card">
                            <div class="result-number" id="points-earned">0</div>
                            <div>Points Earned</div>
                        </div>
                    </div>
                    
                    <div id="certificate-section" style="display: none;">
                        <div class="certificate-earned">
                            <h4>🏆 Congratulations!</h4>
                            <p>You've earned a certificate for completing this training with a passing score!</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button class="btn-quiz btn-success-quiz" onclick="completeTraining()">
                            <i class="fas fa-check"></i> Complete Training
                        </button>
                        <button class="btn-quiz btn-secondary-quiz" onclick="retakeQuiz()" style="margin-left: 10px;">
                            <i class="fas fa-redo"></i> Retake Quiz
                        </button>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="quiz-navigation" id="quiz-navigation" style="display: none;">
                    <button class="btn-quiz btn-secondary-quiz" id="prev-btn" onclick="previousQuestion()" disabled>
                        <i class="fas fa-chevron-left"></i> Previous
                    </button>
                    
                    <div>
                        <button class="btn-quiz btn-primary-quiz" id="next-btn" onclick="nextQuestion()" disabled>
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                        <button class="btn-quiz btn-success-quiz" id="submit-btn" onclick="submitQuiz()" style="display: none;">
                            <i class="fas fa-check"></i> Submit Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="js/libs/jquery.min.js"></script>
    <script src="js/libs/bootstrap.min.js"></script>
    
    <script>
        // Quiz configuration
        const quizData = <?php echo json_encode($quiz_questions); ?>;
        const config = {
            userId: <?php echo json_encode($user_id); ?>,
            moduleId: <?php echo json_encode($module_id); ?>,
            trackerId: <?php echo json_encode($tracker_id); ?>,
            timeLimit: 10 * 60, // 10 minutes in seconds
            passingScore: 70
        };
        
        // Quiz state
        let currentQuestion = 0;
        let answers = {};
        let startTime = null;
        let timerInterval = null;
        let timeRemaining = config.timeLimit;
        
        // Start quiz
        function startQuiz() {
            $('#quiz-instructions').hide();
            $('#quiz-questions').show();
            $('#quiz-navigation').show();
            
            startTime = Date.now();
            startTimer();
            showQuestion(0);
            
            // Log quiz start
            logQuizEvent('quiz_started');
        }
        
        // Timer functions
        function startTimer() {
            timerInterval = setInterval(function() {
                timeRemaining--;
                updateTimerDisplay();
                
                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    submitQuiz(true); // Auto-submit when time runs out
                }
            }, 1000);
        }
        
        function updateTimerDisplay() {
            const minutes = Math.floor(timeRemaining / 60);
            const seconds = timeRemaining % 60;
            const display = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            
            $('#timer-display').text(display);
            
            const timer = $('#quiz-timer');
            if (timeRemaining <= 60) {
                timer.addClass('danger');
            } else if (timeRemaining <= 300) {
                timer.addClass('warning');
            }
        }
        
        // Question navigation
        function showQuestion(questionIndex) {
            $('.question-container').removeClass('active');
            $(`[data-question="${questionIndex}"]`).addClass('active');
            
            currentQuestion = questionIndex;
            updateProgress();
            updateNavigation();
        }
        
        function nextQuestion() {
            if (currentQuestion < quizData.length - 1) {
                showQuestion(currentQuestion + 1);
            }
        }
        
        function previousQuestion() {
            if (currentQuestion > 0) {
                showQuestion(currentQuestion - 1);
            }
        }
        
        function updateProgress() {
            const progress = ((currentQuestion + 1) / quizData.length) * 100;
            $('#quiz-progress').css('width', progress + '%');
            $('#progress-text').text(`Question ${currentQuestion + 1} of ${quizData.length}`);
        }
        
        function updateNavigation() {
            $('#prev-btn').prop('disabled', currentQuestion === 0);
            
            const isLastQuestion = currentQuestion === quizData.length - 1;
            const hasAnswer = answers.hasOwnProperty(currentQuestion);
            
            if (isLastQuestion) {
                $('#next-btn').hide();
                $('#submit-btn').show().prop('disabled', !hasAnswer);
            } else {
                $('#next-btn').show().prop('disabled', !hasAnswer);
                $('#submit-btn').hide();
            }
        }
        
        // Answer selection
        function selectAnswer(questionIndex, answerIndex) {
            // Clear previous selections
            $(`[data-question="${questionIndex}"] .answer-option`).removeClass('selected');
            
            // Select new answer
            $(`[data-question="${questionIndex}"] .answer-option`).eq(answerIndex).addClass('selected');
            
            // Store answer
            answers[questionIndex] = answerIndex;
            
            // Update navigation
            updateNavigation();
            
            // Log answer selection
            logQuizEvent('answer_selected', {
                question_index: questionIndex,
                answer_index: answerIndex,
                time_to_answer: Date.now() - (startTime + (questionIndex * 30000)) // Rough estimate
            });
        }
        
        // Quiz submission
        function submitQuiz(timeExpired = false) {
            clearInterval(timerInterval);
            
            const timeTaken = config.timeLimit - timeRemaining;
            const results = calculateResults();
            
            // Log quiz completion
            logQuizEvent('quiz_completed', {
                time_taken: timeTaken,
                time_expired: timeExpired,
                score: results.score,
                correct_answers: results.correctAnswers
            });
            
            // Show results with explanations
            showResults(results, timeTaken);
            
            // Submit to server
            submitQuizResults(results, timeTaken);
        }
        
        function calculateResults() {
            let correctAnswers = 0;
            
            // Show correct/incorrect answers
            quizData.forEach((question, index) => {
                const userAnswer = answers[index];
                const correctAnswer = question.correct_answer;
                const isCorrect = userAnswer === correctAnswer;
                
                if (isCorrect) {
                    correctAnswers++;
                }
                
                // Update visual feedback
                const questionContainer = $(`[data-question="${index}"]`);
                questionContainer.find('.answer-option').each(function(optIndex) {
                    const option = $(this);
                    
                    if (optIndex === correctAnswer) {
                        option.addClass('correct');
                    } else if (optIndex === userAnswer && !isCorrect) {
                        option.addClass('incorrect');
                    }
                });
                
                // Show explanation
                questionContainer.find('.question-explanation').addClass('show');
            });
            
            const score = Math.round((correctAnswers / quizData.length) * 100);
            const passed = score >= config.passingScore;
            
            return {
                score: score,
                correctAnswers: correctAnswers,
                totalQuestions: quizData.length,
                passed: passed
            };
        }
        
        function showResults(results, timeTaken) {
            $('#quiz-questions').hide();
            $('#quiz-navigation').hide();
            $('#quiz-summary').show();
            
            // Update score circle
            const scoreCircle = $('#score-circle');
            $('#final-score').text(results.score + '%');
            
            if (results.score >= 90) {
                scoreCircle.addClass('score-excellent');
                $('#result-title').text('Excellent Work!');
                $('#result-message').text('Outstanding performance! You have excellent cybersecurity knowledge.');
            } else if (results.score >= 80) {
                scoreCircle.addClass('score-good');
                $('#result-title').text('Well Done!');
                $('#result-message').text('Great job! You have a solid understanding of cybersecurity principles.');
            } else if (results.score >= 70) {
                scoreCircle.addClass('score-fair');
                $('#result-title').text('Good Effort!');
                $('#result-message').text('You passed! Consider reviewing the material to strengthen your knowledge.');
            } else {
                scoreCircle.addClass('score-poor');
                $('#result-title').text('Need Improvement');
                $('#result-message').text('You didn\'t pass this time. Please review the training material and try again.');
            }
            
            // Update result cards
            $('#correct-answers').text(results.correctAnswers);
            $('#time-taken').text(formatTime(timeTaken));
            $('#points-earned').text(calculatePoints(results.score));
            
            // Show certificate section if passed
            if (results.passed) {
                $('#certificate-section').show();
            }
        }
        
        function submitQuizResults(results, timeTaken) {
            $.post('manager/training_integration_manager.php', {
                action_type: 'submit_quiz_results',
                user_id: config.userId,
                module_id: config.moduleId,
                tracker_id: config.trackerId,
                score: results.score,
                correct_answers: results.correctAnswers,
                total_questions: results.totalQuestions,
                time_taken: timeTaken,
                answers: JSON.stringify(answers),
                passed: results.passed
            }, function(response) {
                console.log('Quiz results submitted:', response);
            }, 'json');
        }
        
        // Utility functions
        function formatTime(seconds) {
            const minutes = Math.floor(seconds / 60);
            const remainingSeconds = seconds % 60;
            return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
        }
        
        function calculatePoints(score) {
            return Math.round(score * 10); // 10 points per percentage point
        }
        
        function logQuizEvent(eventType, data = {}) {
            $.post('manager/training_integration_manager.php', {
                action_type: 'log_quiz_event',
                user_id: config.userId,
                module_id: config.moduleId,
                tracker_id: config.trackerId,
                event_type: eventType,
                event_data: JSON.stringify(data),
                timestamp: Date.now()
            }, function(response) {
                // Silent logging
            }, 'json');
        }
        
        // Button handlers
        function completeTraining() {
            window.location.href = `training_completion.php?user_id=${config.userId}&module_id=${config.moduleId}&quiz_completed=1`;
        }
        
        function retakeQuiz() {
            location.reload();
        }
        
        // Prevent page reload during quiz
        window.addEventListener('beforeunload', function(e) {
            if (startTime && !$('#quiz-summary').is(':visible')) {
                e.preventDefault();
                e.returnValue = '';
                return 'Are you sure you want to leave? Your quiz progress will be lost.';
            }
        });
    </script>
</body>
</html>

<?php
/**
 * Helper Functions
 */

function getQuizQuestions($module_id) {
    $db = getDatabaseConnection();
    
    try {
        $sql = "SELECT * FROM tb_training_questions 
                WHERE module_id = ? AND is_active = 1 
                ORDER BY question_order ASC, question_id ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $module_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $questions = array();
        
        while ($row = $result->fetch_assoc()) {
            $questions[] = $row;
        }
        
        return $questions;
        
    } catch (Exception $e) {
        error_log("Error getting quiz questions: " . $e->getMessage());
        return array();
    } finally {
        $db->close();
    }
}

function getPreviousQuizAttempt($user_id, $module_id) {
    $db = getDatabaseConnection();
    
    try {
        $sql = "SELECT * FROM tb_training_quiz_results 
                WHERE user_id = ? AND module_id = ? 
                ORDER BY completed_at DESC LIMIT 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param("si", $user_id, $module_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        return $result->fetch_assoc();
        
    } catch (Exception $e) {
        error_log("Error getting previous quiz attempt: " . $e->getMessage());
        return null;
    } finally {
        $db->close();
    }
}
?>
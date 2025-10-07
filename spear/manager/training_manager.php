<?php
require_once(dirname(__FILE__) . '/session_manager.php');
if(isSessionValid() == false)
    die("Access denied");

require_once(dirname(__FILE__) . '/common_functions.php');

// Training Management Database Operations

function createTrainingTables($conn) {
    $sql = "
    CREATE TABLE IF NOT EXISTS `tb_training_modules` (
        `module_id` varchar(50) NOT NULL PRIMARY KEY,
        `module_name` varchar(200) NOT NULL,
        `module_description` text DEFAULT NULL,
        `module_type` varchar(50) NOT NULL,
        `content_data` longtext DEFAULT NULL,
        `quiz_data` longtext DEFAULT NULL,
        `quiz_enabled` tinyint(1) DEFAULT 0,
        `passing_score` int(11) DEFAULT 70,
        `estimated_duration` int(11) DEFAULT 15,
        `difficulty_level` varchar(20) DEFAULT 'basic',
        `category` varchar(100) DEFAULT NULL,
        `tags` text DEFAULT NULL,
        `status` tinyint(1) DEFAULT 1,
        `created_by` varchar(50) DEFAULT NULL,
        `created_date` varchar(50) NOT NULL,
        `modified_date` varchar(50) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_assignments` (
        `assignment_id` varchar(50) NOT NULL PRIMARY KEY,
        `module_id` varchar(50) NOT NULL,
        `client_id` varchar(50) DEFAULT NULL,
        `campaign_id` varchar(50) DEFAULT NULL,
        `assigned_users` longtext DEFAULT NULL,
        `assigned_departments` text DEFAULT NULL,
        `assignment_type` varchar(50) DEFAULT 'manual',
        `start_date` varchar(50) DEFAULT NULL,
        `end_date` varchar(50) DEFAULT NULL,
        `auto_assign_new_users` tinyint(1) DEFAULT 0,
        `send_reminders` tinyint(1) DEFAULT 1,
        `reminder_interval` int(11) DEFAULT 7,
        `created_date` varchar(50) NOT NULL,
        FOREIGN KEY (`module_id`) REFERENCES `tb_training_modules`(`module_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_progress` (
        `progress_id` varchar(50) NOT NULL PRIMARY KEY,
        `assignment_id` varchar(50) NOT NULL,
        `user_email` varchar(100) NOT NULL,
        `user_name` varchar(100) DEFAULT NULL,
        `client_id` varchar(50) DEFAULT NULL,
        `module_id` varchar(50) NOT NULL,
        `status` varchar(50) DEFAULT 'not_started',
        `start_time` varchar(50) DEFAULT NULL,
        `completion_time` varchar(50) DEFAULT NULL,
        `last_activity` varchar(50) DEFAULT NULL,
        `progress_percentage` int(11) DEFAULT 0,
        `quiz_score` int(11) DEFAULT NULL,
        `quiz_attempts` int(11) DEFAULT 0,
        `time_spent` int(11) DEFAULT 0,
        `certificate_issued` tinyint(1) DEFAULT 0,
        `certificate_id` varchar(50) DEFAULT NULL,
        `feedback_rating` int(11) DEFAULT NULL,
        `feedback_comments` text DEFAULT NULL,
        FOREIGN KEY (`assignment_id`) REFERENCES `tb_training_assignments`(`assignment_id`) ON DELETE CASCADE,
        FOREIGN KEY (`module_id`) REFERENCES `tb_training_modules`(`module_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_certificates` (
        `certificate_id` varchar(50) NOT NULL PRIMARY KEY,
        `progress_id` varchar(50) NOT NULL,
        `user_email` varchar(100) NOT NULL,
        `user_name` varchar(100) NOT NULL,
        `client_id` varchar(50) DEFAULT NULL,
        `module_id` varchar(50) NOT NULL,
        `module_name` varchar(200) NOT NULL,
        `score_achieved` int(11) NOT NULL,
        `completion_date` varchar(50) NOT NULL,
        `certificate_template` varchar(100) DEFAULT 'default',
        `validation_code` varchar(100) NOT NULL,
        `issued_date` varchar(50) NOT NULL,
        `expires_date` varchar(50) DEFAULT NULL,
        `status` tinyint(1) DEFAULT 1,
        FOREIGN KEY (`progress_id`) REFERENCES `tb_training_progress`(`progress_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_rankings` (
        `ranking_id` varchar(50) NOT NULL PRIMARY KEY,
        `client_id` varchar(50) DEFAULT NULL,
        `user_email` varchar(100) NOT NULL,
        `user_name` varchar(100) NOT NULL,
        `department` varchar(100) DEFAULT NULL,
        `total_score` int(11) DEFAULT 0,
        `modules_completed` int(11) DEFAULT 0,
        `certificates_earned` int(11) DEFAULT 0,
        `average_score` decimal(5,2) DEFAULT 0.00,
        `total_time_spent` int(11) DEFAULT 0,
        `last_activity` varchar(50) DEFAULT NULL,
        `rank_position` int(11) DEFAULT 0,
        `points_earned` int(11) DEFAULT 0,
        `badges_earned` text DEFAULT NULL,
        `updated_date` varchar(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_quiz_results` (
        `result_id` varchar(50) NOT NULL PRIMARY KEY,
        `progress_id` varchar(50) NOT NULL,
        `user_email` varchar(100) NOT NULL,
        `module_id` varchar(50) NOT NULL,
        `attempt_number` int(11) NOT NULL,
        `questions_data` longtext NOT NULL,
        `answers_data` longtext NOT NULL,
        `score` int(11) NOT NULL,
        `total_questions` int(11) NOT NULL,
        `correct_answers` int(11) NOT NULL,
        `time_taken` int(11) DEFAULT 0,
        `submitted_date` varchar(50) NOT NULL,
        FOREIGN KEY (`progress_id`) REFERENCES `tb_training_progress`(`progress_id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    CREATE TABLE IF NOT EXISTS `tb_training_questions` (
        `question_id` varchar(50) NOT NULL PRIMARY KEY,
        `question_text` text NOT NULL,
        `question_type` varchar(50) NOT NULL,
        `category` varchar(100) DEFAULT NULL,
        `difficulty` varchar(20) DEFAULT 'basic',
        `choices` longtext DEFAULT NULL,
        `correct_answer` varchar(255) DEFAULT NULL,
        `supporting_html` longtext DEFAULT NULL,
        `explanation` text DEFAULT NULL,
        `created_by` varchar(50) DEFAULT NULL,
        `created_date` varchar(50) NOT NULL,
        `status` tinyint(1) DEFAULT 1
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ";

    return mysqli_multi_query($conn, $sql);
}

if ($_POST) {
    $action_type = $_POST['action_type'] ?? '';

    switch ($action_type) {
        case 'create_tables':
            if (createTrainingTables($conn)) {
                echo json_encode(['result' => 'success', 'message' => 'Training tables created successfully']);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Failed to create training tables: ' . mysqli_error($conn)]);
            }
            break;

        case 'create_module':
            $module_id = generateRandomId();
            $module_name = $_POST['module_name'] ?? '';
            $module_description = $_POST['module_description'] ?? '';
            $module_type = $_POST['module_type'] ?? 'video'; // video, quiz, interactive, mixed
            $content_data = $_POST['content_data'] ?? '';
            $quiz_data = $_POST['quiz_data'] ?? '';
            $passing_score = intval($_POST['passing_score'] ?? 70);
            $estimated_duration = intval($_POST['estimated_duration'] ?? 15);
            $difficulty_level = $_POST['difficulty_level'] ?? 'basic';
            $category = $_POST['category'] ?? '';
            $tags = $_POST['tags'] ?? '';
            
            if (empty($module_name) || empty($module_type)) {
                echo json_encode(['result' => 'failed', 'error' => 'Module name and type are required']);
                break;
            }

            $stmt = $conn->prepare("INSERT INTO tb_training_modules (module_id, module_name, module_description, module_type, content_data, quiz_data, passing_score, estimated_duration, difficulty_level, category, tags, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $current_date = (new DateTime())->format('d-m-Y h:i A');
            $stmt->bind_param('sssssssissss', $module_id, $module_name, $module_description, $module_type, $content_data, $quiz_data, $passing_score, $estimated_duration, $difficulty_level, $category, $tags, $current_date);
            
            if ($stmt->execute()) {
                echo json_encode(['result' => 'success', 'module_id' => $module_id]);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Failed to create module: ' . $stmt->error]);
            }
            break;

        case 'get_modules':
            $stmt = $conn->prepare("SELECT * FROM tb_training_modules WHERE status = 1 ORDER BY created_date DESC");
            $stmt->execute();
            $result = $stmt->get_result();
            $modules = [];
            
            while ($row = $result->fetch_assoc()) {
                $modules[] = $row;
            }
            
            echo json_encode(['result' => 'success', 'data' => $modules]);
            break;

        case 'get_module':
            $module_id = $_POST['module_id'] ?? '';
            if (empty($module_id)) {
                echo json_encode(['result' => 'failed', 'error' => 'Module ID is required']);
                break;
            }

            $stmt = $conn->prepare("SELECT * FROM tb_training_modules WHERE module_id = ?");
            $stmt->bind_param('s', $module_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['result' => 'success', 'data' => $row]);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Module not found']);
            }
            break;

        case 'assign_training':
            $assignment_id = generateRandomId();
            $module_id = $_POST['module_id'] ?? '';
            $client_id = $_POST['client_id'] ?? '';
            $campaign_id = $_POST['campaign_id'] ?? '';
            $assigned_users = $_POST['assigned_users'] ?? '';
            $assigned_departments = $_POST['assigned_departments'] ?? '';
            $assignment_type = $_POST['assignment_type'] ?? 'manual';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            
            if (empty($module_id)) {
                echo json_encode(['result' => 'failed', 'error' => 'Module ID is required']);
                break;
            }

            $stmt = $conn->prepare("INSERT INTO tb_training_assignments (assignment_id, module_id, client_id, campaign_id, assigned_users, assigned_departments, assignment_type, start_date, end_date, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $current_date = (new DateTime())->format('d-m-Y h:i A');
            $stmt->bind_param('ssssssssss', $assignment_id, $module_id, $client_id, $campaign_id, $assigned_users, $assigned_departments, $assignment_type, $start_date, $end_date, $current_date);
            
            if ($stmt->execute()) {
                // Create progress records for assigned users
                if ($assigned_users) {
                    $users = json_decode($assigned_users, true);
                    foreach ($users as $user) {
                        createProgressRecord($conn, $assignment_id, $module_id, $user['email'], $user['name'], $client_id);
                    }
                }
                echo json_encode(['result' => 'success', 'assignment_id' => $assignment_id]);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Failed to assign training: ' . $stmt->error]);
            }
            break;

        case 'get_user_progress':
            $user_email = $_POST['user_email'] ?? '';
            $client_id = $_POST['client_id'] ?? '';
            
            if (empty($user_email)) {
                echo json_encode(['result' => 'failed', 'error' => 'User email is required']);
                break;
            }

            $sql = "SELECT tp.*, tm.module_name, tm.module_type, tm.estimated_duration 
                    FROM tb_training_progress tp 
                    JOIN tb_training_modules tm ON tp.module_id = tm.module_id 
                    WHERE tp.user_email = ?";
            
            if ($client_id) {
                $sql .= " AND tp.client_id = ?";
                $stmt = $conn->prepare($sql . " ORDER BY tp.last_activity DESC");
                $stmt->bind_param('ss', $user_email, $client_id);
            } else {
                $stmt = $conn->prepare($sql . " ORDER BY tp.last_activity DESC");
                $stmt->bind_param('s', $user_email);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $progress = [];
            
            while ($row = $result->fetch_assoc()) {
                $progress[] = $row;
            }
            
            echo json_encode(['result' => 'success', 'data' => $progress]);
            break;

        case 'update_progress':
            $progress_id = $_POST['progress_id'] ?? '';
            $status = $_POST['status'] ?? '';
            $progress_percentage = intval($_POST['progress_percentage'] ?? 0);
            $time_spent = intval($_POST['time_spent'] ?? 0);
            
            if (empty($progress_id)) {
                echo json_encode(['result' => 'failed', 'error' => 'Progress ID is required']);
                break;
            }

            $current_time = (new DateTime())->format('d-m-Y h:i A');
            $completion_time = ($status === 'completed') ? $current_time : null;
            
            $stmt = $conn->prepare("UPDATE tb_training_progress SET status=?, progress_percentage=?, time_spent=?, last_activity=?, completion_time=? WHERE progress_id=?");
            $stmt->bind_param('siisss', $status, $progress_percentage, $time_spent, $current_time, $completion_time, $progress_id);
            
            if ($stmt->execute()) {
                echo json_encode(['result' => 'success']);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Failed to update progress: ' . $stmt->error]);
            }
            break;

        case 'submit_quiz':
            $progress_id = $_POST['progress_id'] ?? '';
            $user_email = $_POST['user_email'] ?? '';
            $module_id = $_POST['module_id'] ?? '';
            $questions_data = $_POST['questions_data'] ?? '';
            $answers_data = $_POST['answers_data'] ?? '';
            $score = intval($_POST['score'] ?? 0);
            $total_questions = intval($_POST['total_questions'] ?? 0);
            $correct_answers = intval($_POST['correct_answers'] ?? 0);
            $time_taken = intval($_POST['time_taken'] ?? 0);
            
            if (empty($progress_id) || empty($user_email) || empty($module_id)) {
                echo json_encode(['result' => 'failed', 'error' => 'Progress ID, user email and module ID are required']);
                break;
            }

            // Get current attempt number
            $stmt = $conn->prepare("SELECT COALESCE(MAX(attempt_number), 0) + 1 as next_attempt FROM tb_training_quiz_results WHERE progress_id = ?");
            $stmt->bind_param('s', $progress_id);
            $stmt->execute();
            $attempt_number = $stmt->get_result()->fetch_assoc()['next_attempt'];

            // Insert quiz result
            $result_id = generateRandomId();
            $current_date = (new DateTime())->format('d-m-Y h:i A');
            
            $stmt = $conn->prepare("INSERT INTO tb_training_quiz_results (result_id, progress_id, user_email, module_id, attempt_number, questions_data, answers_data, score, total_questions, correct_answers, time_taken, submitted_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssississis', $result_id, $progress_id, $user_email, $module_id, $attempt_number, $questions_data, $answers_data, $score, $total_questions, $correct_answers, $time_taken, $current_date);
            
            if ($stmt->execute()) {
                // Update progress with quiz score
                $stmt = $conn->prepare("UPDATE tb_training_progress SET quiz_score=?, quiz_attempts=?, last_activity=? WHERE progress_id=?");
                $stmt->bind_param('iiss', $score, $attempt_number, $current_date, $progress_id);
                $stmt->execute();
                
                // Check if certificate should be issued
                $stmt = $conn->prepare("SELECT tm.passing_score FROM tb_training_modules tm JOIN tb_training_progress tp ON tm.module_id = tp.module_id WHERE tp.progress_id = ?");
                $stmt->bind_param('s', $progress_id);
                $stmt->execute();
                $passing_score = $stmt->get_result()->fetch_assoc()['passing_score'];
                
                if ($score >= $passing_score) {
                    // Issue certificate
                    issueCertificate($conn, $progress_id, $user_email, $module_id, $score);
                }
                
                echo json_encode(['result' => 'success', 'result_id' => $result_id, 'passed' => ($score >= $passing_score)]);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Failed to submit quiz: ' . $stmt->error]);
            }
            break;

        case 'get_rankings':
            $client_id = $_POST['client_id'] ?? '';
            
            $sql = "SELECT * FROM tb_training_rankings";
            if ($client_id) {
                $sql .= " WHERE client_id = ?";
                $stmt = $conn->prepare($sql . " ORDER BY rank_position ASC, total_score DESC");
                $stmt->bind_param('s', $client_id);
            } else {
                $stmt = $conn->prepare($sql . " ORDER BY rank_position ASC, total_score DESC");
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            $rankings = [];
            
            while ($row = $result->fetch_assoc()) {
                $rankings[] = $row;
            }
            
            echo json_encode(['result' => 'success', 'data' => $rankings]);
            break;

        case 'get_stats':
        case 'get_training_stats':
            $client_id = $_POST['client_id'] ?? '';
            
            $stats = [];
            
            // Total modules
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_training_modules WHERE status = 1");
            $stmt->execute();
            $stats['total_modules'] = $stmt->get_result()->fetch_assoc()['total'];
            
            // Active assignments
            $sql = "SELECT COUNT(*) as total FROM tb_training_assignments";
            if ($client_id) {
                $sql .= " WHERE client_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $client_id);
            } else {
                $stmt = $conn->prepare($sql);
            }
            $stmt->execute();
            $stats['active_assignments'] = $stmt->get_result()->fetch_assoc()['total'];
            
            // Completed trainings
            $sql = "SELECT COUNT(*) as total FROM tb_training_progress WHERE status = 'completed'";
            if ($client_id) {
                $sql .= " AND client_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $client_id);
            } else {
                $stmt = $conn->prepare($sql);
            }
            $stmt->execute();
            $stats['completed_trainings'] = $stmt->get_result()->fetch_assoc()['total'];
            
            // Certificates issued
            $sql = "SELECT COUNT(*) as total FROM tb_training_certificates WHERE status = 1";
            if ($client_id) {
                $sql .= " AND client_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $client_id);
            } else {
                $stmt = $conn->prepare($sql);
            }
            $stmt->execute();
            $stats['certificates_issued'] = $stmt->get_result()->fetch_assoc()['total'];
            
            echo json_encode(['result' => 'success', 'data' => $stats]);
            break;

        // Question bank management
        case 'add_question':
            $question_id = generateRandomId();
            $question_text = $_POST['question_text'] ?? '';
            $question_type = $_POST['question_type'] ?? 'multiple_choice';
            $category = $_POST['category'] ?? '';
            $difficulty = $_POST['difficulty'] ?? 'basic';
            $choices = $_POST['choices'] ?? '';// JSON encoded for multiple choice or TF
            $correct_answer = $_POST['correct_answer'] ?? '';
            $explanation = $_POST['explanation'] ?? '';
            $supporting_html = $_POST['supporting_html'] ?? '';

            if (empty($question_text) || empty($question_type)) {
                echo json_encode(['result' => 'failed', 'error' => 'Question text and type are required']);
                break;
            }

            // Normalize and validate choices depending on type
            $normalized_choices = null;
            $normalized_correct = $correct_answer;

            if ($question_type === 'multiple_choice') {
                // Expect choices as JSON array or comma-separated string
                if (is_string($choices)) {
                    $decoded = json_decode($choices, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $choices_arr = array_values(array_filter(array_map('trim', $decoded), function($v){ return $v !== ''; }));
                    } else {
                        // Try CSV fallback
                        $choices_arr = array_values(array_filter(array_map('trim', explode(',', $choices)), function($v){ return $v !== ''; }));
                    }
                } elseif (is_array($choices)) {
                    $choices_arr = array_values(array_filter(array_map('trim', $choices), function($v){ return $v !== ''; }));
                } else {
                    $choices_arr = [];
                }

                if (count($choices_arr) < 2) {
                    echo json_encode(['result' => 'failed', 'error' => 'Multiple choice questions require at least two non-empty options']);
                    break;
                }

                // validate correct answer index
                if ($normalized_correct === '' || $normalized_correct === null) {
                    echo json_encode(['result' => 'failed', 'error' => 'Correct answer is required for multiple choice questions']);
                    break;
                }
                if (!is_numeric($normalized_correct)) {
                    $normalized_correct = intval($normalized_correct);
                } else {
                    $normalized_correct = intval($normalized_correct);
                }
                if ($normalized_correct < 0 || $normalized_correct >= count($choices_arr)) {
                    echo json_encode(['result' => 'failed', 'error' => 'Correct answer index out of range']);
                    break;
                }

                $normalized_choices = json_encode(array_values($choices_arr), JSON_UNESCAPED_UNICODE);
                $normalized_correct = strval($normalized_correct);

            } elseif ($question_type === 'true_false') {
                // Force choices to True/False in Portuguese
                $choices_arr = ['Verdadeiro', 'Falso'];
                // Accept correct as 0 or 1 or strings '0'/'1'
                if ($normalized_correct === '' || $normalized_correct === null) $normalized_correct = '0';
                if (!is_numeric($normalized_correct)) $normalized_correct = ($normalized_correct == 'false' || stripos($normalized_correct,'f')!==false) ? '1' : '0';
                $normalized_correct = strval(intval($normalized_correct) ? 1 : 0);
                $normalized_choices = json_encode($choices_arr, JSON_UNESCAPED_UNICODE);

            } elseif ($question_type === 'text') {
                // text answer: store expected answer if provided
                $expected = '';
                if (is_string($choices)) {
                    // may have been sent as single string
                    $decoded = json_decode($choices, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) $expected = trim($decoded);
                    else $expected = trim($choices);
                }
                $normalized_choices = json_encode([$expected], JSON_UNESCAPED_UNICODE);
                $normalized_correct = '0';
            } else {
                // Unknown type
                echo json_encode(['result' => 'failed', 'error' => 'Unsupported question type']);
                break;
            }

            $stmt = $conn->prepare("INSERT INTO tb_training_questions (question_id, question_text, question_type, category, difficulty, choices, correct_answer, supporting_html, explanation, created_by, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $current_date = (new DateTime())->format('d-m-Y h:i A');
            $created_by = isset($_SESSION['username']) ? $_SESSION['username'] : null;
            $stmt->bind_param('sssssssssss', $question_id, $question_text, $question_type, $category, $difficulty, $normalized_choices, $normalized_correct, $supporting_html, $explanation, $created_by, $current_date);

            if ($stmt->execute()) {
                echo json_encode(['result' => 'success', 'question_id' => $question_id]);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Failed to add question: ' . $stmt->error]);
            }
            break;

        case 'update_question':
            $question_id = $_POST['question_id'] ?? '';
            $question_text = $_POST['question_text'] ?? '';
            $question_type = $_POST['question_type'] ?? 'multiple_choice';
            $category = $_POST['category'] ?? '';
            $difficulty = $_POST['difficulty'] ?? 'basic';
            $choices = $_POST['choices'] ?? '';// JSON encoded for multiple choice or TF
            $correct_answer = $_POST['correct_answer'] ?? '';
            $explanation = $_POST['explanation'] ?? '';
            $supporting_html = $_POST['supporting_html'] ?? '';

            if (empty($question_id) || empty($question_text) || empty($question_type)) {
                echo json_encode(['result' => 'failed', 'error' => 'Question id, text and type are required']);
                break;
            }

            // Reuse same normalization logic as add_question
            $normalized_choices = null;
            $normalized_correct = $correct_answer;

            if ($question_type === 'multiple_choice') {
                if (is_string($choices)) {
                    $decoded = json_decode($choices, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $choices_arr = array_values(array_filter(array_map('trim', $decoded), function($v){ return $v !== ''; }));
                    } else {
                        $choices_arr = array_values(array_filter(array_map('trim', explode(',', $choices)), function($v){ return $v !== ''; }));
                    }
                } elseif (is_array($choices)) {
                    $choices_arr = array_values(array_filter(array_map('trim', $choices), function($v){ return $v !== ''; }));
                } else {
                    $choices_arr = [];
                }

                if (count($choices_arr) < 2) {
                    echo json_encode(['result' => 'failed', 'error' => 'Multiple choice questions require at least two non-empty options']);
                    break;
                }

                if ($normalized_correct === '' || $normalized_correct === null) {
                    echo json_encode(['result' => 'failed', 'error' => 'Correct answer is required for multiple choice questions']);
                    break;
                }
                $normalized_correct = strval(intval($normalized_correct));
                if ($normalized_correct < 0 || $normalized_correct >= count($choices_arr)) {
                    echo json_encode(['result' => 'failed', 'error' => 'Correct answer index out of range']);
                    break;
                }

                $normalized_choices = json_encode(array_values($choices_arr), JSON_UNESCAPED_UNICODE);
                $normalized_correct = strval($normalized_correct);

            } elseif ($question_type === 'true_false') {
                $choices_arr = ['Verdadeiro', 'Falso'];
                if ($normalized_correct === '' || $normalized_correct === null) $normalized_correct = '0';
                if (!is_numeric($normalized_correct)) $normalized_correct = ($normalized_correct == 'false' || stripos($normalized_correct,'f')!==false) ? '1' : '0';
                $normalized_correct = strval(intval($normalized_correct) ? 1 : 0);
                $normalized_choices = json_encode($choices_arr, JSON_UNESCAPED_UNICODE);

            } elseif ($question_type === 'text') {
                $expected = '';
                if (is_string($choices)) {
                    $decoded = json_decode($choices, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) $expected = trim($decoded);
                    else $expected = trim($choices);
                }
                $normalized_choices = json_encode([$expected], JSON_UNESCAPED_UNICODE);
                $normalized_correct = '0';
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Unsupported question type']);
                break;
            }

            $stmt = $conn->prepare("UPDATE tb_training_questions SET question_text=?, question_type=?, category=?, difficulty=?, choices=?, correct_answer=?, supporting_html=?, explanation=? WHERE question_id=?");
            if (!$stmt) { echo json_encode(['result'=>'failed','error'=>'Prepare failed: '.mysqli_error($conn)]); break; }
            $stmt->bind_param('sssssssss', $question_text, $question_type, $category, $difficulty, $normalized_choices, $normalized_correct, $supporting_html, $explanation, $question_id);
            if ($stmt->execute()) {
                echo json_encode(['result' => 'success', 'question_id' => $question_id]);
            } else {
                echo json_encode(['result' => 'failed', 'error' => 'Failed to update question: ' . $stmt->error]);
            }
            break;

        case 'get_questions':
            $category = $_POST['category'] ?? '';
            $difficulty = $_POST['difficulty'] ?? '';
            $sql = "SELECT * FROM tb_training_questions WHERE status = 1";
            $params = [];
            $types = '';

            if ($category) {
                $sql .= " AND category = ?";
                $params[] = $category;
                $types .= 's';
            }
            if ($difficulty) {
                $sql .= " AND difficulty = ?";
                $params[] = $difficulty;
                $types .= 's';
            }

            $sql .= " ORDER BY created_date DESC";
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($r = $result->fetch_assoc()) $rows[] = $r;
            echo json_encode(['result' => 'success', 'data' => $rows]);
            break;

        case 'get_question':
            $question_id = $_POST['question_id'] ?? '';
            if (empty($question_id)) { echo json_encode(['result'=>'failed','error'=>'Question id required']); break; }
            $stmt = $conn->prepare("SELECT * FROM tb_training_questions WHERE question_id = ?");
            $stmt->bind_param('s', $question_id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row) echo json_encode(['result'=>'success','data'=>$row]); else echo json_encode(['result'=>'failed','error'=>'Not found']);
            break;

        case 'delete_question':
            $question_id = $_POST['question_id'] ?? '';
            if (empty($question_id)) { echo json_encode(['result'=>'failed','error'=>'Question id required']); break; }
            $stmt = $conn->prepare("UPDATE tb_training_questions SET status = 0 WHERE question_id = ?");
            $stmt->bind_param('s', $question_id);
            if ($stmt->execute()) echo json_encode(['result'=>'success']); else echo json_encode(['result'=>'failed','error'=>$stmt->error]);
            break;

        case 'get_random_questions':
            // Returns N random questions matching category(s) and difficulty(s)
            $count = intval($_POST['count'] ?? 10);
            $category = $_POST['category'] ?? '';
            $difficulty = $_POST['difficulty'] ?? '';

            $sql = "SELECT * FROM tb_training_questions WHERE status = 1";
            $params = [];
            $types = '';
            if ($category) { $sql .= " AND category = ?"; $params[] = $category; $types .= 's'; }
            if ($difficulty) { $sql .= " AND difficulty = ?"; $params[] = $difficulty; $types .= 's'; }

            $sql .= " ORDER BY RAND() LIMIT ?";
            $stmt = $conn->prepare($sql);
            if (!empty($params)) {
                // bind params + count
                $types_with_count = $types . 'i';
                $stmt->bind_param($types_with_count, ...array_merge($params, [$count]));
            } else {
                $stmt->bind_param('i', $count);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = [];
            while ($r = $result->fetch_assoc()) $rows[] = $r;
            echo json_encode(['result' => 'success', 'data' => $rows]);
            break;

        case 'get_composed_questions':
            // bank_composition expected as JSON: { basic: N, intermediate: M, advanced: K }
            $category = $_POST['bank_category'] ?? '';
            $composition_raw = $_POST['bank_composition'] ?? '';
            if (empty($composition_raw)) { echo json_encode(['result'=>'failed','error'=>'Composition required']); break; }
            $comp = json_decode($composition_raw, true);
            if (!is_array($comp)) { echo json_encode(['result'=>'failed','error'=>'Invalid composition']); break; }

            $final = [];
            foreach (['basic','intermediate','advanced'] as $dif) {
                $n = intval($comp[$dif] ?? 0);
                if ($n <= 0) continue;
                $sql = "SELECT * FROM tb_training_questions WHERE status = 1";
                $params = [];
                $types = '';
                if ($category) { $sql .= " AND category = ?"; $params[] = $category; $types .= 's'; }
                $sql .= " AND difficulty = ?"; $params[] = $dif; $types .= 's';
                $sql .= " ORDER BY RAND() LIMIT ?";
                $stmt = $conn->prepare($sql);
                if (!empty($params)) {
                    $types_with_count = $types . 'i';
                    $stmt->bind_param($types_with_count, ...array_merge($params, [$n]));
                } else {
                    $stmt->bind_param('i', $n);
                }
                $stmt->execute();
                $result = $stmt->get_result();
                while ($r = $result->fetch_assoc()) $final[] = $r;
            }

            if (count($final) === 0) { echo json_encode(['result'=>'failed','error'=>'No questions found for the requested composition']); break; }
            echo json_encode(['result'=>'success','data'=>$final]);
            break;

        case 'upload_question_asset':
            // Simple upload handler for question supporting assets (images)
            if (empty($_FILES['file'])) { echo json_encode(['result'=>'failed','error'=>'No file uploaded']); break; }
            $file = $_FILES['file'];
            if ($file['error'] !== UPLOAD_ERR_OK) { echo json_encode(['result'=>'failed','error'=>'Upload error']); break; }
            // Server-side validation: allowed types and max size
            $allowed_ext = ['jpg','jpeg','png','gif','webp'];
            $max_bytes = 2 * 1024 * 1024; // 2 MB
            $upload_dir = dirname(__FILE__) . '/../uploads/training_questions';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_ext)) { echo json_encode(['result'=>'failed','error'=>'Tipo de arquivo não permitido']); break; }
            if ($file['size'] > $max_bytes) { echo json_encode(['result'=>'failed','error'=>'Arquivo maior que o limite de 2 MB']); break; }

            $target_name = generateRandomId(12) . '.' . $ext;
            $target_path = $upload_dir . '/' . $target_name;

            if (!move_uploaded_file($file['tmp_name'], $target_path)) {
                echo json_encode(['result'=>'failed','error'=>'Failed to move uploaded file']); break;
            }

            // Return a relative web path to the uploaded file
            $web_path = '/SniperPhish-main/spear/uploads/training_questions/' . $target_name;
            echo json_encode(['result'=>'success','path'=>$web_path]);
            break;

        default:
            echo json_encode(['result' => 'failed', 'error' => 'Invalid action type']);
            break;
    }
}

function createProgressRecord($conn, $assignment_id, $module_id, $user_email, $user_name, $client_id) {
    $progress_id = generateRandomId();
    $current_date = (new DateTime())->format('d-m-Y h:i A');
    
    $stmt = $conn->prepare("INSERT INTO tb_training_progress (progress_id, assignment_id, user_email, user_name, client_id, module_id, status, last_activity) VALUES (?, ?, ?, ?, ?, ?, 'not_started', ?)");
    $stmt->bind_param('sssssss', $progress_id, $assignment_id, $user_email, $user_name, $client_id, $module_id, $current_date);
    return $stmt->execute();
}

function issueCertificate($conn, $progress_id, $user_email, $module_id, $score) {
    // Get module and user details
    $stmt = $conn->prepare("SELECT tm.module_name, tp.user_name, tp.client_id FROM tb_training_modules tm JOIN tb_training_progress tp ON tm.module_id = tp.module_id WHERE tp.progress_id = ?");
    $stmt->bind_param('s', $progress_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    $certificate_id = generateRandomId();
    $validation_code = strtoupper(generateRandomId(12));
    $current_date = (new DateTime())->format('d-m-Y h:i A');
    
    $stmt = $conn->prepare("INSERT INTO tb_training_certificates (certificate_id, progress_id, user_email, user_name, client_id, module_id, module_name, score_achieved, completion_date, validation_code, issued_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('sssssssssss', $certificate_id, $progress_id, $user_email, $result['user_name'], $result['client_id'], $module_id, $result['module_name'], $score, $current_date, $validation_code, $current_date);
    
    if ($stmt->execute()) {
        // Mark certificate as issued in progress
        $stmt = $conn->prepare("UPDATE tb_training_progress SET certificate_issued = 1, certificate_id = ? WHERE progress_id = ?");
        $stmt->bind_param('ss', $certificate_id, $progress_id);
        $stmt->execute();
        
        return $certificate_id;
    }
    
    return false;
}

function generateRandomId($length = 10) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

/**
 * TrainingManager Class
 * Main class for training module management operations
 */
class TrainingManager {
    private $db;
    
    public function __construct() {
        global $conn;
        $this->db = $conn;
    }
    
    public function __destruct() {
        if ($this->db) {
            $this->db->close();
        }
    }

    /**
     * Get all training modules
     */
    public function getAllTrainingModules($client_id = null) {
        try {
            $sql = "SELECT * FROM tb_training_modules WHERE status = 1";
            $params = array();
            
            if ($client_id) {
                $sql .= " AND (client_id = ? OR client_id IS NULL)";
                $params[] = $client_id;
            }
            
            $sql .= " ORDER BY created_date DESC";
            
            $stmt = $this->db->prepare($sql);
            if (!empty($params)) {
                $types = str_repeat('s', count($params));
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $modules = array();
            while ($row = $result->fetch_assoc()) {
                $modules[] = $row;
            }
            
            return $modules;
            
        } catch (Exception $e) {
            error_log("Error getting training modules: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Get training module by ID
     */
    public function getTrainingModuleById($module_id) {
        try {
            $sql = "SELECT *, 
                    CASE 
                        WHEN module_type IN ('quiz', 'mixed') OR quiz_data IS NOT NULL THEN 1 
                        ELSE 0 
                    END AS quiz_enabled
                    FROM tb_training_modules WHERE module_id = ? AND status = 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $module_id);
            $stmt->execute();
            
            $result = $stmt->get_result();
            return $result->fetch_assoc();
            
        } catch (Exception $e) {
            error_log("Error getting training module: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Create new training module
     */
    public function createTrainingModule($data) {
        try {
            $module_id = 'TM_' . uniqid();
            
            $sql = "INSERT INTO tb_training_modules 
                    (module_id, module_name, module_description, module_type, content_data, 
                     quiz_data, passing_score, estimated_duration, difficulty_level, 
                     category, tags, created_by, created_date) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $created_date = date('Y-m-d H:i:s');
            
            $stmt->bind_param("ssssssiisssss", 
                $module_id,
                $data['module_name'],
                $data['module_description'],
                $data['module_type'],
                $data['content_data'],
                $data['quiz_data'],
                $data['passing_score'],
                $data['estimated_duration'],
                $data['difficulty_level'],
                $data['category'],
                $data['tags'],
                $data['created_by'],
                $created_date
            );
            
            if ($stmt->execute()) {
                return array('success' => true, 'module_id' => $module_id);
            } else {
                return array('success' => false, 'error' => 'Failed to create module');
            }
            
        } catch (Exception $e) {
            error_log("Error creating training module: " . $e->getMessage());
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    /**
     * Update training module
     */
    public function updateTrainingModule($module_id, $data) {
        try {
            $sql = "UPDATE tb_training_modules SET 
                    module_name = ?, module_description = ?, module_type = ?, 
                    content_data = ?, quiz_data = ?, passing_score = ?, 
                    estimated_duration = ?, difficulty_level = ?, category = ?, 
                    tags = ?, modified_date = ? 
                    WHERE module_id = ?";
            
            $stmt = $this->db->prepare($sql);
            $modified_date = date('Y-m-d H:i:s');
            
            $stmt->bind_param("sssssiisssss", 
                $data['module_name'],
                $data['module_description'],
                $data['module_type'],
                $data['content_data'],
                $data['quiz_data'],
                $data['passing_score'],
                $data['estimated_duration'],
                $data['difficulty_level'],
                $data['category'],
                $data['tags'],
                $modified_date,
                $module_id
            );
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error updating training module: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete training module
     */
    public function deleteTrainingModule($module_id) {
        try {
            // Soft delete
            $sql = "UPDATE tb_training_modules SET status = 0 WHERE module_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("s", $module_id);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            error_log("Error deleting training module: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get training statistics
     */
    public function getTrainingStatistics($client_id = null) {
        try {
            $stats = array();
            
            // Total modules
            $sql = "SELECT COUNT(*) as total FROM tb_training_modules WHERE status = 1";
            if ($client_id) {
                $sql .= " AND (client_id = '$client_id' OR client_id IS NULL)";
            }
            $result = $this->db->query($sql);
            $stats['total_modules'] = $result->fetch_assoc()['total'];
            
            // Active assignments
            $sql = "SELECT COUNT(*) as total FROM tb_training_assignments WHERE status = 1";
            if ($client_id) {
                $sql .= " AND client_id = '$client_id'";
            }
            $result = $this->db->query($sql);
            $stats['active_assignments'] = $result->fetch_assoc()['total'];
            
            // Completed trainings
            $sql = "SELECT COUNT(*) as total FROM tb_training_progress WHERE status = 'completed'";
            if ($client_id) {
                $sql .= " AND client_id = '$client_id'";
            }
            $result = $this->db->query($sql);
            $stats['completed_trainings'] = $result->fetch_assoc()['total'];
            
            // Certificates issued
            $sql = "SELECT COUNT(*) as total FROM tb_training_certificates WHERE 1=1";
            if ($client_id) {
                $sql .= " AND client_id = '$client_id'";
            }
            $result = $this->db->query($sql);
            $stats['certificates_issued'] = $result->fetch_assoc()['total'];
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error getting training statistics: " . $e->getMessage());
            return array(
                'total_modules' => 0,
                'active_assignments' => 0,
                'completed_trainings' => 0,
                'certificates_issued' => 0
            );
        }
    }

    /**
     * Get training rankings
     */
    public function getTrainingRankings($limit = 10, $client_id = null) {
        try {
            $sql = "SELECT 
                        tp.user_email,
                        COUNT(*) as completed_trainings,
                        SUM(tm.points_value) as total_points,
                        AVG(tqr.score) as avg_score
                    FROM tb_training_progress tp
                    JOIN tb_training_modules tm ON tp.module_id = tm.module_id
                    LEFT JOIN tb_training_quiz_results tqr ON tp.user_id = tqr.user_id AND tp.module_id = tqr.module_id
                    WHERE tp.status = 'completed'";
            
            if ($client_id) {
                $sql .= " AND tp.client_id = ?";
            }
            
            $sql .= " GROUP BY tp.user_email
                     ORDER BY total_points DESC, avg_score DESC
                     LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            
            if ($client_id) {
                $stmt->bind_param("si", $client_id, $limit);
            } else {
                $stmt->bind_param("i", $limit);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $rankings = array();
            $position = 1;
            while ($row = $result->fetch_assoc()) {
                $row['position'] = $position++;
                $rankings[] = $row;
            }
            
            return $rankings;
            
        } catch (Exception $e) {
            error_log("Error getting training rankings: " . $e->getMessage());
            return array();
        }
    }

    /**
     * Assign training to users
     */
    public function assignTraining($module_id, $client_id, $user_emails, $start_date = null, $end_date = null) {
        try {
            $assignment_id = 'TA_' . uniqid();
            
            $sql = "INSERT INTO tb_training_assignments 
                    (assignment_id, module_id, client_id, assigned_users, assignment_type, 
                     start_date, end_date, created_date) 
                    VALUES (?, ?, ?, ?, 'manual', ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $created_date = date('Y-m-d H:i:s');
            $assigned_users = is_array($user_emails) ? implode(',', $user_emails) : $user_emails;
            
            $stmt->bind_param("sssssss", 
                $assignment_id,
                $module_id,
                $client_id,
                $assigned_users,
                $start_date,
                $end_date,
                $created_date
            );
            
            if ($stmt->execute()) {
                // Create individual progress records
                $this->createProgressRecords($assignment_id, $user_emails);
                return array('success' => true, 'assignment_id' => $assignment_id);
            } else {
                return array('success' => false, 'error' => 'Failed to assign training');
            }
            
        } catch (Exception $e) {
            error_log("Error assigning training: " . $e->getMessage());
            return array('success' => false, 'error' => $e->getMessage());
        }
    }

    /**
     * Create individual progress records for assigned users
     */
    private function createProgressRecords($assignment_id, $user_emails) {
        try {
            $emails = is_array($user_emails) ? $user_emails : explode(',', $user_emails);
            
            foreach ($emails as $email) {
                $progress_id = 'TP_' . uniqid();
                
                $sql = "INSERT INTO tb_training_progress 
                        (progress_id, assignment_id, user_email, status, created_date) 
                        VALUES (?, ?, ?, 'assigned', ?)";
                
                $stmt = $this->db->prepare($sql);
                $created_date = date('Y-m-d H:i:s');
                
                $stmt->bind_param("ssss", $progress_id, $assignment_id, trim($email), $created_date);
                $stmt->execute();
            }
            
        } catch (Exception $e) {
            error_log("Error creating progress records: " . $e->getMessage());
        }
    }

    /**
     * Get user training progress
     */
    public function getUserProgress($user_email, $client_id = null) {
        try {
            $sql = "SELECT tp.*, tm.module_name, tm.estimated_duration, ta.end_date
                    FROM tb_training_progress tp
                    JOIN tb_training_assignments ta ON tp.assignment_id = ta.assignment_id
                    JOIN tb_training_modules tm ON ta.module_id = tm.module_id
                    WHERE tp.user_email = ?";
            
            if ($client_id) {
                $sql .= " AND ta.client_id = ?";
            }
            
            $sql .= " ORDER BY tp.created_date DESC";
            
            $stmt = $this->db->prepare($sql);
            
            if ($client_id) {
                $stmt->bind_param("ss", $user_email, $client_id);
            } else {
                $stmt->bind_param("s", $user_email);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $progress = array();
            while ($row = $result->fetch_assoc()) {
                $progress[] = $row;
            }
            
            return $progress;
            
        } catch (Exception $e) {
            error_log("Error getting user progress: " . $e->getMessage());
            return array();
        }
    }
}
?>

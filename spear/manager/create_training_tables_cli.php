<?php
// Script to create training tables without session (for local/admin use).
// Usage:
// php spear\manager\create_training_tables_cli.php
// or open in browser: http://localhost/SniperPhish-main/spear/manager/create_training_tables_cli.php

require_once(dirname(__FILE__) . '/../config/db.php');

// Ensure this file is executed only from localhost for safety
$allowed_hosts = ['127.0.0.1', '::1', 'localhost'];
if (php_sapi_name() !== 'cli') {
    $host = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($host, $allowed_hosts)) {
        header('Content-Type: application/json');
        echo json_encode(['result' => 'failed', 'error' => 'Not allowed from remote host']);
        exit;
    }
}

$sql = "
CREATE TABLE IF NOT EXISTS `tb_training_modules` (
    `module_id` varchar(50) NOT NULL PRIMARY KEY,
    `module_name` varchar(200) NOT NULL,
    `module_description` text DEFAULT NULL,
    `module_type` varchar(50) NOT NULL,
    `content_data` longtext DEFAULT NULL,
    `quiz_data` longtext DEFAULT NULL,
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
    `explanation` text DEFAULT NULL,
    `created_by` varchar(50) DEFAULT NULL,
    `created_date` varchar(50) NOT NULL,
    `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

$res = mysqli_multi_query($conn, $sql);
if ($res) {
    // consume results
    do { $x = mysqli_store_result($conn); if ($x) mysqli_free_result($x); } while (mysqli_more_results($conn) && mysqli_next_result($conn));
    $out = ['result' => 'success', 'message' => 'Training tables created or already exist'];
} else {
    $out = ['result' => 'failed', 'error' => mysqli_error($conn)];
}

header('Content-Type: application/json');
echo json_encode($out);

?>

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/

-- bkp
--
-- Host: 127.0.0.1
-- Tempo de geração: 09/10/2025 às 00:14
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `loophish`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_access_ctrl`
--

CREATE TABLE `tb_access_ctrl` (
  `tk_id` varchar(11) NOT NULL,
  `ctrl_ids` varchar(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_access_ctrl`
--

INSERT INTO `tb_access_ctrl` (`tk_id`, `ctrl_ids`) VALUES
('2btlf5', '[\"ffglq0\",\"gjl71f\"]'),
('2dns16', '[\"ffglq0\",\"daxn8c\"]'),
('3rgcuy', '[\"rr1ozc\",\"gjl71f\"]'),
('cboacr', '[\"3ndydg\",\"bi8rsz\"]'),
('dnc1ze', '[\"7bnvcw\",\"bi8rsz\"]'),
('ndrqks', '[\"3ndydg\",\"gjl71f\"]'),
('thi6j8', '[\"z1yy62\"]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_campaign_training_association`
--

CREATE TABLE `tb_campaign_training_association` (
  `association_id` varchar(50) NOT NULL,
  `campaign_id` varchar(50) NOT NULL,
  `campaign_type` enum('mail','web') NOT NULL DEFAULT 'mail',
  `module_id` varchar(50) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `trigger_event` enum('click','submit','immediate') NOT NULL DEFAULT 'click',
  `delay_seconds` int(11) DEFAULT 2,
  `is_active` tinyint(1) DEFAULT 1,
  `created_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_campaign_training_results`
--

CREATE TABLE `tb_campaign_training_results` (
  `id` int(11) NOT NULL,
  `client_id` varchar(50) NOT NULL DEFAULT 'default_org',
  `campaign_id` varchar(50) DEFAULT NULL,
  `tracker_id` varchar(111) DEFAULT NULL,
  `training_module_id` varchar(50) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `participant_name` varchar(255) DEFAULT NULL,
  `training_started_at` datetime DEFAULT NULL,
  `training_completed_at` datetime DEFAULT NULL,
  `training_score` int(3) DEFAULT NULL,
  `status` enum('pending','in_progress','completed','failed') DEFAULT 'pending',
  `questions_answered` int(5) DEFAULT 0,
  `correct_answers` int(5) DEFAULT 0,
  `completion_percentage` decimal(5,2) DEFAULT 0.00,
  `time_spent_minutes` int(10) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `click_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `training_started` timestamp NULL DEFAULT NULL,
  `training_completed` timestamp NULL DEFAULT NULL,
  `completion_score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_clients`
--

CREATE TABLE `tb_clients` (
  `client_id` varchar(50) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_domain` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `logo_path` varchar(500) DEFAULT NULL,
  `brand_colors` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`brand_colors`)),
  `address` text DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_date` varchar(111) DEFAULT NULL,
  `last_modified` varchar(111) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_clients`
--

INSERT INTO `tb_clients` (`client_id`, `client_name`, `client_domain`, `contact_email`, `contact_phone`, `logo_path`, `brand_colors`, `address`, `created_by`, `created_date`, `last_modified`, `status`, `settings`) VALUES
('client_j5pQOiI2', 'Cliente teste', 'localhost', 'davidddf.frota@gmail.com', '88997557530', NULL, '{\"primary\":\"#1f3593\",\"secondary\":\"#38343c\"}', 'teste', 'admin', '03-10-2025 12:45 AM', NULL, 1, '[]'),
('client_M4Q1pK2q', 'cliente final teste', 'exemplo.com.br', 'davidddf.frota@gmail.com', '88997557530', NULL, '{\"primary\":\"#e6e6e6\",\"secondary\":\"#a14a4a\"}', 'rua', 'admin', '07-10-2025 08:07 PM', NULL, 1, '[]'),
('client_xuj7DqeN', 'Cliente teste2', 'exemplo.com', 'davidddf.frota@gmail.com', '88997557530', NULL, '{\"primary\":\"#667eea\",\"secondary\":\"#764ba2\"}', '', 'admin', '03-10-2025 02:20 PM', NULL, 1, '[]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_client_settings`
--

CREATE TABLE `tb_client_settings` (
  `setting_id` varchar(50) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_type` enum('string','json','number','boolean') DEFAULT 'string',
  `created_date` varchar(111) DEFAULT NULL,
  `updated_date` varchar(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_client_users`
--

CREATE TABLE `tb_client_users` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `client_id` varchar(50) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `department_id` varchar(50) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `added_date` varchar(111) DEFAULT NULL,
  `last_updated` varchar(111) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `user_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`user_data`)),
  `campaign_count` int(11) DEFAULT 0,
  `last_campaign_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_client_users`
--

INSERT INTO `tb_client_users` (`id`, `user_email`, `user_name`, `first_name`, `last_name`, `client_id`, `department`, `department_id`, `position`, `phone`, `added_date`, `last_updated`, `status`, `user_data`, `campaign_count`, `last_campaign_date`) VALUES
(1, 'davidddf.frota@gmail.com', 'david damasceno da frota', 'david', 'damasceno da frota', 'client_xuj7DqeN', 'TI', 'dept_TI_8IVY', NULL, NULL, '06-10-2025 11:16 PM', '06-10-2025 11:16 PM', 1, NULL, 0, NULL),
(2, 'alice@example.com', 'Alice', 'Alice', '', 'client_xuj7DqeN', 'NoteA', 'dept_NOTEA_N9IW', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(3, 'bob@example.com', 'Bob', 'Bob', '', 'client_xuj7DqeN', 'NoteB', 'dept_NOTEB_7T44', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(4, 'charlie@example.com', 'Charlie', 'Charlie', '', 'client_xuj7DqeN', 'NoteC', 'dept_NOTEC_TDCY', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(5, 'david@example.com', 'David', 'David', '', 'client_xuj7DqeN', 'NoteD', 'dept_NOTED_BA2W', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(6, 'eve@example.com', 'Eve', 'Eve', '', 'client_xuj7DqeN', 'NoteE', 'dept_NOTEE_66FV', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(7, 'frank@example.com', 'Frank', 'Frank', '', 'client_xuj7DqeN', 'NoteF', 'dept_NOTEF_0GGY', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(8, 'grace@example.com', 'Grace', 'Grace', '', 'client_xuj7DqeN', 'NoteG', 'dept_NOTEG_WPAV', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(9, 'heidi@example.com', 'Heidi', 'Heidi', '', 'client_xuj7DqeN', 'NoteH', 'dept_NOTEH_OUPV', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(10, 'ivan@example.com', 'Ivan', 'Ivan', '', 'client_xuj7DqeN', 'NoteI', 'dept_NOTEI_Q8O7', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(11, 'judy@example.com', 'Judy', 'Judy', '', 'client_xuj7DqeN', 'NoteJ', 'dept_NOTEJ_Z5GB', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(12, 'mallory@example.com', 'Mallory', 'Mallory', '', 'client_xuj7DqeN', 'NoteM', 'dept_NOTEM_3EKX', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(13, 'niaj@example.com', 'Niaj', 'Niaj', '', 'client_xuj7DqeN', 'NoteN', 'dept_NOTEN_NY99', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(14, 'olivia@example.com', 'Olivia', 'Olivia', '', 'client_xuj7DqeN', 'NoteO', 'dept_NOTEO_28CE', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(15, 'peggy@example.com', 'Peggy', 'Peggy', '', 'client_xuj7DqeN', 'NoteP', 'dept_NOTEP_SN80', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL),
(16, 'sybil@example.com', 'Sybil', 'Sybil', '', 'client_xuj7DqeN', 'NoteS', 'dept_NOTES_JTCH', NULL, NULL, '06-10-2025 11:17 PM', '06-10-2025 11:17 PM', 1, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_core_mailcamp_config`
--

CREATE TABLE `tb_core_mailcamp_config` (
  `mconfig_id` varchar(50) NOT NULL,
  `mconfig_name` varchar(50) DEFAULT NULL,
  `mconfig_data` mediumtext DEFAULT NULL,
  `date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_core_mailcamp_config`
--

INSERT INTO `tb_core_mailcamp_config` (`mconfig_id`, `mconfig_name`, `mconfig_data`, `date`) VALUES
('default', 'Default Configuration', '{\"mail_sign\":{\"cert\":[],\"pvk\":[]},\"mail_enc\":{\"cert\":[]},\"peer_verification\":true,\"recipient_type\":\"to\",\"signed_mail\":false,\"encrypted_mail\":false,\"antiflood\":{\"limit\":\"50\",\"pause\":\"30\"},\"msg_priority\":\"3\"}', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_core_mailcamp_list`
--

CREATE TABLE `tb_core_mailcamp_list` (
  `campaign_id` varchar(50) NOT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `campaign_name` varchar(50) NOT NULL,
  `campaign_data` varchar(1111) NOT NULL,
  `date` varchar(111) NOT NULL,
  `scheduled_time` varchar(111) NOT NULL,
  `stop_time` varchar(111) DEFAULT NULL,
  `camp_status` int(11) NOT NULL DEFAULT 0,
  `training_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `training_module_id` varchar(50) DEFAULT NULL,
  `training_target_percentage` int(3) DEFAULT 100,
  `training_notification_emails` text DEFAULT NULL,
  `camp_lock` tinyint(4) NOT NULL DEFAULT 0,
  `training_completion_rate` decimal(5,2) DEFAULT 0.00 COMMENT 'Taxa de conclusão do treinamento'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_mailcamp_list`
--

INSERT INTO `tb_core_mailcamp_list` (`campaign_id`, `client_id`, `campaign_name`, `campaign_data`, `date`, `scheduled_time`, `stop_time`, `camp_status`, `training_enabled`, `training_module_id`, `training_target_percentage`, `training_notification_emails`, `camp_lock`, `training_completion_rate`) VALUES
('3ndydg', 'client_xuj7DqeN', 'Email Campaigns fulano', '{\"user_group\":{\"id\":\"u11civ\",\"name\":\"Email User Groups\"},\"mail_template\":{\"id\":\"jn1i4h\",\"name\":\"My Bank\"},\"mail_sender\":{\"id\":\"58tc5o\",\"name\":\"teste\"},\"mail_config\":{\"id\":\"default\",\"name\":\"Default Configuration\"},\"msg_interval\":\"0000-0000\",\"msg_fail_retry\":\"2\"}', '04-10-2025 10:02 AM', '04-10-2025 10:02 AM', '06-10-2025 11:32 PM', 3, 0, NULL, 100, NULL, 1, 0.00),
('7bnvcw', 'client_xuj7DqeN', 'Prince', '{\"user_group\":{\"id\":\"ts79nh\",\"name\":\"Grupo teste\"},\"mail_template\":{\"id\":\"jn1i4h\",\"name\":\"My Bank (Important! Your consent is required)\"},\"mail_sender\":{\"id\":\"e2ss7q\",\"name\":\"Mailsenderrprince (davidddf.frota@gmail.com)\"},\"mail_config\":{\"id\":\"default\",\"name\":\"Default Configuration\"},\"msg_interval\":\"0000-0000\",\"msg_fail_retry\":\"2\"}', '06-10-2025 11:32 PM', '06-10-2025 11:32 PM', '07-10-2025 12:02 AM', 3, 0, NULL, 100, NULL, 1, 0.00),
('ffglq0', 'client_j5pQOiI2', 'Email Campaigns cliente teste', '{\"user_group\":{\"id\":\"7sfr1w\",\"name\":\"Email User Groups teste david\"},\"mail_template\":{\"id\":\"jn1i4h\",\"name\":\"My Bank (Important! Your consent is required)\"},\"mail_sender\":{\"id\":\"e2ss7q\",\"name\":\"Mailsenderrprince (davidddf.frota@gmail.com)\"},\"mail_config\":{\"id\":\"default\",\"name\":\"Default Configuration\"},\"msg_interval\":\"0000-0000\",\"msg_fail_retry\":\"2\"}', '06-10-2025 07:33 PM', '06-10-2025 07:32 PM', NULL, 4, 0, NULL, 100, NULL, 1, 0.00),
('ss90ns', 'client_xuj7DqeN', 'campanha do nodeste', '{\"user_group\":{\"id\":\"ts79nh\",\"name\":\"Grupo teste\"},\"mail_template\":{\"id\":\"jn1i4h\",\"name\":\"My Bank (Important! Your consent is required)\"},\"mail_sender\":{\"id\":\"e2ss7q\",\"name\":\"Mailsenderrprince (davidddf.frota@gmail.com)\"},\"mail_config\":{\"id\":\"default\",\"name\":\"Default Configuration\"},\"msg_interval\":\"0000-0000\",\"msg_fail_retry\":\"2\"}', '06-10-2025 11:22 PM', '06-10-2025 11:21 PM', '06-10-2025 11:23 PM', 3, 0, NULL, 100, NULL, 1, 0.00),
('v12wnr', 'client_xuj7DqeN', 'teste final', '{\"user_group\":{\"id\":\"ts79nh\",\"name\":\"Grupo teste\"},\"mail_template\":{\"id\":\"uma0dc\",\"name\":\"Track me (Thanks!)\"},\"mail_sender\":{\"id\":\"e2ss7q\",\"name\":\"Mailsenderrprince (davidddf.frota@gmail.com)\"},\"mail_config\":{\"id\":\"default\",\"name\":\"Default Configuration\"},\"msg_interval\":\"0000-0000\",\"msg_fail_retry\":\"2\"}', '06-10-2025 11:37 PM', '06-10-2025 11:37 PM', '07-10-2025 12:02 AM', 3, 0, NULL, 100, NULL, 1, 0.00),
('z1yy62', 'client_xuj7DqeN', 'teste21', '{\"user_group\":{\"id\":\"ts79nh\",\"name\":\"Grupo teste\"},\"mail_template\":{\"id\":\"uma0dc\",\"name\":\"Track me (Thanks!)\"},\"mail_sender\":{\"id\":\"e2ss7q\",\"name\":\"Mailsenderrprince (davidddf.frota@gmail.com)\"},\"mail_config\":{\"id\":\"default\",\"name\":\"Default Configuration\"},\"msg_interval\":\"0000-0000\",\"msg_fail_retry\":\"2\"}', '07-10-2025 12:02 AM', '07-10-2025 12:02 AM', NULL, 4, 0, NULL, 100, NULL, 1, 0.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_core_mailcamp_sender_list`
--

CREATE TABLE `tb_core_mailcamp_sender_list` (
  `sender_list_id` varchar(111) NOT NULL,
  `client_id` varchar(50) DEFAULT 'default_org',
  `sender_name` varchar(50) NOT NULL,
  `sender_smtp_server` varchar(50) NOT NULL,
  `sender_from` varchar(111) NOT NULL,
  `sender_acc_username` varchar(111) NOT NULL,
  `sender_acc_pwd` varchar(50) NOT NULL,
  `auto_mailbox` tinyint(1) NOT NULL DEFAULT 0,
  `sender_mailbox` varchar(1111) DEFAULT NULL,
  `cust_headers` varchar(1111) DEFAULT NULL,
  `dsn_type` varchar(111) DEFAULT NULL,
  `date` varchar(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_mailcamp_sender_list`
--

INSERT INTO `tb_core_mailcamp_sender_list` (`sender_list_id`, `client_id`, `sender_name`, `sender_smtp_server`, `sender_from`, `sender_acc_username`, `sender_acc_pwd`, `auto_mailbox`, `sender_mailbox`, `cust_headers`, `dsn_type`, `date`) VALUES
('58tc5o', '', 'teste', 'NA', 'davidddf.frota@gmailcom', 'username@gmail.com', '', 1, 'None', '[]', 'gmail', '04-10-2025 10:00 AM'),
('e2ss7q', 'client_j5pQOiI2', 'Mailsenderrprince', 'smtp.gmail.com:587', 'davidddf.frota@gmail.com', 'siapp.2025.07@gmail.com', 'cxxanchcrpgxkhih', 1, 'None', '[]', 'custom', '06-10-2025 07:24 PM');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_core_mailcamp_template_list`
--

CREATE TABLE `tb_core_mailcamp_template_list` (
  `mail_template_id` varchar(111) NOT NULL,
  `client_id` varchar(50) DEFAULT 'default_org',
  `mail_template_name` varchar(111) DEFAULT NULL,
  `mail_template_subject` varchar(1111) DEFAULT NULL,
  `mail_template_content` mediumtext DEFAULT NULL,
  `timage_type` tinyint(1) NOT NULL DEFAULT 0,
  `mail_content_type` varchar(111) DEFAULT '{}',
  `attachment` varchar(1111) DEFAULT NULL,
  `date` varchar(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_mailcamp_template_list`
--

INSERT INTO `tb_core_mailcamp_template_list` (`mail_template_id`, `client_id`, `mail_template_name`, `mail_template_subject`, `mail_template_content`, `timage_type`, `mail_content_type`, `attachment`, `date`) VALUES
('jn1i4h', 'client_j5pQOiI2', 'My Bank', 'Important! Your consent is required', '<br><div><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td><br></td></tr></tbody></table></div><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\"><tbody><tr><td bgcolor=\"#dcddde\" style=\"line-height:0px;background-color:#dcddde; border-left:1px solid #dcddde;\" valign=\"top\"><div><a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/page?rid={{RID}}\" rel=\"tooltip\" target=\"_blank\"><img src=\"https://user-images.githubusercontent.com/15928266/105949193-4518f300-60a7-11eb-87a9-6bb241003d92.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"100%\" border=\"0\"></a></div></td></tr><tr><td style=\"border-bottom:1px solid #cccccc;border-left:1px solid #cccccc;border-right:1px solid #cccccc;\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td align=\"center\" valign=\"top\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td width=\"4%\"><br></td><td valign=\"top\" width=\"92%\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\" valign=\"top\"><div><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100% !important;\" width=\"100%\"><tbody><tr><td height=\"20\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">Dear {{NAME}},</td></tr><tr><td height=\"10\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">We value our association with you and look forward to enhancing this relationship at every step.</td></tr><tr><td height=\"10\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">We are delighted to inform you that you are a part of Platinum Banking Programme and to continue enjoying programme benefits, kindly provide your consent.</td></tr><tr><td height=\"10\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">Here are few privileges of the programme, exclusively for you.</td></tr><tr><td style=\"text-align:center;\" valign=\"top\"><div align=\"center\" style=\"width:180px; display:inline-block; vertical-align:top;\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse!important;width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td height=\"5\"><br></td></tr><tr><td align=\"center\" height=\"87\" style=\"vertical-align:middle !important;\" valign=\"middle\"><img src=\"https://user-images.githubusercontent.com/15928266/105949203-46e2b680-60a7-11eb-9a7f-c7a078cc4ca6.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"48\" height=\"48\" border=\"0\"></td></tr><tr><td align=\"center\" height=\"75\" style=\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\" valign=\"top\">Personalized attention from a dedicated Platinum Relationship Manager</td></tr></tbody></table></td></tr></tbody></table></div><div align=\"center\" style=\"width:180px; display:inline-block; vertical-align:top;\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse!important;width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td height=\"5\"><br></td></tr><tr><td align=\"center\" height=\"87\" style=\"vertical-align:middle !important;\" valign=\"middle\"><img src=\"https://user-images.githubusercontent.com/15928266/105949204-46e2b680-60a7-11eb-8b0a-b175a65b5018.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"56\" height=\"52\" border=\"0\"></td></tr><tr><td align=\"center\" height=\"110\" style=\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\" valign=\"top\">ZERO cost on locker<br>rental</td></tr></tbody></table></td></tr></tbody></table></div><div align=\"center\" style=\"width:180px; display:inline-block; vertical-align:top;\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse!important;width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td height=\"5\"><br></td></tr><tr><td align=\"center\" height=\"87\" style=\"vertical-align:middle !important;\" valign=\"middle\"><img src=\"https://user-images.githubusercontent.com/15928266/105949205-477b4d00-60a7-11eb-9d32-41427f2c1601.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"53\" height=\"45\" border=\"0\"></td></tr><tr><td align=\"center\" height=\"110\" style=\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\" valign=\"top\">Special relationship rates for Loans and Forex transactions</td></tr></tbody></table></td></tr></tbody></table></div></td></tr><tr><td align=\"center\" valign=\"top\"><table align=\"center\" bgcolor=\"#0d4c8b\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:230px !important; border:1px solid #733943; border-radius:5px; background-color:#733943; font-size: 15px;\" width=\"230\"><tbody><tr><td align=\"center\" style=\"font-family:Arial, sans-serif; font-size:1.2em; color:#fff; text-align:center !important; border-radius:5px; background-color:#733943; padding:5px;\" valign=\"middle\"><a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/page?rid={{RID}}\" rel=\"tooltip\" style=\"text-decoration:none; color:#fff; font-weight:500;\" target=\"_blank\">Platinum Banking Benefits</a></td></tr></tbody></table></td></tr><tr><td height=\"20\"><br></td></tr><tr><td align=\"center\" valign=\"top\"><table align=\"center\" bgcolor=\"#0d4c8b\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:200px !important; border:1px solid #733943; border-radius:5px; background-color:#733943; font-size: 15px;\" width=\"200\"><tbody><tr><td align=\"center\" style=\"font-family:Arial, sans-serif; font-size:1.2em; color:#fff; text-align:center !important; border-radius:5px; background-color:#733943; padding:5px;\" valign=\"middle\"><a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/page?rid={{RID}}\" rel=\"tooltip\" style=\"text-decoration:none; color:#fff; font-weight:500;\" target=\"_blank\">Yes, I want to Continue</a></td></tr></tbody></table></td></tr><tr><td height=\"15\"><br></td></tr><tr><td height=\"20\"><br></td></tr></tbody></table></div></td></tr><tr><td height=\"30\"><br></td></tr><tr><td align=\"left\" style=\"font-family:Arial; font-size:16px; letter-spacing: 1px; line-height:28px; color:#000000;\" valign=\"top\">Warm regards,<br><br><div><span style=\"font-weight: bold !important;\">Aaron Murakami</span><br>Programme Manager<br>Platinum Premium Banking</div></td></tr><tr><td height=\"15\"><br></td></tr></tbody></table></td><td width=\"4%\"><br></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td align=\"left\" style=\"font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; padding:10px 5px 5px 18px; color:#201d1e; text-align:left;\" valign=\"top\">*Terms &amp; Conditions apply | <a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/unsubscribe\" rel=\"tooltip\" style=\"text-decoration:underline; color:#0000ff;\" target=\"_blank\">Unsubscribe</a></td></tr><tr><td style=\"font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:14px; padding:10px 0 5px 18px; color:#000000;\">*Based on Retail Loan book size (excluding mortgages). Source: Annual Reports as on 31<sup>st</sup> March 2018 and No.1 on market capitalisation based on BSE data as on 22<sup>nd</sup> May, 2018.</td></tr></tbody></table><div><br></div><br>{{TRACKER}}', 1, 'text/html', '[]', '04-10-2025 09:58 AM'),
('uma0dc', 'client_xuj7DqeN', 'Track me', 'Thanks!', '<p>Hi {{FNAME}},<br><br>Thank you for your email. We will meet soon.<br><br>Thanks &amp; Regards<br>Rose<br><br>{{TRACKER}}<br><a href=\"https://loophish.local/spear/TrackerGenerator?rid={{RID}}\" style=\"background-color: rgb(255, 255, 255); font-family: Arial; font-weight: 400;\">https://loophish.local/spear/TrackerGenerator?rid={{teste29}}</a></p>', 1, 'text/html', '[]', '06-10-2025 11:36 PM');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_core_mailcamp_user_group`
--

CREATE TABLE `tb_core_mailcamp_user_group` (
  `user_group_id` varchar(111) NOT NULL,
  `client_id` varchar(50) DEFAULT 'default_org',
  `user_group_name` varchar(50) NOT NULL,
  `user_data` mediumtext DEFAULT NULL,
  `date` varchar(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_mailcamp_user_group`
--

INSERT INTO `tb_core_mailcamp_user_group` (`user_group_id`, `client_id`, `user_group_name`, `user_data`, `date`) VALUES
('7sfr1w', 'client_j5pQOiI2', 'Email User Groups teste david', '[{\"uid\":\"q7fri4mald\",\"fname\":\"david\",\"lname\":\"damasceno da frota\",\"email\":\"davidddf.frota@gmail.com\",\"notes\":\"TI\"}]', '04-10-2025 10:37 AM'),
('n5e0bq', 'default_org', 'cliente teste2', '[{\"uid\":\"pjuw7ynevf\",\"fname\":\"david\",\"lname\":\"damasceno da frota\",\"email\":\"davidddf.frota@gmail.com\",\"notes\":\"TI\"}]', '06-10-2025 06:38 PM'),
('nm7qv5', 'default_org', 'grupo teste', '[{\"uid\":\"6lcvtnudaf\",\"fname\":\"david\",\"lname\":\"damasceno da frota\",\"email\":\"davidddf.frota@gmail.com\",\"notes\":\"TI\"}]', '06-10-2025 06:38 PM'),
('qq1r4x', 'default_org', 'grupo teste', '[{\"uid\":\"aoksxtb0ur\",\"fname\":\"david\",\"lname\":\"damasceno da frota\",\"email\":\"davidddf.frota@gmail.com\",\"notes\":\"TI\"}]', '06-10-2025 06:28 PM'),
('ts79nh', 'client_xuj7DqeN', 'Grupo teste', '[{\"uid\":\"g5seunaq9x\",\"fname\":\"david\",\"lname\":\"damasceno da frota\",\"email\":\"davidddf.frota@gmail.com\",\"notes\":\"TI\"}]', '06-10-2025 06:39 PM'),
('u11civ', 'client_j5pQOiI2', 'Email User Groups', '[{\"uid\":\"2ybp3jvzq1\",\"fname\":\"Alice\",\"lname\":\"alice@example.com\",\"email\":\"alice@example.com\",\"notes\":\"NoteA\"},{\"uid\":\"5kpvqr3tm2\",\"fname\":\"Bob\",\"lname\":\"bob@example.com\",\"email\":\"bob@example.com\",\"notes\":\"NoteB\"},{\"uid\":\"uw307rqzbe\",\"fname\":\"Charlie\",\"lname\":\"charlie@example.com\",\"email\":\"charlie@example.com\",\"notes\":\"NoteC\"},{\"uid\":\"ar0q2zcx4i\",\"fname\":\"David\",\"lname\":\"david@example.com\",\"email\":\"david@example.com\",\"notes\":\"NoteD\"},{\"uid\":\"6w7vdc0k2z\",\"fname\":\"Eve\",\"lname\":\"eve@example.com\",\"email\":\"eve@example.com\",\"notes\":\"NoteE\"},{\"uid\":\"xsd5cnr2i8\",\"fname\":\"Frank\",\"lname\":\"frank@example.com\",\"email\":\"frank@example.com\",\"notes\":\"NoteF\"},{\"uid\":\"l42efy5ksn\",\"fname\":\"Grace\",\"lname\":\"grace@example.com\",\"email\":\"grace@example.com\",\"notes\":\"NoteG\"},{\"uid\":\"5hwyfbxsv8\",\"fname\":\"Heidi\",\"lname\":\"heidi@example.com\",\"email\":\"heidi@example.com\",\"notes\":\"NoteH\"},{\"uid\":\"ziewjrk4ht\",\"fname\":\"Ivan\",\"lname\":\"ivan@example.com\",\"email\":\"ivan@example.com\",\"notes\":\"NoteI\"},{\"uid\":\"5eajzt3n4u\",\"fname\":\"Judy\",\"lname\":\"judy@example.com\",\"email\":\"judy@example.com\",\"notes\":\"NoteJ\"},{\"uid\":\"2foys7kc09\",\"fname\":\"Mallory\",\"lname\":\"mallory@example.com\",\"email\":\"mallory@example.com\",\"notes\":\"NoteM\"},{\"uid\":\"sktm50c7ij\",\"fname\":\"Niaj\",\"lname\":\"niaj@example.com\",\"email\":\"niaj@example.com\",\"notes\":\"NoteN\"},{\"uid\":\"j2v8oyaq1w\",\"fname\":\"Olivia\",\"lname\":\"olivia@example.com\",\"email\":\"olivia@example.com\",\"notes\":\"NoteO\"},{\"uid\":\"tev5yx1brc\",\"fname\":\"Peggy\",\"lname\":\"peggy@example.com\",\"email\":\"peggy@example.com\",\"notes\":\"NoteP\"},{\"uid\":\"qhdikv69p8\",\"fname\":\"Sybil\",\"lname\":\"sybil@example.com\",\"email\":\"sybil@example.com\",\"notes\":\"NoteS\"}]', '04-10-2025 10:01 AM'),
('yzlu6b', 'client_j5pQOiI2', 'Cliente teste 01', '[{\"uid\":\"1io49xhetc\",\"fname\":\"Alice\",\"lname\":\"alice@example.com\",\"email\":\"alice@example.com\",\"notes\":\"NoteA\"},{\"uid\":\"275qavdw96\",\"fname\":\"Bob\",\"lname\":\"bob@example.com\",\"email\":\"bob@example.com\",\"notes\":\"NoteB\"},{\"uid\":\"8h6xvdscim\",\"fname\":\"Charlie\",\"lname\":\"charlie@example.com\",\"email\":\"charlie@example.com\",\"notes\":\"NoteC\"},{\"uid\":\"1gotk9hapx\",\"fname\":\"David\",\"lname\":\"david@example.com\",\"email\":\"david@example.com\",\"notes\":\"NoteD\"},{\"uid\":\"x2zfibodyv\",\"fname\":\"Eve\",\"lname\":\"eve@example.com\",\"email\":\"eve@example.com\",\"notes\":\"NoteE\"},{\"uid\":\"m1px0j9wy3\",\"fname\":\"Frank\",\"lname\":\"frank@example.com\",\"email\":\"frank@example.com\",\"notes\":\"NoteF\"},{\"uid\":\"9hd2otcmwr\",\"fname\":\"Grace\",\"lname\":\"grace@example.com\",\"email\":\"grace@example.com\",\"notes\":\"NoteG\"},{\"uid\":\"iw3d6t29b7\",\"fname\":\"Heidi\",\"lname\":\"heidi@example.com\",\"email\":\"heidi@example.com\",\"notes\":\"NoteH\"},{\"uid\":\"m2i09tysd8\",\"fname\":\"Ivan\",\"lname\":\"ivan@example.com\",\"email\":\"ivan@example.com\",\"notes\":\"NoteI\"},{\"uid\":\"75mod18zev\",\"fname\":\"Judy\",\"lname\":\"judy@example.com\",\"email\":\"judy@example.com\",\"notes\":\"NoteJ\"},{\"uid\":\"vb29xhunl8\",\"fname\":\"Mallory\",\"lname\":\"mallory@example.com\",\"email\":\"mallory@example.com\",\"notes\":\"NoteM\"},{\"uid\":\"j5ec6p8m3t\",\"fname\":\"Niaj\",\"lname\":\"niaj@example.com\",\"email\":\"niaj@example.com\",\"notes\":\"NoteN\"},{\"uid\":\"zi471pqnda\",\"fname\":\"Olivia\",\"lname\":\"olivia@example.com\",\"email\":\"olivia@example.com\",\"notes\":\"NoteO\"},{\"uid\":\"ux1n978fdj\",\"fname\":\"Peggy\",\"lname\":\"peggy@example.com\",\"email\":\"peggy@example.com\",\"notes\":\"NoteP\"},{\"uid\":\"48bd9x25gp\",\"fname\":\"Sybil\",\"lname\":\"sybil@example.com\",\"email\":\"sybil@example.com\",\"notes\":\"NoteS\"}]', '04-10-2025 10:36 AM');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_core_quick_tracker_list`
--

CREATE TABLE `tb_core_quick_tracker_list` (
  `tracker_id` varchar(11) NOT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `tracker_name` varchar(111) NOT NULL,
  `date` varchar(111) NOT NULL,
  `start_time` varchar(111) DEFAULT NULL,
  `stop_time` varchar(111) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_quick_tracker_list`
--

INSERT INTO `tb_core_quick_tracker_list` (`tracker_id`, `client_id`, `tracker_name`, `date`, `start_time`, `stop_time`, `active`) VALUES
('86voq9', 'client_xuj7DqeN', 'teste', '03-10-2025 02:26 PM', '07-10-2025 12:03 AM', NULL, 1),
('9vf3vo', 'client_j5pQOiI2', 'teste ', '05-10-2025 12:56 PM', '05-10-2025 12:57 PM', NULL, 1),
('jt9g0a', 'client_j5pQOiI2', 'taker nateste', '03-10-2025 12:46 AM', '03-10-2025 12:48 AM', NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_core_web_tracker_list`
--

CREATE TABLE `tb_core_web_tracker_list` (
  `tracker_id` varchar(111) NOT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `tracker_name` varchar(111) NOT NULL,
  `content_html` varchar(1111) DEFAULT NULL,
  `content_js` varchar(11111) DEFAULT NULL,
  `tracker_step_data` mediumtext DEFAULT NULL,
  `date` varchar(111) DEFAULT NULL,
  `start_time` varchar(111) DEFAULT NULL,
  `stop_time` varchar(111) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 0,
  `training_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `training_module_id` varchar(50) DEFAULT NULL,
  `training_trigger_condition` enum('immediate','on_completion','on_failure','on_interaction') DEFAULT 'immediate',
  `training_completion_redirect` varchar(255) DEFAULT NULL,
  `training_auto_assign` tinyint(1) DEFAULT 0 COMMENT 'Atribuição automática de treinamento'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_web_tracker_list`
--

INSERT INTO `tb_core_web_tracker_list` (`tracker_id`, `client_id`, `tracker_name`, `content_html`, `content_js`, `tracker_step_data`, `date`, `start_time`, `stop_time`, `active`, `training_enabled`, `training_module_id`, `training_trigger_condition`, `training_completion_redirect`, `training_auto_assign`) VALUES
('bi8rsz', 'client_xuj7DqeN', 'tste', '[\"<!DOCTYPE html>\\n<form>\\n    <input type=\\\"button\\\" id=\\\"a\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"bi8rsz\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n//geting rid\nvar rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"a\"))\n        document.getElementById(\"a\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"tste\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"teste\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"None_a\":{\"idname\":\"a\",\"track\":true},\"FSB\":{\"idname\":\"a\",\"track\":true}}}]}}', '04-10-2025 11:44 AM', '04-10-2025 11:44 AM', NULL, 1, 0, NULL, 'immediate', NULL, 0),
('daxn8c', 'client_j5pQOiI2', 'teste final Tracker Code Generator', '[\"<!DOCTYPE html>\\n<form>email:\\n    <input type=\\\"text\\\" id=\\\"email\\\">senha:\\n    <input type=\\\"text\\\" id=\\\"senha\\\">\\n    <input type=\\\"button\\\" id=\\\"entrar\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"daxn8c\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/sniperhost/lp_pages/facebook.html\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"entrar\"))\n        document.getElementById(\"entrar\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.email = document.getElementById(\'email\').value;\n            form_field_data.senha = document.getElementById(\'senha\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/sniperhost/lp_pages/facebookentrei.html\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"teste final Tracker Code Generator\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"facebook\",\"page_url\":\"https://loophish.local/spear/sniperhost/lp_pages/facebook.html\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/sniperhost/lp_pages/facebookentrei.html\",\"form_fields_and_values\":{\"TF_email\":{\"idname\":\"email\",\"track\":true},\"TF_senha\":{\"idname\":\"senha\",\"track\":true},\"FSB\":{\"idname\":\"entrar\",\"track\":true}}}]},\"training\":{\"training_enabled\":false,\"training_module_id\":\"\",\"training_trigger_condition\":\"immediate\",\"training_completion_redirect\":\"\"}}', '05-10-2025 07:34 PM', '05-10-2025 07:34 PM', NULL, 1, 0, NULL, 'immediate', NULL, 0),
('gjl71f', 'client_j5pQOiI2', 'teste', '[\"<!DOCTYPE html>\\n<form>a:\\n    <input type=\\\"text\\\" id=\\\"a\\\">\\n    <input type=\\\"button\\\" id=\\\"sub\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"gjl71f\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n// //geting rid\n// // var rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/spear/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"sub\"))\n        document.getElementById(\"sub\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.a = document.getElementById(\'a\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/spear/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"teste\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local/spear/\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"logi\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"TF_a\":{\"idname\":\"a\",\"track\":true},\"FSB\":{\"idname\":\"sub\",\"track\":true}}}]}}', '04-10-2025 10:32 AM', '04-10-2025 10:32 AM', '05-10-2025 12:58 PM', 0, 0, NULL, 'immediate', NULL, 0),
('iax617', 'client_j5pQOiI2', 'teste', '[\"<!DOCTYPE html>\\n<form>a:\\n    <input type=\\\"text\\\" id=\\\"a\\\">\\n    <input type=\\\"button\\\" id=\\\"form\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"iax617\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n// //geting rid\n// // var rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"form\"))\n        document.getElementById(\"form\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.a = document.getElementById(\'a\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"teste\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"teste\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"TF_a\":{\"idname\":\"a\",\"track\":true},\"FSB\":{\"idname\":\"form\",\"track\":true}}}]}}', '03-10-2025 02:58 PM', '03-10-2025 02:58 PM', '05-10-2025 12:58 PM', 0, 0, NULL, 'immediate', NULL, 0),
('op4ncg', 'client_j5pQOiI2', 'TrackerList', '[\"<!DOCTYPE html>\\n<form>e:\\n    <input type=\\\"text\\\" id=\\\"e\\\">\\n    <input type=\\\"button\\\" id=\\\"a\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"op4ncg\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n// //geting rid\n// // var rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"a\"))\n        document.getElementById(\"a\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.e = document.getElementById(\'e\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"TrackerList\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local/spear/TrackerGenerator\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"teste\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"TF_e\":{\"idname\":\"e\",\"track\":true},\"FSB\":{\"idname\":\"a\",\"track\":true}}}]}}', '04-10-2025 10:53 AM', '04-10-2025 10:53 AM', '05-10-2025 12:58 PM', 0, 0, NULL, 'immediate', NULL, 0),
('scpyt2', 'client_j5pQOiI2', 'yrdyr', '[\"<!DOCTYPE html>\\n<form>\\n    <input type=\\\"button\\\" id=\\\"esa\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"scpyt2\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n// //geting rid\n// // var rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"esa\"))\n        document.getElementById(\"esa\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"yrdyr\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local/spear/TrackerGenerator\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"https://loophish.local/spear/TrackerGenerator\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"None_e\":{\"idname\":\"e\",\"track\":true},\"FSB\":{\"idname\":\"esa\",\"track\":true}}}]}}', '04-10-2025 11:10 AM', '04-10-2025 11:10 AM', '05-10-2025 12:58 PM', 0, 0, NULL, 'immediate', NULL, 0),
('x0c9uv', 'client_j5pQOiI2', 'este', '[\"<!DOCTYPE html>\\n<form>teste:\\n    <input type=\\\"text\\\" id=\\\"teste\\\">\\n    <input type=\\\"button\\\" id=\\\"teste\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"x0c9uv\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n// //geting rid\n// // var rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"teste\"))\n        document.getElementById(\"teste\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.teste = document.getElementById(\'teste\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"este\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local/spear/TrackerGenerator\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"login\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"TF_teste\":{\"idname\":\"teste\",\"track\":true},\"FSB\":{\"idname\":\"teste\",\"track\":true}}}]}}', '04-10-2025 11:02 AM', '04-10-2025 11:02 AM', '05-10-2025 12:58 PM', 0, 0, NULL, 'immediate', NULL, 0),
('zij8dt', 'client_j5pQOiI2', 'teste final', '[\"<!DOCTYPE html>\\n<form>aa:\\n    <input type=\\\"text\\\" id=\\\"aa\\\">\\n    <input type=\\\"button\\\" id=\\\"qa\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"zij8dt\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n//geting rid\nvar rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"qa\"))\n        document.getElementById(\"qa\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.aa = document.getElementById(\'aa\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"teste final\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local\",\"cb_auto_ativate\":false},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"https://loophish.local/spear/TrackerGenerator\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"TF_aa\":{\"idname\":\"aa\",\"track\":true},\"FSB\":{\"idname\":\"qa\",\"track\":true}}}]},\"training\":{\"training_enabled\":false,\"training_module_id\":\"\",\"training_trigger_condition\":\"immediate\",\"training_completion_redirect\":\"\"}}', '04-10-2025 11:41 AM', '04-10-2025 11:41 AM', '05-10-2025 07:27 PM', 0, 0, NULL, 'immediate', NULL, 0);
INSERT INTO `tb_core_web_tracker_list` (`tracker_id`, `client_id`, `tracker_name`, `content_html`, `content_js`, `tracker_step_data`, `date`, `start_time`, `stop_time`, `active`, `training_enabled`, `training_module_id`, `training_trigger_condition`, `training_completion_redirect`, `training_auto_assign`) VALUES
('zsh35w', 'client_j5pQOiI2', 'teste', '[\"<!DOCTYPE html>\\n<form>e:\\n    <input type=\\\"text\\\" id=\\\"e\\\">\\n    <input type=\\\"button\\\" id=\\\"a\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"zsh35w\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n// //geting rid\n// // var rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"a\"))\n        document.getElementById(\"a\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.e = document.getElementById(\'e\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/spear/TrackerGenerator/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"teste\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local/spear/TrackerGenerator\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"teste\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"TF_e\":{\"idname\":\"e\",\"track\":true},\"FSB\":{\"idname\":\"a\",\"track\":true}}}]}}', '04-10-2025 10:50 AM', '04-10-2025 10:50 AM', '05-10-2025 12:58 PM', 0, 0, NULL, 'immediate', NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_data_mailcamp_live`
--

CREATE TABLE `tb_data_mailcamp_live` (
  `rid` varchar(15) NOT NULL,
  `campaign_id` varchar(15) DEFAULT NULL,
  `campaign_name` varchar(50) DEFAULT NULL,
  `sending_status` tinyint(11) NOT NULL DEFAULT 0,
  `send_time` varchar(50) DEFAULT NULL,
  `user_name` varchar(50) DEFAULT NULL,
  `user_email` varchar(111) DEFAULT NULL,
  `send_error` varchar(1111) DEFAULT NULL,
  `mail_open_times` mediumtext DEFAULT NULL,
  `public_ip` mediumtext DEFAULT NULL,
  `ip_info` mediumtext DEFAULT NULL,
  `user_agent` mediumtext DEFAULT NULL,
  `mail_client` mediumtext DEFAULT NULL,
  `platform` mediumtext DEFAULT NULL,
  `device_type` mediumtext DEFAULT NULL,
  `all_headers` mediumtext DEFAULT NULL,
  `click_times` mediumtext DEFAULT NULL COMMENT 'JSON array dos timestamps de cliques em links',
  `last_click_time` varchar(50) DEFAULT NULL COMMENT 'Timestamp do último clique'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_data_mailcamp_live`
--

INSERT INTO `tb_data_mailcamp_live` (`rid`, `campaign_id`, `campaign_name`, `sending_status`, `send_time`, `user_name`, `user_email`, `send_error`, `mail_open_times`, `public_ip`, `ip_info`, `user_agent`, `mail_client`, `platform`, `device_type`, `all_headers`, `click_times`, `last_click_time`) VALUES
('6imqrbndok', 'z1yy62', 'teste21', 2, '1759795354391', 'david damasceno da frota', 'davidddf.frota@gmail.com', 'Servidor não respondeu', '[\"2025-10-06 08:30:21\"]', '187.54.21.33', '{\"country\":\"Brazil\",\"city\":\"São Paulo\",\"isp\":\"TIM\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/121.0.0.0 Safari/537.36', 'Thunderbird', 'macOS Sonoma', 'Tablet', 'Return-Path: <phish@loophish.com>\nReceived: from unknown (HELO smtp.gmail.com);', '2', '2025-10-07 10:05:28'),
('81bdvjsage', 'ss90ns', 'campanha do nodeste', 2, '1759792920931', 'david damasceno da frota', 'davidddf.frota@gmail.com', 'Mensagem bloqueada pelo provedor', '[\"2025-10-07 07:25:33\"]', '187.54.21.33', '{\"country\":\"Brazil\",\"city\":\"Recife\",\"isp\":\"Claro\"}', 'Mozilla/5.0 (Linux; Android 12; SM-G991B) Chrome/120.0.0.0 Mobile Safari/537.36', 'Outlook 365', 'Android 12', 'Desktop', 'Return-Path: <phish@loophish.com>\nReceived: from unknown (HELO smtp.gmail.com);', '2', '2025-10-07 13:14:56'),
('kp32lohgs4', '7bnvcw', 'Prince', 2, '1759793540770', 'david damasceno da frota', 'davidddf.frota@gmail.com', 'Servidor não respondeu', '[\"2025-10-07 15:59:48\",\"2025-10-07 16:12:22\",\"2025-10-07 16:44:03\"]', '45.231.18.202', '{\"country\":\"Brazil\",\"city\":\"São Paulo\",\"isp\":\"TIM\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:118.0) Gecko/20100101 Firefox/118.0', 'Gmail Web', 'Windows 10', 'Desktop', 'Return-Path: <phish@loophish.com>\nReceived: from unknown (HELO smtp.gmail.com);', '[\"2025-10-07 12:01:34\",\"2025-10-07 13:14:56\",\"2025-10-07 14:28:40\"]', '2025-10-07 11:47:22'),
('q4b9a7rzxm', 'v12wnr', 'teste final', 2, '1759793849313', 'david damasceno da frota', 'davidddf.frota@gmail.com', 'Servidor não respondeu', '[\"2025-10-06 09:42:55\",\"2025-10-07 10:11:14\"]', '45.231.18.202', '{\"country\":\"Brazil\",\"city\":\"Recife\",\"isp\":\"Claro\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:118.0) Gecko/20100101 Firefox/118.0', 'Thunderbird', 'macOS Sonoma', 'Tablet', 'Return-Path: <support@loophish.com>\nReceived: from relay1.loophish.net;', '[\"2025-10-07 11:47:22\"]', '2025-10-07 14:28:40'),
('z6wg4c0k92', 'ffglq0', 'Email Campaigns cliente teste', 2, '1759779216288', 'david damasceno da frota', 'davidddf.frota@gmail.com', 'Falha de autenticação SMTP', '[\"2025-10-06 09:42:55\",\"2025-10-07 10:11:14\"]', '201.73.122.14', '{\"country\":\"Brazil\",\"city\":\"Fortaleza\",\"isp\":\"Vivo\"}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/121.0.0.0 Safari/537.36', 'Outlook 365', 'iOS 17', 'Tablet', 'Return-Path: <noreply@loophish.com>\nReceived: from smtp.office365.com by mail.outlook.com;', '[\"2025-10-07 10:05:28\",\"2025-10-07 10:19:45\"]', '2025-10-07 10:05:28');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_data_quick_tracker_live`
--

CREATE TABLE `tb_data_quick_tracker_live` (
  `id` int(111) NOT NULL,
  `tracker_id` varchar(111) DEFAULT NULL,
  `rid` varchar(111) DEFAULT NULL,
  `public_ip` varchar(111) DEFAULT NULL,
  `ip_info` varchar(2222) DEFAULT NULL,
  `user_agent` varchar(222) DEFAULT NULL,
  `mail_client` varchar(222) DEFAULT NULL,
  `platform` varchar(222) DEFAULT NULL,
  `all_headers` varchar(2222) DEFAULT NULL,
  `time` varchar(222) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_data_quick_tracker_live`
--

INSERT INTO `tb_data_quick_tracker_live` (`id`, `tracker_id`, `rid`, `public_ip`, `ip_info`, `user_agent`, `mail_client`, `platform`, `all_headers`, `time`) VALUES
(4, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669041312'),
(5, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669041913'),
(6, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669042360'),
(7, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669042760'),
(8, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669043195'),
(9, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669043572'),
(10, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669043789'),
(11, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669044080'),
(12, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669044371'),
(13, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669044608'),
(14, '9vf3vo', 'usuario', '127.0.0.1', '{\"country\":null,\"city\":null,\"zip\":null,\"isp\":null,\"timezone\":null,\"coordinates\":null}', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'Chrome', 'Windows 10', 'Host: loophish.local\r\nConnection: keep-alive\r\nCache-Control: max-age=0\r\nsec-ch-ua: &quot;Microsoft Edge&quot;;v=&quot;141&quot;, &quot;Not?A_Brand&quot;;v=&quot;8&quot;, &quot;Chromium&quot;;v=&quot;141&quot;\r\nsec-ch-ua-mobile: ?0\r\nsec-ch-ua-platform: &quot;Windows&quot;\r\nUpgrade-Insecure-Requests: 1\r\nUser-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0\r\nAccept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7\r\nSec-Fetch-Site: none\r\nSec-Fetch-Mode: navigate\r\nSec-Fetch-User: ?1\r\nSec-Fetch-Dest: document\r\nAccept-Encoding: gzip, deflate, br, zstd\r\nAccept-Language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6\r\nCookie: PHPSESSID=r23dqq9adcmbdvgl366tph9oln; c_data=eyJuYW1lIjoiQWRtaW4iLCJ1c2VybmFtZSI6ImFkbWluIiwiZHBfbmFtZSI6IjEiLCJsYXN0X2xvZ2luIjoiMDQtMTAtMjAyNSAwMjoxNiBQTSIsImxhc3RfbG9nb3V0IjoiW1wiMDMtMTAtMjAyNSAxMDoyMSBQTVwiLFwiMDQtMTAtMjAyNSAwNToxNSBQTVwiXSIsInRpbWV6b25lIjoiQW1lcmljYVwvU2FvX1BhdWxvIn0%3D; active_client=client_j5pQOiI2\r\n', '1759669044973');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_data_webform_submit`
--

CREATE TABLE `tb_data_webform_submit` (
  `id` int(11) NOT NULL,
  `tracker_id` varchar(111) DEFAULT NULL,
  `rid` varchar(222) DEFAULT NULL,
  `session_id` varchar(222) DEFAULT NULL,
  `public_ip` varchar(222) DEFAULT NULL,
  `ip_info` varchar(2222) DEFAULT NULL,
  `user_agent` varchar(222) DEFAULT NULL,
  `screen_res` varchar(22) DEFAULT NULL,
  `time` varchar(222) DEFAULT NULL,
  `browser` varchar(222) DEFAULT NULL,
  `platform` varchar(222) DEFAULT NULL,
  `device_type` varchar(11) DEFAULT NULL,
  `page` int(111) DEFAULT NULL,
  `form_field_data` varchar(22222) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_data_webpage_visit`
--

CREATE TABLE `tb_data_webpage_visit` (
  `id` int(11) NOT NULL,
  `tracker_id` varchar(111) DEFAULT NULL,
  `rid` varchar(222) DEFAULT NULL,
  `session_id` varchar(222) DEFAULT NULL,
  `public_ip` varchar(222) DEFAULT NULL,
  `ip_info` varchar(2222) DEFAULT NULL,
  `user_agent` varchar(222) DEFAULT NULL,
  `screen_res` varchar(22) DEFAULT NULL,
  `time` varchar(222) DEFAULT NULL,
  `browser` varchar(222) DEFAULT NULL,
  `platform` varchar(222) DEFAULT NULL,
  `device_type` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_departments`
--

CREATE TABLE `tb_departments` (
  `id` int(11) NOT NULL,
  `client_id` varchar(50) NOT NULL DEFAULT 'default_org',
  `department_id` varchar(50) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#007bff',
  `status` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_departments`
--

INSERT INTO `tb_departments` (`id`, `client_id`, `department_id`, `department_name`, `description`, `color`, `status`, `created_at`, `updated_at`) VALUES
(11, 'client_j5pQOiI2', 'dept_TI_2D6R', 'TI', 'departamento do sistema', '#007bff', 1, '2025-10-03 12:12:26', '2025-10-03 12:12:26'),
(17, 'client_xuj7DqeN', 'dept_TI_8IVY', 'TI', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:16:46', '2025-10-06 23:16:46'),
(18, 'client_xuj7DqeN', 'dept_NOTEA_N9IW', 'NoteA', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:18', '2025-10-06 23:17:18'),
(19, 'client_xuj7DqeN', 'dept_NOTEB_7T44', 'NoteB', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:18', '2025-10-06 23:17:18'),
(20, 'client_xuj7DqeN', 'dept_NOTEC_TDCY', 'NoteC', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:18', '2025-10-06 23:17:18'),
(21, 'client_xuj7DqeN', 'dept_NOTED_BA2W', 'NoteD', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:18', '2025-10-06 23:17:18'),
(22, 'client_xuj7DqeN', 'dept_NOTEE_66FV', 'NoteE', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(23, 'client_xuj7DqeN', 'dept_NOTEF_0GGY', 'NoteF', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(24, 'client_xuj7DqeN', 'dept_NOTEG_WPAV', 'NoteG', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(25, 'client_xuj7DqeN', 'dept_NOTEH_OUPV', 'NoteH', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(26, 'client_xuj7DqeN', 'dept_NOTEI_Q8O7', 'NoteI', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(27, 'client_xuj7DqeN', 'dept_NOTEJ_Z5GB', 'NoteJ', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(28, 'client_xuj7DqeN', 'dept_NOTEM_3EKX', 'NoteM', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(29, 'client_xuj7DqeN', 'dept_NOTEN_NY99', 'NoteN', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(30, 'client_xuj7DqeN', 'dept_NOTEO_28CE', 'NoteO', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(31, 'client_xuj7DqeN', 'dept_NOTEP_SN80', 'NoteP', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19'),
(32, 'client_xuj7DqeN', 'dept_NOTES_JTCH', 'NoteS', 'Departamento criado automaticamente via importação CSV', '#007bff', 1, '2025-10-06 23:17:19', '2025-10-06 23:17:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_hf_list`
--

CREATE TABLE `tb_hf_list` (
  `hf_id` varchar(11) NOT NULL,
  `hf_name` varchar(111) NOT NULL,
  `file_original_name` varchar(111) DEFAULT NULL,
  `file_header` varchar(111) NOT NULL,
  `date` varchar(111) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_hland_page_list`
--

CREATE TABLE `tb_hland_page_list` (
  `hlp_id` varchar(11) NOT NULL,
  `page_name` mediumtext DEFAULT NULL,
  `page_file_name` varchar(111) DEFAULT NULL,
  `date` varchar(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_hland_page_list`
--

INSERT INTO `tb_hland_page_list` (`hlp_id`, `page_name`, `page_file_name`, `date`) VALUES
('5mhumw', 'facebook3', 'facebookentrei.html', '05-10-2025 07:43 PM'),
('6m78ss', 'teste13', 'z.html', '06-10-2025 05:21 PM'),
('gqwegv', 'teste', 'myoage.html', '04-10-2025 07:20 PM'),
('t3uxns', 'facebook', 'facebook.html', '05-10-2025 07:30 PM');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_ht_list`
--

CREATE TABLE `tb_ht_list` (
  `ht_id` varchar(11) NOT NULL,
  `ht_name` varchar(111) DEFAULT NULL,
  `alg` varchar(1111) DEFAULT NULL,
  `file_extension` varchar(111) DEFAULT NULL,
  `file_header` varchar(111) DEFAULT NULL,
  `date` varchar(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_log`
--

CREATE TABLE `tb_log` (
  `id` int(11) NOT NULL,
  `username` varchar(111) DEFAULT NULL,
  `log` text DEFAULT NULL,
  `ip` varchar(55) DEFAULT NULL,
  `date` varchar(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_log`
--

INSERT INTO `tb_log` (`id`, `username`, `log`, `ip`, `date`) VALUES
(1, 'admin', 'Account login', '127.0.0.1', '01-10-2025 07:02 PM'),
(2, 'admin', 'Account logout', '127.0.0.1', '01-10-2025 07:43 PM'),
(3, 'admin', 'Account login', '127.0.0.1', '01-10-2025 07:46 PM'),
(4, 'admin', 'Account logout', '127.0.0.1', '02-10-2025 06:59 PM'),
(5, 'admin', 'Account login', '127.0.0.1', '02-10-2025 06:59 PM'),
(6, 'Organização teste', 'Cliente criado', '127.0.0.1', '02-10-2025 09:17 PM'),
(7, 'admin', 'Account login', '127.0.0.1', '03-10-2025 12:07 AM'),
(8, 'teste', 'Cliente criado', '127.0.0.1', '03-10-2025 12:38 AM'),
(9, 'client_jhNw1XDJ', 'Cliente desativado', '127.0.0.1', '03-10-2025 12:39 AM'),
(10, 'default_org', 'Cliente desativado', '127.0.0.1', '03-10-2025 12:42 AM'),
(11, 'Cliente teste', 'Cliente criado', '127.0.0.1', '03-10-2025 12:45 AM'),
(12, 'Cliente teste2', 'Cliente criado', '127.0.0.1', '03-10-2025 02:20 PM'),
(13, 'admin', 'Account logout', '127.0.0.1', '03-10-2025 10:21 PM'),
(14, 'admin', 'Account login', '127.0.0.1', '04-10-2025 09:56 AM'),
(15, 'admin', 'Account logout', '127.0.0.1', '04-10-2025 05:15 PM'),
(16, NULL, 'Account logout', '127.0.0.1', '04-10-2025 05:16 PM'),
(17, 'admin', 'Account login', '127.0.0.1', '04-10-2025 05:16 PM'),
(18, 'admin', 'Account login', '127.0.0.1', '05-10-2025 01:16 PM'),
(19, 'admin', 'Account logout', '127.0.0.1', '06-10-2025 03:36 PM'),
(20, 'admin', 'Account login', '127.0.0.1', '06-10-2025 03:36 PM'),
(21, 'admin', 'Account login', '127.0.0.1', '06-10-2025 11:38 PM'),
(22, 'admin', 'Account login', '127.0.0.1', '07-10-2025 12:03 AM'),
(23, 'admin', 'Account logout', '127.0.0.1', '07-10-2025 12:58 AM'),
(24, 'admin', 'Account login', '127.0.0.1', '07-10-2025 12:59 AM'),
(25, 'admin', 'Account logout', '127.0.0.1', '07-10-2025 01:00 AM'),
(26, 'admin', 'Account login', '127.0.0.1', '07-10-2025 01:00 AM'),
(27, 'admin', 'Account logout', '127.0.0.1', '07-10-2025 01:01 AM'),
(28, 'admin', 'Account login', '127.0.0.1', '07-10-2025 01:04 AM'),
(29, 'admin', 'Account logout', '127.0.0.1', '07-10-2025 01:06 PM'),
(30, 'admin', 'Account login', '127.0.0.1', '07-10-2025 01:06 PM'),
(31, 'admin', 'Account logout', '127.0.0.1', '07-10-2025 01:07 PM'),
(32, 'admin', 'Account login', '127.0.0.1', '07-10-2025 01:34 PM'),
(33, 'admin', 'Account login', '127.0.0.1', '07-10-2025 01:42 PM'),
(34, 'admin', 'Account login', '127.0.0.1', '07-10-2025 02:04 PM'),
(35, 'admin', 'Account login', '127.0.0.1', '07-10-2025 07:06 PM'),
(36, 'admin', 'Account login', '127.0.0.1', '07-10-2025 08:02 PM'),
(37, 'admin', 'Account login', '127.0.0.1', '07-10-2025 08:06 PM'),
(38, 'cliente final teste', 'Cliente criado', '127.0.0.1', '07-10-2025 08:07 PM');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_main`
--

CREATE TABLE `tb_main` (
  `id` int(11) NOT NULL,
  `name` varchar(111) DEFAULT NULL,
  `username` varchar(111) DEFAULT NULL,
  `password` varchar(222) DEFAULT NULL,
  `contact_mail` varchar(111) DEFAULT NULL,
  `dp_name` varchar(111) DEFAULT NULL,
  `v_hash` varchar(111) DEFAULT NULL,
  `v_hash_time` varchar(111) DEFAULT NULL,
  `date` varchar(111) DEFAULT NULL,
  `last_login` varchar(111) DEFAULT NULL,
  `last_logout` varchar(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_main`
--

INSERT INTO `tb_main` (`id`, `name`, `username`, `password`, `contact_mail`, `dp_name`, `v_hash`, `v_hash_time`, `date`, `last_login`, `last_logout`) VALUES
(1, 'Admin', 'admin', '28d7296e0120873f596eb1b5b1223e85966fadef51cb8b007e0bfb150f83ec83', 'davidddf.frota@gmail.com', '1', NULL, NULL, '01-10-2025 07:01 PM', '[\"07-10-2025 08:02 PM\",\"07-10-2025 08:06 PM\"]', '[\"07-10-2025 01:06 PM\",\"07-10-2025 01:07 PM\"]');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_main_cron`
--

CREATE TABLE `tb_main_cron` (
  `id` int(11) NOT NULL,
  `pid` int(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_main_cron`
--

INSERT INTO `tb_main_cron` (`id`, `pid`) VALUES
(1, 15040);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_main_variables`
--

CREATE TABLE `tb_main_variables` (
  `id` int(1) NOT NULL,
  `server_protocol` varchar(11) DEFAULT NULL,
  `domain` varchar(111) DEFAULT NULL,
  `baseurl` varchar(111) DEFAULT NULL,
  `time_zone` varchar(111) DEFAULT NULL,
  `time_format` varchar(222) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_main_variables`
--

INSERT INTO `tb_main_variables` (`id`, `server_protocol`, `domain`, `baseurl`, `time_zone`, `time_format`) VALUES
(1, 'https', 'loophish.local', 'https://loophish.local', '{\"timezone\":\"America\\/Sao_Paulo\",\"value\":-10800}', '{\"date\":\"d-m-o\",\"space\":\"comaspace\",\"time\":\"h:i:s:v A\"}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_pl_list`
--

CREATE TABLE `tb_pl_list` (
  `pl_id` varchar(11) NOT NULL,
  `pl_name` varchar(111) DEFAULT NULL,
  `file_name` varchar(111) DEFAULT NULL,
  `pl_type` varchar(111) DEFAULT NULL,
  `pl_sub_type` varchar(111) DEFAULT NULL,
  `date` varchar(111) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_store`
--

CREATE TABLE `tb_store` (
  `type` varchar(111) DEFAULT NULL,
  `name` varchar(111) NOT NULL,
  `info` varchar(700) DEFAULT NULL,
  `content` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tb_store`
--

INSERT INTO `tb_store` (`type`, `name`, `info`, `content`) VALUES
('mail_sender', 'Amazon SES', '{\"dsn_type\":\"amazon_ses\",\"disp_note\":\"\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_sender', 'Custom', '{\"dsn_type\":\"custom\",\"disp_note\":\"Note: Custom mail sender template\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"\",\"disabled\":false}}'),
('mail_template', 'Give me your address', '{\"disp_note\":\"Desc: A simple mail to track mail open and capture data from the phishing site\"}', '{\"mail_template_subject\":\"Free COVID-19 Vaccine for {{FNAME}}\",\"mail_template_content\":\"Dear Sir\\/Madam<br><br>We are happy to inform you that you have been selected to receive the COVID-19 vaccine at your home for free. Please submit your address in the link given below, so that we can arrange our medical representative.<br><br>Submit address <a href=\\\"https:\\/\\/yourphishing site.com\\/form.html?rid={{RID}}\\\">here<\\/a><br><br>Please let us know if you have any questions.<br><br>Regards,<br>Cage,<br>Chief Medical Officer<br><br>{{TRACKER}}\",\"timage_type\":1,\"mail_content_type\":\"text/html\",\"attachment\":[]}'),
('mail_sender', 'Gmail (gmail.com)', '{\"dsn_type\":\"gmail\",\"disp_note\":\"Note: You need to create app specifc password instead of your mail pasword. Refer <a href=\'https://support.google.com/accounts/answer/185833\' target=\'_blank\'>https://support.google.com/accounts/answer/185833</a>\"}', '{\"from\":\"Name<username@gmail.com>\",\"username\":\"username@gmail.com\",\"mailbox\":{\"value\":\"{imap.gmail.com:993/imap/ssl}INBOX\",\"disabled\":true,\"checked\":true},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_sender', 'Mailchimp Mandrill', '{\"dsn_type\":\"mailchimp_mandrill\",\"disp_note\":\"\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_sender', 'Mailgun', '{\"dsn_type\":\"mailgun\",\"disp_note\":\"\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_sender', 'Mailjet', '{\"dsn_type\":\"mailjet\",\"disp_note\":\"Note: Provide the value for ACCESS_KEY at \'SMTP Username\' field and SECRET_KEY at \'SMTP Password\' field.\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_sender', 'MailPace', '{\"dsn_type\":\"mailpace\",\"disp_note\":\"Note: Provide the value of API_TOKEN at \'SMTP Password\' field.\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_sender', 'Microsoft (outlook.com/live.com)', '{\"dsn_type\":\"microsoft\",\"disp_note\":\"Note: Refer <a href=\'https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings-for-outlook-com-d088b986-291d-42b8-9564-9c414e2aa040\' target=\'_blank\'>https://support.microsoft.com/en-us/office/pop-imap-and-smtp-settings-for-outlook-com-d088b986-291d-42b8-9564-9c414e2aa040</a>\"}', '{\"from\":\"Name<username@outlook.com>\",\"username\":\"username@outlook.com\",\"mailbox\":{\"value\":\"{outlook.office365.com:993/imap/ssl/novalidate-cert}INBOX\",\"disabled\":true,\"checked\":true},\"smtp\":{\"value\":\"smtp.office365.com:587\",\"disabled\":false}}'),
('mail_template', 'My Bank', '{\"disp_note\":\"Desc: A sample HTML rich phishing mail\"}', '{\"mail_template_subject\":\"Important! Your consent is required\",\"mail_template_content\":\"<br><div><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"100%\\\"><tbody><tr><td><br><\\/td><\\/tr><\\/tbody><\\/table><\\/div><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"600\\\"><tbody><tr><td bgcolor=\\\"#dcddde\\\" style=\\\"line-height:0px;background-color:#dcddde; border-left:1px solid #dcddde;\\\" valign=\\\"top\\\"><div><a data-original-title=\\\"Mark as smart link\\\" href=\\\"https:\\/\\/myphishingsite.com\\/page?rid={{RID}}\\\" rel=\\\"tooltip\\\" target=\\\"_blank\\\"><img src=\\\"https:\\/\\/user-images.githubusercontent.com\\/15928266\\/105949193-4518f300-60a7-11eb-87a9-6bb241003d92.jpg\\\" alt=\\\"\\\" class=\\\"fr-fic fr-dii\\\" width=\\\"100%\\\" border=\\\"0\\\"><\\/a><\\/div><\\/td><\\/tr><tr><td style=\\\"border-bottom:1px solid #cccccc;border-left:1px solid #cccccc;border-right:1px solid #cccccc;\\\"><table border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"100%\\\"><tbody><tr><td align=\\\"center\\\" valign=\\\"top\\\"><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"100%\\\"><tbody><tr><td width=\\\"4%\\\"><br><\\/td><td valign=\\\"top\\\" width=\\\"92%\\\"><table border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"width:100%!important;\\\" width=\\\"100%\\\"><tbody><tr><td align=\\\"center\\\" valign=\\\"top\\\"><div><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"width:100% !important;\\\" width=\\\"100%\\\"><tbody><tr><td height=\\\"20\\\"><br><\\/td><\\/tr><tr><td style=\\\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\\\">Dear {{NAME}},<\\/td><\\/tr><tr><td height=\\\"10\\\"><br><\\/td><\\/tr><tr><td style=\\\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\\\">We value our association with you and look forward to enhancing this relationship at every step.<\\/td><\\/tr><tr><td height=\\\"10\\\"><br><\\/td><\\/tr><tr><td style=\\\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\\\">We are delighted to inform you that you are a part of Platinum Banking Programme and to continue enjoying programme benefits, kindly provide your consent.<\\/td><\\/tr><tr><td height=\\\"10\\\"><br><\\/td><\\/tr><tr><td style=\\\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\\\">Here are few privileges of the programme, exclusively for you.<\\/td><\\/tr><tr><td style=\\\"text-align:center;\\\" valign=\\\"top\\\"><div align=\\\"center\\\" style=\\\"width:180px; display:inline-block; vertical-align:top;\\\"><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"border-collapse:collapse!important;width:100%!important;\\\" width=\\\"100%\\\"><tbody><tr><td align=\\\"center\\\"><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"100%\\\"><tbody><tr><td height=\\\"5\\\"><br><\\/td><\\/tr><tr><td align=\\\"center\\\" height=\\\"87\\\" style=\\\"vertical-align:middle !important;\\\" valign=\\\"middle\\\"><img src=\\\"https:\\/\\/user-images.githubusercontent.com\\/15928266\\/105949203-46e2b680-60a7-11eb-9a7f-c7a078cc4ca6.jpg\\\" alt=\\\"\\\" class=\\\"fr-fic fr-dii\\\" width=\\\"48\\\" height=\\\"48\\\" border=\\\"0\\\"><\\/td><\\/tr><tr><td align=\\\"center\\\" height=\\\"75\\\" style=\\\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\\\" valign=\\\"top\\\">Personalized attention from a dedicated Platinum Relationship Manager<\\/td><\\/tr><\\/tbody><\\/table><\\/td><\\/tr><\\/tbody><\\/table><\\/div><div align=\\\"center\\\" style=\\\"width:180px; display:inline-block; vertical-align:top;\\\"><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"border-collapse:collapse!important;width:100%!important;\\\" width=\\\"100%\\\"><tbody><tr><td align=\\\"center\\\"><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"100%\\\"><tbody><tr><td height=\\\"5\\\"><br><\\/td><\\/tr><tr><td align=\\\"center\\\" height=\\\"87\\\" style=\\\"vertical-align:middle !important;\\\" valign=\\\"middle\\\"><img src=\\\"https:\\/\\/user-images.githubusercontent.com\\/15928266\\/105949204-46e2b680-60a7-11eb-8b0a-b175a65b5018.jpg\\\" alt=\\\"\\\" class=\\\"fr-fic fr-dii\\\" width=\\\"56\\\" height=\\\"52\\\" border=\\\"0\\\"><\\/td><\\/tr><tr><td align=\\\"center\\\" height=\\\"110\\\" style=\\\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\\\" valign=\\\"top\\\">ZERO cost on locker<br>rental<\\/td><\\/tr><\\/tbody><\\/table><\\/td><\\/tr><\\/tbody><\\/table><\\/div><div align=\\\"center\\\" style=\\\"width:180px; display:inline-block; vertical-align:top;\\\"><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"border-collapse:collapse!important;width:100%!important;\\\" width=\\\"100%\\\"><tbody><tr><td align=\\\"center\\\"><table align=\\\"center\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" width=\\\"100%\\\"><tbody><tr><td height=\\\"5\\\"><br><\\/td><\\/tr><tr><td align=\\\"center\\\" height=\\\"87\\\" style=\\\"vertical-align:middle !important;\\\" valign=\\\"middle\\\"><img src=\\\"https:\\/\\/user-images.githubusercontent.com\\/15928266\\/105949205-477b4d00-60a7-11eb-9d32-41427f2c1601.jpg\\\" alt=\\\"\\\" class=\\\"fr-fic fr-dii\\\" width=\\\"53\\\" height=\\\"45\\\" border=\\\"0\\\"><\\/td><\\/tr><tr><td align=\\\"center\\\" height=\\\"110\\\" style=\\\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\\\" valign=\\\"top\\\">Special relationship rates for Loans and Forex transactions<\\/td><\\/tr><\\/tbody><\\/table><\\/td><\\/tr><\\/tbody><\\/table><\\/div><\\/td><\\/tr><tr><td align=\\\"center\\\" valign=\\\"top\\\"><table align=\\\"center\\\" bgcolor=\\\"#0d4c8b\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"width:230px !important; border:1px solid #733943; border-radius:5px; background-color:#733943; font-size: 15px;\\\" width=\\\"230\\\"><tbody><tr><td align=\\\"center\\\" style=\\\"font-family:Arial, sans-serif; font-size:1.2em; color:#fff; text-align:center !important; border-radius:5px; background-color:#733943; padding:5px;\\\" valign=\\\"middle\\\"><a data-original-title=\\\"Mark as smart link\\\" href=\\\"https:\\/\\/myphishingsite.com\\/page?rid={{RID}}\\\" rel=\\\"tooltip\\\" style=\\\"text-decoration:none; color:#fff; font-weight:500;\\\" target=\\\"_blank\\\">Platinum Banking Benefits<\\/a><\\/td><\\/tr><\\/tbody><\\/table><\\/td><\\/tr><tr><td height=\\\"20\\\"><br><\\/td><\\/tr><tr><td align=\\\"center\\\" valign=\\\"top\\\"><table align=\\\"center\\\" bgcolor=\\\"#0d4c8b\\\" border=\\\"0\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"width:200px !important; border:1px solid #733943; border-radius:5px; background-color:#733943; font-size: 15px;\\\" width=\\\"200\\\"><tbody><tr><td align=\\\"center\\\" style=\\\"font-family:Arial, sans-serif; font-size:1.2em; color:#fff; text-align:center !important; border-radius:5px; background-color:#733943; padding:5px;\\\" valign=\\\"middle\\\"><a data-original-title=\\\"Mark as smart link\\\" href=\\\"https:\\/\\/myphishingsite.com\\/page?rid={{RID}}\\\" rel=\\\"tooltip\\\" style=\\\"text-decoration:none; color:#fff; font-weight:500;\\\" target=\\\"_blank\\\">Yes, I want to Continue<\\/a><\\/td><\\/tr><\\/tbody><\\/table><\\/td><\\/tr><tr><td height=\\\"15\\\"><br><\\/td><\\/tr><tr><td height=\\\"20\\\"><br><\\/td><\\/tr><\\/tbody><\\/table><\\/div><\\/td><\\/tr><tr><td height=\\\"30\\\"><br><\\/td><\\/tr><tr><td align=\\\"left\\\" style=\\\"font-family:Arial; font-size:16px; letter-spacing: 1px; line-height:28px; color:#000000;\\\" valign=\\\"top\\\">Warm regards,<br><br><div><span style=\\\"font-weight: bold !important;\\\">Aaron Murakami<\\/span><br>Programme Manager<br>Platinum Premium Banking<\\/div><\\/td><\\/tr><tr><td height=\\\"15\\\"><br><\\/td><\\/tr><\\/tbody><\\/table><\\/td><td width=\\\"4%\\\"><br><\\/td><\\/tr><\\/tbody><\\/table><\\/td><\\/tr><\\/tbody><\\/table><\\/td><\\/tr><tr><td align=\\\"left\\\" style=\\\"font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; padding:10px 5px 5px 18px; color:#201d1e; text-align:left;\\\" valign=\\\"top\\\">*Terms &amp; Conditions apply | <a data-original-title=\\\"Mark as smart link\\\" href=\\\"https:\\/\\/myphishingsite.com\\/unsubscribe\\\" rel=\\\"tooltip\\\" style=\\\"text-decoration:underline; color:#0000ff;\\\" target=\\\"_blank\\\">Unsubscribe<\\/a><\\/td><\\/tr><tr><td style=\\\"font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:14px; padding:10px 0 5px 18px; color:#000000;\\\">*Based on Retail Loan book size (excluding mortgages). Source: Annual Reports as on 31<sup>st<\\/sup> March 2018 and No.1 on market capitalisation based on BSE data as on 22<sup>nd<\\/sup> May, 2018.<\\/td><\\/tr><\\/tbody><\\/table><div><br><\\/div><br>{{TRACKER}}\",\"timage_type\":1,\"mail_content_type\":\"text/html\",\"attachment\":[]}'),
('mail_sender', 'Postmark', '{\"dsn_type\":\"postmark\",\"disp_note\":\"Note: Provide the value of ID at \'SMTP Password\' field.\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_template', 'Scan me - QR', '{\"disp_note\":\"Desc: A QR based email. QR code is generated dynamicly\"}', '{\"mail_template_subject\":\"Lucky You\",\"mail_template_content\":\"Dear Customer,<br><br>Please scan the QR image shown below to confirm your prize!<br><br><img src=\\\"http:\\/\\/localhost\\/mod?type=qr_att&amp;content=<your text here>&amp;img_name=code.png\\\" class=\\\"fr-fic fr-dii\\\"><br><br>{{TRACKER}}\",\"timage_type\":1,\"mail_content_type\":\"text/html\",\"attachment\":[]}'),
('mail_sender', 'Sendgrid', '{\"dsn_type\":\"sendgrid\",\"disp_note\":\"Note: Provide value of KEY at \'SMTP Password\' field.\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_sender', 'Sendinblue', '{\"dsn_type\":\"sendinblue\",\"disp_note\":\"\"}', '{\"from\":\"\",\"username\":\"\",\"mailbox\":{\"value\":\"\",\"disabled\":false,\"checked\":false},\"smtp\":{\"value\":\"NA\",\"disabled\":true}}'),
('mail_template', 'Track me', '{\"disp_note\":\"Desc: A simple mail to track when the mail is opened\"}', '{\"mail_template_subject\":\"Thanks!\",\"mail_template_content\":\"Hi {{FNAME}},<br><br>Thank you for your email. We will meet soon.<br><br>Thanks &amp; Regards<br>Rose<br><br>{{TRACKER}}\",\"timage_type\":1,\"mail_content_type\":\"text/html\",\"attachment\":[]}'),
('mail_sender', 'Yahoo (yahoo.com/ymail.com) - SSL', '{\"dsn_type\":\"yahoo_ssl\",\"disp_note\":\"Note: You may need to turn on less secure apps. Refer <a href=\'https://help.yahoo.com/kb/access-yahoo-mail-third-party-apps-sln15241.html\' target=\'_blank\'>https://help.yahoo.com/kb/access-yahoo-mail-third-party-apps-sln15241.html</a>\"}', '{\"from\":\"Name<username@yahoo.com>\",\"username\":\"username@yahoo.com\",\"mailbox\":{\"value\":\"{imap.mail.yahoo.com:993/imap/ssl}INBOX\",\"disabled\":true,\"checked\":true},\"smtp\":{\"value\":\"smtp.mail.yahoo.com:465\",\"disabled\":false}}'),
('mail_sender', 'Yahoo (yahoo.com/ymail.com) - TLS', '{\"dsn_type\":\"yahoo_tls\",\"disp_note\":\"Note: You may need to turn on less secure apps. Refer <a href=\'https://help.yahoo.com/kb/access-yahoo-mail-third-party-apps-sln15241.html\' target=\'_blank\'>https://help.yahoo.com/kb/access-yahoo-mail-third-party-apps-sln15241.html</a>\"}', '{\"from\":\"Name<username@yahoo.com>\",\"username\":\"username@yahoo.com\",\"mailbox\":{\"value\":\"{imap.mail.yahoo.com:993/imap/ssl}INBOX\",\"disabled\":true,\"checked\":true},\"smtp\":{\"value\":\"smtp.mail.yahoo.com:587\",\"disabled\":false}}');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_training_assignments`
--

CREATE TABLE `tb_training_assignments` (
  `assignment_id` varchar(50) NOT NULL,
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
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_training_certificates`
--

CREATE TABLE `tb_training_certificates` (
  `certificate_id` varchar(50) NOT NULL,
  `progress_id` varchar(50) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `module_id` varchar(50) NOT NULL,
  `module_name` varchar(200) NOT NULL,
  `score_achieved` int(11) NOT NULL,
  `completion_date` varchar(50) NOT NULL,
  `validation_code` varchar(50) NOT NULL,
  `issued_date` varchar(50) NOT NULL,
  `template_data` longtext DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_training_modules`
--

CREATE TABLE `tb_training_modules` (
  `module_id` varchar(50) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tb_training_modules`
--

INSERT INTO `tb_training_modules` (`module_id`, `module_name`, `module_description`, `module_type`, `content_data`, `quiz_data`, `quiz_enabled`, `passing_score`, `estimated_duration`, `difficulty_level`, `category`, `tags`, `status`, `created_by`, `created_date`, `modified_date`) VALUES
('xpBWKu6U0J', 'teste', 'teste ', 'video', '<p><iframe frameborder=\"0\" src=\"//www.youtube.com/embed/oVFw7BBgdXs\" width=\"640\" height=\"360\" class=\"note-video-clip\"></iframe><br></p>', '', 0, 70, 15, 'basic', 'phishing', '', 1, NULL, '06-10-2025 01:47 AM', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_training_progress`
--

CREATE TABLE `tb_training_progress` (
  `progress_id` varchar(50) NOT NULL,
  `assignment_id` varchar(50) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `module_id` varchar(50) NOT NULL,
  `status` varchar(30) DEFAULT 'not_started',
  `progress_percentage` int(11) DEFAULT 0,
  `time_spent` int(11) DEFAULT 0,
  `quiz_score` int(11) DEFAULT 0,
  `quiz_attempts` int(11) DEFAULT 0,
  `completion_time` varchar(50) DEFAULT NULL,
  `certificate_issued` tinyint(1) DEFAULT 0,
  `certificate_id` varchar(50) DEFAULT NULL,
  `last_activity` varchar(50) DEFAULT NULL,
  `created_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_training_questions`
--

CREATE TABLE `tb_training_questions` (
  `question_id` varchar(50) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_training_quiz_results`
--

CREATE TABLE `tb_training_quiz_results` (
  `result_id` varchar(50) NOT NULL,
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
  `submitted_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_training_rankings`
--

CREATE TABLE `tb_training_rankings` (
  `ranking_id` varchar(50) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `client_id` varchar(50) DEFAULT NULL,
  `total_score` int(11) DEFAULT 0,
  `completed_modules` int(11) DEFAULT 0,
  `certificates_earned` int(11) DEFAULT 0,
  `avg_quiz_score` decimal(5,2) DEFAULT 0.00,
  `total_time_spent` int(11) DEFAULT 0,
  `last_activity` varchar(50) DEFAULT NULL,
  `rank_position` int(11) DEFAULT 0,
  `points_earned` int(11) DEFAULT 0,
  `badges_earned` text DEFAULT NULL,
  `updated_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tb_user_campaign_history`
--

CREATE TABLE `tb_user_campaign_history` (
  `id` int(11) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `campaign_id` varchar(50) NOT NULL,
  `campaign_type` enum('mail','web','combined') NOT NULL DEFAULT 'mail',
  `participation_date` datetime DEFAULT current_timestamp(),
  `clicked` tinyint(1) DEFAULT 0,
  `submitted_data` tinyint(1) DEFAULT 0,
  `completed_training` tinyint(1) DEFAULT 0,
  `last_activity` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tb_access_ctrl`
--
ALTER TABLE `tb_access_ctrl`
  ADD PRIMARY KEY (`tk_id`);

--
-- Índices de tabela `tb_campaign_training_association`
--
ALTER TABLE `tb_campaign_training_association`
  ADD PRIMARY KEY (`association_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `idx_campaign_training` (`campaign_id`,`campaign_type`,`is_active`);

--
-- Índices de tabela `tb_campaign_training_results`
--
ALTER TABLE `tb_campaign_training_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_campaign_training_client` (`client_id`),
  ADD KEY `idx_campaign_training_campaign` (`campaign_id`),
  ADD KEY `idx_campaign_training_tracker` (`tracker_id`),
  ADD KEY `idx_campaign_training_module` (`training_module_id`),
  ADD KEY `idx_campaign_training_email` (`user_email`),
  ADD KEY `idx_campaign_training_status` (`status`);

--
-- Índices de tabela `tb_clients`
--
ALTER TABLE `tb_clients`
  ADD PRIMARY KEY (`client_id`),
  ADD KEY `idx_client_name` (`client_name`),
  ADD KEY `idx_client_status` (`status`);

--
-- Índices de tabela `tb_client_settings`
--
ALTER TABLE `tb_client_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `unique_client_setting` (`client_id`,`setting_key`);

--
-- Índices de tabela `tb_client_users`
--
ALTER TABLE `tb_client_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_department` (`department`),
  ADD KEY `idx_user_email_client` (`user_email`,`client_id`),
  ADD KEY `idx_department_id` (`department_id`);

--
-- Índices de tabela `tb_core_mailcamp_config`
--
ALTER TABLE `tb_core_mailcamp_config`
  ADD PRIMARY KEY (`mconfig_id`);

--
-- Índices de tabela `tb_core_mailcamp_list`
--
ALTER TABLE `tb_core_mailcamp_list`
  ADD PRIMARY KEY (`campaign_id`),
  ADD UNIQUE KEY `id` (`campaign_id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_mailcamp_client_status` (`client_id`,`camp_status`),
  ADD KEY `idx_campaign_training_enabled` (`client_id`,`training_enabled`,`camp_status`);

--
-- Índices de tabela `tb_core_mailcamp_sender_list`
--
ALTER TABLE `tb_core_mailcamp_sender_list`
  ADD PRIMARY KEY (`sender_list_id`),
  ADD UNIQUE KEY `sender_list_id` (`sender_list_id`),
  ADD KEY `idx_sender_client_id` (`client_id`);

--
-- Índices de tabela `tb_core_mailcamp_template_list`
--
ALTER TABLE `tb_core_mailcamp_template_list`
  ADD PRIMARY KEY (`mail_template_id`),
  ADD KEY `idx_template_client_id` (`client_id`);

--
-- Índices de tabela `tb_core_mailcamp_user_group`
--
ALTER TABLE `tb_core_mailcamp_user_group`
  ADD PRIMARY KEY (`user_group_id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_client_user_group` (`client_id`,`user_group_id`);

--
-- Índices de tabela `tb_core_quick_tracker_list`
--
ALTER TABLE `tb_core_quick_tracker_list`
  ADD PRIMARY KEY (`tracker_id`),
  ADD KEY `idx_client_id` (`client_id`);

--
-- Índices de tabela `tb_core_web_tracker_list`
--
ALTER TABLE `tb_core_web_tracker_list`
  ADD PRIMARY KEY (`tracker_id`),
  ADD UNIQUE KEY `id_2` (`tracker_id`),
  ADD KEY `id` (`tracker_id`),
  ADD KEY `id_3` (`tracker_id`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_webtrack_client_active` (`client_id`,`active`),
  ADD KEY `idx_tracker_training_enabled` (`client_id`,`training_enabled`,`active`);

--
-- Índices de tabela `tb_data_mailcamp_live`
--
ALTER TABLE `tb_data_mailcamp_live`
  ADD PRIMARY KEY (`rid`),
  ADD KEY `idx_campaign_email` (`campaign_id`,`user_email`),
  ADD KEY `idx_click_tracking` (`campaign_id`,`last_click_time`);

--
-- Índices de tabela `tb_data_quick_tracker_live`
--
ALTER TABLE `tb_data_quick_tracker_live`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_data_webform_submit`
--
ALTER TABLE `tb_data_webform_submit`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_data_webpage_visit`
--
ALTER TABLE `tb_data_webpage_visit`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_departments`
--
ALTER TABLE `tb_departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_dept_client` (`department_id`,`client_id`),
  ADD KEY `idx_client_status` (`client_id`,`status`);

--
-- Índices de tabela `tb_hf_list`
--
ALTER TABLE `tb_hf_list`
  ADD PRIMARY KEY (`hf_id`);

--
-- Índices de tabela `tb_hland_page_list`
--
ALTER TABLE `tb_hland_page_list`
  ADD PRIMARY KEY (`hlp_id`);

--
-- Índices de tabela `tb_ht_list`
--
ALTER TABLE `tb_ht_list`
  ADD PRIMARY KEY (`ht_id`);

--
-- Índices de tabela `tb_log`
--
ALTER TABLE `tb_log`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_main`
--
ALTER TABLE `tb_main`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_main_cron`
--
ALTER TABLE `tb_main_cron`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_main_variables`
--
ALTER TABLE `tb_main_variables`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `tb_pl_list`
--
ALTER TABLE `tb_pl_list`
  ADD PRIMARY KEY (`pl_id`);

--
-- Índices de tabela `tb_store`
--
ALTER TABLE `tb_store`
  ADD PRIMARY KEY (`name`);

--
-- Índices de tabela `tb_training_assignments`
--
ALTER TABLE `tb_training_assignments`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Índices de tabela `tb_training_certificates`
--
ALTER TABLE `tb_training_certificates`
  ADD PRIMARY KEY (`certificate_id`),
  ADD UNIQUE KEY `validation_code` (`validation_code`);

--
-- Índices de tabela `tb_training_modules`
--
ALTER TABLE `tb_training_modules`
  ADD PRIMARY KEY (`module_id`);

--
-- Índices de tabela `tb_training_progress`
--
ALTER TABLE `tb_training_progress`
  ADD PRIMARY KEY (`progress_id`);

--
-- Índices de tabela `tb_training_questions`
--
ALTER TABLE `tb_training_questions`
  ADD PRIMARY KEY (`question_id`);

--
-- Índices de tabela `tb_training_quiz_results`
--
ALTER TABLE `tb_training_quiz_results`
  ADD PRIMARY KEY (`result_id`);

--
-- Índices de tabela `tb_training_rankings`
--
ALTER TABLE `tb_training_rankings`
  ADD PRIMARY KEY (`ranking_id`);

--
-- Índices de tabela `tb_user_campaign_history`
--
ALTER TABLE `tb_user_campaign_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_campaign` (`user_email`,`campaign_id`,`client_id`),
  ADD KEY `idx_user_email` (`user_email`),
  ADD KEY `idx_client_id` (`client_id`),
  ADD KEY `idx_campaign_id` (`campaign_id`),
  ADD KEY `idx_participation_date` (`participation_date`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_campaign_training_results`
--
ALTER TABLE `tb_campaign_training_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_client_users`
--
ALTER TABLE `tb_client_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `tb_data_quick_tracker_live`
--
ALTER TABLE `tb_data_quick_tracker_live`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `tb_data_webform_submit`
--
ALTER TABLE `tb_data_webform_submit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `tb_data_webpage_visit`
--
ALTER TABLE `tb_data_webpage_visit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `tb_departments`
--
ALTER TABLE `tb_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de tabela `tb_log`
--
ALTER TABLE `tb_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `tb_main`
--
ALTER TABLE `tb_main`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_main_cron`
--
ALTER TABLE `tb_main_cron`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `tb_user_campaign_history`
--
ALTER TABLE `tb_user_campaign_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tb_campaign_training_association`
--
ALTER TABLE `tb_campaign_training_association`
  ADD CONSTRAINT `tb_campaign_training_association_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `tb_training_modules` (`module_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tb_campaign_training_results`
--
ALTER TABLE `tb_campaign_training_results`
  ADD CONSTRAINT `fk_campaign_training_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `tb_client_settings`
--
ALTER TABLE `tb_client_settings`
  ADD CONSTRAINT `tb_client_settings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tb_client_users`
--
ALTER TABLE `tb_client_users`
  ADD CONSTRAINT `fk_user_department` FOREIGN KEY (`department_id`) REFERENCES `tb_departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_client_users_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tb_departments`
--
ALTER TABLE `tb_departments`
  ADD CONSTRAINT `fk_departments_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `tb_user_campaign_history`
--
ALTER TABLE `tb_user_campaign_history`
  ADD CONSTRAINT `tb_user_campaign_history_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

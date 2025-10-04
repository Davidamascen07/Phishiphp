-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 04/10/2025 às 12:10
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
  `client_id` varchar(50) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `added_date` varchar(111) DEFAULT NULL,
  `last_updated` varchar(111) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `user_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`user_data`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `camp_lock` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_mailcamp_list`
--

INSERT INTO `tb_core_mailcamp_list` (`campaign_id`, `client_id`, `campaign_name`, `campaign_data`, `date`, `scheduled_time`, `stop_time`, `camp_status`, `camp_lock`) VALUES
('3ndydg', 'client_xuj7DqeN', 'Email Campaigns fulano', '{\"user_group\":{\"id\":\"u11civ\",\"name\":\"Email User Groups\"},\"mail_template\":{\"id\":\"jn1i4h\",\"name\":\"My Bank\"},\"mail_sender\":{\"id\":\"58tc5o\",\"name\":\"teste\"},\"mail_config\":{\"id\":\"default\",\"name\":\"Default Configuration\"},\"msg_interval\":\"0000-0000\",\"msg_fail_retry\":\"2\"}', '04-10-2025 10:02 AM', '04-10-2025 10:02 AM', NULL, 2, 1);

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
('58tc5o', 'default_org', 'teste', 'NA', 'davidddf.frota@gmailcom', 'username@gmail.com', '', 1, 'None', '[]', 'gmail', '04-10-2025 10:00 AM');

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
('jn1i4h', 'client_j5pQOiI2', 'My Bank', 'Important! Your consent is required', '<br><div><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td><br></td></tr></tbody></table></div><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"600\"><tbody><tr><td bgcolor=\"#dcddde\" style=\"line-height:0px;background-color:#dcddde; border-left:1px solid #dcddde;\" valign=\"top\"><div><a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/page?rid={{RID}}\" rel=\"tooltip\" target=\"_blank\"><img src=\"https://user-images.githubusercontent.com/15928266/105949193-4518f300-60a7-11eb-87a9-6bb241003d92.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"100%\" border=\"0\"></a></div></td></tr><tr><td style=\"border-bottom:1px solid #cccccc;border-left:1px solid #cccccc;border-right:1px solid #cccccc;\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td align=\"center\" valign=\"top\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td width=\"4%\"><br></td><td valign=\"top\" width=\"92%\"><table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\" valign=\"top\"><div><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:100% !important;\" width=\"100%\"><tbody><tr><td height=\"20\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">Dear {{NAME}},</td></tr><tr><td height=\"10\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">We value our association with you and look forward to enhancing this relationship at every step.</td></tr><tr><td height=\"10\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">We are delighted to inform you that you are a part of Platinum Banking Programme and to continue enjoying programme benefits, kindly provide your consent.</td></tr><tr><td height=\"10\"><br></td></tr><tr><td style=\"font-family:Arial; font-size:1em; line-height:22px; color:#595959;\">Here are few privileges of the programme, exclusively for you.</td></tr><tr><td style=\"text-align:center;\" valign=\"top\"><div align=\"center\" style=\"width:180px; display:inline-block; vertical-align:top;\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse!important;width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td height=\"5\"><br></td></tr><tr><td align=\"center\" height=\"87\" style=\"vertical-align:middle !important;\" valign=\"middle\"><img src=\"https://user-images.githubusercontent.com/15928266/105949203-46e2b680-60a7-11eb-9a7f-c7a078cc4ca6.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"48\" height=\"48\" border=\"0\"></td></tr><tr><td align=\"center\" height=\"75\" style=\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\" valign=\"top\">Personalized attention from a dedicated Platinum Relationship Manager</td></tr></tbody></table></td></tr></tbody></table></div><div align=\"center\" style=\"width:180px; display:inline-block; vertical-align:top;\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse!important;width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td height=\"5\"><br></td></tr><tr><td align=\"center\" height=\"87\" style=\"vertical-align:middle !important;\" valign=\"middle\"><img src=\"https://user-images.githubusercontent.com/15928266/105949204-46e2b680-60a7-11eb-8b0a-b175a65b5018.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"56\" height=\"52\" border=\"0\"></td></tr><tr><td align=\"center\" height=\"110\" style=\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\" valign=\"top\">ZERO cost on locker<br>rental</td></tr></tbody></table></td></tr></tbody></table></div><div align=\"center\" style=\"width:180px; display:inline-block; vertical-align:top;\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse!important;width:100%!important;\" width=\"100%\"><tbody><tr><td align=\"center\"><table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\"><tbody><tr><td height=\"5\"><br></td></tr><tr><td align=\"center\" height=\"87\" style=\"vertical-align:middle !important;\" valign=\"middle\"><img src=\"https://user-images.githubusercontent.com/15928266/105949205-477b4d00-60a7-11eb-9d32-41427f2c1601.jpg\" alt=\"\" class=\"fr-fic fr-dii\" width=\"53\" height=\"45\" border=\"0\"></td></tr><tr><td align=\"center\" height=\"110\" style=\"font-family:Arial, Helvetica, sans-serif; line-height:22px; font-size:0.938em; color:#595959; text-align:center;\" valign=\"top\">Special relationship rates for Loans and Forex transactions</td></tr></tbody></table></td></tr></tbody></table></div></td></tr><tr><td align=\"center\" valign=\"top\"><table align=\"center\" bgcolor=\"#0d4c8b\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:230px !important; border:1px solid #733943; border-radius:5px; background-color:#733943; font-size: 15px;\" width=\"230\"><tbody><tr><td align=\"center\" style=\"font-family:Arial, sans-serif; font-size:1.2em; color:#fff; text-align:center !important; border-radius:5px; background-color:#733943; padding:5px;\" valign=\"middle\"><a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/page?rid={{RID}}\" rel=\"tooltip\" style=\"text-decoration:none; color:#fff; font-weight:500;\" target=\"_blank\">Platinum Banking Benefits</a></td></tr></tbody></table></td></tr><tr><td height=\"20\"><br></td></tr><tr><td align=\"center\" valign=\"top\"><table align=\"center\" bgcolor=\"#0d4c8b\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"width:200px !important; border:1px solid #733943; border-radius:5px; background-color:#733943; font-size: 15px;\" width=\"200\"><tbody><tr><td align=\"center\" style=\"font-family:Arial, sans-serif; font-size:1.2em; color:#fff; text-align:center !important; border-radius:5px; background-color:#733943; padding:5px;\" valign=\"middle\"><a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/page?rid={{RID}}\" rel=\"tooltip\" style=\"text-decoration:none; color:#fff; font-weight:500;\" target=\"_blank\">Yes, I want to Continue</a></td></tr></tbody></table></td></tr><tr><td height=\"15\"><br></td></tr><tr><td height=\"20\"><br></td></tr></tbody></table></div></td></tr><tr><td height=\"30\"><br></td></tr><tr><td align=\"left\" style=\"font-family:Arial; font-size:16px; letter-spacing: 1px; line-height:28px; color:#000000;\" valign=\"top\">Warm regards,<br><br><div><span style=\"font-weight: bold !important;\">Aaron Murakami</span><br>Programme Manager<br>Platinum Premium Banking</div></td></tr><tr><td height=\"15\"><br></td></tr></tbody></table></td><td width=\"4%\"><br></td></tr></tbody></table></td></tr></tbody></table></td></tr><tr><td align=\"left\" style=\"font-family:Arial, Helvetica, sans-serif; font-size:11px; line-height:16px; padding:10px 5px 5px 18px; color:#201d1e; text-align:left;\" valign=\"top\">*Terms &amp; Conditions apply | <a data-original-title=\"Mark as smart link\" href=\"https://myphishingsite.com/unsubscribe\" rel=\"tooltip\" style=\"text-decoration:underline; color:#0000ff;\" target=\"_blank\">Unsubscribe</a></td></tr><tr><td style=\"font-family:Arial, Helvetica, sans-serif; font-size:12px; line-height:14px; padding:10px 0 5px 18px; color:#000000;\">*Based on Retail Loan book size (excluding mortgages). Source: Annual Reports as on 31<sup>st</sup> March 2018 and No.1 on market capitalisation based on BSE data as on 22<sup>nd</sup> May, 2018.</td></tr></tbody></table><div><br></div><br>{{TRACKER}}', 1, 'text/html', '[]', '04-10-2025 09:58 AM');

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
('u11civ', 'default_org', 'Email User Groups', '[{\"uid\":\"2ybp3jvzq1\",\"fname\":\"Alice\",\"lname\":\"alice@example.com\",\"email\":\"alice@example.com\",\"notes\":\"NoteA\"},{\"uid\":\"5kpvqr3tm2\",\"fname\":\"Bob\",\"lname\":\"bob@example.com\",\"email\":\"bob@example.com\",\"notes\":\"NoteB\"},{\"uid\":\"uw307rqzbe\",\"fname\":\"Charlie\",\"lname\":\"charlie@example.com\",\"email\":\"charlie@example.com\",\"notes\":\"NoteC\"},{\"uid\":\"ar0q2zcx4i\",\"fname\":\"David\",\"lname\":\"david@example.com\",\"email\":\"david@example.com\",\"notes\":\"NoteD\"},{\"uid\":\"6w7vdc0k2z\",\"fname\":\"Eve\",\"lname\":\"eve@example.com\",\"email\":\"eve@example.com\",\"notes\":\"NoteE\"},{\"uid\":\"xsd5cnr2i8\",\"fname\":\"Frank\",\"lname\":\"frank@example.com\",\"email\":\"frank@example.com\",\"notes\":\"NoteF\"},{\"uid\":\"l42efy5ksn\",\"fname\":\"Grace\",\"lname\":\"grace@example.com\",\"email\":\"grace@example.com\",\"notes\":\"NoteG\"},{\"uid\":\"5hwyfbxsv8\",\"fname\":\"Heidi\",\"lname\":\"heidi@example.com\",\"email\":\"heidi@example.com\",\"notes\":\"NoteH\"},{\"uid\":\"ziewjrk4ht\",\"fname\":\"Ivan\",\"lname\":\"ivan@example.com\",\"email\":\"ivan@example.com\",\"notes\":\"NoteI\"},{\"uid\":\"5eajzt3n4u\",\"fname\":\"Judy\",\"lname\":\"judy@example.com\",\"email\":\"judy@example.com\",\"notes\":\"NoteJ\"},{\"uid\":\"2foys7kc09\",\"fname\":\"Mallory\",\"lname\":\"mallory@example.com\",\"email\":\"mallory@example.com\",\"notes\":\"NoteM\"},{\"uid\":\"sktm50c7ij\",\"fname\":\"Niaj\",\"lname\":\"niaj@example.com\",\"email\":\"niaj@example.com\",\"notes\":\"NoteN\"},{\"uid\":\"j2v8oyaq1w\",\"fname\":\"Olivia\",\"lname\":\"olivia@example.com\",\"email\":\"olivia@example.com\",\"notes\":\"NoteO\"},{\"uid\":\"tev5yx1brc\",\"fname\":\"Peggy\",\"lname\":\"peggy@example.com\",\"email\":\"peggy@example.com\",\"notes\":\"NoteP\"},{\"uid\":\"qhdikv69p8\",\"fname\":\"Sybil\",\"lname\":\"sybil@example.com\",\"email\":\"sybil@example.com\",\"notes\":\"NoteS\"}]', '04-10-2025 10:01 AM');

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
('86voq9', 'client_xuj7DqeN', 'teste', '03-10-2025 02:26 PM', NULL, NULL, 0),
('grebcz', 'client_j5pQOiI2', 'teste', '03-10-2025 02:25 PM', NULL, NULL, 0),
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
  `active` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Despejando dados para a tabela `tb_core_web_tracker_list`
--

INSERT INTO `tb_core_web_tracker_list` (`tracker_id`, `client_id`, `tracker_name`, `content_html`, `content_js`, `tracker_step_data`, `date`, `start_time`, `stop_time`, `active`) VALUES
('iax617', 'client_j5pQOiI2', 'teste', '[\"<!DOCTYPE html>\\n<form>a:\\n    <input type=\\\"text\\\" id=\\\"a\\\">\\n    <input type=\\\"button\\\" id=\\\"form\\\" value=\\\"Submit\\\">\\n<\\/form>\"]', 'var sess_id = \"\";\nvar comp_name = \"\";\nvar comp_username = \"\";\nvar tracker_id = \"iax617\";\nvar form_field_data;\nvar ip_info;\nvar xhr = new XMLHttpRequest();\n\n// //geting rid\n// // var rid = window.location.search.split(\"rid=\")[1].split(\"&\")[0];\n//geting rid (robust: handle absence of rid in querystring)\nvar rid = (function() {\n    try {\n        var m = window.location.search.match(/[?&]rid=([^&]+)/);\n        return m ? m[1] : \"\";\n    } catch (e) {\n        return \"\";\n    }\n})();\n\n//IE 8 supports\nif (typeof Array.prototype.forEach != \'function\') {\n    Array.prototype.forEach = function(callback) {\n        for (var i = 0; i < this.length; i++) {\n            callback.apply(this, [this[i], i, this]);\n        }\n    };\n}\nif (typeof String.prototype.trim !== \'function\') {\n    String.prototype.trim = function() {\n        return this.replace(/^s+|s+$/g, \'\');\n    };\n}\n//-----------------------------------------------------------\n\n//creating session cookie\nif (document.cookie.indexOf(\"tsess_id=\") >= 0) { // cookie exists\n    cookie_arr = document.cookie.split(\';\');\n    cookie_arr.forEach(function(cookie) {\n        if (cookie.split(\'=\')[0].trim() == \'tsess_id\')\n            sess_id = cookie.split(\'=\')[1];\n    });\n} else {\n    sess_id = Math.random().toString(36).substring(8);\n    document.cookie = \"tsess_id=\" + sess_id + \";SameSite=Lax\";\n}\n\nvar curr_page = (window.location.host + window.location.pathname).toLowerCase();\nvar first_page = \"loophish.local/spear/trackergenerator\";\ngetIPInfo();\n\nfunction getIPInfo() {\n    var xhr1 = new XMLHttpRequest();\n    try { //IE8 error catch\n        xhr1.open(\'GET\', \'https://ipapi.co/json\', true);\n        xhr1.onload = function() {\n            if (xhr1.readyState === xhr1.DONE) {\n                ip_info = JSON.parse(xhr1.response);\n                if (curr_page == first_page) //if starting page\n                    do_track_req_visit();\n            }\n        };\n        xhr1.onerror = function() {\n            if (curr_page == first_page) //if starting page, send even error occurred.\n                do_track_req_visit();\n        };\n        xhr1.send(null);\n    } catch (err) {\n        do_track_req_visit();\n    }\n}\n\nfunction do_track_req_visit() {\n    xhr.open(\"POST\", \"https://loophish.local/track\", true);\n    xhr.send(JSON.stringify({\n        page: 0,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        rid: rid,\n        ip_info: ip_info\n    }));\n}\n//-----------------------------------------------------------\n\n\nvar domIsReady = (function(domIsReady) {\n    var isBrowserIeOrNot = function() {\n        return (!document.attachEvent || typeof document.attachEvent === \"undefined\" ? \'not-ie\' : \'ie\');\n    }\n\n    domIsReady = function(callback) {\n        if (callback && typeof callback === \'function\') {\n            if (isBrowserIeOrNot() !== \'ie\') {\n                document.addEventListener(\"DOMContentLoaded\", function() {\n                    return callback();\n                });\n            } else {\n                document.attachEvent(\"onreadystatechange\", function() {\n                    if (document.readyState === \"complete\") {\n                        return callback();\n                    }\n                });\n            }\n        } else {\n            console.error(\'The callback is not a function!\');\n        }\n    }\n\n    return domIsReady;\n})(domIsReady || {});\n\n(function(document, window, domIsReady, undefined) {\n    domIsReady(function() {\n        onReady();\n    });\n})(document, window, domIsReady);\n\n\nfunction onReady() { //Events registration\n    if (document.getElementById(\"form\"))\n        document.getElementById(\"form\").onclick = function(e) {\n            e = e || window.event; //IE8 support\n            form_field_data = {};\n            form_field_data.a = document.getElementById(\'a\').value;\n            do_track_req(e, 1, \"https://loophish.local/spear/TrackerGenerator\");\n        }\n};\n//-----------------------------------------------------------\nfunction do_track_req(e, page, next_page_url) {\n    e.preventDefault ? e.preventDefault() : (e.returnValue = false);\n    xhr.open(\"POST\", \"https://loophish.local/track\", false);\n    xhr.send(JSON.stringify({\n        page: page,\n        trackerId: tracker_id,\n        sess_id: sess_id,\n        screen_res: screen.width + \"x\" + screen.height,\n        form_field_data: form_field_data,\n        rid: rid,\n        ip_info: ip_info\n    }));\n\n    if (next_page_url != \"#\")\n        window.top.location.href = next_page_url + \"?rid=\" + rid;\n}', '{\"start\":{\"tb_tracker_name\":\"teste\",\"selector_webhook_type\":\"sp_base\",\"tb_webhook_url\":\"https://loophish.local\",\"cb_auto_ativate\":true},\"trackers\":{},\"web_forms\":{\"count\":1,\"data\":[{\"page_name\":\"teste\",\"page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"link_next_page\":true,\"next_page_url\":\"https://loophish.local/spear/TrackerGenerator\",\"form_fields_and_values\":{\"TF_a\":{\"idname\":\"a\",\"track\":true},\"FSB\":{\"idname\":\"form\",\"track\":true}}}]}}', '03-10-2025 02:58 PM', '03-10-2025 02:58 PM', NULL, 1);

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
  `all_headers` mediumtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(11, 'client_j5pQOiI2', 'dept_TI_2D6R', 'TI', 'departamento do sistema', '#007bff', 1, '2025-10-03 12:12:26', '2025-10-03 12:12:26');

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
(14, 'admin', 'Account login', '127.0.0.1', '04-10-2025 09:56 AM');

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
(1, 'Admin', 'admin', '28d7296e0120873f596eb1b5b1223e85966fadef51cb8b007e0bfb150f83ec83', 'davidddf.frota@gmail.com', '1', NULL, NULL, '01-10-2025 07:01 PM', '[\"03-10-2025 12:07 AM\",\"04-10-2025 09:56 AM\"]', '[\"02-10-2025 06:59 PM\",\"03-10-2025 10:21 PM\"]');

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
(1, 11872);

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

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `tb_access_ctrl`
--
ALTER TABLE `tb_access_ctrl`
  ADD PRIMARY KEY (`tk_id`);

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
  ADD KEY `idx_department` (`department`);

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
  ADD KEY `idx_mailcamp_client_status` (`client_id`,`camp_status`);

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
  ADD KEY `idx_webtrack_client_active` (`client_id`,`active`);

--
-- Índices de tabela `tb_data_mailcamp_live`
--
ALTER TABLE `tb_data_mailcamp_live`
  ADD PRIMARY KEY (`rid`);

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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `tb_client_users`
--
ALTER TABLE `tb_client_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_data_quick_tracker_live`
--
ALTER TABLE `tb_data_quick_tracker_live`
  MODIFY `id` int(111) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `tb_data_webform_submit`
--
ALTER TABLE `tb_data_webform_submit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_data_webpage_visit`
--
ALTER TABLE `tb_data_webpage_visit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tb_departments`
--
ALTER TABLE `tb_departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `tb_log`
--
ALTER TABLE `tb_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `tb_client_settings`
--
ALTER TABLE `tb_client_settings`
  ADD CONSTRAINT `tb_client_settings_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tb_client_users`
--
ALTER TABLE `tb_client_users`
  ADD CONSTRAINT `tb_client_users_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `tb_departments`
--
ALTER TABLE `tb_departments`
  ADD CONSTRAINT `fk_departments_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients` (`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

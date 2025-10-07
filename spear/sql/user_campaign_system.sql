-- Script SQL para criação da tabela de histórico de campanhas por usuário

-- Tabela para histórico de participação em campanhas
CREATE TABLE IF NOT EXISTS `tb_user_campaign_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(255) NOT NULL,
  `client_id` varchar(50) NOT NULL,
  `campaign_id` varchar(50) NOT NULL,
  `campaign_type` enum('mail', 'web', 'combined') NOT NULL DEFAULT 'mail',
  `participation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `clicked` tinyint(1) DEFAULT 0,
  `submitted_data` tinyint(1) DEFAULT 0,
  `completed_training` tinyint(1) DEFAULT 0,
  `last_activity` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_email` (`user_email`),
  KEY `idx_client_id` (`client_id`),
  KEY `idx_campaign_id` (`campaign_id`),
  KEY `idx_participation_date` (`participation_date`),
  UNIQUE KEY `unique_user_campaign` (`user_email`, `campaign_id`, `client_id`),
  FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Melhorar tabela tb_client_users para suportar melhor o sistema
ALTER TABLE `tb_client_users` 
ADD COLUMN IF NOT EXISTS `first_name` varchar(100) DEFAULT NULL AFTER `user_name`,
ADD COLUMN IF NOT EXISTS `last_name` varchar(100) DEFAULT NULL AFTER `first_name`,
ADD COLUMN IF NOT EXISTS `department_id` varchar(50) DEFAULT NULL AFTER `department`,
ADD COLUMN IF NOT EXISTS `campaign_count` int(11) DEFAULT 0 AFTER `user_data`,
ADD COLUMN IF NOT EXISTS `last_campaign_date` datetime DEFAULT NULL AFTER `campaign_count`,
ADD INDEX IF NOT EXISTS `idx_user_email_client` (`user_email`, `client_id`),
ADD INDEX IF NOT EXISTS `idx_department_id` (`department_id`);

-- Adicionar foreign key para departamento se não existir
ALTER TABLE `tb_client_users` 
ADD CONSTRAINT `fk_user_department` 
FOREIGN KEY (`department_id`) REFERENCES `tb_departments`(`department_id`) 
ON DELETE SET NULL ON UPDATE CASCADE;
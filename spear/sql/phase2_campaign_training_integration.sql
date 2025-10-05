-- ========================================
-- LOOPHISH V2.0 - FASE 2: INTEGRAÇÃO CAMPANHA-TREINAMENTO
-- Script para adicionar campos de integração entre campanhas e treinamentos
-- ========================================

-- ETAPA 1: Adicionar campos de treinamento em tb_core_web_tracker_list
-- ========================================

-- Verificar se coluna training_enabled existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'training_enabled');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_enabled` tinyint(1) NOT NULL DEFAULT 0 AFTER `active`;',
    'SELECT "Campo training_enabled já existe em tb_core_web_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar se coluna training_module_id existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'training_module_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_module_id` varchar(50) DEFAULT NULL AFTER `training_enabled`;',
    'SELECT "Campo training_module_id já existe em tb_core_web_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar se coluna training_trigger_condition existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'training_trigger_condition');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_trigger_condition` enum("immediate","on_completion","on_failure","on_interaction") DEFAULT "immediate" AFTER `training_module_id`;',
    'SELECT "Campo training_trigger_condition já existe em tb_core_web_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar se coluna training_completion_redirect existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'training_completion_redirect');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `training_completion_redirect` varchar(255) DEFAULT NULL AFTER `training_trigger_condition`;',
    'SELECT "Campo training_completion_redirect já existe em tb_core_web_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 2: Adicionar campos de treinamento em tb_core_mailcamp_list
-- ========================================

-- Verificar se coluna training_enabled existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'training_enabled');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_enabled` tinyint(1) NOT NULL DEFAULT 0 AFTER `camp_status`;',
    'SELECT "Campo training_enabled já existe em tb_core_mailcamp_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar se coluna training_module_id existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'training_module_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_module_id` varchar(50) DEFAULT NULL AFTER `training_enabled`;',
    'SELECT "Campo training_module_id já existe em tb_core_mailcamp_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar se coluna training_target_percentage existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'training_target_percentage');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_target_percentage` int(3) DEFAULT 100 AFTER `training_module_id`;',
    'SELECT "Campo training_target_percentage já existe em tb_core_mailcamp_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar se coluna training_notification_emails existe
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'training_notification_emails');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `training_notification_emails` text DEFAULT NULL AFTER `training_target_percentage`;',
    'SELECT "Campo training_notification_emails já existe em tb_core_mailcamp_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 3: Criar tabela de relacionamento campanha-treinamento
-- ========================================

-- Verificar se tabela existe
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_campaign_training_results');

SET @sql = IF(@table_exists = 0, 
    'CREATE TABLE `tb_campaign_training_results` (
        `result_id` varchar(50) NOT NULL PRIMARY KEY,
        `client_id` varchar(50) NOT NULL,
        `campaign_id` varchar(50) DEFAULT NULL,
        `tracker_id` varchar(50) DEFAULT NULL,
        `training_module_id` varchar(50) NOT NULL,
        `participant_email` varchar(255) NOT NULL,
        `participant_name` varchar(255) DEFAULT NULL,
        `training_started_at` datetime DEFAULT NULL,
        `training_completed_at` datetime DEFAULT NULL,
        `training_score` int(3) DEFAULT NULL,
        `training_status` enum("not_started","in_progress","completed","failed") DEFAULT "not_started",
        `questions_answered` int(5) DEFAULT 0,
        `correct_answers` int(5) DEFAULT 0,
        `completion_percentage` decimal(5,2) DEFAULT 0.00,
        `time_spent_minutes` int(10) DEFAULT 0,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX `idx_campaign_training_client` (`client_id`),
        INDEX `idx_campaign_training_campaign` (`campaign_id`),
        INDEX `idx_campaign_training_tracker` (`tracker_id`),
        INDEX `idx_campaign_training_module` (`training_module_id`),
        INDEX `idx_campaign_training_email` (`participant_email`),
        INDEX `idx_campaign_training_status` (`training_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;',
    'SELECT "Tabela tb_campaign_training_results já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 4: Criar índices para performance
-- ========================================

-- Índice para busca por campanha com treinamento
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND INDEX_NAME = 'idx_campaign_training_enabled');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_campaign_training_enabled` ON `tb_core_mailcamp_list` (`client_id`, `training_enabled`, `camp_status`);',
    'SELECT "Índice idx_campaign_training_enabled já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Índice para busca por tracker com treinamento
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND INDEX_NAME = 'idx_tracker_training_enabled');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_tracker_training_enabled` ON `tb_core_web_tracker_list` (`client_id`, `training_enabled`, `active`);',
    'SELECT "Índice idx_tracker_training_enabled já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 5: Criar Foreign Keys (se as tabelas de treinamento existirem)
-- ========================================

-- Verificar se tabela tb_training_modules existe
SET @training_table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_training_modules');

-- FK para tb_campaign_training_results -> tb_clients
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_campaign_training_results' AND CONSTRAINT_NAME = 'fk_campaign_training_client');

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_campaign_training_results` ADD CONSTRAINT `fk_campaign_training_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_campaign_training_client já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK para tb_campaign_training_results -> tb_core_mailcamp_list (se campanhas existirem)
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_campaign_training_results' AND CONSTRAINT_NAME = 'fk_campaign_training_campaign');

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_campaign_training_results` ADD CONSTRAINT `fk_campaign_training_campaign` FOREIGN KEY (`campaign_id`) REFERENCES `tb_core_mailcamp_list`(`campaign_id`) ON DELETE SET NULL ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_campaign_training_campaign já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK para tb_campaign_training_results -> tb_core_web_tracker_list (se trackers existirem)
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_campaign_training_results' AND CONSTRAINT_NAME = 'fk_campaign_training_tracker');

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_campaign_training_results` ADD CONSTRAINT `fk_campaign_training_tracker` FOREIGN KEY (`tracker_id`) REFERENCES `tb_core_web_tracker_list`(`tracker_id`) ON DELETE SET NULL ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_campaign_training_tracker já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FINALIZAÇÃO
-- ========================================
SELECT '🎯 FASE 2 - Integração Campanha-Treinamento configurada com sucesso!' as final_message;
SELECT 'Campos adicionados:' as info;
SELECT '- tb_core_web_tracker_list: training_enabled, training_module_id, training_trigger_condition, training_completion_redirect' as tracker_fields;
SELECT '- tb_core_mailcamp_list: training_enabled, training_module_id, training_target_percentage, training_notification_emails' as campaign_fields;
SELECT '- tb_campaign_training_results: Nova tabela para resultados de treinamento' as new_table;
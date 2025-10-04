-- =====================================================
-- Script para Correção dos Relacionamentos Cliente
-- Loophish - Sistema Multi-tenancy
-- =====================================================
-- ATENÇÃO: Execute primeiro o script fix_primary_keys.sql
-- =====================================================

-- 0. Verificar se PRIMARY KEY existe na tabela tb_clients (deve existir após fix_primary_keys.sql)
SET @pk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clients' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@pk_exists = 0, 
    'SELECT "ERRO: PRIMARY KEY não existe em tb_clients. Execute primeiro fix_primary_keys.sql" as error_message;',
    'SELECT "PRIMARY KEY confirmada em tb_clients. Prosseguindo..." as success_message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 1. Adicionar client_id nas tabelas core que não possuem (com verificação de duplicidade)

-- tb_core_mailcamp_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `campaign_id`, ADD INDEX `idx_mailcamp_client_id` (`client_id`);',
    'SELECT "Coluna client_id já existe em tb_core_mailcamp_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_web_tracker_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `tracker_id`, ADD INDEX `idx_webtracker_client_id` (`client_id`);',
    'SELECT "Coluna client_id já existe em tb_core_web_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_quick_tracker_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_quick_tracker_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `tracker_id`, ADD INDEX `idx_quicktracker_client_id` (`client_id`);',
    'SELECT "Coluna client_id já existe em tb_core_quick_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2. Adicionar client_id nas tabelas relacionadas que também não possuem

-- tb_core_mailcamp_user_group
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_user_group' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_user_group` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `user_group_id`, ADD INDEX `idx_usergroup_client_id` (`client_id`);',
    'SELECT "Coluna client_id já existe em tb_core_mailcamp_user_group" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_template_list  
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_template_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_template_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `mail_template_id`, ADD INDEX `idx_template_client_id` (`client_id`);',
    'SELECT "Coluna client_id já existe em tb_core_mailcamp_template_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_sender_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_sender_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_sender_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `sender_list_id`, ADD INDEX `idx_sender_client_id` (`client_id`);',
    'SELECT "Coluna client_id já existe em tb_core_mailcamp_sender_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_hland_page_list (verificar se a tabela existe)
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_hland_page_list');

SET @column_exists = IF(@table_exists > 0, 
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_hland_page_list' AND COLUMN_NAME = 'client_id'),
    0);

SET @sql = IF(@table_exists = 0, 
    'SELECT "Tabela tb_hland_page_list não existe" as message;',
    IF(@column_exists = 0, 
    'ALTER TABLE `tb_hland_page_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `hlp_id`, ADD INDEX `idx_landpage_client_id` (`client_id`);',
        'SELECT "Coluna client_id já existe em tb_hland_page_list" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3. Criar Foreign Keys para garantir integridade referencial (com verificação de duplicidade)

-- Verificar e criar FK para tb_core_mailcamp_list
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND CONSTRAINT_NAME = 'fk_mailcamp_client');
-- ensure column charset/collation matches parent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_list - pulando modify" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_list` ADD CONSTRAINT `fk_mailcamp_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_mailcamp_client já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar e criar FK para tb_core_web_tracker_list
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND CONSTRAINT_NAME = 'fk_webtracker_client');
-- ensure column charset/collation matches parent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_web_tracker_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Coluna client_id não existe em tb_core_web_tracker_list - pulando modify" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_core_web_tracker_list` ADD CONSTRAINT `fk_webtracker_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_webtracker_client já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar e criar FK para tb_core_quick_tracker_list
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND CONSTRAINT_NAME = 'fk_quicktracker_client');
-- ensure column charset/collation matches parent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_quick_tracker_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Coluna client_id não existe em tb_core_quick_tracker_list - pulando modify" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_core_quick_tracker_list` ADD CONSTRAINT `fk_quicktracker_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_quicktracker_client já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar e criar FK para tb_core_mailcamp_user_group
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_user_group' AND CONSTRAINT_NAME = 'fk_usergroup_client');
-- ensure column charset/collation matches parent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_user_group' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_user_group` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_user_group - pulando modify" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_user_group` ADD CONSTRAINT `fk_usergroup_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_usergroup_client já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar e criar FK para tb_core_mailcamp_template_list
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_template_list' AND CONSTRAINT_NAME = 'fk_template_client');
-- ensure column charset/collation matches parent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_template_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_template_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_template_list - pulando modify" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_template_list` ADD CONSTRAINT `fk_template_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_template_client já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar e criar FK para tb_core_mailcamp_sender_list
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_sender_list' AND CONSTRAINT_NAME = 'fk_sender_client');
-- ensure column charset/collation matches parent
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_sender_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_sender_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_sender_list - pulando modify" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_sender_list` ADD CONSTRAINT `fk_sender_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
    'SELECT "Foreign Key fk_sender_client já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Verificar e criar FK para tb_hland_page_list (se a tabela existir)
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_hland_page_list');

SET @constraint_exists = IF(@table_exists > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_hland_page_list' AND CONSTRAINT_NAME = 'fk_landpage_client'),
    1); -- Se a tabela não existe, considera que a constraint "existe" para pular

-- if table exists, ensure column matches and then add FK
SET @sql = IF(@table_exists = 0, 
    'SELECT "Tabela tb_hland_page_list não existe - FK não criada" as message;',
    IF(@constraint_exists = 0, 
        (SELECT IF(
            (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_hland_page_list' AND COLUMN_NAME = 'client_id') > 0,
            'ALTER TABLE `tb_hland_page_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\'; ALTER TABLE `tb_hland_page_list` ADD CONSTRAINT `fk_landpage_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
            'SELECT "Coluna client_id não existe em tb_hland_page_list - FK não criada" as message;'
        ));
    ,
        'SELECT "Foreign Key fk_landpage_client já existe ou tabela não existe" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 4. Atualizar dados existentes com client_id apropriado apenas se a coluna existir
-- tb_core_mailcamp_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_mailcamp_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_web_tracker_list  
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_web_tracker_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_web_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_quick_tracker_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_quick_tracker_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_quick_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_user_group
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_user_group' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_mailcamp_user_group` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_user_group" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_template_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_template_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_mailcamp_template_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_template_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_sender_list
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_sender_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_mailcamp_sender_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_sender_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_hland_page_list
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_hland_page_list');

SET @column_exists = IF(@table_exists > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_hland_page_list' AND COLUMN_NAME = 'client_id'),
    0);

SET @sql = IF(@table_exists = 0, 
    'SELECT "Tabela tb_hland_page_list não existe" as message;',
    IF(@column_exists > 0, 
        'UPDATE `tb_hland_page_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
        'SELECT "Coluna client_id não existe em tb_hland_page_list" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5. Criar Views para facilitar consultas multi-tenant (DROP e CREATE para evitar duplicidade)
DROP VIEW IF EXISTS `v_client_campaigns`;
CREATE VIEW `v_client_campaigns` AS
SELECT 
    c.client_name,
    c.client_id,
    mcl.campaign_id,
    mcl.campaign_name,
    mcl.camp_status,
    mcl.date as created_date,
    mcl.scheduled_time,
    mcl.stop_time
FROM `tb_clients` c
LEFT JOIN `tb_core_mailcamp_list` mcl ON c.client_id = mcl.client_id
WHERE c.status = 1;

DROP VIEW IF EXISTS `v_client_trackers`;
CREATE VIEW `v_client_trackers` AS
SELECT 
    c.client_name,
    c.client_id,
    'web' as tracker_type,
    wtl.tracker_id,
    wtl.tracker_name,
    wtl.active,
    wtl.date as created_date,
    wtl.start_time,
    wtl.stop_time
FROM `tb_clients` c
LEFT JOIN `tb_core_web_tracker_list` wtl ON c.client_id = wtl.client_id
WHERE c.status = 1
UNION ALL
SELECT 
    c.client_name,
    c.client_id,
    'quick' as tracker_type,
    qtl.tracker_id,
    qtl.tracker_name,
    qtl.active,
    qtl.date as created_date,
    qtl.start_time,
    qtl.stop_time
FROM `tb_clients` c
LEFT JOIN `tb_core_quick_tracker_list` qtl ON c.client_id = qtl.client_id
WHERE c.status = 1;

-- 6. Criar índices compostos para melhor performance (com verificação de duplicidade)
-- tb_core_mailcamp_list
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND INDEX_NAME = 'idx_campaign_client_status');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_campaign_client_status` ON `tb_core_mailcamp_list` (`client_id`, `camp_status`);',
    'SELECT "Índice idx_campaign_client_status já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_web_tracker_list
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND INDEX_NAME = 'idx_webtracker_client_active');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_webtracker_client_active` ON `tb_core_web_tracker_list` (`client_id`, `active`);',
    'SELECT "Índice idx_webtracker_client_active já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_quick_tracker_list
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND INDEX_NAME = 'idx_quicktracker_client_active');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_quicktracker_client_active` ON `tb_core_quick_tracker_list` (`client_id`, `active`);',
    'SELECT "Índice idx_quicktracker_client_active já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7. Mensagem de sucesso
SELECT 'Script de correção dos relacionamentos cliente executado com sucesso!' as message;
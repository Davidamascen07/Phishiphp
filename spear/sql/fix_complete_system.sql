-- =====================================================
-- Script COMBINADO para Correção Completa do Sistema
-- Loophish - Sistema Multi-tenancy
-- =====================================================
-- Este script executa AMBAS as correções em ordem segura
-- Pode ser executado múltiplas vezes sem problemas
-- =====================================================

-- ETAPA 1: Corrigir PRIMARY KEYs Faltantes
-- =====================================================

-- Adicionar PRIMARY KEY para tb_clients se não existir
SET @pk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clients' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@pk_exists = 0, 
    'ALTER TABLE `tb_clients` ADD PRIMARY KEY (`client_id`);',
    'SELECT "PRIMARY KEY já existe em tb_clients" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Adicionar PRIMARY KEY para tb_client_settings se não existir
SET @pk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_client_settings' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@pk_exists = 0, 
    'ALTER TABLE `tb_client_settings` ADD PRIMARY KEY (`setting_id`);',
    'SELECT "PRIMARY KEY já existe em tb_client_settings" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Adicionar PRIMARY KEYs para tabelas de benchmark se não existirem
SET @pk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_benchmarking_data' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @sql = IF(@pk_exists = 0, 
    'ALTER TABLE `tb_benchmarking_data` ADD PRIMARY KEY (`benchmark_id`);',
    'SELECT "PRIMARY KEY já existe em tb_benchmarking_data" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 2: Relacionamentos Multi-tenant
-- =====================================================

-- Verificar se PRIMARY KEY existe na tb_clients
SET @pk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_clients' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

-- Se não tiver PRIMARY KEY, parar execução
SET @sql = IF(@pk_exists = 0, 
    'SELECT "ERRO CRÍTICO: PRIMARY KEY não existe em tb_clients. Script interrompido." as error_message;',
    'SELECT "✓ PRIMARY KEY confirmada em tb_clients. Prosseguindo com relacionamentos..." as success_message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Continuar apenas se PRIMARY KEY existe
SET @continue_execution = @pk_exists;

-- tb_core_mailcamp_list
SET @column_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'client_id'),
    1); -- Se não deve continuar, simula que coluna já existe

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando tb_core_mailcamp_list - erro anterior" as message;',
    IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `campaign_id`, ADD INDEX `idx_mailcamp_client_id` (`client_id`);',
        'SELECT "Coluna client_id já existe em tb_core_mailcamp_list" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_web_tracker_list
SET @column_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'client_id'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando tb_core_web_tracker_list - erro anterior" as message;',
    IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_web_tracker_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `tracker_id`, ADD INDEX `idx_webtracker_client_id` (`client_id`);',
        'SELECT "Coluna client_id já existe em tb_core_web_tracker_list" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_quick_tracker_list
SET @column_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND COLUMN_NAME = 'client_id'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando tb_core_quick_tracker_list - erro anterior" as message;',
    IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_quick_tracker_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `tracker_id`, ADD INDEX `idx_quicktracker_client_id` (`client_id`);',
        'SELECT "Coluna client_id já existe em tb_core_quick_tracker_list" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_user_group
SET @column_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_user_group' AND COLUMN_NAME = 'client_id'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando tb_core_mailcamp_user_group - erro anterior" as message;',
    IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_user_group` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `user_group_id`, ADD INDEX `idx_usergroup_client_id` (`client_id`);',
        'SELECT "Coluna client_id já existe em tb_core_mailcamp_user_group" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_template_list
SET @column_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_template_list' AND COLUMN_NAME = 'client_id'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando tb_core_mailcamp_template_list - erro anterior" as message;',
    IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_template_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `mail_template_id`, ADD INDEX `idx_template_client_id` (`client_id`);',
        'SELECT "Coluna client_id já existe em tb_core_mailcamp_template_list" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_sender_list
SET @column_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_sender_list' AND COLUMN_NAME = 'client_id'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando tb_core_mailcamp_sender_list - erro anterior" as message;',
    IF(@column_exists = 0, 
    'ALTER TABLE `tb_core_mailcamp_sender_list` ADD COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\' AFTER `sender_list_id`, ADD INDEX `idx_sender_client_id` (`client_id`);',
        'SELECT "Coluna client_id já existe em tb_core_mailcamp_sender_list" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 3: Foreign Keys (apenas se PRIMARY KEY existe)
-- =====================================================

-- ETAPA 2.5: Normalizar engines e definições de colunas antes de criar FK
-- Algumas instalações usam latin1 / MyISAM em tabelas filha. Isso causa errno:150.
-- Vamos garantir que a tabela pai e as filhas usem InnoDB e que as colunas tenham
-- exatamente a mesma definição (varchar(50) utf8mb4_general_ci).

-- Normalizar tabela pai tb_clients
SET @sql = 'ALTER TABLE `tb_clients` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @sql = 'ALTER TABLE `tb_clients` ENGINE=InnoDB;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Lista de tabelas filhas a serem normalizadas
-- Para cada tabela: (1) forçar ENGINE=InnoDB (2) se a coluna existir, forçar a mesma definição
SET @child_tables = 'tb_core_mailcamp_list,tb_core_web_tracker_list,tb_core_quick_tracker_list,tb_core_mailcamp_user_group,tb_core_mailcamp_template_list,tb_core_mailcamp_sender_list';

-- Iterar (simples) — não temos loop PL/SQL; faremos chamadas manuais para cada tabela para manter legibilidade

-- tb_core_mailcamp_list
SET @sql = 'ALTER TABLE `tb_core_mailcamp_list` ENGINE=InnoDB;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Pulando MODIFY tb_core_mailcamp_list.client_id - coluna nao encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_web_tracker_list
SET @sql = 'ALTER TABLE `tb_core_web_tracker_list` ENGINE=InnoDB;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_web_tracker_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Pulando MODIFY tb_core_web_tracker_list.client_id - coluna nao encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_quick_tracker_list
SET @sql = 'ALTER TABLE `tb_core_quick_tracker_list` ENGINE=InnoDB;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_quick_tracker_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Pulando MODIFY tb_core_quick_tracker_list.client_id - coluna nao encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_user_group
SET @sql = 'ALTER TABLE `tb_core_mailcamp_user_group` ENGINE=InnoDB;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_user_group' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_user_group` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Pulando MODIFY tb_core_mailcamp_user_group.client_id - coluna nao encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_template_list
SET @sql = 'ALTER TABLE `tb_core_mailcamp_template_list` ENGINE=InnoDB;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_template_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_template_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Pulando MODIFY tb_core_mailcamp_template_list.client_id - coluna nao encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- tb_core_mailcamp_sender_list
SET @sql = 'ALTER TABLE `tb_core_mailcamp_sender_list` ENGINE=InnoDB;';
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_sender_list' AND COLUMN_NAME = 'client_id');
SET @sql = IF(@col_exists > 0,
    'ALTER TABLE `tb_core_mailcamp_sender_list` MODIFY COLUMN `client_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT \'default_org\';',
    'SELECT "Pulando MODIFY tb_core_mailcamp_sender_list.client_id - coluna nao encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;


-- FK para tb_core_mailcamp_list
SET @constraint_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND CONSTRAINT_NAME = 'fk_mailcamp_client'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando FK tb_core_mailcamp_list - erro anterior" as message;',
    IF(@constraint_exists = 0, 
        'ALTER TABLE `tb_core_mailcamp_list` ADD CONSTRAINT `fk_mailcamp_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'SELECT "Foreign Key fk_mailcamp_client já existe" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK para tb_core_web_tracker_list
SET @constraint_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND CONSTRAINT_NAME = 'fk_webtracker_client'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando FK tb_core_web_tracker_list - erro anterior" as message;',
    IF(@constraint_exists = 0, 
        'ALTER TABLE `tb_core_web_tracker_list` ADD CONSTRAINT `fk_webtracker_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'SELECT "Foreign Key fk_webtracker_client já existe" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK para tb_core_quick_tracker_list
SET @constraint_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND CONSTRAINT_NAME = 'fk_quicktracker_client'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando FK tb_core_quick_tracker_list - erro anterior" as message;',
    IF(@constraint_exists = 0, 
        'ALTER TABLE `tb_core_quick_tracker_list` ADD CONSTRAINT `fk_quicktracker_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'SELECT "Foreign Key fk_quicktracker_client já existe" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK para tb_core_mailcamp_user_group
SET @constraint_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_user_group' AND CONSTRAINT_NAME = 'fk_usergroup_client'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando FK tb_core_mailcamp_user_group - erro anterior" as message;',
    IF(@constraint_exists = 0, 
        'ALTER TABLE `tb_core_mailcamp_user_group` ADD CONSTRAINT `fk_usergroup_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'SELECT "Foreign Key fk_usergroup_client já existe" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK para tb_core_mailcamp_template_list
SET @constraint_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_template_list' AND CONSTRAINT_NAME = 'fk_template_client'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando FK tb_core_mailcamp_template_list - erro anterior" as message;',
    IF(@constraint_exists = 0, 
        'ALTER TABLE `tb_core_mailcamp_template_list` ADD CONSTRAINT `fk_template_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'SELECT "Foreign Key fk_template_client já existe" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FK para tb_core_mailcamp_sender_list
SET @constraint_exists = IF(@continue_execution > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_sender_list' AND CONSTRAINT_NAME = 'fk_sender_client'),
    1);

SET @sql = IF(@continue_execution = 0, 
    'SELECT "Pulando FK tb_core_mailcamp_sender_list - erro anterior" as message;',
    IF(@constraint_exists = 0, 
        'ALTER TABLE `tb_core_mailcamp_sender_list` ADD CONSTRAINT `fk_sender_client` FOREIGN KEY (`client_id`) REFERENCES `tb_clients`(`client_id`) ON DELETE CASCADE ON UPDATE CASCADE;',
        'SELECT "Foreign Key fk_sender_client já existe" as message;'
    )
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 4: Atualizar Dados Existentes
-- =====================================================

-- Atualizar dados apenas se as colunas existem
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_mailcamp_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_mailcamp_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_web_tracker_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_web_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND COLUMN_NAME = 'client_id');

SET @sql = IF(@column_exists > 0, 
    'UPDATE `tb_core_quick_tracker_list` SET `client_id` = \'default_org\' WHERE `client_id` = \'\' OR `client_id` IS NULL;',
    'SELECT "Coluna client_id não existe em tb_core_quick_tracker_list" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ETAPA 5: Views e Índices
-- =====================================================

-- Criar Views (DROP e CREATE para evitar duplicidade)
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

-- Criar índices compostos (com verificação)
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_mailcamp_list' AND INDEX_NAME = 'idx_campaign_client_status');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_campaign_client_status` ON `tb_core_mailcamp_list` (`client_id`, `camp_status`);',
    'SELECT "Índice idx_campaign_client_status já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_web_tracker_list' AND INDEX_NAME = 'idx_webtracker_client_active');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_webtracker_client_active` ON `tb_core_web_tracker_list` (`client_id`, `active`);',
    'SELECT "Índice idx_webtracker_client_active já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_core_quick_tracker_list' AND INDEX_NAME = 'idx_quicktracker_client_active');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX `idx_quicktracker_client_active` ON `tb_core_quick_tracker_list` (`client_id`, `active`);',
    'SELECT "Índice idx_quicktracker_client_active já existe" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- FINALIZAÇÃO
-- =====================================================
SELECT '🎉 Script COMBINADO executado com sucesso! Sistema Loophish Multi-tenant configurado.' as final_message;
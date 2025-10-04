-- =====================================================
-- Script para Correção de PRIMARY KEYs Faltantes
-- Loophish - Sistema Multi-tenancy
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

-- Adicionar PRIMARY KEY para tb_client_users se não existir (assumindo que tem uma coluna id)
SET @table_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_client_users');

SET @column_exists = IF(@table_exists > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_client_users' AND COLUMN_NAME = 'user_id'),
    0);

SET @pk_exists = IF(@table_exists > 0,
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_client_users' AND CONSTRAINT_TYPE = 'PRIMARY KEY'),
    1); -- Se a tabela não existe, considera que tem PK para pular

SET @sql = IF(@table_exists = 0, 
    'SELECT "Tabela tb_client_users não existe" as message;',
    IF(@pk_exists = 0 AND @column_exists > 0, 
        'ALTER TABLE `tb_client_users` ADD PRIMARY KEY (`user_id`);',
        'SELECT "PRIMARY KEY já existe em tb_client_users ou coluna user_id não encontrada" as message;'
    )
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

SET @pk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_advanced_analytics' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_advanced_analytics' AND COLUMN_NAME = 'analytics_id');

SET @sql = IF(@pk_exists = 0 AND @column_exists > 0, 
    'ALTER TABLE `tb_advanced_analytics` ADD PRIMARY KEY (`analytics_id`);',
    'SELECT "PRIMARY KEY já existe em tb_advanced_analytics ou coluna analytics_id não encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @pk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_automated_recommendations' AND CONSTRAINT_TYPE = 'PRIMARY KEY');

SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tb_automated_recommendations' AND COLUMN_NAME = 'recommendation_id');

SET @sql = IF(@pk_exists = 0 AND @column_exists > 0, 
    'ALTER TABLE `tb_automated_recommendations` ADD PRIMARY KEY (`recommendation_id`);',
    'SELECT "PRIMARY KEY já existe em tb_automated_recommendations ou coluna recommendation_id não encontrada" as message;'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT 'Script de correção de PRIMARY KEYs executado com sucesso!' as message;
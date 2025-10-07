-- Adicionar colunas para rastreamento de cliques em campanhas de email
ALTER TABLE `tb_data_mailcamp_live` 
ADD COLUMN `click_times` MEDIUMTEXT DEFAULT NULL COMMENT 'JSON array dos timestamps de cliques em links',
ADD COLUMN `last_click_time` VARCHAR(50) DEFAULT NULL COMMENT 'Timestamp do último clique';

-- Adicionar índices para performance
ALTER TABLE `tb_data_mailcamp_live` 
ADD INDEX `idx_campaign_email` (`campaign_id`, `user_email`),
ADD INDEX `idx_click_tracking` (`campaign_id`, `last_click_time`);
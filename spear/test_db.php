<?php
require_once('config/db.php');

echo "Testando conexão com banco de dados...\n";

if ($conn) {
    echo "✓ Conexão com banco estabelecida\n";
    
    // Testar uma consulta simples
    $result = mysqli_query($conn, "SELECT 1");
    if ($result) {
        echo "✓ Consulta SQL funcionando\n";
        
        // Verificar se table tb_training_modules existe
        $result = mysqli_query($conn, "SHOW TABLES LIKE 'tb_training_modules'");
        if (mysqli_num_rows($result) > 0) {
            echo "✓ Tabela tb_training_modules existe\n";
        } else {
            echo "✗ Tabela tb_training_modules NÃO existe\n";
            
            // Tentar criar a tabela principal
            $create_sql = "CREATE TABLE IF NOT EXISTS `tb_training_modules` (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            if (mysqli_query($conn, $create_sql)) {
                echo "✓ Tabela tb_training_modules criada\n";
            } else {
                echo "✗ Erro ao criar tabela: " . mysqli_error($conn) . "\n";
            }
        }
        
    } else {
        echo "✗ Erro na consulta: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "✗ Falha na conexão: " . mysqli_connect_error() . "\n";
}
?>
<?php
echo "Teste simples PHP<br>";
echo "Date: " . date("Y-m-d H:i:s") . "<br>";

// Tentar incluir o arquivo de configuração
if(file_exists('spear/config/db.php')) {
    echo "Arquivo db.php encontrado<br>";
    try {
        require_once('spear/config/db.php');
        echo "Configuração carregada<br>";
        
        // Tentar conectar
        $conn = new mysqli($HOST, $USERNAME, $PASSWORD, $DB_NAME);
        if ($conn->connect_error) {
            echo "Erro de conexão: " . $conn->connect_error . "<br>";
        } else {
            echo "Conexão com o banco estabelecida<br>";
            
            // Verificar se a tabela existe
            $result = $conn->query("SHOW TABLES LIKE 'tb_core_web_tracker_list'");
            if($result->num_rows > 0) {
                echo "Tabela tb_core_web_tracker_list encontrada<br>";
                
                // Verificar colunas
                $result = $conn->query("SHOW COLUMNS FROM tb_core_web_tracker_list");
                echo "Colunas da tabela:<br>";
                while($row = $result->fetch_assoc()) {
                    echo "- " . $row["Field"] . " (" . $row["Type"] . ")<br>";
                }
            } else {
                echo "Tabela tb_core_web_tracker_list NÃO encontrada<br>";
            }
            
            $conn->close();
        }
    } catch(Exception $e) {
        echo "Erro: " . $e->getMessage() . "<br>";
    }
} else {
    echo "Arquivo db.php NÃO encontrado<br>";
}
?>
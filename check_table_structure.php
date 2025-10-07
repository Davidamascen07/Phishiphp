<?php
require_once "spear/config/db.php";

global $conn;

echo "<h1>Verificação da Estrutura da Tabela de Campanhas</h1>\n";

// Verificar estrutura da tabela tb_core_mailcamp_list
echo "<h2>Estrutura da tb_core_mailcamp_list:</h2>\n";
$result = mysqli_query($conn, "DESCRIBE tb_core_mailcamp_list");
if ($result) {
    echo "<table border='1'>\n";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th></tr>\n";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr><td>{$row['Field']}</td><td>{$row['Type']}</td><td>{$row['Null']}</td><td>{$row['Key']}</td></tr>\n";
    }
    echo "</table>\n";
} else {
    echo "Erro: " . mysqli_error($conn) . "\n";
}

// Verificar algumas campanhas usando campos que existem
echo "<h2>Campanhas Existentes (primeiros 5 registros):</h2>\n";
$result = mysqli_query($conn, "SELECT * FROM tb_core_mailcamp_list ORDER BY created_date DESC LIMIT 5");
if ($result) {
    echo "<table border='1'>\n";
    $first = true;
    while ($row = mysqli_fetch_assoc($result)) {
        if ($first) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                echo "<th>$key</th>";
            }
            echo "</tr>\n";
            $first = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>\n";
    }
    echo "</table>\n";
} else {
    echo "Erro: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
?>
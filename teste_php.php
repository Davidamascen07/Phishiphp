<?php
// Teste de extensões PHP
echo "<h1>Teste de Extensões PHP</h1>";

echo "<h2>Informações do PHP</h2>";
echo "Versão do PHP: " . phpversion() . "<br>";

echo "<h2>Extensões Carregadas</h2>";
$extensions = get_loaded_extensions();
sort($extensions);

if (in_array('curl', $extensions)) {
    echo "<span style='color: green;'>✓ cURL está habilitado!</span><br>";
    
    // Teste básico do cURL
    if (function_exists('curl_init')) {
        echo "<span style='color: green;'>✓ Função curl_init() está disponível!</span><br>";
        
        // Teste simples
        $ch = curl_init();
        if ($ch) {
            echo "<span style='color: green;'>✓ curl_init() funcionando corretamente!</span><br>";
            curl_close($ch);
        } else {
            echo "<span style='color: red;'>✗ Erro ao inicializar cURL</span><br>";
        }
    } else {
        echo "<span style='color: red;'>✗ Função curl_init() não está disponível!</span><br>";
    }
} else {
    echo "<span style='color: red;'>✗ cURL NÃO está habilitado!</span><br>";
}

echo "<h2>Todas as extensões carregadas:</h2>";
echo "<ul>";
foreach ($extensions as $ext) {
    echo "<li>$ext</li>";
}
echo "</ul>";

echo "<h2>Informações completas do PHP</h2>";
echo "<details><summary>Clique para ver phpinfo()</summary>";
phpinfo();
echo "</details>";
?>
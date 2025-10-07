<?php
require_once "spear/config/db.php";
require_once "spear/manager/common_functions.php";

global $conn;

echo "<h1>Verificação da URL de Tracking</h1>";

// Verificar configuração do baseurl
$stmt = $conn->prepare("SELECT * FROM tb_core_server_variables");
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Configurações do Servidor:</h2>";
echo "<table border='1'>";
echo "<tr><th>Variable</th><th>Value</th></tr>";

$serv_variables = [];
while ($row = $result->fetch_assoc()) {
    echo "<tr><td>{$row['variable']}</td><td>{$row['value']}</td></tr>";
    $serv_variables[$row['variable']] = $row['value'];
}
echo "</table>";

// Verificar se baseurl está configurado corretamente
if (isset($serv_variables['baseurl'])) {
    echo "<h2>Base URL Atual: <code>" . $serv_variables['baseurl'] . "</code></h2>";
    
    // Testar se a URL está acessível
    $tracking_url = $serv_variables['baseurl'] . '/tmail.php?rid=teste&mid=teste&mtid=teste';
    echo "<h3>URL de Tracking de Exemplo:</h3>";
    echo "<p><a href='$tracking_url' target='_blank'>$tracking_url</a></p>";
    
    // Verificar se a URL está correta
    $expected_url = "http://localhost/loophishx";
    if ($serv_variables['baseurl'] == $expected_url) {
        echo "<p>✅ Base URL está configurada corretamente</p>";
    } else {
        echo "<p>❌ Base URL pode estar incorreta. Esperado: <code>$expected_url</code></p>";
    }
    
} else {
    echo "<h2>❌ Base URL não está configurada!</h2>";
    echo "<p>Isso pode ser o motivo pelo qual o tracking não funciona.</p>";
}

// Verificar um email real enviado
echo "<h2>Verificação de Email Real Enviado:</h2>";
$stmt = $conn->prepare("SELECT campaign_id, rid, user_email, send_time FROM tb_data_mailcamp_live ORDER BY id DESC LIMIT 1");
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<p><strong>Último email enviado:</strong></p>";
    echo "<p>Campaign ID: {$row['campaign_id']}</p>";
    echo "<p>RID: {$row['rid']}</p>";
    echo "<p>Email: {$row['user_email']}</p>";
    echo "<p>Enviado em: {$row['send_time']}</p>";
    
    // Construir URL de tracking para este email
    $real_tracking_url = $serv_variables['baseurl'] . "/tmail?mid={$row['campaign_id']}&rid={$row['rid']}&mtid=teste29";
    echo "<h3>URL de Tracking Real:</h3>";
    echo "<p><a href='$real_tracking_url' target='_blank'>$real_tracking_url</a></p>";
    echo "<p><button onclick=\"testRealTracking('{$real_tracking_url}')\">Testar Esta URL</button></p>";
    echo "<div id='real-test-result'></div>";
    
} else {
    echo "<p>❌ Nenhum email encontrado na tabela tb_data_mailcamp_live</p>";
}

echo "<h2>Como Resolver Problemas de Tracking:</h2>";
echo "<ol>";
echo "<li><strong>Gmail bloqueia imagens por padrão:</strong> O usuário precisa clicar em 'Exibir imagens' no Gmail</li>";
echo "<li><strong>Email vai para spam:</strong> Verificar se o email não foi para a pasta de spam</li>";
echo "<li><strong>Base URL incorreta:</strong> Verificar se o baseurl está configurado corretamente</li>";
echo "<li><strong>Firewall/Antivirus:</strong> Pode bloquear o carregamento da imagem</li>";
echo "<li><strong>Cliente de email offline:</strong> Alguns clientes não carregam imagens automaticamente</li>";
echo "</ol>";

?>

<script>
function testRealTracking(url) {
    let img = new Image();
    img.onload = function() {
        document.getElementById('real-test-result').innerHTML = '<p style="color: green;">✅ URL de tracking funcionou!</p>';
    };
    img.onerror = function() {
        document.getElementById('real-test-result').innerHTML = '<p style="color: red;">❌ Erro ao carregar URL de tracking</p>';
    };
    img.src = url + '&cache_bust=' + Date.now();
    document.getElementById('real-test-result').innerHTML = '<p style="color: blue;">🔄 Testando...</p>';
}
</script>
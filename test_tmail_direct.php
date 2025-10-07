<?php
// Teste direto do tmail.php com dados mock
echo "<h1>Teste Direto do tmail.php</h1>";

// Simular parâmetros GET para o tmail.php
$_GET['rid'] = 'kp32lohg4';  // RID de exemplo
$_GET['mid'] = 'mcamp_6707E2801F9E3_testeprinceQ2ZC7kJu';  // Campaign ID de exemplo
$_GET['mtid'] = 'teste29_123';  // Template ID de exemplo

echo "<h2>Parâmetros de Teste:</h2>";
echo "<p><strong>RID:</strong> " . $_GET['rid'] . "</p>";
echo "<p><strong>Campaign ID:</strong> " . $_GET['mid'] . "</p>";
echo "<p><strong>Template ID:</strong> " . $_GET['mtid'] . "</p>";

echo "<h2>Resultado do tmail.php:</h2>";

// Capturar output do tmail.php
ob_start();
try {
    require_once 'tmail.php';
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
$output = ob_get_clean();

echo "<p><strong>Content-Type Headers:</strong> Verificar se são headers de imagem</p>";
echo "<p><strong>Output Length:</strong> " . strlen($output) . " bytes</p>";

if (strlen($output) > 0) {
    echo "<p>✅ tmail.php produziu output (provavelmente uma imagem)</p>";
    
    // Verificar se é uma imagem válida
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $tempFile = tempnam(sys_get_temp_dir(), 'tracking_test');
    file_put_contents($tempFile, $output);
    $mimeType = $finfo->file($tempFile);
    unlink($tempFile);
    
    echo "<p><strong>MIME Type:</strong> $mimeType</p>";
    
    if (strpos($mimeType, 'image/') === 0) {
        echo "<p>✅ Output é uma imagem válida</p>";
        
        // Criar data URL para mostrar a imagem
        $base64 = base64_encode($output);
        echo "<p><strong>Imagem gerada:</strong></p>";
        echo "<img src='data:$mimeType;base64,$base64' style='border: 2px solid red; max-width: 200px;' alt='Tracking Image'>";
    } else {
        echo "<p>❌ Output não é uma imagem válida</p>";
        echo "<pre>" . htmlspecialchars(substr($output, 0, 500)) . "</pre>";
    }
} else {
    echo "<p>❌ tmail.php não produziu output</p>";
}

echo "<h2>Verificação no Banco de Dados:</h2>";

// Verificar se os dados foram atualizados no banco
require_once "spear/config/db.php";
global $conn;

$campaign_id = $_GET['mid'];
$rid = $_GET['rid'];

$stmt = $conn->prepare("SELECT user_email, mail_open_times FROM tb_data_mailcamp_live WHERE campaign_id = ? AND rid = ?");
$stmt->bind_param("ss", $campaign_id, $rid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<p><strong>Email:</strong> " . $row['user_email'] . "</p>";
    echo "<p><strong>Mail Open Times:</strong> " . $row['mail_open_times'] . "</p>";
    
    $openTimes = json_decode($row['mail_open_times'], true);
    if ($openTimes && count($openTimes) > 0) {
        echo "<p>✅ Email foi marcado como aberto " . count($openTimes) . " vez(es)</p>";
        echo "<p><strong>Última abertura:</strong> " . date('Y-m-d H:i:s', end($openTimes)/1000) . "</p>";
    } else {
        echo "<p>❌ Email ainda não foi marcado como aberto</p>";
    }
} else {
    echo "<p>❌ Registro não encontrado no banco para Campaign ID: $campaign_id, RID: $rid</p>";
}
?>
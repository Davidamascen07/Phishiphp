<?php
require_once('spear/config/db.php');

echo "🔧 SCRIPT DE CORREÇÃO DO BUG RID NO TRACKER JAVASCRIPT\n";
echo "======================================================\n\n";

// Buscar todos os trackers que têm a linha problemática
$query = "SELECT tracker_id, content_js FROM tb_core_web_tracker_list WHERE content_js LIKE '%window.location.search.split(\"rid=\")[1].split(\"&\")[0]%'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    echo "📋 Trackers encontrados com o bug RID: " . $result->num_rows . "\n\n";
    
    $updated_count = 0;
    
    while($row = $result->fetch_assoc()) {
        $tracker_id = $row['tracker_id'];
        $old_js = $row['content_js'];
        
        echo "🔍 Processando tracker: " . $tracker_id . "\n";
        
        // Substituir a linha problemática pela versão corrigida
        $new_js = str_replace(
            'var rid = window.location.search.split("rid=")[1].split("&")[0];',
            '//geting rid (robust: handle absence of rid in querystring)
var rid = (function() {
    try {
        var m = window.location.search.match(/[?&]rid=([^&]+)/);
        return m ? m[1] : "";
    } catch (e) {
        return "";
    }
})();',
            $old_js
        );
        
        // Verificar se houve alteração
        if ($new_js !== $old_js) {
            // Atualizar no banco de dados
            $update_stmt = $conn->prepare("UPDATE tb_core_web_tracker_list SET content_js = ? WHERE tracker_id = ?");
            $update_stmt->bind_param("ss", $new_js, $tracker_id);
            
            if ($update_stmt->execute()) {
                echo "✅ Tracker " . $tracker_id . " corrigido com sucesso!\n";
                $updated_count++;
            } else {
                echo "❌ Erro ao atualizar tracker " . $tracker_id . ": " . $update_stmt->error . "\n";
            }
            
            $update_stmt->close();
        } else {
            echo "⚠️  Tracker " . $tracker_id . " não precisou de correção\n";
        }
        
        echo "\n";
    }
    
    echo "🎉 RESULTADO FINAL:\n";
    echo "- Total de trackers verificados: " . $result->num_rows . "\n";
    echo "- Total de trackers corrigidos: " . $updated_count . "\n\n";
    
    if ($updated_count > 0) {
        echo "✅ Correção aplicada com sucesso! O erro 'Cannot read properties of undefined (reading 'split')' foi resolvido.\n";
        echo "🔄 Agora os trackers funcionarão mesmo quando a URL não contiver o parâmetro ?rid=\n";
    } else {
        echo "ℹ️  Nenhum tracker precisou de correção.\n";
    }
    
} else {
    echo "ℹ️  Nenhum tracker encontrado com o bug RID.\n";
    echo "✅ Todos os trackers já estão usando a versão corrigida!\n";
}

$conn->close();
echo "\n🏁 Script finalizado.\n";
?>
<?php
// Training Landing Page - Shown to users after phishing interaction
require_once(dirname(__FILE__) . '/manager/session_manager.php');
require_once(dirname(__FILE__) . '/manager/common_functions.php');

// Get parameters
$module_id = $_GET['module'] ?? '';
$user_email = $_GET['user'] ?? '';
$interaction_id = $_GET['interaction'] ?? '';

if (empty($module_id) || empty($user_email)) {
    header('Location: index.php');
    exit;
}

// Get module information
$stmt = $conn->prepare("SELECT * FROM tb_training_modules WHERE module_id = ? AND status = 1");
$stmt->bind_param('s', $module_id);
$stmt->execute();
$module = $stmt->get_result()->fetch_assoc();

if (!$module) {
    header('Location: index.php');
    exit;
}

// Check if user has assignment for this module
$stmt = $conn->prepare("SELECT * FROM tb_training_assignments WHERE user_email = ? AND module_id = ? AND status = 'active'");
$stmt->bind_param('ss', $user_email, $module_id);
$stmt->execute();
$assignment = $stmt->get_result()->fetch_assoc();

// Get user's current progress
$progress = null;
if ($assignment) {
    $stmt = $conn->prepare("SELECT * FROM tb_training_progress WHERE assignment_id = ?");
    $stmt->bind_param('s', $assignment['assignment_id']);
    $stmt->execute();
    $progress = $stmt->get_result()->fetch_assoc();
}

$page_title = "Treinamento em Segurança";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $page_title; ?> - LooPhish</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/loophish-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .training-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .security-alert {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .security-alert i {
            font-size: 4em;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        
        .training-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .progress-tracker {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .step {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        
        .step-number.completed {
            background: #28a745;
            color: white;
        }
        
        .step-number.current {
            background: #007bff;
            color: white;
        }
        
        .cta-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            color: white;
            font-size: 18px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="training-container">
        <!-- Security Alert -->
        <div class="security-alert">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>⚠️ ALERTA DE SEGURANÇA ⚠️</h2>
            <p class="mb-0">
                Você acabou de interagir com uma simulação de ataque de phishing! 
                Esta foi uma ação de teste para avaliar a conscientização em segurança.
            </p>
        </div>

        <!-- Training Information -->
        <div class="training-card">
            <div class="text-center mb-4">
                <i class="fas fa-graduation-cap" style="font-size: 3em; color: #667eea;"></i>
                <h1 class="mt-3"><?php echo htmlspecialchars($module['module_name']); ?></h1>
                <p class="lead"><?php echo htmlspecialchars($module['description']); ?></p>
            </div>

            <div class="info-box">
                <h5><i class="fas fa-info-circle"></i> Por que isto é importante?</h5>
                <p class="mb-0">
                    Ataques de phishing são uma das principais ameaças à segurança digital. 
                    Este treinamento irá ajudá-lo a identificar e evitar futuras tentativas de ataque.
                </p>
            </div>

            <?php if ($progress): ?>
                <!-- Show Progress -->
                <div class="progress-tracker">
                    <h5><i class="fas fa-chart-line"></i> Seu Progresso</h5>
                    
                    <div class="step">
                        <div class="step-number completed">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <strong>Simulação Detectada</strong><br>
                            <small class="text-muted">Você interagiu com a simulação de phishing</small>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number <?php echo $progress['content_progress'] > 0 ? 'completed' : 'current'; ?>">
                            <?php echo $progress['content_progress'] > 0 ? '<i class="fas fa-check"></i>' : '2'; ?>
                        </div>
                        <div>
                            <strong>Conteúdo do Treinamento</strong><br>
                            <small class="text-muted">Aprenda sobre segurança digital</small>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number <?php echo $progress['quiz_completed'] ? 'completed' : ($progress['content_progress'] >= 100 ? 'current' : ''); ?>">
                            <?php echo $progress['quiz_completed'] ? '<i class="fas fa-check"></i>' : '3'; ?>
                        </div>
                        <div>
                            <strong>Avaliação</strong><br>
                            <small class="text-muted">Teste seus conhecimentos</small>
                        </div>
                    </div>
                    
                    <div class="step">
                        <div class="step-number <?php echo $progress['status'] == 'completed' ? 'completed' : ''; ?>">
                            <?php echo $progress['status'] == 'completed' ? '<i class="fas fa-check"></i>' : '4'; ?>
                        </div>
                        <div>
                            <strong>Certificado</strong><br>
                            <small class="text-muted">Receba seu certificado de conclusão</small>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button class="btn cta-button" onclick="continueTraining()">
                        <i class="fas fa-play"></i> Continuar Treinamento
                    </button>
                </div>
            <?php else: ?>
                <!-- First Time -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $module['duration_minutes']; ?></div>
                        <div>Minutos</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $module['total_lessons']; ?></div>
                        <div>Lições</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $module['quiz_questions']; ?></div>
                        <div>Questões</div>
                    </div>
                </div>

                <h4>O que você vai aprender:</h4>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success"></i> Como identificar emails de phishing</li>
                    <li><i class="fas fa-check text-success"></i> Técnicas comuns de engenharia social</li>
                    <li><i class="fas fa-check text-success"></i> Melhores práticas de segurança</li>
                    <li><i class="fas fa-check text-success"></i> Como reportar tentativas de ataque</li>
                </ul>

                <div class="text-center">
                    <button class="btn cta-button" onclick="startTraining()">
                        <i class="fas fa-rocket"></i> Começar Treinamento
                    </button>
                </div>
            <?php endif; ?>

            <div class="mt-4 text-center">
                <small class="text-muted">
                    <i class="fas fa-shield-alt"></i> 
                    Este treinamento é parte do programa de conscientização em segurança da sua organização
                </small>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>

    <script>
        const moduleId = '<?php echo $module_id; ?>';
        const userEmail = '<?php echo $user_email; ?>';
        const assignmentId = '<?php echo $assignment['assignment_id'] ?? ''; ?>';

        function startTraining() {
            // Create assignment if doesn't exist
            if (!assignmentId) {
                $.ajax({
                    url: 'manager/training_manager.php',
                    type: 'POST',
                    data: {
                        action_type: 'assign_training',
                        user_email: userEmail,
                        module_id: moduleId,
                        auto_start: true
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.result === 'success') {
                            window.location.href = 'training_player.php?assignment=' + response.assignment_id;
                        } else {
                            alert('Erro ao iniciar treinamento: ' + response.error);
                        }
                    },
                    error: function() {
                        alert('Erro na comunicação com o servidor');
                    }
                });
            } else {
                window.location.href = 'training_player.php?assignment=' + assignmentId;
            }
        }

        function continueTraining() {
            if (assignmentId) {
                window.location.href = 'training_player.php?assignment=' + assignmentId;
            } else {
                startTraining();
            }
        }

        // Auto-record that user viewed training landing
        $(document).ready(function() {
            $.ajax({
                url: 'manager/training_manager.php',
                type: 'POST',
                data: {
                    action_type: 'track_training_view',
                    user_email: userEmail,
                    module_id: moduleId,
                    interaction_id: '<?php echo $interaction_id; ?>'
                },
                dataType: 'json'
            });
        });
    </script>
</body>
</html>

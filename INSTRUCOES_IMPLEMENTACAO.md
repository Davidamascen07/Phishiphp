# 🚀 INSTRUÇÕES DE IMPLEMENTAÇÃO - LOOPHISH V2.0
## Guia Passo-a-Passo para Desenvolvimento

---

## 📋 **FASE 1: INTEGRAÇÃO AO MENU E ESTRUTURA BASE**

### **PASSO 1.1: Atualização do Menu Principal (z_menu.php)**

**Localização:** `spear/z_menu.php` (linha ~1150)

**Ação:** Adicionar após o último item do menu (antes do fechamento do `sidebar-menu`):

```php
<!-- ADICIONAR APÓS A SEÇÃO DE CONFIGURAÇÕES -->

<!-- Módulo de Clientes -->
<div class="menu-item menu-toggle" onclick="toggleSubmenu('clients')">
    <div style="display: flex; align-items: center;">
        <i class="mdi mdi-office-building"></i>
        <span>Gestão de Clientes</span>
    </div>
</div>
<div class="submenu" id="submenu-clients">
    <a href="/spear/ClientList" class="submenu-item">
        <i class="mdi mdi-format-list-bulleted"></i> Lista de Clientes
    </a>
    <a href="/spear/ClientAdd" class="submenu-item">
        <i class="mdi mdi-plus"></i> Novo Cliente
    </a>
</div>

<!-- Módulo de Treinamento -->
<div class="menu-item menu-toggle" onclick="toggleSubmenu('training')">
    <div style="display: flex; align-items: center;">
        <i class="mdi mdi-school"></i>
        <span>Treinamentos</span>
    </div>
</div>
<div class="submenu" id="submenu-training">
    <a href="/spear/treinamento/TrainingManagement" class="submenu-item">
        <i class="mdi mdi-book-open"></i> Gestão de Treinamentos
    </a>
    <a href="/spear/treinamento/TrainingRankings" class="submenu-item">
        <i class="mdi mdi-trophy"></i> Rankings
    </a>
    <a href="/spear/treinamento/TrainingCertificates" class="submenu-item">
        <i class="mdi mdi-certificate"></i> Certificados
    </a>
</div>

<!-- Módulo de Relatórios -->
<div class="menu-item menu-toggle" onclick="toggleSubmenu('reports')">
    <div style="display: flex; align-items: center;">
        <i class="mdi mdi-chart-line"></i>
        <span>Relatórios</span>
    </div>
</div>
<div class="submenu" id="submenu-reports">
    <a href="/spear/ReportsExecutive" class="submenu-item">
        <i class="mdi mdi-file-chart"></i> Relatórios Executivos
    </a>
    <a href="/spear/ReportsTechnical" class="submenu-item">
        <i class="mdi mdi-file-table"></i> Relatórios Técnicos
    </a>
</div>
```

---

### **PASSO 1.2: Criação da Página ClientList.php**

**Localização:** `spear/ClientList.php` ja temos um arquivo ClientList.php, mas está incompleto. Vamos criar uma versão funcional básica.)

**Template base:**
```php
<?php
session_start();
include_once 'manager/session_manager.php';
include_once 'config/db.php';

// Verificar sessão
if (!isSessionValid()) {
    header("Location: index.php");
    exit;
}

// Apenas usuários admin podem gerenciar clientes
// TODO: Implementar verificação de permissão
?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Clientes | Loophish</title>
    
    <!-- CSS Files -->
    <link href="css/loophish-theme-2025.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
</head>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        
        <!-- Menu Superior e Lateral -->
        <?php include_once 'z_menu.php'; ?>
        
        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Container fluid  -->
            <div class="container-fluid">
                <!-- Título da página -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Gestão de Clientes</h4>
                                <!-- TODO: Implementar CRUD de clientes -->
                                <div id="clientsTable">
                                    <p>Interface de gestão de clientes em desenvolvimento...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
    <script src="js/libs/bootstrap.min.js"></script>
    <script src="js/libs/custom.min.js"></script>
    <!-- TODO: Criar js/client_management.js -->
</body>
</html>
```

---

### **PASSO 1.3: Correção de Paths no Módulo de Treinamento**

**Arquivos a corrigir:**
- `spear/treinamento/TrainingManagement.php`
- `spear/treinamento/TrainingCertificates.php`
- `spear/treinamento/TrainingRankings.php`
- `spear/treinamento/training_manager.php`

**Correção principal:**
```php
// EM TODOS OS ARQUIVOS PHP DO MÓDULO TREINAMENTO:
// ALTERAR:
require_once(dirname(__FILE__) . '/manager/session_manager.php');

// PARA:
require_once(dirname(__FILE__) . '/../manager/session_manager.php');

// E ALTERAR:
require_once(dirname(__FILE__) . '/session_manager.php');

// PARA:
require_once(dirname(__FILE__) . '/../manager/session_manager.php');
```

---

## 📋 **FASE 2: SISTEMA DE INTEGRAÇÃO CAMPANHA-TREINAMENTO**

### **PASSO 2.1: Modificação das Tabelas do Banco de Dados**

**Executar via SQL ou criar script PHP:**

```sql
-- 1. Adicionar campos para integração com treinamento nas campanhas web
ALTER TABLE tb_core_web_tracker_list 
ADD COLUMN training_integration_type ENUM('none', 'direct', 'post_submit') DEFAULT 'none' AFTER tracker_code,
ADD COLUMN training_module_id VARCHAR(50) NULL AFTER training_integration_type,
ADD COLUMN require_password_change TINYINT(1) DEFAULT 0 AFTER training_module_id,
ADD INDEX idx_training_module (training_module_id);

-- 2. Adicionar campos para integração com treinamento nas campanhas de email
ALTER TABLE tb_core_mailcamp_list 
ADD COLUMN training_integration_type ENUM('none', 'direct', 'post_submit') DEFAULT 'none' AFTER camp_lock,
ADD COLUMN training_module_id VARCHAR(50) NULL AFTER training_integration_type,
ADD COLUMN require_password_change TINYINT(1) DEFAULT 0 AFTER training_module_id,
ADD INDEX idx_training_module (training_module_id);

-- 3. Criar tabela de tokens temporários para acesso ao treinamento
CREATE TABLE IF NOT EXISTS tb_training_access_tokens (
    token_id VARCHAR(50) PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    user_name VARCHAR(100) DEFAULT NULL,
    campaign_id VARCHAR(50) NOT NULL,
    campaign_type ENUM('web', 'email') NOT NULL,
    training_module_id VARCHAR(50) NOT NULL,
    client_id VARCHAR(50) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'used', 'expired') DEFAULT 'active',
    user_ip VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    
    INDEX idx_token_hash (token_hash),
    INDEX idx_expires (expires_at),
    INDEX idx_user_email (user_email),
    INDEX idx_campaign (campaign_id, campaign_type),
    
    FOREIGN KEY (client_id) REFERENCES tb_clients(client_id) ON DELETE CASCADE,
    FOREIGN KEY (training_module_id) REFERENCES tb_training_modules(module_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Adicionar foreign keys (apenas se as tabelas de treinamento existirem)
-- ALTER TABLE tb_core_web_tracker_list ADD CONSTRAINT fk_webtracker_training 
--     FOREIGN KEY (training_module_id) REFERENCES tb_training_modules(module_id) ON DELETE SET NULL;

-- ALTER TABLE tb_core_mailcamp_list ADD CONSTRAINT fk_mailcamp_training 
--     FOREIGN KEY (training_module_id) REFERENCES tb_training_modules(module_id) ON DELETE SET NULL;
```

---

### **PASSO 2.2: Modificação do TrackerGenerator.php**

**Localização:** `spear/TrackerGenerator.php` (linha ~140-150, após o campo "Final destination URL")

**Adicionar seção:**
```html
<!-- INTEGRAÇÃO COM TREINAMENTO -->
<div class="row mb-3 align-items-left m-t-20">
    <label for="training_integration_type" class="col-sm-2 text-left control-label col-form-label">Integração com Treinamento: </label>
    <div class="col-sm-8 custom-control">
        <select class="form-control" id="training_integration_type" name="training_integration_type">
            <option value="none">Sem integração</option>
            <option value="direct">Redirecionamento direto ao treinamento</option>
            <option value="post_submit">Após submissão dos dados</option>
        </select>
    </div>
    <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Define como o usuário será direcionado para o treinamento."></i>
</div>

<div class="row mb-3 align-items-left" id="training_module_row" style="display:none;">
    <label for="training_module_id" class="col-sm-2 text-left control-label col-form-label">Módulo de Treinamento: </label>
    <div class="col-sm-8 custom-control">
        <select class="form-control" id="training_module_id" name="training_module_id">
            <option value="">Selecione um módulo...</option>
            <!-- Carregado via AJAX -->
        </select>
    </div>
    <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Selecione o módulo de treinamento que será apresentado."></i>
</div>

<div class="row mb-3 align-items-left" id="password_change_row" style="display:none;">
    <label for="require_password_change" class="col-sm-2 text-left control-label col-form-label">Exigir mudança de senha: </label>
    <div class="col-sm-8 custom-control">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" id="require_password_change" name="require_password_change">
            <label class="form-check-label" for="require_password_change">
                Forçar usuário a criar nova senha antes do treinamento
            </label>
        </div>
    </div>
    <i class="mdi mdi-information cursor-pointer m-t-5" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Se marcado, o usuário precisará definir uma nova senha antes de acessar o treinamento."></i>
</div>
```

**Adicionar JavaScript no final do arquivo:**
```javascript
<script>
$(document).ready(function() {
    // Controlar exibição das opções de treinamento
    $('#training_integration_type').change(function() {
        const value = $(this).val();
        if (value === 'none') {
            $('#training_module_row, #password_change_row').hide();
        } else {
            $('#training_module_row').show();
            loadTrainingModules();
            
            if (value === 'post_submit') {
                $('#password_change_row').show();
            } else {
                $('#password_change_row').hide();
            }
        }
    });
    
    // Carregar módulos de treinamento via AJAX
    function loadTrainingModules() {
        $.ajax({
            url: 'manager/training_integration_manager.php',
            method: 'POST',
            data: {
                action: 'getTrainingModules',
                client_id: getCurrentClientId() // Função já existente
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const select = $('#training_module_id');
                    select.empty().append('<option value="">Selecione um módulo...</option>');
                    
                    response.modules.forEach(function(module) {
                        select.append(`<option value="${module.module_id}">${module.module_name}</option>`);
                    });
                }
            },
            error: function() {
                console.error('Erro ao carregar módulos de treinamento');
            }
        });
    }
});
</script>
```

---

### **PASSO 2.3: Criação do Gerenciador de Integração**

**Localização:** `spear/manager/training_integration_manager.php` (criar novo arquivo)

```php
<?php
require_once(dirname(__FILE__) . '/session_manager.php');
if(isSessionValid() == false)
    die("Access denied");

require_once(dirname(__FILE__) . '/common_functions.php');

// Receber dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    $data = $_POST;
}

$action = $data['action'] ?? '';

switch($action) {
    case 'getTrainingModules':
        getTrainingModules($conn, $data['client_id'] ?? getCurrentClientId());
        break;
        
    case 'generateAccessToken':
        generateAccessToken($conn, $data);
        break;
        
    case 'validateAccessToken':
        validateAccessToken($conn, $data['token'] ?? '');
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
        break;
}

/**
 * Buscar módulos de treinamento por cliente
 */
function getTrainingModules($conn, $client_id) {
    try {
        // Verificar se as tabelas de treinamento existem
        $check = $conn->query("SHOW TABLES LIKE 'tb_training_modules'");
        if ($check->num_rows == 0) {
            echo json_encode(['success' => false, 'message' => 'Módulo de treinamento não instalado']);
            return;
        }
        
        // Buscar módulos ativos para o cliente
        $stmt = $conn->prepare("
            SELECT module_id, module_name, module_description, estimated_duration
            FROM tb_training_modules 
            WHERE status = 1 
            ORDER BY module_name ASC
        ");
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $modules = [];
        while ($row = $result->fetch_assoc()) {
            $modules[] = $row;
        }
        
        echo json_encode(['success' => true, 'modules' => $modules]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro ao buscar módulos: ' . $e->getMessage()]);
    }
}

/**
 * Gerar token de acesso temporário para treinamento
 */
function generateAccessToken($conn, $data) {
    try {
        $user_email = $data['user_email'] ?? '';
        $user_name = $data['user_name'] ?? '';
        $campaign_id = $data['campaign_id'] ?? '';
        $campaign_type = $data['campaign_type'] ?? 'web';
        $training_module_id = $data['training_module_id'] ?? '';
        $client_id = $data['client_id'] ?? getCurrentClientId();
        
        if (empty($user_email) || empty($campaign_id) || empty($training_module_id)) {
            echo json_encode(['success' => false, 'message' => 'Dados obrigatórios não fornecidos']);
            return;
        }
        
        // Gerar token único
        $token_id = generateUniqueId();
        $token_hash = hash('sha256', $token_id . time() . $user_email);
        
        // Definir expiração (24 horas)
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Inserir token no banco
        $stmt = $conn->prepare("
            INSERT INTO tb_training_access_tokens 
            (token_id, user_email, user_name, campaign_id, campaign_type, 
             training_module_id, client_id, token_hash, expires_at, user_ip, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $user_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt->bind_param('sssssssssss', 
            $token_id, $user_email, $user_name, $campaign_id, $campaign_type,
            $training_module_id, $client_id, $token_hash, $expires_at, $user_ip, $user_agent
        );
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'token' => $token_hash,
                'expires_at' => $expires_at,
                'training_url' => "/spear/treinamento/training_landing.php?token=" . urlencode($token_hash)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao gerar token']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro interno: ' . $e->getMessage()]);
    }
}

/**
 * Validar token de acesso
 */
function validateAccessToken($conn, $token) {
    try {
        if (empty($token)) {
            echo json_encode(['success' => false, 'message' => 'Token não fornecido']);
            return;
        }
        
        // Buscar token no banco
        $stmt = $conn->prepare("
            SELECT token_id, user_email, user_name, training_module_id, client_id, 
                   expires_at, status, used_at
            FROM tb_training_access_tokens 
            WHERE token_hash = ? AND status = 'active'
        ");
        
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Verificar se não expirou
            if (strtotime($row['expires_at']) < time()) {
                // Marcar como expirado
                $update_stmt = $conn->prepare("UPDATE tb_training_access_tokens SET status = 'expired' WHERE token_hash = ?");
                $update_stmt->bind_param('s', $token);
                $update_stmt->execute();
                
                echo json_encode(['success' => false, 'message' => 'Token expirado']);
                return;
            }
            
            echo json_encode(['success' => true, 'token_data' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Token inválido']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro na validação: ' . $e->getMessage()]);
    }
}
?>
```

---

## 📋 **FASE 3: IMPLEMENTAÇÃO MULTI-TENANT NO MÓDULO TREINAMENTO**

### **PASSO 3.1: Atualização do training_manager.php**

**Localização:** `spear/treinamento/training_manager.php`

**Modificações necessárias:**

1. **Adicionar isolamento por cliente em todas as funções:**

```php
// EXEMPLO: Modificar função de buscar módulos de treinamento
function getTrainingModules($conn) {
    $current_client_id = getCurrentClientId(); // Adicionar esta linha
    
    // Alterar query para incluir filtro por cliente
    $stmt = $conn->prepare("
        SELECT module_id, module_name, module_description, module_type, 
               passing_score, estimated_duration, status, created_date
        FROM tb_training_modules 
        WHERE client_id = ? OR client_id IS NULL -- Permitir módulos globais
        ORDER BY created_date DESC
    ");
    $stmt->bind_param('s', $current_client_id);
    // ... resto da função
}

// EXEMPLO: Função para salvar módulo
function saveTrainingModule($conn, $moduleData) {
    $current_client_id = getCurrentClientId(); // Adicionar esta linha
    
    // Incluir client_id ao inserir
    if (isset($moduleData['module_id']) && !empty($moduleData['module_id'])) {
        // Update
        $stmt = $conn->prepare("
            UPDATE tb_training_modules 
            SET module_name=?, module_description=?, content_data=?, quiz_data=?, passing_score=?
            WHERE module_id=? AND client_id=?
        ");
        $stmt->bind_param('sssssss', 
            $moduleData['module_name'], $moduleData['module_description'], 
            $moduleData['content_data'], $moduleData['quiz_data'], 
            $moduleData['passing_score'], $moduleData['module_id'], $current_client_id
        );
    } else {
        // Insert
        $module_id = generateUniqueId();
        $stmt = $conn->prepare("
            INSERT INTO tb_training_modules 
            (module_id, client_id, module_name, module_description, content_data, quiz_data, passing_score, created_date)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $created_date = date('Y-m-d H:i:s');
        $stmt->bind_param('ssssssss', 
            $module_id, $current_client_id, $moduleData['module_name'], 
            $moduleData['module_description'], $moduleData['content_data'], 
            $moduleData['quiz_data'], $moduleData['passing_score'], $created_date
        );
    }
    // ... resto da função
}
```

---

### **PASSO 3.2: Atualização das Tabelas de Treinamento**

**Executar SQL para adicionar client_id onde necessário:**

```sql
-- 1. Adicionar client_id nas tabelas de treinamento se não existir
ALTER TABLE tb_training_modules 
ADD COLUMN client_id VARCHAR(50) DEFAULT NULL AFTER module_id,
ADD INDEX idx_client (client_id);

ALTER TABLE tb_training_assignments 
ADD COLUMN client_id VARCHAR(50) DEFAULT NULL AFTER assignment_id,
ADD INDEX idx_client (client_id);

-- Obs: tb_training_progress e tb_training_certificates já têm client_id

-- 2. Atualizar registros existentes com client_id padrão
UPDATE tb_training_modules SET client_id = 'default_org' WHERE client_id IS NULL;
UPDATE tb_training_assignments SET client_id = 'default_org' WHERE client_id IS NULL;

-- 3. Adicionar foreign keys
ALTER TABLE tb_training_modules 
ADD CONSTRAINT fk_training_modules_client 
FOREIGN KEY (client_id) REFERENCES tb_clients(client_id) ON DELETE CASCADE;

ALTER TABLE tb_training_assignments 
ADD CONSTRAINT fk_training_assignments_client 
FOREIGN KEY (client_id) REFERENCES tb_clients(client_id) ON DELETE CASCADE;
```

---

## 📋 **RESUMO DE PRIORIDADES DE IMPLEMENTAÇÃO**

### **🔥 ALTA PRIORIDADE (Fazer primeiro):**
1. ✅ **FASE 1.1:** Atualizar menu principal
2. ✅ **FASE 1.3:** Corrigir paths do módulo treinamento
3. ✅ **FASE 2.1:** Modificar tabelas do banco
4. ✅ **FASE 2.2:** Modificar TrackerGenerator.php
5. ✅ **FASE 3.1:** Implementar multi-tenant no treinamento

### **⚡ MÉDIA PRIORIDADE (Fazer em seguida):**
6. **FASE 1.2:** Criar página ClientList.php completa
7. **FASE 2.3:** Criar gerenciador de integração
8. **FASE 3.2:** Funcionalidades avançadas de treinamento

### **💡 BAIXA PRIORIDADE (Fazer por último):**
9. Sistema de certificados
10. Relatórios avançados
11. Personalização de hospedagem

---

## 🔧 **COMANDOS ÚTEIS PARA DEBUGGING**

### **Verificar estrutura das tabelas:**
```sql
DESCRIBE tb_training_modules;
DESCRIBE tb_training_access_tokens;
SHOW COLUMNS FROM tb_core_web_tracker_list LIKE '%training%';
```

### **Verificar dados de teste:**
```sql
SELECT * FROM tb_clients;
SELECT * FROM tb_training_modules WHERE client_id = 'default_org';
SELECT * FROM tb_training_access_tokens WHERE status = 'active';
```

### **Debug JavaScript (Console do navegador):**
```javascript
// Verificar se jQuery carregou
console.log('jQuery version:', $.fn.jquery);

// Testar chamada AJAX
$.post('manager/training_integration_manager.php', {
    action: 'getTrainingModules',
    client_id: 'default_org'
}, function(data) {
    console.log('Response:', data);
});
```

---

**🎯 PRÓXIMOS PASSOS:** Seguir a ordem de prioridade alta primeiro, testar cada implementação antes de prosseguir para a próxima fase.
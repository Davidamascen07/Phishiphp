# 📋 PLANO DE DESENVOLVIMENTO - LOOPHISH V2.0
## Expansão dos Módulos de Treinamento, Cliente e Relatórios

### 📊 **ANÁLISE DO ESTADO ATUAL**

#### ✅ **Sistema Multi-tenant Implementado**
- ✅ Tabela `tb_clients` criada com estrutura completa
- ✅ Sistema de sessão com `getCurrentClientId()` funcional
- ✅ Isolamento de dados implementado em:
  - QuickTracker (referência padrão)
  - WebTracker (implementado recentemente)
  - MailCampaign, MailTemplate, MailSender, MailUserGroup
- ✅ Interface de seleção de cliente no header

#### ✅ **Módulo de Treinamento Base Criado**
- ✅ Estrutura de tabelas definida:
  - `tb_training_modules` - Módulos de treinamento
  - `tb_training_assignments` - Atribuições por cliente/campanha
  - `tb_training_progress` - Progresso individual dos usuários
  - `tb_training_certificates` - Certificados emitidos
  - `tb_training_rankings` - Rankings gamificados
- ✅ Páginas base criadas (TrainingManagement.php, etc.)

#### ✅ **Sistema de Relatórios Executivos**
- ✅ Base implementada (ReportsExecutive.php)
- ✅ Framework Chart.js integrado
- ✅ Estrutura de relatórios dinâmicos

#### ⚠️ **Lacunas Identificadas**
- ❌ Módulos não integrados ao menu principal
- ❌ Fluxo de integração campanha → treinamento inexistente
- ❌ Sistema de tokens temporários não implementado
- ❌ Gamificação e certificados não funcionais
- ❌ Relatórios não conectados aos dados reais

---

## 🎯 **PLANO DE IMPLEMENTAÇÃO**

### **FASE 1: INTEGRAÇÃO AO MENU E ESTRUTURA BASE (Prioridade: ALTA)**

#### 1.1 **Atualização do Menu Principal**
```php
// Adicionar ao z_menu.php
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

<div class="menu-item menu-toggle" onclick="toggleSubmenu('clients')">
    <div style="display: flex; align-items: center;">
        <i class="mdi mdi-office-building"></i>
        <span>Clientes</span>
    </div>
</div>
<div class="submenu" id="submenu-clients">
    <a href="/spear/ClientList" class="submenu-item">
        <i class="mdi mdi-format-list-bulleted"></i> Lista de Clientes
    </a>
</div>

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

#### 1.2 **Criação da Página ClientList.php**
- Listar clientes com filtros
- Operações CRUD completas
- Integração com sistema multi-tenant

#### 1.3 **Correção de Paths nos Módulos de Treinamento**
- Ajustar includes para usar caminhos relativos corretos
- Garantir carregamento do session_manager.php

---

### **FASE 2: SISTEMA DE INTEGRAÇÃO CAMPANHA-TREINAMENTO (Prioridade: ALTA)**

#### 2.1 **Modificação da Estrutura de Campanhas**

##### **Tabela tb_core_web_tracker_list - Adicionar campos:**
```sql
ALTER TABLE tb_core_web_tracker_list ADD COLUMN training_integration_type ENUM('none', 'direct', 'post_submit') DEFAULT 'none';
ALTER TABLE tb_core_web_tracker_list ADD COLUMN training_module_id VARCHAR(50) NULL;
ALTER TABLE tb_core_web_tracker_list ADD COLUMN require_password_change TINYINT(1) DEFAULT 0;
ALTER TABLE tb_core_web_tracker_list ADD FOREIGN KEY (training_module_id) REFERENCES tb_training_modules(module_id);
```

##### **Tabela tb_core_mailcamp_list - Adicionar campos:**
```sql
ALTER TABLE tb_core_mailcamp_list ADD COLUMN training_integration_type ENUM('none', 'direct', 'post_submit') DEFAULT 'none';
ALTER TABLE tb_core_mailcamp_list ADD COLUMN training_module_id VARCHAR(50) NULL;
ALTER TABLE tb_core_mailcamp_list ADD COLUMN require_password_change TINYINT(1) DEFAULT 0;
ALTER TABLE tb_core_mailcamp_list ADD FOREIGN KEY (training_module_id) REFERENCES tb_training_modules(module_id);
```

#### 2.2 **Sistema de Tokens Temporários**

##### **Nova tabela tb_training_access_tokens:**
```sql
CREATE TABLE tb_training_access_tokens (
    token_id VARCHAR(50) PRIMARY KEY,
    user_email VARCHAR(100) NOT NULL,
    campaign_id VARCHAR(50) NOT NULL,
    training_module_id VARCHAR(50) NOT NULL,
    client_id VARCHAR(50) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'used', 'expired') DEFAULT 'active',
    INDEX idx_token_hash (token_hash),
    INDEX idx_expires (expires_at),
    FOREIGN KEY (client_id) REFERENCES tb_clients(client_id),
    FOREIGN KEY (training_module_id) REFERENCES tb_training_modules(module_id)
);
```

#### 2.3 **Fluxos de Integração**

##### **Fluxo Completo (post_submit):**
1. Usuário clica no link da campanha
2. Acessa página de phishing
3. Submete dados → Redirecionado para treinamento
4. Token temporário gerado e enviado por email
5. Acesso ao treinamento com mudança de senha obrigatória

##### **Fluxo Simples (direct):**
1. Usuário clica no link da campanha
2. Redirecionado diretamente para treinamento
3. Token temporário gerado automaticamente

#### 2.4 **Modificação do TrackerGenerator.php**
```php
// Adicionar seção de configuração de treinamento
<div class="row mb-3">
    <label class="col-sm-3 control-label">Integração com Treinamento:</label>
    <div class="col-sm-9">
        <select id="training_integration_type" class="form-control">
            <option value="none">Sem integração</option>
            <option value="direct">Redirecionamento direto</option>
            <option value="post_submit">Após submissão dos dados</option>
        </select>
    </div>
</div>

<div class="row mb-3" id="training_module_selection" style="display:none;">
    <label class="col-sm-3 control-label">Módulo de Treinamento:</label>
    <div class="col-sm-9">
        <select id="training_module_id" class="form-control">
            <!-- Carregado via AJAX por cliente -->
        </select>
    </div>
</div>

<div class="row mb-3" id="password_change_option" style="display:none;">
    <label class="col-sm-3 control-label">Exigir mudança de senha:</label>
    <div class="col-sm-9">
        <input type="checkbox" id="require_password_change"> Sim
    </div>
</div>
```

---

### **FASE 3: MÓDULO DE TREINAMENTO FUNCIONAL (Prioridade: ALTA)**

#### 3.1 **Implementação Multi-tenant no Módulo de Treinamento**
- Adicionar `getCurrentClientId()` em todas as operações
- Filtrar dados por cliente em todas as consultas
- Garantir isolamento completo entre clientes

#### 3.2 **Sistema de Player de Treinamento**
```php
// training_player.php - Melhorias necessárias:
// 1. Validação de token de acesso
// 2. Controle de progresso em tempo real
// 3. Sistema de quiz interativo
// 4. Salvamento automático de progresso
```

#### 3.3 **Editor de Conteúdo de Treinamento**
- Editor WYSIWYG para criação de conteúdo
- Upload de vídeos e imagens
- Criador de quiz com múltiplas opções
- Sistema de templates de treinamento

#### 3.4 **Sistema de Gamificação**
```php
// Implementar em training_rankings.js:
// 1. Ranking em tempo real por cliente
// 2. Sistema de pontos por atividade
// 3. Badges e conquistas
// 4. Comparação entre departamentos
```

---

### **FASE 4: SISTEMA DE CERTIFICADOS (Prioridade: MÉDIA)**

#### 4.1 **Gerador de Certificados**
- Templates personalizáveis por cliente
- Sistema de validação com QR Code
- Geração automática em PDF
- Portal de verificação de certificados

#### 4.2 **Templates de Certificado**
```html
<!-- Estrutura base para template de certificado -->
<div class="certificate-template">
    <div class="certificate-header">
        <img src="[CLIENT_LOGO]" alt="Logo">
        <h1>CERTIFICADO DE CONCLUSÃO</h1>
    </div>
    <div class="certificate-body">
        <p>Certificamos que</p>
        <h2>[USER_NAME]</h2>
        <p>concluiu com sucesso o treinamento</p>
        <h3>[TRAINING_MODULE_NAME]</h3>
        <p>com pontuação de [SCORE]% em [COMPLETION_DATE]</p>
    </div>
    <div class="certificate-footer">
        <div class="validation">
            <p>Código de validação: [VALIDATION_CODE]</p>
            <div class="qr-code">[QR_CODE]</div>
        </div>
    </div>
</div>
```

---

### **FASE 5: SISTEMA DE RELATÓRIOS AVANÇADOS (Prioridade: MÉDIA)**

#### 5.1 **Relatórios Executivos**
- Dashboard executivo por cliente
- Métricas de efetividade das campanhas
- Análise de progresso nos treinamentos
- Identificação de usuários/departamentos críticos

#### 5.2 **Relatórios Técnicos (CSV)**
```php
// Estrutura do relatório técnico:
// 1. Dados de abertura de email (quantas vezes, quando)
// 2. Dados de cliques em links (quantas vezes, quando)
// 3. Dados de submissão de formulários
// 4. Progresso nos treinamentos
// 5. Top 5 usuários mais críticos
// 6. Top 3 departamentos mais críticos
```

#### 5.3 **Relatórios em PDF**
- Relatórios executivos formatados
- Gráficos e charts integrados
- Personalização por cliente (logo, cores)
- Agendamento automático de relatórios

---

### **FASE 6: PERSONALIZAÇÃO E HOSPEDAGEM (Prioridade: BAIXA)**

#### 6.1 **Remoção de Referências ao Código Original**
- Alterar diretório padrão de `/spear/` para `/loophish/`
- Remover referências a "SniperPhish"
- Atualizar todos os links e caminhos
- Personalizar branding completamente

#### 6.2 **Configuração de Hospedagem Personalizada**
- Sistema de configuração de domínios por cliente
- SSL automático por cliente
- Isolamento de assets por cliente

---

## 🛠️ **INSTRUÇÕES DE IMPLEMENTAÇÃO**

### **ORDEM DE EXECUÇÃO RECOMENDADA:**

1. **FASE 1 → FASE 2 → FASE 3** (Implementação paralela possível)
2. **FASE 4 → FASE 5** (Podem ser implementadas em paralelo)
3. **FASE 6** (Última fase - ajustes finais)

### **CRITÉRIOS DE QUALIDADE:**

#### ✅ **Checklist de Implementação para cada Módulo:**
- [ ] Isolamento multi-tenant implementado
- [ ] Validação de sessão e permissões
- [ ] Interface responsiva e moderna
- [ ] Tratamento de erros adequado
- [ ] Logging de atividades
- [ ] Testes de funcionalidade
- [ ] Documentação atualizada

#### 🔄 **Padrões de Desenvolvimento:**
- **Backend:** PHP 7.4+ com prepared statements
- **Frontend:** Bootstrap 5, jQuery, Chart.js
- **Database:** MySQL 8.0+ com foreign keys
- **Security:** Validação de entrada, sanitização, CSRF protection
- **Performance:** Lazy loading, cache inteligente

### **ESTRUTURA DE ARQUIVOS SUGERIDA:**
```
spear/
├── clients/
│   ├── ClientList.php
│   ├── ClientManager.php
│   └── js/client_management.js
├── treinamento/
│   ├── TrainingManagement.php
│   ├── TrainingPlayer.php
│   ├── TrainingCertificates.php
│   ├── TrainingRankings.php
│   ├── manager/training_manager.php
│   └── js/training_*.js
├── reports/
│   ├── ReportsExecutive.php
│   ├── ReportsTechnical.php
│   ├── manager/reports_manager.php
│   └── js/reports_*.js
└── integration/
    ├── campaign_training_integration.php
    ├── token_manager.php
    └── js/campaign_integration.js
```

---

## 📊 **MÉTRICAS DE SUCESSO**

### **Indicadores de Implementação:**
- ✅ 100% dos módulos integrados ao menu principal
- ✅ Sistema multi-tenant funcionando em todos os módulos
- ✅ Fluxo completo campanha → treinamento operacional
- ✅ Sistema de certificados gerando PDFs válidos
- ✅ Relatórios exportando dados corretos

### **Indicadores de Qualidade:**
- ✅ Zero quebras de isolamento entre clientes
- ✅ Tempo de resposta < 2s para operações principais
- ✅ Interface 100% responsiva
- ✅ Código seguindo padrões PSR

### **Validação Final:**
- ✅ Teste completo do fluxo: Campanha → Clique → Phishing → Treinamento → Certificado
- ✅ Teste de isolamento: Cliente A não vê dados do Cliente B
- ✅ Teste de performance: Sistema suporta 100+ usuários simultâneos
- ✅ Teste de relatórios: Dados exportados conferem com dados do banco

---

**🎯 RESULTADO ESPERADO:** Plataforma Loophish completamente funcional com módulos integrados de treinamento, gestão de clientes e relatórios avançados, mantendo total isolamento multi-tenant e performance otimizada.
# 🔒 Loophish

**Plataforma de Awareness em Segurança Cibernética**

Loophish é uma plataforma abrangente para testes de phishing, conscientização em segurança e treinamento de colaboradores, projetada para organizações que desejam fortalecer sua postura de segurança cibernética através de simulações realistas e campanhas educativas.

![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=flat&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat&logo=javascript&logoColor=black)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3+-7952B3?style=flat&logo=bootstrap&logoColor=white)

## 📋 Índice

- [Características Principais](#-características-principais)
- [Arquitetura Multi-tenant](#-arquitetura-multi-tenant)
- [Pré-requisitos](#-pré-requisitos)
- [Instalação](#-instalação)
- [Configuração](#-configuração)
- [Módulos do Sistema](#-módulos-do-sistema)
- [API e Integrações](#-api-e-integrações)
- [Estrutura do Banco de Dados](#-estrutura-do-banco-de-dados)
- [Segurança](#-segurança)
- [Monitoramento e Analytics](#-monitoramento-e-analytics)
- [Contribuindo](#-contribuindo)
- [Licença](#-licença)

## 🚀 Características Principais

### 🎯 Campanhas de Phishing
- **Criação de Campanhas Personalizadas**: Templates profissionais e editáveis
- **Agendamento Avançado**: Sistema de cronograma flexível para execução automatizada
- **Gestão de Grupos de Usuários**: Segmentação inteligente por departamentos e perfis
- **Múltiplos Remetentes**: Configuração de diversos perfis SMTP para realismo
- **Relatórios Detalhados**: Analytics completos de abertura, cliques e submissões

### 🕸️ Rastreadores Web
- **Páginas de Phishing Realistas**: Editor WYSIWYG para criação de landing pages
- **Captura de Credenciais**: Monitoramento seguro de dados inseridos
- **Rastreamento de Comportamento**: Analytics de navegação e interação
- **Templates Responsivos**: Páginas otimizadas para desktop e mobile
- **Integração com Redes Sociais**: Simulação de plataformas populares

### ⚡ Trackers Rápidos
- **Links de Rastreamento Instantâneo**: Geração rápida de URLs monitoradas
- **QR Codes Dinâmicos**: Criação automática de códigos para campanhas móveis
- **Métricas em Tempo Real**: Monitoramento live de cliques e acessos
- **Geolocalização**: Tracking de origem geográfica dos acessos
- **Análise de Dispositivos**: Detecção de browsers, SOs e dispositivos

### 👥 Gestão Multi-tenant
- **Múltiplos Clientes**: Isolamento completo de dados entre organizações
- **Permissões Granulares**: Controle de acesso por usuário e funcionalidade
- **Branding Personalizado**: Logos, cores e identidade visual por cliente
- **Configurações Específicas**: Parâmetros personalizáveis por organização
- **Relatórios Segregados**: Analytics exclusivos para cada cliente

## 🏗️ Arquitetura Multi-tenant

O Loophish implementa uma arquitetura multi-tenant robusta que permite:

```
┌─────────────────────────────────────────────────────────────┐
│                    LOOPHISH PLATFORM                        │
├─────────────────┬─────────────────┬─────────────────────────┤
│   CLIENTE A     │   CLIENTE B     │      CLIENTE C          │
│                 │                 │                         │
│ ┌─ Campanhas    │ ┌─ Campanhas    │ ┌─ Campanhas            │
│ ├─ Trackers     │ ├─ Trackers     │ ├─ Trackers             │
│ ├─ Usuários     │ ├─ Usuários     │ ├─ Usuários             │
│ ├─ Templates    │ ├─ Templates    │ ├─ Templates            │
│ └─ Analytics    │ └─ Analytics    │ └─ Analytics            │
└─────────────────┴─────────────────┴─────────────────────────┘
```

### Isolamento de Dados
- **Separação Física**: Cada cliente possui seus próprios dados isolados
- **Foreign Keys**: Relacionamentos garantem integridade referencial
- **Views Dedicadas**: Consultas automaticamente filtradas por contexto
- **Auditoria Completa**: Log de todas as ações por cliente

## 📋 Pré-requisitos

### Servidor Web
- **Apache 2.4+** ou **Nginx 1.18+**
- **PHP 7.4+** (Recomendado: PHP 8.1+)
- **MySQL 8.0+** ou **MariaDB 10.5+**

### Extensões PHP Obrigatórias
```bash
php-mysqli
php-json
php-session
php-curl
php-mbstring
php-openssl
php-gd
php-zip
```

### Extensões PHP Recomendadas
```bash
php-opcache      # Performance
php-redis        # Cache (opcional)
php-memcached    # Cache (opcional)
php-imagick      # Manipulação de imagens
```

### Recursos do Servidor
- **RAM**: Mínimo 2GB (Recomendado: 4GB+)
- **Storage**: Mínimo 5GB (Recomendado: 20GB+ para logs e uploads)
- **CPU**: 2+ cores para melhor performance

## 🛠️ Instalação

### 1. Download e Extração
```bash
# Clone do repositório
git clone https://github.com/seu-usuario/loophish.git
cd loophish

# Ou download direto
wget -O loophish.zip [URL_DO_PROJETO]
unzip loophish.zip
```

### 2. Configuração do Servidor Web

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Proteção de arquivos sensíveis
<Files "*.sql">
    Order allow,deny
    Deny from all
</Files>

<Files "config.php">
    Order allow,deny
    Deny from all
</Files>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name seu-dominio.com;
    root /var/www/loophish;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Proteção de arquivos
    location ~ \.(sql|env|config)$ {
        deny all;
    }
}
```

### 3. Configuração do Banco de Dados

```sql
-- Criar banco de dados
CREATE DATABASE db_loophish CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário
CREATE USER 'loophish'@'localhost' IDENTIFIED BY 'sua_senha_segura';
GRANT ALL PRIVILEGES ON db_loophish.* TO 'loophish'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Importação da Estrutura
```bash
# Importar estrutura básica
mysql -u loophish -p db_loophish < spear/sql/db_loophish.sql

# Aplicar correções de relacionamento
mysql -u loophish -p db_loophish < spear/sql/fix_client_relationships.sql
```

### 5. Configuração da Aplicação

Copie e configure o arquivo de conexão:
```bash
cp spear/config/db.php.example spear/config/db.php
```

Edite o arquivo `spear/config/db.php`:
```php
<?php
$servername = "localhost";
$db_username = "loophish";
$db_password = "sua_senha_segura";
$dbname = "db_loophish";

// Criar conexão
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

### 6. Permissões de Arquivo
```bash
# Permissões básicas
chmod -R 755 loophish/
chmod -R 777 loophish/spear/uploads/
chmod -R 777 loophish/spear/logs/

# Proteger arquivos de configuração
chmod 600 spear/config/db.php
```

### 7. Acesso Inicial

Acesse: `http://seu-dominio.com/install.php`

**Credenciais Padrão:**
- Usuário: `admin`
- Senha: `admin123`

⚠️ **IMPORTANTE**: Altere as credenciais padrão imediatamente após o primeiro login!

## ⚙️ Configuração

### Configuração SMTP

Para campanhas de email, configure os servidores SMTP em **Configurações → Remetentes**:

```php
// Exemplo: Gmail
Servidor SMTP: smtp.gmail.com:587
Autenticação: TLS
Usuário: seu-email@gmail.com
Senha: senha-do-app (não a senha da conta)
```

### Configuração de DNS

Para melhor entregabilidade, configure registros SPF e DKIM:

```dns
; SPF Record
example.com. IN TXT "v=spf1 include:_spf.google.com ~all"

; DKIM Record (configure no provedor de email)
default._domainkey.example.com. IN TXT "v=DKIM1; k=rsa; p=SUA_CHAVE_PUBLICA"
```

### Configuração de SSL/TLS

Para páginas de phishing realistas, configure HTTPS:

```bash
# Certbot (Let's Encrypt)
certbot --apache -d seu-dominio.com

# Ou configure certificado próprio no Apache/Nginx
```

## 📦 Módulos do Sistema

### 🎯 Campanhas de Email

#### Funcionalidades
- **Editor de Templates**: Interface WYSIWYG para criação de emails
- **Gestão de Remetentes**: Múltiplos perfis SMTP configuráveis
- **Grupos de Usuários**: Segmentação inteligente de destinatários
- **Agendamento**: Sistema de cronograma para envios automatizados
- **Tracking Avançado**: Monitoramento de aberturas, cliques e submissões

#### Arquivos Principais
```
spear/
├── MailCampaignList.php     # Listagem de campanhas
├── MailTemplate.php         # Editor de templates
├── MailSender.php          # Configuração de remetentes
├── MailUserGroup.php       # Gestão de grupos
└── manager/
    ├── mail_campaign_manager.php
    └── mail_template_manager.php
```

### 🕸️ Rastreadores Web

#### Funcionalidades
- **Landing Pages**: Criação de páginas de phishing personalizadas
- **Editor Visual**: Interface drag-and-drop para design
- **Captura de Dados**: Formulários monitorados com validação
- **Analytics**: Métricas detalhadas de navegação e conversão
- **Templates Prontos**: Biblioteca de páginas pré-configuradas

#### Arquivos Principais
```
spear/
├── TrackerGenerator.php     # Criação de rastreadores
├── TrackerList.php         # Listagem e gestão
├── TrackerReport.php       # Relatórios e analytics
└── sniperhost/
    ├── FileHost.php        # Hospedagem de arquivos
    └── lp_pages/           # Landing pages
```

### ⚡ Trackers Rápidos

#### Funcionalidades
- **Geração Instantânea**: Links de rastreamento rápidos
- **QR Codes**: Geração automática de códigos QR
- **Encurtamento de URL**: Links personalizados e rastreáveis
- **Métricas em Tempo Real**: Dashboard live de cliques
- **Análise Geográfica**: Mapeamento de origem dos acessos

#### Arquivos Principais
```
spear/
├── QuickTracker.php        # Interface principal
├── QuickTrackerReport.php  # Relatórios detalhados
└── qt.php                  # Endpoint de tracking
```

### 👥 Gestão de Clientes

#### Funcionalidades
- **Multi-tenancy**: Isolamento completo entre clientes
- **Configurações Personalizadas**: Parâmetros por organização
- **Branding**: Logos e cores personalizáveis
- **Relatórios Segregados**: Analytics exclusivos por cliente
- **Permissões Granulares**: Controle de acesso detalhado

#### Arquivos Principais
```
spear/
├── ClientList.php          # Listagem de clientes
├── SettingsGeneral.php     # Configurações gerais
└── manager/
    ├── client_manager.php
    └── session_manager.php
```

## 🔌 API e Integrações

### API REST

O Loophish oferece uma API REST para integração com sistemas externos:

#### Endpoints Principais

```http
# Autenticação
POST /api/auth/login
POST /api/auth/logout

# Campanhas
GET    /api/campaigns
POST   /api/campaigns
PUT    /api/campaigns/{id}
DELETE /api/campaigns/{id}

# Trackers
GET    /api/trackers
POST   /api/trackers
GET    /api/trackers/{id}/stats

# Relatórios
GET    /api/reports/campaigns/{id}
GET    /api/reports/trackers/{id}
GET    /api/reports/overview
```

#### Exemplo de Uso

```javascript
// Autenticação
const auth = await fetch('/api/auth/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
        username: 'admin',
        password: 'sua_senha'
    })
});

// Criar campanha
const campaign = await fetch('/api/campaigns', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`
    },
    body: JSON.stringify({
        name: 'Campanha Teste',
        template_id: 'template_123',
        user_group_id: 'group_456'
    })
});
```

### Webhooks

Configure webhooks para receber eventos em tempo real:

```php
// webhook.php - Exemplo de receptor
<?php
$payload = json_decode(file_get_contents('php://input'), true);

switch($payload['event']) {
    case 'email.opened':
        // Processar abertura de email
        break;
    case 'link.clicked':
        // Processar clique em link
        break;
    case 'form.submitted':
        // Processar submissão de formulário
        break;
}
?>
```

## 🗄️ Estrutura do Banco de Dados

### Tabelas Principais

#### Clientes e Multi-tenancy
```sql
tb_clients                  -- Informações dos clientes
tb_client_settings         -- Configurações por cliente
tb_client_users           -- Usuários por cliente
```

#### Campanhas de Email
```sql
tb_core_mailcamp_list          -- Campanhas criadas
tb_core_mailcamp_template_list -- Templates de email
tb_core_mailcamp_sender_list   -- Configurações SMTP
tb_core_mailcamp_user_group    -- Grupos de usuários
tb_data_mailcamp_live         -- Dados de envio e tracking
```

#### Rastreadores
```sql
tb_core_web_tracker_list      -- Rastreadores web
tb_core_quick_tracker_list    -- Trackers rápidos
tb_data_webpage_visit         -- Visitas às páginas
tb_data_webform_submit        -- Submissões de formulários
tb_data_quick_tracker_live    -- Cliques em links rápidos
```

#### Analytics e Relatórios
```sql
tb_executive_reports          -- Relatórios executivos
tb_executive_kpis            -- KPIs e métricas
tb_benchmarking_data         -- Dados de benchmark
tb_critical_users            -- Usuários de alto risco
tb_critical_departments      -- Departamentos vulneráveis
```

### Relacionamentos Chave

```sql
-- Foreign Keys para Multi-tenancy
ALTER TABLE tb_core_mailcamp_list 
ADD CONSTRAINT fk_mailcamp_client 
FOREIGN KEY (client_id) REFERENCES tb_clients(client_id);

-- Índices para Performance
CREATE INDEX idx_campaign_client_status 
ON tb_core_mailcamp_list (client_id, camp_status);
```

### Views para Consultas

```sql
-- Campanhas por Cliente
CREATE VIEW v_client_campaigns AS
SELECT c.client_name, c.client_id, mcl.*
FROM tb_clients c
LEFT JOIN tb_core_mailcamp_list mcl ON c.client_id = mcl.client_id;

-- Trackers por Cliente
CREATE VIEW v_client_trackers AS
SELECT c.client_name, c.client_id, 'web' as type, wtl.*
FROM tb_clients c
LEFT JOIN tb_core_web_tracker_list wtl ON c.client_id = wtl.client_id
UNION ALL
SELECT c.client_name, c.client_id, 'quick' as type, qtl.*
FROM tb_clients c
LEFT JOIN tb_core_quick_tracker_list qtl ON c.client_id = qtl.client_id;
```

## 🔐 Segurança

### Autenticação e Autorização

- **Hash de Senhas**: SHA-256 com salt
- **Sessões Seguras**: Configuração HTTPOnly e Secure
- **Controle de Acesso**: Verificação por página e função
- **Timeout de Sessão**: Expiração automática por inatividade

### Proteção de Dados

```php
// Sanitização de entrada
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Prepared Statements
$stmt = $conn->prepare("SELECT * FROM tb_users WHERE username = ? AND client_id = ?");
$stmt->bind_param("ss", $username, $client_id);
$stmt->execute();
```

### Configurações de Segurança

```php
// spear/config/security.php
return [
    'session_timeout' => 3600,        // 1 hora
    'max_login_attempts' => 5,        // Máximo de tentativas
    'lockout_duration' => 900,        // 15 minutos de bloqueio
    'password_min_length' => 8,       // Tamanho mínimo da senha
    'require_https' => true,          // Forçar HTTPS
    'csrf_protection' => true,        // Proteção CSRF
];
```

### Headers de Segurança

```php
// Configurações de segurança no Apache/Nginx
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options SAMEORIGIN
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
Header always set Content-Security-Policy "default-src 'self'"
```

## 📊 Monitoramento e Analytics

### Dashboard Executivo

O Loophish oferece um dashboard completo com:

- **KPIs em Tempo Real**: Métricas atualizadas automaticamente
- **Comparação Temporal**: Análise de tendências e evolução
- **Benchmark de Mercado**: Comparação com dados da indústria
- **Alertas Inteligentes**: Notificações de eventos críticos

### Métricas Principais

#### Campanhas de Email
- Taxa de Entrega
- Taxa de Abertura
- Taxa de Cliques
- Taxa de Submissão
- Tempo de Resposta

#### Rastreadores Web
- Visitantes Únicos
- Taxa de Conversão
- Tempo na Página
- Origem do Tráfego
- Dispositivos Utilizados

#### Indicadores de Risco
- Usuários de Alto Risco
- Departamentos Vulneráveis
- Evolução da Conscientização
- Score de Segurança Organizacional

### Relatórios Automáticos

```php
// Exemplo: Relatório Semanal Automatizado
class WeeklyReport {
    public function generate($client_id) {
        $data = [
            'campaigns' => $this->getCampaignStats($client_id),
            'trackers' => $this->getTrackerStats($client_id),
            'risks' => $this->getRiskAnalysis($client_id),
            'recommendations' => $this->getRecommendations($client_id)
        ];
        
        return $this->renderReport($data);
    }
}
```

### Exportação de Dados

Formatos suportados:
- **PDF**: Relatórios formatados para apresentação
- **Excel**: Dados brutos para análise avançada
- **CSV**: Integração com ferramentas externas
- **JSON**: API e integrações automatizadas

## 🔧 Manutenção e Troubleshooting

### Logs do Sistema

```bash
# Localização dos logs
tail -f spear/logs/application.log    # Logs da aplicação
tail -f spear/logs/security.log       # Eventos de segurança
tail -f spear/logs/email.log         # Campanhas de email
tail -f spear/logs/error.log         # Erros do sistema
```

### Backup Automatizado

```bash
#!/bin/bash
# backup.sh - Script de backup diário

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/loophish"

# Backup do banco de dados
mysqldump -u loophish -p db_loophish > ${BACKUP_DIR}/db_${DATE}.sql

# Backup dos arquivos
tar -czf ${BACKUP_DIR}/files_${DATE}.tar.gz /var/www/loophish

# Manter apenas os últimos 7 dias
find ${BACKUP_DIR} -name "*.sql" -mtime +7 -delete
find ${BACKUP_DIR} -name "*.tar.gz" -mtime +7 -delete
```

### Monitoramento de Performance

```php
// monitor.php - Script de monitoramento
<?php
$checks = [
    'database' => checkDatabase(),
    'storage' => checkStorage(),
    'memory' => checkMemory(),
    'queue' => checkEmailQueue()
];

if (array_search(false, $checks) !== false) {
    // Enviar alerta
    sendAlert('Sistema com problemas', $checks);
}
?>
```

### Troubleshooting Comum

#### Emails não são enviados
1. Verificar configurações SMTP
2. Testar conectividade com o servidor
3. Verificar logs de email
4. Validar credenciais

#### Páginas de phishing não carregam
1. Verificar permissões de arquivo
2. Confirmar configuração do servidor web
3. Verificar logs de erro
4. Testar conectividade

#### Dashboard com dados incorretos
1. Verificar relacionamentos de banco
2. Executar script de correção
3. Validar configuração de cliente
4. Atualizar estatísticas

## 🤝 Contribuindo

### Como Contribuir

1. **Fork do Projeto**
```bash
git clone https://github.com/seu-usuario/loophish.git
cd loophish
git checkout -b feature/nova-funcionalidade
```

2. **Padrões de Código**
- Seguir PSR-12 para PHP
- Usar ESLint para JavaScript
- Documentar funções públicas
- Incluir testes quando aplicável

3. **Submissão de PR**
- Descrever claramente as mudanças
- Incluir screenshots se aplicável
- Referenciar issues relacionadas
- Aguardar review da equipe

### Reportar Bugs

Use o template de issue para reportar problemas:

```markdown
## Descrição do Bug
[Descrição clara do problema]

## Passos para Reproduzir
1. Acessar [página]
2. Clicar em [botão]
3. Observar [comportamento]

## Comportamento Esperado
[O que deveria acontecer]

## Screenshots
[Se aplicável]

## Ambiente
- SO: [Windows/Linux/macOS]
- Navegador: [Chrome/Firefox/Safari]
- Versão do PHP: [7.4/8.0/8.1]
- Versão do MySQL: [8.0/5.7]
```

### Roadmap

#### Versão 2.0 (Q2 2025)
- [ ] API REST completa
- [ ] Interface mobile nativa
- [ ] Integração com LDAP/AD
- [ ] Módulo de treinamento interativo
- [ ] Dashboard em tempo real

#### Versão 2.1 (Q3 2025)
- [ ] Machine Learning para detecção de padrões
- [ ] Integração com SIEM
- [ ] Simulações de malware
- [ ] Gamificação de treinamentos
- [ ] Relatórios automatizados

## 📄 Licença

Este projeto está licenciado sob a **MIT License** - veja o arquivo [LICENSE](LICENSE) para detalhes.

### Uso Comercial

Para uso comercial ou customizações específicas, entre em contato através de:
- 📧 Email: contato@loophish.com
- 🌐 Website: https://loophish.com
- 💬 Discord: [Servidor da Comunidade](https://discord.gg/loophish)

---

## 📞 Suporte

### Documentação
- 📚 [Wiki Completa](https://github.com/seu-usuario/loophish/wiki)
- 🎥 [Tutoriais em Vídeo](https://youtube.com/loophish)
- 📖 [API Documentation](https://api.loophish.com/docs)

### Comunidade
- 💬 [Discord](https://discord.gg/loophish)
- 🐦 [Twitter](https://twitter.com/loophish)
- 📧 [Mailing List](mailto:comunidade@loophish.com)

### Suporte Técnico
- 🔧 [Issues GitHub](https://github.com/seu-usuario/loophish/issues)
- 📧 [Suporte](mailto:suporte@loophish.com)
- 📞 [Contato Comercial](tel:+5511999999999)

---

<p align="center">
  Desenvolvido com ❤️ pela equipe Loophish<br>
  <a href="https://loophish.com">https://loophish.com</a>
</p>
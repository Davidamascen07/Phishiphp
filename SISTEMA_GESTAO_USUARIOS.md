# Sistema de Gestão de Usuários e Campanhas - LooPhish V2.0

## 📋 Visão Geral

O Sistema de Gestão de Usuários e Campanhas é uma extensão completa do LooPhish V2.0 que permite:

- **Importação de usuários via CSV**
- **Criação automática de departamentos**
- **Rastreamento de participação em campanhas**
- **Gestão centralizada de usuários**
- **Relatórios de atividades**

## 🏗️ Arquitetura do Sistema

### 1. Banco de Dados

#### Tabelas Principais:
- **`tb_client_users`** - Usuários do sistema (aprimorada)
- **`tb_departments`** - Departamentos organizacionais
- **`tb_user_campaign_history`** - Histórico de participação em campanhas
- **`tb_data_mailcamp_live`** - Dados de campanhas de email (com tracking de cliques)

### 2. Componentes Backend

#### **user_management_manager.php**
- API REST completa para gestão de usuários
- Importação CSV automatizada
- CRUD de usuários e departamentos
- Rastreamento de campanhas

#### **user_campaign_hooks.php**
- Hooks automáticos para integração com campanhas
- Registro de atividades (cliques, submissões, treinamentos)
- Sincronização com sistema existente

#### **link_click.php**
- Processamento de cliques em links de campanhas
- Redirecionamento inteligente para treinamentos
- Rastreamento de atividades

### 3. Interface Frontend

#### **UserManagement.php**
- Interface web completa
- 4 abas principais:
  - **Usuários**: Lista e gestão de usuários
  - **Importar CSV**: Upload e processamento de arquivos
  - **Departamentos**: Visualização e gestão
  - **Histórico**: Atividades de campanhas

#### **user_management.js**
- Funcionalidade JavaScript completa
- DataTables com filtros avançados
- Processamento CSV em tempo real
- Modais e formulários interativos

## 📂 Estrutura de Arquivos

```
loophishx/
├── spear/
│   ├── UserManagement.php          # Interface principal
│   ├── manager/
│   │   ├── user_management_manager.php     # API Backend
│   │   └── user_campaign_hooks.php         # Hooks de integração
│   ├── js/
│   │   └── user_management.js               # Frontend JavaScript
│   └── sql/
│       ├── user_campaign_system.sql        # Schema principal
│       └── user_campaign_email_tracking.sql # Tracking de emails
├── link_click.php                  # Processador de cliques
└── tmail.php                       # Atualizado com hooks
```

## 🚀 Funcionalidades Principais

### 1. Importação de Usuários CSV

#### Formato Suportado:
```csv
First Name,Last Name,Email,Notes
João,Silva,joao.silva@empresa.com,TI
Maria,Santos,maria.santos@empresa.com,RH
```

#### Processo Automático:
1. **Upload** do arquivo CSV
2. **Validação** de formato e dados
3. **Prevenção** de duplicatas
4. **Criação** automática de departamentos
5. **Inserção** em lote no banco

### 2. Rastreamento de Campanhas

#### Eventos Rastreados:
- ✅ **Abertura de emails** (pixel tracking)
- ✅ **Cliques em links** (URL tracking)
- ✅ **Submissão de formulários** (web/email)
- ✅ **Conclusão de treinamentos**

#### Dados Coletados:
- Timestamp das atividades
- Informações do navegador/dispositivo
- IP e geolocalização
- Tipo de campanha (mail/web)

### 3. Multi-tenant com Isolamento

#### Estratégia Híbrida:
- **Usuários**: Isolados por cliente
- **Campanhas**: Isoladas por cliente
- **Templates/Remetentes**: Compartilhados (reutilização)
- **Departamentos**: Isolados por cliente

## 🔧 Instalação e Configuração

### 1. Execute os Scripts SQL

```sql
-- 1. Schema principal
SOURCE spear/sql/user_campaign_system.sql;

-- 2. Tracking de emails
SOURCE spear/sql/user_campaign_email_tracking.sql;
```

### 2. Configuração do Menu

O sistema foi automaticamente adicionado ao menu em:
**Gestão de Clientes > Gestão de Usuários**

### 3. Permissões de Arquivos

Certifique-se que o diretório `spear/uploads/csv/` tenha permissões de escrita.

## 📊 Exemplos de Uso

### 1. Importar Usuários

```php
// Via interface web
1. Acesse UserManagement.php
2. Clique na aba "Importar CSV"
3. Selecione arquivo CSV
4. Clique "Processar Importação"

// Via API (POST)
{
    "action_type": "import_csv",
    "csv_data": [
        ["João", "Silva", "joao@empresa.com", "TI"],
        ["Maria", "Santos", "maria@empresa.com", "RH"]
    ]
}
```

### 2. Consultar Histórico de Campanhas

```php
// Via API (POST)
{
    "action_type": "get_user_campaign_history",
    "user_email": "joao@empresa.com"
}
```

### 3. Estatísticas de Departamento

```php
// Via API (POST)
{
    "action_type": "get_department_stats",
    "department_id": "123"
}
```

## 🔗 Integração com Campanhas

### 1. Campanhas de Email

O sistema se integra automaticamente via hooks:

```php
// Em tmail.php - já integrado
onEmailOpened($conn, $user_email, $campaign_id);

// Em link_click.php - novo arquivo
onLinkClicked($conn, $user_email, $campaign_id);
```

### 2. Campanhas Web

```php
// Em track.php - já integrado
onWebPageVisited($conn, $user_email, $tracker_id, $page_info);
onWebFormSubmitted($conn, $user_email, $tracker_id, $form_data);
```

### 3. Treinamentos

```php
// Integração automática
onTrainingCompleted($conn, $user_email, $campaign_id, $campaign_type);
```

## 📈 Relatórios e Métricas

### 1. Por Usuário
- Total de campanhas participadas
- Data da última atividade
- Tipos de interação (clique/submissão/treinamento)

### 2. Por Departamento
- Número de usuários
- Taxa de participação
- Estatísticas de segurança

### 3. Por Campanha
- Lista de participantes
- Taxa de abertura/clique
- Efetividade do treinamento

## 🛡️ Segurança e Privacidade

### 1. Validação de Dados
- Sanitização de entrada CSV
- Validação de emails
- Prevenção de SQL injection

### 2. Isolamento Multi-tenant
- Filtros por `client_id` em todas as consultas
- Prevenção de vazamento de dados entre clientes

### 3. Auditoria
- Log de todas as importações
- Histórico de modificações
- Rastreamento de atividades

## 🎯 Próximos Passos

### 1. Funcionalidades Avançadas
- [ ] Exportação de relatórios em Excel/PDF
- [ ] Agendamento de importações automáticas
- [ ] Integração com Active Directory/LDAP
- [ ] Dashboard de analytics em tempo real

### 2. Melhorias de Performance
- [ ] Cache de consultas frequentes
- [ ] Otimização de queries para grandes volumes
- [ ] Processamento assíncrono de importações

### 3. Interface
- [ ] Modo escuro
- [ ] Filtros avançados
- [ ] Gráficos interativos
- [ ] Notificações em tempo real

## 📞 Suporte Técnico

### Logs de Debug
- Verifique console do navegador para erros JavaScript
- Analise logs do PHP em `error_log`
- Monitore queries SQL para performance

### Resolução de Problemas
1. **Importação falhando**: Verifique formato CSV e permissões
2. **Usuários não aparecendo**: Verifique `client_id` correto
3. **Hooks não funcionando**: Verifique inclusão dos arquivos

---

**Sistema desenvolvido para LooPhish V2.0**  
**Versão:** 1.0  
**Data:** Dezembro 2024
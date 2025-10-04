# 🔧 Instruções para Aplicar as Correções - Loophish

## ⚠️ IMPORTANTE - BACKUP
Antes de aplicar qualquer alteração, faça backup completo do sistema:

```bash
# Backup do banco de dados
mysqldump -u seu_usuario -p db_loophish > backup_loophish_$(date +%Y%m%d_%H%M%S).sql

# Backup dos arquivos
tar -czf backup_arquivos_$(date +%Y%m%d_%H%M%S).tar.gz /caminho/para/loophishx/
```

## 🗄️ 1. Correções do Banco de Dados

### PASSO 1: Corrigir PRIMARY KEYs
⚠️ **EXECUTAR PRIMEIRO** - Corrige PRIMARY KEYs faltantes (especialmente tb_clients):

```sql
-- Conecte ao MySQL e execute:
mysql -u seu_usuario -p db_loophish < spear/sql/fix_primary_keys.sql
```

### PASSO 2: Aplicar Script de Relacionamentos
⚠️ **EXECUTAR DEPOIS** - Adiciona relacionamentos multi-tenant:

```sql
-- Execute após o script anterior:
mysql -u seu_usuario -p db_loophish < spear/sql/fix_client_relationships.sql
```

**O que estes scripts fazem:**
- **fix_primary_keys.sql**: Corrige PRIMARY KEYs faltantes na tb_clients e outras tabelas
- **fix_client_relationships.sql**: 
  - Adiciona coluna `client_id` nas tabelas core (mailcamp_list, web_tracker_list, quick_tracker_list)
  - Cria Foreign Keys para garantir integridade referencial  
  - Adiciona índices para melhor performance
  - Cria Views para facilitar consultas multi-tenant
  - Atualiza dados existentes para usar 'default_org' como cliente padrão
  - **Inclui proteções contra duplicidade** (pode ser executado múltiplas vezes)

## ✅ 3. Verificar se as Correções Funcionaram

Execute estas consultas para confirmar que tudo funcionou:

```sql
-- 1. Verificar se a PRIMARY KEY foi criada na tb_clients
SHOW CREATE TABLE tb_clients;

-- 2. Verificar se as colunas client_id foram adicionadas
DESCRIBE tb_core_mailcamp_list;
DESCRIBE tb_core_web_tracker_list;
DESCRIBE tb_core_quick_tracker_list;

-- 3. Verificar se as Foreign Keys foram criadas
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    REFERENCED_TABLE_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE REFERENCED_TABLE_SCHEMA = 'db_loophish' 
    AND REFERENCED_TABLE_NAME = 'tb_clients';

-- 4. Verificar se as Views foram criadas
SHOW TABLES LIKE 'v_%';

-- 5. Testar uma consulta com client_id
SELECT COUNT(*) FROM tb_core_mailcamp_list WHERE client_id = 'default_org';
```

### `/spear/manager/home_stats_manager.php`
- ✅ Compatibilidade com tabelas com e sem `client_id`
- ✅ Verificação automática da estrutura do banco
- ✅ Fallback para versão sem multi-tenancy

### `/spear/manager/session_api.php`
- ✅ Correção dos tipos de dados (string em vez de int) para `client_id`
- ✅ Melhoria no tratamento de erros
- ✅ API consistente para mudança de contexto

### `/spear/manager/session_manager.php`
- ✅ Funções de gestão de contexto de cliente
- ✅ Cookies para persistência de seleção
- ✅ Consultas otimizadas para clientes acessíveis

### `/spear/z_menu.php`
- ✅ Seletor de cliente adicionado no header
- ✅ JavaScript para mudança dinâmica de cliente
- ✅ Interface moderna com dropdown estilizado

### `/spear/Home.php`
- ✅ Seletor de cliente funcional no dashboard
- ✅ Carregamento automático de estatísticas por cliente
- ✅ Interface responsiva e moderna

## 🚀 3. Testando as Correções

### 3.1 Verificar Estrutura do Banco
```sql
-- Verificar se as colunas client_id foram adicionadas
SHOW COLUMNS FROM tb_core_mailcamp_list LIKE 'client_id';
SHOW COLUMNS FROM tb_core_web_tracker_list LIKE 'client_id';
SHOW COLUMNS FROM tb_core_quick_tracker_list LIKE 'client_id';

-- Verificar Foreign Keys
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE REFERENCED_TABLE_NAME = 'tb_clients';
```

### 3.2 Testar Interface
1. **Acesse o dashboard**: `/spear/Home.php`
2. **Verifique o seletor no header**: Deve aparecer um dropdown "Selecionar Cliente"
3. **Teste mudança de cliente**: Selecione diferentes clientes e veja se as estatísticas atualizam
4. **Verifique persistência**: Recarregue a página e confirme se o cliente selecionado se mantém

### 3.3 Verificar Logs
```bash
# Verifique se há erros nos logs do Apache/Nginx
tail -f /var/log/apache2/error.log
# ou
tail -f /var/log/nginx/error.log

# Verifique logs PHP (se configurado)
tail -f /var/log/php_errors.log
```

## 🎯 4. Recursos Implementados

### Multi-tenancy Completo
- ✅ Isolamento de dados por cliente
- ✅ Seletor de cliente no header e dashboard
- ✅ Persistência de contexto via cookies
- ✅ Foreign Keys para integridade referencial

### Interface Moderna
- ✅ Design responsivo
- ✅ Dropdown estilizado no header
- ✅ Feedback visual nas mudanças
- ✅ Loading states

### API Robusta
- ✅ Endpoints para gestão de clientes
- ✅ Tratamento de erros consistente
- ✅ Compatibilidade retroativa

### Performance Otimizada
- ✅ Índices de banco de dados
- ✅ Views para consultas complexas
- ✅ Cache de contexto de cliente

## 🔍 5. Troubleshooting

### Problema: Seletor não aparece
**Solução:**
1. Verifique se o JavaScript está carregando
2. Abra DevTools → Console para ver erros
3. Confirme se a API `/spear/manager/session_api.php` está acessível

### Problema: Estatísticas não atualizam
**Solução:**
1. Verifique se as colunas `client_id` foram adicionadas
2. Confirme se há dados nas tabelas relacionadas
3. Teste a API diretamente: `POST /spear/manager/home_stats_manager.php`

### Problema: Erro 500 ao mudar cliente
**Solução:**
1. Verifique logs de erro do servidor
2. Confirme permissões dos arquivos PHP
3. Teste conexão com banco de dados

### Problema: Foreign Key errors
**Solução:**
```sql
-- Remover Foreign Keys se necessário
ALTER TABLE tb_core_mailcamp_list DROP FOREIGN KEY fk_mailcamp_client;

-- Verificar dados órfãos
SELECT * FROM tb_core_mailcamp_list WHERE client_id NOT IN (SELECT client_id FROM tb_clients);

-- Recriar Foreign Keys após correção
ALTER TABLE tb_core_mailcamp_list 
ADD CONSTRAINT fk_mailcamp_client 
FOREIGN KEY (client_id) REFERENCES tb_clients(client_id) ON DELETE CASCADE ON UPDATE CASCADE;
```

## 📋 6. Checklist de Verificação

### Banco de Dados
- [ ] Script `fix_client_relationships.sql` executado com sucesso
- [ ] Colunas `client_id` presentes em todas as tabelas core
- [ ] Foreign Keys criadas corretamente
- [ ] Dados existentes migrados para 'default_org'

### Interface
- [ ] Seletor de cliente visível no header
- [ ] Dropdown funcional com lista de clientes
- [ ] Mudança de cliente atualiza estatísticas
- [ ] Interface responsiva em mobile

### Funcionalidade
- [ ] Estatísticas carregam corretamente por cliente
- [ ] Persistência de seleção entre páginas
- [ ] Feedback visual nas ações
- [ ] Não há erros JavaScript no console

### Performance
- [ ] Consultas executam rapidamente
- [ ] Não há queries N+1
- [ ] Cache de sessão funcionando
- [ ] Índices de banco otimizados

## 🆘 Suporte

Se encontrar problemas durante a aplicação das correções:

1. **Reverta para o backup** se necessário
2. **Verifique logs** de erro detalhadamente
3. **Teste em ambiente de desenvolvimento** primeiro
4. **Documente o erro** com detalhes para suporte

## 🎉 Resultado Esperado

Após aplicar todas as correções:

- **Dashboard funcional** com seletor de cliente
- **Estatísticas corretas** filtradas por cliente
- **Interface moderna** e responsiva
- **Sistema multi-tenant** completamente operacional
- **Dados isolados** por organização
- **Performance otimizada** com índices apropriados

---

**Data da documentação:** 02/10/2025  
**Versão:** 1.0  
**Compatibilidade:** PHP 7.4+ | MySQL 8.0+ | MariaDB 10.5+
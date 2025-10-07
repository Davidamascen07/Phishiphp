# Documentação - Dashboards Públicos LooPhish

## Visão Geral
Os dashboards de campanhas de email e web tracking agora suportam modo de visualização pública, permitindo compartilhar links limpos sem navegação interna.

## Como Funciona

### Detecção de Modo Público
O sistema detecta automaticamente quando um dashboard está sendo acessado em modo público através dos parâmetros da URL:

**MailCmpDashboard.php:**
- Requer: `mcamp` + `tk`
- Exemplo: `MailCmpDashboard.php?mcamp=1&tk=abc123`

**WebMailCmpDashboard.php:**
- Requer: `mcamp` + (`tracker` OU `tk`)
- Exemplo: `WebMailCmpDashboard.php?mcamp=1&tracker=abc123`
- Exemplo: `WebMailCmpDashboard.php?mcamp=1&tk=abc123`

### Diferenças no Modo Público

#### Interface Limpa:
- ❌ Menu lateral/navegação oculto
- ❌ Header/navbar removido  
- ❌ Scripts desnecessários não carregados
- ✅ Layout focado apenas no conteúdo
- ✅ Estilo profissional para compartilhamento

#### Funcionalidades Mantidas:
- ✅ Todos os gráficos e estatísticas
- ✅ Dados em tempo real
- ✅ Responsividade
- ✅ Interatividade dos gráficos

## Estilos CSS Aplicados

### MailCmpDashboard Público:
```css
/* Cabeçalho verde com gradiente */
body::before {
    background: linear-gradient(135deg, #28a745, #20c997);
}

/* Remove padding do wrapper */
.page-wrapper {
    padding-left: 0 !important;
}
```

### WebMailCmpDashboard Público:
```css
/* Cabeçalho azul com gradiente */  
body::before {
    background: linear-gradient(135deg, #007bff, #17a2b8);
}

/* Layout otimizado */
.page-wrapper {
    padding-left: 0 !important;
}
```

## Exemplos de URLs Públicas

### Dashboard de Email Campaign:
```
http://seusite.com/spear/MailCmpDashboard.php?mcamp=5&tk=campaign_token_123
```

### Dashboard de Web Tracking:
```
http://seusite.com/spear/WebMailCmpDashboard.php?mcamp=3&tracker=web_token_456
```

## Como Gerar Links Públicos

### No Código:
```php
// Para Email Campaign
$publicLink = "MailCmpDashboard.php?mcamp=" . $campaignId . "&tk=" . $trackingToken;

// Para Web Tracking  
$publicLink = "WebMailCmpDashboard.php?mcamp=" . $campaignId . "&tracker=" . $trackerToken;
```

### Integração com Sistema:
1. Gere tokens únicos para cada campanha
2. Armazene a relação campanha ↔ token no banco
3. Construa URLs públicas conforme necessário
4. Compartilhe links sem expor dados sensíveis

## Segurança

### Considerações:
- ✅ URLs públicas não expõem IDs internos diretamente
- ✅ Tokens podem ter expiração
- ✅ Acesso limitado apenas aos dados da campanha específica
- ⚠️ Validar tokens antes de exibir dados
- ⚠️ Implementar rate limiting se necessário

### Recomendações:
- Use tokens longos e únicos
- Considere expiração automática
- Monitore acessos aos links públicos
- Valide parâmetros de entrada

## Benefícios

### Para Compartilhamento:
- Interface profissional e limpa
- Foco total no conteúdo/dados
- Melhor experiência para clientes/stakeholders
- Páginas otimizadas (menos scripts/CSS)

### Para Performance:
- Carregamento mais rápido
- Menos recursos carregados
- Scripts desnecessários omitidos
- Layout simplificado

## Manutenção

### Arquivos Modificados:
- `spear/MailCmpDashboard.php`
- `spear/WebMailCmpDashboard.php`

### Variáveis Adicionadas:
- `$isPublicView` - Boolean que detecta modo público
- Lógica condicional para menu e scripts

### CSS Inline:
- Estilos específicos para modo público
- Gradientes diferenciados por tipo
- Layout responsivo mantido

---

**Última Atualização:** 2025-01-30  
**Versão:** LooPhish V2.0 Enhanced
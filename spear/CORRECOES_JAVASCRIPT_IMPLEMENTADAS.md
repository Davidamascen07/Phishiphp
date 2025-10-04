# Sistema Multi-Tenant - Correções de JavaScript e Integração

## 🚨 Problemas Identificados e Corrigidos

### 1. **Erro: "changeHeaderClient is not defined"**
- **Causa**: Função estava definida dentro de closure e não estava disponível globalmente
- **Correção**: Movida para escopo global e adicionada à window
- **Arquivo**: `spear/z_menu.php`

### 2. **Erro: "Cookies is not defined"**
- **Causa**: common_scripts.js tentava usar biblioteca Cookies que não estava incluída
- **Correção**: Criada função getCookie() nativa e tratamento de erro para cookies
- **Arquivo**: `spear/js/common_scripts.js`

### 3. **Erro: querySelector com seletor vazio**
- **Causa**: IDs vazios ou indefinidos sendo passados para querySelector
- **Correção**: Adicionada validação de elementos antes de usar querySelector

### 4. **Dados não atualizam após troca de cliente**
- **Causa**: Conflito entre seletor da Home.php e dropdown do header
- **Correção**: Sincronização via eventos customizados entre os sistemas

## 🔧 Arquivos Modificados

### `spear/js/common_scripts.js`
```javascript
// Antes (ERRO)
var cookie_c_data = JSON.parse(atob(decodeURIComponent(Cookies.get('c_data'))));

// Depois (CORRIGIDO)
function getCookie(name) {
    let nameEQ = name + "=";
    let ca = document.cookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

try {
    const cookieData = getCookie('c_data');
    if (cookieData) {
        cookie_c_data = JSON.parse(atob(decodeURIComponent(cookieData)));
    } else {
        cookie_c_data = { name: 'Usuário', last_login: 'N/A', timezone: 'UTC', dp_name: '1' };
    }
} catch (e) {
    cookie_c_data = { name: 'Usuário', last_login: 'N/A', timezone: 'UTC', dp_name: '1' };
}
```

### `spear/z_menu.php`
```javascript
// Função movida para escopo global
function changeHeaderClient(clientId, clientName) {
    fetch('/spear/manager/session_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'setClientContext', clientId: clientId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar interface
            const currentClientNameEl = document.getElementById('currentClientName');
            if (currentClientNameEl) {
                currentClientNameEl.textContent = clientName;
            }
            
            // Disparar evento para sincronizar outras partes
            const event = new CustomEvent('clientChanged', {
                detail: { clientId: clientId, clientName: clientName }
            });
            window.dispatchEvent(event);
            
            // Mostrar sucesso e recarregar
            toastr.success(`Cliente alterado para: ${clientName}`);
            setTimeout(() => window.location.reload(), 1500);
        }
    });
}

// Tornar função disponível globalmente
window.changeHeaderClient = changeHeaderClient;
```

### `spear/Home.php`
```javascript
// Adicionado listener para sincronização
window.addEventListener('clientChanged', function(event) {
    $('#clientSelector').val(event.detail.clientId).trigger('change.select2');
    loadClientStats();
});
```

## 🧪 Como Testar as Correções

### 1. **Teste da Troca de Cliente no Header**
1. Abra a página Home
2. Clique no dropdown de cliente no header
3. Selecione um cliente diferente
4. Verificar se:
   - ✅ Não aparece erro "changeHeaderClient is not defined"
   - ✅ Aparece mensagem de sucesso
   - ✅ Página recarrega com dados do novo cliente

### 2. **Teste da Criação de Cliente**
1. Vá para `/spear/ClientList`
2. Clique no botão "+" para adicionar cliente
3. Verificar se:
   - ✅ Não aparece erro "Cookies is not defined"
   - ✅ Modal abre corretamente
   - ✅ Formulário funciona

### 3. **Teste de Sincronização**
1. Na Home, troque cliente pelo header
2. Verificar se o select2 da página também atualiza
3. Verificar se dados são recarregados

## 🔍 Comandos de Debug

### Console do Browser
```javascript
// Verificar se funções estão disponíveis
console.log(typeof window.changeHeaderClient); // deve retornar "function"
console.log(typeof getCookie); // deve retornar "function"

// Verificar cookies
console.log(cookie_c_data); // deve mostrar objeto com dados do usuário

// Testar troca de cliente manualmente
changeHeaderClient('default_org', 'Organização Padrão');
```

### Verificar Requisições de API
1. Abrir DevTools → Network
2. Trocar cliente
3. Verificar se chamada para `session_api.php` retorna `{"success": true}`

## 📋 Próximas Validações

- [ ] Testar em diferentes navegadores
- [ ] Verificar se todas as páginas respeitam o client_id selecionado
- [ ] Validar se campanhas, templates e trackers filtram corretamente
- [ ] Testar criação/edição de clientes
- [ ] Verificar se sistema funciona com múltiplos usuários

## 🚨 Possíveis Problemas Restantes

1. **Cache do Browser**: Limpe cache se ainda houver problemas
2. **Sessões Antigas**: Faça logout/login para limpar sessões antigas
3. **Banco de Dados**: Execute o script SQL se ainda não foi executado

## 🎯 Status das Correções

- ✅ **changeHeaderClient is not defined** - CORRIGIDO
- ✅ **Cookies is not defined** - CORRIGIDO
- ✅ **querySelector inválido** - CORRIGIDO
- ✅ **Sincronização de dados** - MELHORADO
- ⚠️ **Testes finais** - PENDENTE

---

**Atualizado em**: 02/10/2025
**Status**: Correções principais implementadas, aguardando testes do usuário
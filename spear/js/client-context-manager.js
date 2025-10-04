/**
 * Client Context Manager - Sistema de gestão de contexto de cliente
 * Loophish - Fase 1 Implementation
 */

class ClientContextManager {
    constructor() {
        this.currentClientId = null;
        this.currentClientName = null;
        this.clients = [];
        this.apiUrl = '/spear/manager/session_api.php';
        this.init();
    }

    init() {
        this.loadAvailableClients();
        this.updateClientSelectorDisplay();
    }

    async loadAvailableClients() {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'getUserAccessibleClients'
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const text = await response.text();
            
            // Check if response is empty
            if (!text || text.trim() === '') {
                throw new Error('Resposta vazia do servidor');
            }
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (parseError) {
                console.error('Erro ao parsear JSON:', text);
                throw new Error('Resposta inválida do servidor');
            }
            
            if (data.success) {
                this.clients = data.clients || [];
                this.currentClientId = data.currentClientId;
                this.currentClientName = data.currentClientName || 'Nenhum cliente selecionado';
                this.populateClientDropdown();
            } else {
                console.error('Erro ao carregar clientes:', data.message);
                this.showError('Erro ao carregar lista de clientes: ' + (data.message || 'Erro desconhecido'));
            }
        } catch (error) {
            console.error('Erro na requisição:', error);
            this.showError('Erro de conexão ao carregar clientes: ' + error.message);
        }
    }

    populateClientDropdown() {
        const dropdown = document.getElementById('headerClientList');
        if (!dropdown) return;

        // Clear existing items except header
        const header = dropdown.querySelector('.dropdown-header');
        dropdown.innerHTML = '';
        if (header) dropdown.appendChild(header);

        // Add clients
        this.clients.forEach(client => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.className = 'dropdown-item';
            a.href = '#';
            a.innerHTML = `<i class="mdi mdi-domain"></i> ${client.client_name}`;
            
            if (client.client_id == this.currentClientId) {
                a.classList.add('active');
            }

            a.addEventListener('click', (e) => {
                e.preventDefault();
                this.switchClient(client.client_id, client.client_name);
            });

            li.appendChild(a);
            dropdown.appendChild(li);
        });

        // Add admin options if user has access
        if (this.clients.length > 0) {
            const divider = document.createElement('li');
            divider.innerHTML = '<hr class="dropdown-divider">';
            dropdown.appendChild(divider);

            const adminLi = document.createElement('li');
            const adminA = document.createElement('a');
            adminA.className = 'dropdown-item';
            adminA.href = '/spear/ClientList';
            adminA.innerHTML = '<i class="mdi mdi-cog"></i> Gerenciar Clientes';
            adminLi.appendChild(adminA);
            dropdown.appendChild(adminLi);
        }

        this.updateClientSelectorDisplay();
    }

    async switchClient(clientId, clientName) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'setClientContext',
                    clientId: clientId
                })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const text = await response.text();
            
            if (!text || text.trim() === '') {
                throw new Error('Resposta vazia do servidor');
            }
            
            let data;
            try {
                data = JSON.parse(text);
            } catch (parseError) {
                console.error('Erro ao parsear JSON:', text);
                throw new Error('Resposta inválida do servidor');
            }
            
            if (data.success) {
                this.currentClientId = clientId;
                this.currentClientName = clientName;
                this.updateClientSelectorDisplay();
                this.updateActiveClientInDropdown();
                
                // Show success message
                this.showSuccess(`Cliente alterado para: ${clientName}`);
                
                // Reload page to update context
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                
            } else {
                this.showError(data.message || 'Erro ao trocar cliente');
            }
        } catch (error) {
            console.error('Erro ao trocar cliente:', error);
            this.showError('Erro de conexão ao trocar cliente: ' + error.message);
        }
    }

    updateClientSelectorDisplay() {
        const clientNameEl = document.getElementById('selectedClientName');
        if (clientNameEl) {
            clientNameEl.textContent = this.currentClientName || 'Nenhum cliente';
        }
    }

    updateActiveClientInDropdown() {
        const dropdown = document.getElementById('headerClientList');
        if (!dropdown) return;

        // Remove active class from all items
        dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            item.classList.remove('active');
        });

        // Add active class to current client
        dropdown.querySelectorAll('.dropdown-item').forEach(item => {
            const text = item.textContent.trim();
            if (text.includes(this.currentClientName)) {
                item.classList.add('active');
            }
        });
    }

    showSuccess(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Sucesso',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    showError(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: message
            });
        } else {
            alert('Erro: ' + message);
        }
    }
}

// Initialize client context manager when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if the client selector elements exist
    if (document.getElementById('headerClientSelector') || document.getElementById('selectedClientName')) {
        window.clientContextManager = new ClientContextManager();
    }
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ClientContextManager;
}
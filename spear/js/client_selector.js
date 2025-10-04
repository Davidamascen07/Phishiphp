/**
 * Gerenciador do Seletor de Clientes - Fase 2 LooPhish
 * Controla a seleção dinâmica de clientes nos relatórios executivos
 */

class ClientSelectorManager {
    constructor() {
        this.currentClientId = null;
        this.clientSelectElement = null;
        this.isInitialized = false;
        
        // Configurações do Select2
        this.select2Config = {
            placeholder: 'Selecione um cliente...',
            allowClear: true,
            width: '100%',
            language: 'pt-BR',
            ajax: {
                url: '../manager/client_manager.php',
                type: 'POST',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return JSON.stringify({
                        action: 'getClients',
                        search: params.term || '',
                        page: params.page || 1
                    });
                },
                processResults: function (data, params) {
                    if (data.result === 'success') {
                        return {
                            results: data.data,
                            pagination: {
                                more: false
                            }
                        };
                    }
                    return { results: [] };
                },
                cache: true
            },
            minimumInputLength: 0,
            templateResult: this.formatClient,
            templateSelection: this.formatClientSelection,
            escapeMarkup: function (markup) { return markup; }
        };
    }

    /**
     * Inicializa o seletor de clientes
     */
    async init() {
        if (this.isInitialized) return;

        try {
            this.clientSelectElement = $('#client-selector');
            
            if (!this.clientSelectElement.length) {
                console.warn('Elemento client-selector não encontrado');
                return;
            }

            // Configura o Select2
            this.clientSelectElement.select2(this.select2Config);

            // Carrega o cliente ativo atual
            await this.loadActiveClient();

            // Configura eventos
            this.setupEventListeners();

            this.isInitialized = true;
            console.log('ClientSelectorManager inicializado com sucesso');

        } catch (error) {
            console.error('Erro ao inicializar ClientSelectorManager:', error);
        }
    }

    /**
     * Carrega o cliente ativo atual
     */
    async loadActiveClient() {
        try {
            const response = await this.makeRequest({
                action: 'getActiveClient'
            });

            if (response.result === 'success' && response.data) {
                this.currentClientId = response.data.client_id;
                
                // Se não for o cliente padrão, adiciona ao select e seleciona
                if (this.currentClientId && this.currentClientId !== 'default_org') {
                    const option = new Option(
                        response.data.client_name, 
                        this.currentClientId, 
                        true, 
                        true
                    );
                    this.clientSelectElement.append(option);
                }
                
                this.clientSelectElement.trigger('change');
            }

        } catch (error) {
            console.error('Erro ao carregar cliente ativo:', error);
        }
    }

    /**
     * Configura os event listeners
     */
    setupEventListeners() {
        // Mudança de cliente
        this.clientSelectElement.on('select2:select', async (e) => {
            const clientId = e.params.data.id;
            await this.setActiveClient(clientId);
        });

        // Limpeza da seleção
        this.clientSelectElement.on('select2:clear', async () => {
            await this.setActiveClient('default_org');
        });

        // Erro no carregamento
        this.clientSelectElement.on('select2:error', (e) => {
            console.error('Erro no Select2:', e);
        });
    }

    /**
     * Define o cliente ativo
     */
    async setActiveClient(clientId) {
        try {
            const response = await this.makeRequest({
                action: 'setActiveClient',
                client_id: clientId
            });

            if (response.result === 'success') {
                this.currentClientId = clientId;
                
                // Dispara evento customizado para notificar outros componentes
                const event = new CustomEvent('clientChanged', {
                    detail: { clientId: clientId }
                });
                document.dispatchEvent(event);

                // Atualiza a interface
                this.updateUI();

                console.log('Cliente ativo alterado para:', clientId);
            } else {
                throw new Error(response.message || 'Erro ao alterar cliente');
            }

        } catch (error) {
            console.error('Erro ao definir cliente ativo:', error);
            this.showError('Erro ao alterar cliente: ' + error.message);
        }
    }

    /**
     * Formata a exibição do cliente no dropdown
     */
    formatClient(client) {
        if (client.loading) {
            return client.text;
        }

        const container = $(`
            <div class="client-option">
                <div class="client-name">${client.text}</div>
                ${client.domain ? `<div class="client-domain">${client.domain}</div>` : ''}
                ${client.industry ? `<span class="client-industry">${client.industry}</span>` : ''}
            </div>
        `);

        return container;
    }

    /**
     * Formata a exibição do cliente selecionado
     */
    formatClientSelection(client) {
        return client.text || client.id;
    }

    /**
     * Atualiza a interface após mudança de cliente
     */
    updateUI() {
        // Atualiza badges de status se existirem
        const statusBadges = document.querySelectorAll('.client-status-badge');
        statusBadges.forEach(badge => {
            if (this.currentClientId && this.currentClientId !== 'default_org') {
                badge.style.display = 'inline-block';
                badge.textContent = `Cliente: ${this.currentClientId}`;
            } else {
                badge.style.display = 'none';
            }
        });

        // Atualiza títulos de páginas
        const pageTitle = document.querySelector('.page-title-client');
        if (pageTitle && this.currentClientId && this.currentClientId !== 'default_org') {
            pageTitle.textContent = `(${this.currentClientId})`;
            pageTitle.style.display = 'inline';
        } else if (pageTitle) {
            pageTitle.style.display = 'none';
        }
    }

    /**
     * Faz requisição AJAX para o manager
     */
    async makeRequest(data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '../manager/client_manager.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(data),
                dataType: 'json',
                success: function(response) {
                    resolve(response);
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', error);
                    reject(new Error(error || 'Erro na comunicação com o servidor'));
                }
            });
        });
    }

    /**
     * Exibe mensagem de erro
     */
    showError(message) {
        // Usa toastr se disponível, senão alert simples
        if (typeof toastr !== 'undefined') {
            toastr.error(message, 'Erro');
        } else {
            alert(message);
        }
    }

    /**
     * Obtém o cliente atual
     */
    getCurrentClientId() {
        return this.currentClientId;
    }

    /**
     * Recarrega a lista de clientes
     */
    refresh() {
        if (this.clientSelectElement) {
            this.clientSelectElement.empty();
            this.loadActiveClient();
        }
    }
}

// Instância global do gerenciador
let clientSelectorManager = null;

// Inicialização automática quando o DOM estiver pronto
$(document).ready(function() {
    clientSelectorManager = new ClientSelectorManager();
    clientSelectorManager.init();
});

// Exporta para uso global
window.ClientSelectorManager = ClientSelectorManager;
window.clientSelectorManager = clientSelectorManager;
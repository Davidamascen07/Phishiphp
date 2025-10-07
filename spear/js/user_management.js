// Debug: Verificar se jQuery está carregado
console.log('jQuery disponível:', typeof $ !== 'undefined');
console.log('Bootstrap disponível:', typeof bootstrap !== 'undefined');

var dt_users, csvData = [];
var currentClientId = null;

// Função para obter parâmetros da URL
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Detectar client_id da URL
currentClientId = getUrlParameter('client_id');
if (currentClientId) {
    console.log('Client ID detectado da URL:', currentClientId);
    // Adicionar indicador visual de qual cliente está sendo gerenciado
    setTimeout(function() {
        if ($('#clientIndicator').length === 0) {
            $('.page-title').after('<div id="clientIndicator" class="alert alert-info mt-2"><i class="fas fa-building"></i> Gerenciando usuários do cliente: <strong>' + currentClientId + '</strong></div>');
        }
    }, 1000);
}

$(document).ready(function() {
    console.log('Document ready executado');
    initializeUserManagement();
    loadUserStats();
    loadUsers();
    loadDepartments();
    loadTopUsers();
    
    // Configurar Select2
    $('#userDepartment').select2({
        placeholder: "Selecionar Departamento",
        allowClear: true,
        dropdownParent: $('#modalAddUser')
    });
});

/**
 * Inicializar componentes da página
 */
function initializeUserManagement() {
    // Verificar dependências
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables não está carregado');
        return;
    }
    if (typeof $.fn.select2 === 'undefined') {
        console.error('Select2 não está carregado');
        return;
    }
    
    console.log('Inicializando UserManagement...');
    
    // DataTable para usuários
    dt_users = $('#tableUsers').DataTable({
        "language": {
            "url": "js/libs/Portuguese-Brasil.json"
        },
        "pageLength": 25,
        "order": [[0, "asc"]],
        "columnDefs": [
            { "orderable": false, "targets": [6] } // Coluna de ações
        ]
    });
    
    // Event handlers
    $('#btnImportCSV').click(importCSVData);
    $('#btnPreviewCSV').click(previewCSVData);
    $('#csvFile').change(loadCSVFile);
    $('#btnSaveUser').click(saveUser);
    $('#btnSaveDepartment').click(saveDepartment);
    
    // Pesquisa de usuários
    $('#searchUsers').on('keyup', function() {
        dt_users.search(this.value).draw();
    });
}

/**
 * Carregar estatísticas
 */
function loadUserStats() {
    var requestData = {
        action_type: "get_user_stats"
    };
    
    // Adicionar client_id se disponível
    if (currentClientId) {
        requestData.client_id = currentClientId;
    }
    
    $.ajax({
        url: 'manager/user_management_manager.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(requestData),
        success: function(response) {
            if (response.success) {
                const stats = response.stats;
                $('#stat_total_users').text(stats.total_users);
                $('#stat_total_departments').text(stats.total_departments);
                
                // Calcular participação média (placeholder)
                let avgParticipation = stats.total_users > 0 ? 
                    Math.round((stats.top_users.length / stats.total_users) * 100) : 0;
                $('#stat_avg_participation').text(avgParticipation + '%');
            }
        },
        error: function() {
            showToast('Erro ao carregar estatísticas', 'error');
        }
    });
}

/**
 * Carregar lista de usuários
 */
function loadUsers() {
    var requestData = {
        action_type: "get_user_list"
    };
    
    // Adicionar client_id se disponível
    if (currentClientId) {
        requestData.client_id = currentClientId;
    }
    
    $.ajax({
        url: 'manager/user_management_manager.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(requestData),
        success: function(response) {
            if (response.success) {
                populateUsersTable(response.users);
            }
        },
        error: function() {
            showToast('Erro ao carregar usuários', 'error');
        }
    });
}

/**
 * Popular tabela de usuários
 */
function populateUsersTable(users) {
    dt_users.clear();
    
    users.forEach(function(user) {
        const departmentBadge = user.dept_name ? 
            `<span class="badge" style="background-color: ${user.dept_color || '#007bff'}">${user.dept_name}</span>` :
            '<span class="text-muted">Sem departamento</span>';
            
        const statusBadge = user.status == 1 ? 
            '<span class="badge badge-success">Ativo</span>' :
            '<span class="badge badge-secondary">Inativo</span>';
            
        const lastCampaign = user.last_campaign_date ? 
            new Date(user.last_campaign_date).toLocaleDateString('pt-BR') :
            '<span class="text-muted">Nunca</span>';
            
        const actions = `
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary" onclick="viewUserHistory('${user.user_email}')" title="Ver Histórico">
                    <i class="fas fa-history"></i>
                </button>
                <button class="btn btn-outline-warning" onclick="editUser('${user.id}')" title="Editar">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger" onclick="deleteUser('${user.id}')" title="Excluir">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`;
        
        dt_users.row.add([
            user.user_name || user.first_name + ' ' + user.last_name,
            user.user_email,
            departmentBadge,
            user.campaign_count || 0,
            lastCampaign,
            statusBadge,
            actions
        ]);
    });
    
    dt_users.draw();
}

/**
 * Carregar arquivo CSV
 */
function loadCSVFile() {
    const file = $('#csvFile')[0].files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const csv = e.target.result;
        csvData = parseCSV(csv);
        
        if (csvData.length > 0) {
            $('#btnPreviewCSV, #btnImportCSV').prop('disabled', false);
            $('#btnImportCSV').html('<i class="fas fa-upload"></i> Importar Usuários');
            $('#btnPreviewCSV').html('<i class="fas fa-eye"></i> Visualizar Dados');
            showToast(`${csvData.length} registros carregados`, 'success');
        } else {
            $('#btnPreviewCSV, #btnImportCSV').prop('disabled', true);
            $('#btnImportCSV').html('<i class="fas fa-upload"></i> Aguarde - sem dados');
            $('#btnPreviewCSV').html('<i class="fas fa-eye"></i> Aguarde - sem dados');
            showToast('Arquivo CSV vazio ou inválido', 'error');
        }
    };
    reader.readAsText(file);
}

/**
 * Parser simples de CSV
 */
function parseCSV(csv) {
    const lines = csv.split('\n');
    const headers = lines[0].split(',').map(h => h.replace(/"/g, '').trim());
    const data = [];
    
    for (let i = 1; i < lines.length; i++) {
        if (lines[i].trim() === '') continue;
        
        const values = parseCSVLine(lines[i]);
        if (values.length === headers.length) {
            const row = {};
            headers.forEach((header, index) => {
                row[header] = values[index] ? values[index].replace(/"/g, '').trim() : '';
            });
            data.push(row);
        }
    }
    
    return data;
}

/**
 * Parser de linha CSV (lida com vírgulas em valores)
 */
function parseCSVLine(line) {
    const result = [];
    let current = '';
    let inQuotes = false;
    
    for (let i = 0; i < line.length; i++) {
        const char = line[i];
        
        if (char === '"') {
            inQuotes = !inQuotes;
        } else if (char === ',' && !inQuotes) {
            result.push(current);
            current = '';
        } else {
            current += char;
        }
    }
    
    result.push(current);
    return result;
}

/**
 * Visualizar dados do CSV
 */
function previewCSVData() {
    if (csvData.length === 0) {
        showToast('Nenhum dado para visualizar', 'warning');
        return;
    }
    
    const headers = Object.keys(csvData[0]);
    const previewTable = $('#previewTable');
    
    // Cabeçalho
    const headerRow = headers.map(h => `<th>${h}</th>`).join('');
    previewTable.find('thead').html(`<tr>${headerRow}</tr>`);
    
    // Dados (primeiras 10 linhas)
    const tbody = previewTable.find('tbody');
    tbody.empty();
    
    const preview = csvData.slice(0, 10);
    preview.forEach(function(row) {
        const rowData = headers.map(h => `<td>${row[h] || ''}</td>`).join('');
        tbody.append(`<tr>${rowData}</tr>`);
    });
    
    $('#csvPreview').show();
}

/**
 * Importar dados do CSV
 */
function importCSVData() {
    if (csvData.length === 0) {
        showToast('Nenhum dado para importar', 'warning');
        return;
    }
    
    $('#btnImportCSV').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Importando...');
    
    $.ajax({
        url: 'manager/user_management_manager.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            action_type: "import_users_from_csv",
            csv_data: csvData
        }),
        success: function(response) {
            try {
                if (response.success) {
                    const results = response.results;
                    const resultHtml = `
                        <div class="text-success">
                            <p><strong>Importação Concluída!</strong></p>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check"></i> Processados: ${results.total_processed}</li>
                                <li><i class="fas fa-plus"></i> Criados: ${results.users_created}</li>
                                <li><i class="fas fa-edit"></i> Atualizados: ${results.users_updated}</li>
                                <li><i class="fas fa-building"></i> Departamentos: ${results.departments_created}</li>
                            </ul>
                            ${results.errors.length > 0 ? `<div class="text-warning"><small>Erros: ${results.errors.length}</small></div>` : ''}
                        </div>`;
                    
                    $('#importResults').html(resultHtml);
                    
                    // Recarregar dados
                    loadUsers();
                    loadDepartments();
                    loadUserStats();
                    loadTopUsers();
                    
                    showToast('Importação realizada com sucesso!', 'success');
                } else {
                    const errorMsg = response.error || response.message || 'Erro desconhecido na importação';
                    showToast('Erro na importação: ' + errorMsg, 'error');
                    $('#importResults').html(`<div class="text-danger">Erro: ${errorMsg}</div>`);
                }
            } catch (e) {
                console.error('Erro ao processar resposta:', e);
                showToast('Erro ao processar resposta do servidor', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro AJAX:', {xhr: xhr, status: status, error: error});
            let errorMsg = 'Erro na comunicação com o servidor';
            if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.error || response.message || errorMsg;
                } catch (e) {
                    errorMsg = xhr.responseText;
                }
            }
            showToast(errorMsg, 'error');
            $('#importResults').html(`<div class="text-danger">Erro: ${errorMsg}</div>`);
        },
        complete: function() {
            $('#btnImportCSV').prop('disabled', false).html('<i class="fas fa-upload"></i> Importar Usuários');
        }
    });
}

/**
 * Carregar departamentos
 */
function loadDepartments() {
    $.ajax({
        url: 'manager/user_management_manager.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            action_type: "get_department_list"
        }),
        success: function(response) {
            if (response.success) {
                populateDepartmentCards(response.departments);
                populateDepartmentSelect(response.departments);
            }
        },
        error: function() {
            showToast('Erro ao carregar departamentos', 'error');
        }
    });
}

/**
 * Popular cards de departamentos
 */
function populateDepartmentCards(departments) {
    const container = $('#departmentCards');
    container.empty();
    
    departments.forEach(function(dept) {
        const card = `
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle" style="width: 50px; height: 50px; background-color: ${dept.color}; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-building text-white"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">${dept.department_name}</h6>
                                <p class="text-muted mb-0">${dept.user_count} usuários</p>
                                <small class="text-muted">${dept.description || 'Sem descrição'}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        container.append(card);
    });
    
    if (departments.length === 0) {
        container.html('<div class="col-12"><p class="text-muted text-center">Nenhum departamento encontrado.</p></div>');
    }
}

/**
 * Popular select de departamentos
 */
function populateDepartmentSelect(departments) {
    const select = $('#userDepartment');
    select.find('option:not(:first)').remove();
    
    departments.forEach(function(dept) {
        select.append(`<option value="${dept.department_id}">${dept.department_name}</option>`);
    });
}

/**
 * Salvar departamento
 */
function saveDepartment() {
    const name = $('#deptName').val().trim();
    const description = $('#deptDescription').val().trim();
    const color = $('#deptColor').val();
    
    if (!name) {
        showToast('Nome do departamento é obrigatório', 'warning');
        return;
    }
    
    $.ajax({
        url: 'manager/user_management_manager.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            action_type: "create_department",
            department_name: name,
            description: description,
            color: color
        }),
        success: function(response) {
            try {
                if (response.success) {
                    $('#modalAddDepartment').modal('hide');
                    $('#formAddDepartment')[0].reset();
                    loadDepartments();
                    showToast('Departamento criado com sucesso!', 'success');
                } else {
                    const errorMsg = response.error || response.message || 'Erro desconhecido ao criar departamento';
                    showToast('Erro ao criar departamento: ' + errorMsg, 'error');
                }
            } catch (e) {
                console.error('Erro ao processar resposta:', e);
                showToast('Erro ao processar resposta do servidor', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro AJAX:', {xhr: xhr, status: status, error: error});
            let errorMsg = 'Erro na comunicação com o servidor';
            if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.error || response.message || errorMsg;
                } catch (e) {
                    errorMsg = xhr.responseText;
                }
            }
            showToast(errorMsg, 'error');
        }
    });
}

/**
 * Carregar top usuários
 */
function loadTopUsers() {
    $.ajax({
        url: 'manager/user_management_manager.php',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({
            action_type: "get_user_stats"
        }),
        success: function(response) {
            if (response.success) {
                populateTopUsersTable(response.stats.top_users);
            }
        },
        error: function() {
            showToast('Erro ao carregar ranking de usuários', 'error');
        }
    });
}

/**
 * Popular tabela de top usuários
 */
function populateTopUsersTable(users) {
    const tbody = $('#tableTopUsers tbody');
    tbody.empty();
    
    users.forEach(function(user, index) {
        const position = index + 1;
        const medal = position <= 3 ? 
            `<i class="fas fa-medal text-${position === 1 ? 'warning' : position === 2 ? 'secondary' : 'warning'}"></i>` : 
            position;
            
        const lastCampaign = user.last_campaign_date ? 
            new Date(user.last_campaign_date).toLocaleDateString('pt-BR') :
            'Nunca';
        
        const row = `
            <tr>
                <td class="text-center">${medal}</td>
                <td>${user.user_name}</td>
                <td>${user.user_email}</td>
                <td>${user.department_name || '<span class="text-muted">Sem departamento</span>'}</td>
                <td><span class="badge badge-primary">${user.campaign_count}</span></td>
                <td>${lastCampaign}</td>
            </tr>`;
        tbody.append(row);
    });
    
    if (users.length === 0) {
        tbody.html('<tr><td colspan="6" class="text-center text-muted">Nenhum usuário com campanhas encontrado.</td></tr>');
    }
}

/**
 * Ver histórico do usuário
 */
function viewUserHistory(email) {
    // Implementar modal com histórico de campanhas
    showToast('Funcionalidade em desenvolvimento', 'info');
}

/**
 * Editar usuário
 */
function editUser(userId) {
    // Implementar edição de usuário
    showToast('Funcionalidade em desenvolvimento', 'info');
}

/**
 * Excluir usuário
 */
function deleteUser(userId) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        // Implementar exclusão de usuário
        showToast('Funcionalidade em desenvolvimento', 'info');
    }
}

/**
 * Salvar usuário
 */
function saveUser() {
    // Implementar criação manual de usuário
    showToast('Funcionalidade em desenvolvimento', 'info');
}

/**
 * Mostrar toast
 */
function showToast(message, type = 'info') {
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "3000"
    };
    
    switch(type) {
        case 'success':
            toastr.success(message);
            break;
        case 'error':
            toastr.error(message);
            break;
        case 'warning':
            toastr.warning(message);
            break;
        default:
            toastr.info(message);
    }
}
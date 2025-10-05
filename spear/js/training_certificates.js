// Training Certificates Management Functions
let certificatesTable;

$(document).ready(function() {
    initializeCertificatesPage();
});

function initializeCertificatesPage() {
    initializeCertificatesTable();
    loadCertificateStats();
    setupEventHandlers();
    loadCertificateFilters();
}

function initializeCertificatesTable() {
    certificatesTable = $('#certificatesTable').DataTable({
        responsive: true,
        processing: true,
        language: {
            url: '/SniperPhish-main/spear/js/libs/Portuguese-Brasil.json'
        },
        columns: [
            { data: 'certificate_id', title: 'ID' },
            { data: 'user_name', title: 'Usuário' },
            { data: 'module_name', title: 'Módulo' },
            { data: 'client_name', title: 'Cliente' },
            { data: 'score_achieved', title: 'Pontuação' },
            { data: 'issued_date', title: 'Emitido em' },
            { 
                data: 'status', 
                title: 'Status',
                render: function(data, type, row) {
                    if (data == 1) {
                        return '<span class="badge badge-success">Válido</span>';
                    } else {
                        return '<span class="badge badge-danger">Revogado</span>';
                    }
                }
            },
            { 
                data: null, 
                title: 'Ações',
                orderable: false,
                render: function(data, type, row) {
                    let actions = `
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary btn-sm" onclick="viewCertificate('${row.certificate_id}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-success btn-sm" onclick="downloadCertificate('${row.certificate_id}')">
                                <i class="fas fa-download"></i>
                            </button>`;
                    
                    if (row.status == 1) {
                        actions += `<button type="button" class="btn btn-warning btn-sm" onclick="revokeCertificate('${row.certificate_id}')">
                                        <i class="fas fa-ban"></i>
                                    </button>`;
                    } else {
                        actions += `<button type="button" class="btn btn-info btn-sm" onclick="restoreCertificate('${row.certificate_id}')">
                                        <i class="fas fa-undo"></i>
                                    </button>`;
                    }
                    
                    actions += `</div>`;
                    return actions;
                }
            }
        ]
    });
}

function loadCertificates() {
    const clientId = $('#filterClient').val();
    const moduleId = $('#filterModule').val();
    const status = $('#filterStatus').val();
    
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: {
            action_type: 'get_certificates',
            client_id: clientId,
            module_id: moduleId,
            status: status
        },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                certificatesTable.clear().rows.add(response.data).draw();
            } else {
                showAlert('danger', 'Erro ao carregar certificados: ' + (response.error || 'Erro desconhecido'));
            }
        },
        error: function() {
            showAlert('danger', 'Erro na comunicação com o servidor');
        }
    });
}

function loadCertificateStats() {
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: {
            action_type: 'get_certificate_stats'
        },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                updateStatsCards(response.data);
            }
        }
    });
}

function updateStatsCards(stats) {
    $('#totalCertificates').text(stats.total_certificates || 0);
    $('#monthlyCertificates').text(stats.monthly_certificates || 0);
    $('#validCertificates').text(stats.valid_certificates || 0);
    $('#downloadedCertificates').text(stats.downloaded_certificates || 0);
}

function loadCertificateFilters() {
    // Load clients for filter
    $.ajax({
        url: 'manager/client_manager.php',
        type: 'POST',
        data: { action_type: 'get_clients' },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                const clientSelect = $('#filterClient');
                clientSelect.empty().append('<option value="">Todos os Clientes</option>');
                response.data.forEach(client => {
                    clientSelect.append(`<option value="${client.client_id}">${client.client_name}</option>`);
                });
            }
        }
    });
    
    // Load modules for filter
    $.ajax({
        url: 'manager/training_manager.php',
        type: 'POST',
        data: { action_type: 'get_modules' },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                const moduleSelect = $('#filterModule');
                moduleSelect.empty().append('<option value="">Todos os Módulos</option>');
                response.data.forEach(module => {
                    moduleSelect.append(`<option value="${module.module_id}">${module.module_name}</option>`);
                });
            }
        }
    });
}

function setupEventHandlers() {
    // Filter change handlers
    $('#filterClient, #filterModule, #filterStatus').on('change', function() {
        loadCertificates();
    });
    
    // Validation form handler
    $('#validationForm').on('submit', function(e) {
        e.preventDefault();
        validateCertificate();
    });
    
    // Bulk issue form handler
    $('#bulkIssueForm').on('submit', function(e) {
        e.preventDefault();
        bulkIssueCertificates();
    });
    
    // Initial load
    loadCertificates();
}

function validateCertificate() {
    const validationCode = $('#validationCode').val().trim();
    
    if (!validationCode) {
        showAlert('warning', 'Por favor, insira o código de validação');
        return;
    }
    
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: {
            action_type: 'validate_certificate',
            validation_code: validationCode
        },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                displayValidationResult(response.data);
            } else {
                showAlert('danger', response.error || 'Certificado não encontrado ou inválido');
                $('#validationResult').hide();
            }
        },
        error: function() {
            showAlert('danger', 'Erro na comunicação com o servidor');
        }
    });
}

function displayValidationResult(certificate) {
    const resultDiv = $('#validationResult');
    const statusClass = certificate.is_valid ? 'success' : 'danger';
    const statusText = certificate.is_valid ? 'VÁLIDO' : 'EXPIRADO/INVÁLIDO';
    
    let expirationInfo = '';
    if (certificate.expires_date) {
        expirationInfo = `<p><strong>Expira em:</strong> ${certificate.expires_date}</p>`;
    }
    
    resultDiv.html(`
        <div class="alert alert-${statusClass}">
            <h5><i class="fas fa-certificate"></i> Status: ${statusText}</h5>
            <hr>
            <p><strong>Usuário:</strong> ${certificate.user_name}</p>
            <p><strong>Módulo:</strong> ${certificate.module_name}</p>
            <p><strong>Cliente:</strong> ${certificate.client_name}</p>
            <p><strong>Pontuação:</strong> ${certificate.score_achieved}%</p>
            <p><strong>Data de Conclusão:</strong> ${certificate.completion_date}</p>
            <p><strong>Data de Emissão:</strong> ${certificate.issued_date}</p>
            ${expirationInfo}
        </div>
    `).show();
}

function viewCertificate(certificateId) {
    // Open certificate in new window/modal
    window.open(`certificate_view.php?id=${certificateId}`, '_blank');
}

function downloadCertificate(certificateId) {
    const template = 'default'; // Could be made configurable
    
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: {
            action_type: 'generate_certificate_pdf',
            certificate_id: certificateId,
            template: template
        },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                // Open PDF in new window
                window.open(response.pdf_url, '_blank');
                showAlert('success', 'Certificado gerado com sucesso!');
            } else {
                showAlert('danger', 'Erro ao gerar certificado: ' + (response.error || 'Erro desconhecido'));
            }
        },
        error: function() {
            showAlert('danger', 'Erro na comunicação com o servidor');
        }
    });
}

function revokeCertificate(certificateId) {
    if (!confirm('Tem certeza que deseja revogar este certificado?')) {
        return;
    }
    
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: {
            action_type: 'revoke_certificate',
            certificate_id: certificateId
        },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                showAlert('success', 'Certificado revogado com sucesso!');
                loadCertificates();
                loadCertificateStats();
            } else {
                showAlert('danger', 'Erro ao revogar certificado: ' + (response.error || 'Erro desconhecido'));
            }
        },
        error: function() {
            showAlert('danger', 'Erro na comunicação com o servidor');
        }
    });
}

function restoreCertificate(certificateId) {
    if (!confirm('Tem certeza que deseja restaurar este certificado?')) {
        return;
    }
    
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: {
            action_type: 'restore_certificate',
            certificate_id: certificateId
        },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                showAlert('success', 'Certificado restaurado com sucesso!');
                loadCertificates();
                loadCertificateStats();
            } else {
                showAlert('danger', 'Erro ao restaurar certificado: ' + (response.error || 'Erro desconhecido'));
            }
        },
        error: function() {
            showAlert('danger', 'Erro na comunicação com o servidor');
        }
    });
}

function bulkIssueCertificates() {
    const moduleId = $('#bulkModuleId').val();
    const clientId = $('#bulkClientId').val();
    const minScore = $('#bulkMinScore').val();
    
    if (!moduleId) {
        showAlert('warning', 'Por favor, selecione um módulo');
        return;
    }
    
    if (!confirm('Tem certeza que deseja emitir certificados em lote para os usuários que atendem aos critérios?')) {
        return;
    }
    
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: {
            action_type: 'bulk_issue_certificates',
            module_id: moduleId,
            client_id: clientId,
            min_score: minScore
        },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                showAlert('success', `${response.issued_count} certificados emitidos com sucesso!`);
                loadCertificates();
                loadCertificateStats();
                $('#bulkIssueModal').modal('hide');
            } else {
                showAlert('danger', 'Erro ao emitir certificados: ' + (response.error || 'Erro desconhecido'));
            }
        },
        error: function() {
            showAlert('danger', 'Erro na comunicação com o servidor');
        }
    });
}

function showBulkIssueModal() {
    // Load modules for bulk issue
    $.ajax({
        url: 'manager/training_manager.php',
        type: 'POST',
        data: { action_type: 'get_modules' },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                const moduleSelect = $('#bulkModuleId');
                moduleSelect.empty().append('<option value="">Selecione um módulo</option>');
                response.data.forEach(module => {
                    moduleSelect.append(`<option value="${module.module_id}">${module.module_name}</option>`);
                });
            }
        }
    });
    
    // Load clients for bulk issue
    $.ajax({
        url: 'manager/client_manager.php',
        type: 'POST',
        data: { action_type: 'get_clients' },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                const clientSelect = $('#bulkClientId');
                clientSelect.empty().append('<option value="">Todos os clientes</option>');
                response.data.forEach(client => {
                    clientSelect.append(`<option value="${client.client_id}">${client.client_name}</option>`);
                });
            }
        }
    });
    
    $('#bulkIssueModal').modal('show');
}

function loadCertificateTemplates() {
    $.ajax({
        url: 'manager/certificate_manager.php',
        type: 'POST',
        data: { action_type: 'get_certificate_templates' },
        dataType: 'json',
        success: function(response) {
            if (response.result === 'success') {
                displayTemplates(response.data);
            }
        }
    });
}

function displayTemplates(templates) {
    const container = $('#templateContainer');
    container.empty();
    
    templates.forEach(template => {
        container.append(`
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="${template.preview_url}" class="card-img-top" alt="${template.name}" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title">${template.name}</h5>
                        <p class="card-text">${template.description}</p>
                        <button class="btn btn-primary btn-sm" onclick="selectTemplate('${template.id}')">
                            Selecionar
                        </button>
                    </div>
                </div>
            </div>
        `);
    });
}

function selectTemplate(templateId) {
    $('#selectedTemplate').val(templateId);
    showAlert('success', 'Template selecionado com sucesso!');
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    $('#alertContainer').html(alertHtml);
    
    setTimeout(() => {
        $('.alert').fadeOut();
    }, 5000);
}

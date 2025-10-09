/**
 * CSV Import System - JavaScript
 * Sistema completo de importação de usuários e departamentos via CSV
 */

let currentStep = 1;
let csvData = null;
let previewData = null;

$(document).ready(function() {
    initializeCSVImport();
});

function initializeCSVImport() {
    // Initialize file upload
    initFileUpload();
    
    // Initialize step navigation
    initStepNavigation();
    
    // Initialize form controls
    initFormControls();
    
    // Load existing user groups
    loadUserGroups();
}

function initFileUpload() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('csvFile');
    
    // Click to upload
    uploadZone.addEventListener('click', () => {
        fileInput.click();
    });
    
    // Drag and drop functionality
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });
    
    uploadZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
    });
    
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelection(files[0]);
        }
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });
}

function handleFileSelection(file) {
    // Validate file
    if (!validateFile(file)) {
        return;
    }
    
    // Show file info
    showFileInfo(file);
    
    // Read file content
    readFileContent(file);
}

function validateFile(file) {
    // Check file type
    const allowedTypes = ['text/csv', 'text/plain', 'application/csv'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(file.type) && !['csv', 'txt'].includes(fileExtension)) {
        toastr.error('Tipo de arquivo não suportado. Use arquivos CSV ou TXT.', 'Erro de Arquivo');
        return false;
    }
    
    // Check file size (10MB max)
    const maxSize = 10 * 1024 * 1024; // 10MB
    if (file.size > maxSize) {
        toastr.error('Arquivo muito grande. Tamanho máximo: 10MB.', 'Erro de Arquivo');
        return false;
    }
    
    return true;
}

function showFileInfo(file) {
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('fileDate').textContent = new Date(file.lastModified).toLocaleString('pt-BR');
    
    document.getElementById('fileInfo').classList.remove('hidden');
    document.getElementById('btnNext1').disabled = false;
}

function readFileContent(file) {
    const reader = new FileReader();
    
    reader.onload = function(e) {
        csvData = e.target.result;
        toastr.success('Arquivo carregado com sucesso!', 'Upload Concluído');
    };
    
    reader.onerror = function() {
        toastr.error('Erro ao ler o arquivo. Tente novamente.', 'Erro de Leitura');
        csvData = null;
        document.getElementById('btnNext1').disabled = true;
    };
    
    reader.readAsText(file, 'UTF-8');
}

function initStepNavigation() {
    // Step 1 -> 2
    document.getElementById('btnNext1').addEventListener('click', () => {
        if (csvData) {
            getPreviewData();
        }
    });
    
    // Step 2 -> 3
    document.getElementById('btnNext2').addEventListener('click', () => {
        goToStep(3);
    });
    
    // Step 2 <- 1
    document.getElementById('btnBack2').addEventListener('click', () => {
        goToStep(1);
    });
    
    // Step 3 <- 2
    document.getElementById('btnBack3').addEventListener('click', () => {
        goToStep(2);
    });
    
    // Start import
    document.getElementById('btnStartImport').addEventListener('click', () => {
        startImportProcess();
    });
}

function initFormControls() {
    // User group checkbox
    document.getElementById('updateUserGroup').addEventListener('change', function() {
        const selector = document.getElementById('userGroupSelector');
        selector.style.display = this.checked ? 'block' : 'none';
    });
}

function getPreviewData() {
    if (!csvData) {
        toastr.error('Nenhum dado CSV disponível.', 'Erro');
        return;
    }
    
    // Show loading
    const btn = document.getElementById('btnNext1');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Analisando...';
    btn.disabled = true;
    
    $.post({
        url: "manager/csv_import_manager.php",
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify({
            action_type: "get_import_preview",
            csv_data: csvData
        })
    }).done(function(response) {
        if (response.result === 'success') {
            previewData = response;
            showPreviewData(response);
            goToStep(2);
        } else {
            toastr.error(response.error || 'Erro ao analisar os dados.', 'Erro de Preview');
        }
    }).fail(function() {
        toastr.error('Erro de comunicação com o servidor.', 'Erro');
    }).always(function() {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function showPreviewData(data) {
    // Update total users
    document.getElementById('totalUsers').textContent = data.total_users;
    
    // Show sample data
    showSampleData(data.sample_data);
    
    // Show departments
    showDepartmentPreview(data.departments);
}

function showSampleData(sampleData) {
    const preview = document.getElementById('csvPreview');
    
    if (!sampleData || sampleData.length === 0) {
        preview.innerHTML = '<p class="text-muted">Nenhuma amostra disponível.</p>';
        return;
    }
    
    let html = `
        <table class="table table-sm table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>Nome</th>
                    <th>Sobrenome</th>
                    <th>Email</th>
                    <th>Departamento</th>
                </tr>
            </thead>
            <tbody>
    `;
    
    sampleData.forEach(user => {
        html += `
            <tr>
                <td>${escapeHtml(user.fname)}</td>
                <td>${escapeHtml(user.lname)}</td>
                <td>${escapeHtml(user.email)}</td>
                <td><span class="badge badge-secondary">${escapeHtml(user.department || 'Sem departamento')}</span></td>
            </tr>
        `;
    });
    
    html += `
            </tbody>
        </table>
        <small class="text-muted">Mostrando os primeiros ${sampleData.length} registros de ${previewData.total_users} total.</small>
    `;
    
    preview.innerHTML = html;
}

function showDepartmentPreview(departments) {
    const preview = document.getElementById('departmentPreview');
    
    if (!departments || departments.length === 0) {
        preview.innerHTML = '<p class="text-muted">Nenhum departamento identificado.</p>';
        return;
    }
    
    let html = '';
    let newCount = 0;
    let existingCount = 0;
    
    departments.forEach(dept => {
        const badgeClass = dept.exists ? 'department-existing' : 'department-new';
        const statusText = dept.exists ? 'Existente' : 'Novo';
        
        if (dept.exists) {
            existingCount++;
        } else {
            newCount++;
        }
        
        html += `
            <div class="department-preview">
                <span class="department-badge ${badgeClass}">
                    ${escapeHtml(dept.name)} (${dept.user_count} usuários) - ${statusText}
                </span>
            </div>
        `;
    });
    
    // Add summary
    const summary = `
        <div class="alert alert-info mt-3">
            <h6>📊 Resumo dos Departamentos:</h6>
            <ul class="mb-0">
                <li><strong>${newCount}</strong> departamentos serão criados</li>
                <li><strong>${existingCount}</strong> departamentos já existem</li>
                <li><strong>${departments.length}</strong> departamentos no total</li>
            </ul>
        </div>
    `;
    
    preview.innerHTML = html + summary;
}

function loadUserGroups() {
    $.post({
        url: "manager/userlist_campaignlist_mailtemplate_manager.php",
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify({
            action_type: "get_user_group_list"
        })
    }).done(function(response) {
        if (response.data && Array.isArray(response.data)) {
            const select = document.getElementById('userGroupSelect');
            select.innerHTML = '<option value="">Criar novo grupo...</option>';
            
            response.data.forEach(group => {
                const option = document.createElement('option');
                option.value = group.user_group_id;
                option.textContent = `${group.user_group_name} (${group.user_count} usuários)`;
                select.appendChild(option);
            });
        }
    }).fail(function() {
        console.log('Erro ao carregar grupos de usuários');
    });
}

function startImportProcess() {
    if (!csvData || !previewData) {
        toastr.error('Dados não disponíveis para importação.', 'Erro');
        return;
    }
    
    // Go to import step
    goToStep(4);
    
    // Prepare import data
    const importData = {
        action_type: "import_users_with_departments",
        csv_data: csvData,
        create_departments: document.getElementById('createDepartments').checked,
        create_users: document.getElementById('createUsers').checked,
        update_user_group: document.getElementById('updateUserGroup').checked,
        user_group_id: document.getElementById('userGroupSelect').value
    };
    
    // Update progress
    updateImportProgress(10, 'Iniciando importação...');
    
    setTimeout(() => {
        updateImportProgress(30, 'Processando departamentos...');
        
        setTimeout(() => {
            updateImportProgress(60, 'Criando usuários...');
            
            // Start actual import
            performImport(importData);
        }, 1000);
    }, 500);
}

function performImport(importData) {
    updateImportProgress(80, 'Finalizando importação...');
    
    $.post({
        url: "manager/csv_import_manager.php",
        contentType: 'application/json; charset=utf-8',
        data: JSON.stringify(importData)
    }).done(function(response) {
        updateImportProgress(100, 'Importação concluída!');
        
        setTimeout(() => {
            if (response.result === 'success') {
                showImportResults(response);
                goToStep(5);
            } else {
                toastr.error(response.error || 'Erro durante a importação.', 'Erro de Importação');
                goToStep(3); // Go back to configuration
            }
        }, 1000);
        
    }).fail(function() {
        toastr.error('Erro de comunicação com o servidor.', 'Erro');
        goToStep(3);
    });
}

function updateImportProgress(percentage, text) {
    const progressBar = document.getElementById('importProgress');
    const progressText = document.getElementById('progressText');
    const statusDiv = document.getElementById('importStatus');
    
    progressBar.style.width = percentage + '%';
    progressText.textContent = text;
    
    const statusMessages = {
        10: '<p><i class="fas fa-cog fa-spin mr-2"></i>Preparando dados para importação...</p>',
        30: '<p><i class="fas fa-building fa-spin mr-2"></i>Analisando e criando departamentos...</p>',
        60: '<p><i class="fas fa-users fa-spin mr-2"></i>Processando usuários...</p>',
        80: '<p><i class="fas fa-envelope fa-spin mr-2"></i>Atualizando grupos de email...</p>',
        100: '<p><i class="fas fa-check-circle mr-2 text-success"></i>Importação finalizada com sucesso!</p>'
    };
    
    if (statusMessages[percentage]) {
        statusDiv.innerHTML = statusMessages[percentage];
    }
}

function showImportResults(results) {
    const summary = document.getElementById('resultSummary');
    
    let html = '<h6>📊 Resultados da Importação:</h6>';
    
    // Departments summary
    if (results.departments_created || results.departments_existing) {
        html += `
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="bg-success text-white p-3 rounded text-center">
                        <h4>${results.departments_created || 0}</h4>
                        <small>Departamentos Criados</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="bg-info text-white p-3 rounded text-center">
                        <h4>${results.departments_existing || 0}</h4>
                        <small>Departamentos Existentes</small>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Users summary
    html += `
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="bg-primary text-white p-3 rounded text-center">
                    <h4>${results.users_created || 0}</h4>
                    <small>Usuários Criados</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-warning text-white p-3 rounded text-center">
                    <h4>${results.users_updated || 0}</h4>
                    <small>Usuários Atualizados</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="bg-secondary text-white p-3 rounded text-center">
                    <h4>${results.users_skipped || 0}</h4>
                    <small>Usuários Ignorados</small>
                </div>
            </div>
        </div>
    `;
    
    // Errors
    if (results.errors && results.errors.length > 0) {
        html += '<div class="alert alert-warning mt-3">';
        html += '<h6>⚠️ Avisos/Erros:</h6>';
        html += '<ul class="mb-0">';
        results.errors.forEach(error => {
            html += `<li>${escapeHtml(error)}</li>`;
        });
        html += '</ul></div>';
    }
    
    // Department details
    if (results.department_summary && results.department_summary.length > 0) {
        html += '<div class="mt-3">';
        html += '<h6>🏢 Departamentos Processados:</h6>';
        html += '<div class="row">';
        
        results.department_summary.forEach(dept => {
            const badgeClass = dept.created ? 'badge-success' : 'badge-secondary';
            const status = dept.created ? 'Criado' : 'Existente';
            
            html += `
                <div class="col-md-6 mb-2">
                    <span class="badge ${badgeClass} mr-2">${status}</span>
                    ${escapeHtml(dept.name)}
                </div>
            `;
        });
        
        html += '</div></div>';
    }
    
    summary.innerHTML = html;
    
    // Show success message
    toastr.success('Importação realizada com sucesso!', 'Sucesso');
}

function goToStep(stepNumber) {
    // Hide all cards
    for (let i = 1; i <= 5; i++) {
        document.getElementById(`card-step${i}`).classList.add('hidden');
        document.getElementById(`step${i}`).classList.remove('active', 'completed');
    }
    
    // Mark completed steps
    for (let i = 1; i < stepNumber; i++) {
        document.getElementById(`step${i}`).classList.add('completed');
    }
    
    // Show current step
    document.getElementById(`card-step${stepNumber}`).classList.remove('hidden');
    document.getElementById(`step${stepNumber}`).classList.add('active');
    
    currentStep = stepNumber;
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Utility functions
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
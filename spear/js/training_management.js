// LooPhish - Training Management JavaScript
var moduleQuestionCount = 0;

$(document).ready(function() {
    // Initialize DataTable
    var modulesTable = $('#modulesTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        language: {
            url: '../js/libs/Portuguese-Brasil.json'
        },
        columnDefs: [
            { targets: -1, orderable: false } // Disable ordering on actions column
        ]
    });

    // Initialize Summernote for content editor
    $('#content_data').summernote({
        height: 300,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    // Module type change handler
    $('#module_type').change(function() {
        var selectedType = $(this).val();
        if (selectedType === 'quiz' || selectedType === 'mixed') {
            $('#quiz_section').show();
        } else {
            $('#quiz_section').hide();
        }
    });

    // Toggle bank config when question source changes
    $(document).on('change', 'input[name="question_source"]', function(){
        var v = $(this).val();
        if (v === 'bank') $('#bank_config').show(); else $('#bank_config').hide();
    });

    // Load initial data
    loadModules();
    loadTrainingStats();
    loadClients();
    loadRankings();

    // Auto-refresh every 30 seconds
    setInterval(function() {
        loadTrainingStats();
        loadRankings();
    }, 30000);

    // Form handlers
    $('#addModuleForm').on('submit', function(e) {
        e.preventDefault();
        saveModule();
    });

    $('#quickAssignForm').on('submit', function(e) {
        e.preventDefault();
        quickAssignTraining();
    });
});

function loadModules() {
    $.post('../manager/training_manager.php', {
        action_type: 'get_modules'
    }, function(response) {
        if (response.result === 'success') {
            populateModulesTable(response.data);
            populateModuleSelect(response.data);
        } else {
            toastr.error('Erro ao carregar módulos: ' + (response.error || 'Erro desconhecido'));
        }
    }, 'json').fail(function() {
        toastr.error('Erro de comunicação com o servidor');
    });
}

function populateModulesTable(modules) {
    var table = $('#modulesTable').DataTable();
    table.clear();

    modules.forEach(function(module) {
        var typeBadge = getModuleTypeBadge(module.module_type);
        var statusBadge = module.status == '1' 
            ? '<span class="status-indicator status-active"></span>Ativo'
            : '<span class="status-indicator status-inactive"></span>Inativo';
        
        var category = module.category || 'N/A';
        var duration = module.estimated_duration + ' min';
        var level = getDifficultyLevelBadge(module.difficulty_level);
        
        var actions = `
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-sm btn-primary" onclick="viewModule('${module.module_id}')" title="Ver Detalhes">
                    <i class="mdi mdi-eye"></i>
                </button>
                <button type="button" class="btn btn-sm btn-warning" onclick="editModule('${module.module_id}')" title="Editar">
                    <i class="mdi mdi-pencil"></i>
                </button>
                <button type="button" class="btn btn-sm btn-info" onclick="assignModule('${module.module_id}')" title="Atribuir">
                    <i class="mdi mdi-account-multiple-plus"></i>
                </button>
                <button type="button" class="btn btn-sm btn-success" onclick="previewModule('${module.module_id}')" title="Pré-visualizar">
                    <i class="mdi mdi-play"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="deleteModule('${module.module_id}')" title="Excluir">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        `;

        table.row.add([
            module.module_name,
            typeBadge,
            category,
            duration,
            level,
            statusBadge,
            actions
        ]);
    });

    table.draw();
}

function populateModuleSelect(modules) {
    var select = $('#quick_module_id');
    select.empty().append('<option value="">Selecione um módulo...</option>');
    
    modules.forEach(function(module) {
        select.append(`<option value="${module.module_id}">${module.module_name}</option>`);
    });
}

function loadClients() {
    $.post('../manager/client_manager.php', {
        action_type: 'get_clients'
    }, function(response) {
        if (response.result === 'success') {
            var select = $('#quick_client_id');
            select.empty().append('<option value="">Todos os clientes</option>');
            
            response.data.forEach(function(client) {
                select.append(`<option value="${client.client_id}">${client.client_name}</option>`);
            });
        }
    }, 'json').catch(function() {
        // Client management might not be available
        $('#quick_client_id').closest('.form-group').hide();
    });
}

function loadTrainingStats() {
    $.post('../manager/training_manager.php', {
        action_type: 'get_training_stats'
    }, function(response) {
        if (response.result === 'success') {
            var stats = response.data;
            $('#total-modules').text(stats.total_modules || 0);
            $('#active-assignments').text(stats.active_assignments || 0);
            $('#certificates-issued').text(stats.certificates_issued || 0);
            $('#completed-trainings').text(stats.completed_trainings || 0);
        }
    }, 'json').fail(function() {
        // Initialize with zeros if failed
        $('#total-modules, #active-assignments, #certificates-issued, #completed-trainings').text('0');
    });
}

function loadRankings() {
    $.post('../manager/training_manager.php', {
        action_type: 'get_rankings'
    }, function(response) {
        if (response.result === 'success') {
            populateRankingList(response.data.slice(0, 5)); // Top 5
        }
    }, 'json').fail(function() {
        $('#rankingList').html('<p class="text-muted">Nenhum ranking disponível</p>');
    });
}

function populateRankingList(rankings) {
    var container = $('#rankingList');
    container.empty();
    
    if (rankings.length === 0) {
        container.html('<p class="text-muted">Nenhum ranking disponível</p>');
        return;
    }
    
    rankings.forEach(function(rank, index) {
        var positionClass = '';
        if (index === 0) positionClass = 'first';
        else if (index === 1) positionClass = 'second';
        else if (index === 2) positionClass = 'third';
        
        var rankingCard = `
            <div class="ranking-card">
                <div class="ranking-position ${positionClass}">${index + 1}</div>
                <div class="flex-grow-1">
                    <h6 class="mb-1">${rank.user_name}</h6>
                    <small class="text-muted">${rank.user_email}</small>
                </div>
                <div class="score-badge">${rank.total_score} pts</div>
            </div>
        `;
        container.append(rankingCard);
    });
}

function saveModule() {
    // Validate required fields
    var moduleName = $('#module_name').val().trim();
    var moduleType = $('#module_type').val();
    
    if (!moduleName || !moduleType) {
        toastr.error('Nome do módulo e tipo são obrigatórios');
        return;
    }

    // Prepare quiz data if applicable
    var quizData = null;
    if (moduleType === 'quiz' || moduleType === 'mixed') {
        quizData = collectQuizData();
        if (!quizData) {
            toastr.error('Por favor, adicione pelo menos uma pergunta ao quiz');
            return;
        }
    }

    var formData = {
        action_type: 'create_module',
        module_name: moduleName,
        module_description: $('#module_description').val(),
        module_type: moduleType,
        content_data: $('#content_data').summernote('code'),
        quiz_data: quizData ? JSON.stringify(quizData) : '',
        passing_score: $('#passing_score').val() || 70,
        estimated_duration: $('#estimated_duration').val() || 15,
        difficulty_level: $('#difficulty_level').val() || 'basic',
        category: $('#category').val(),
        tags: $('#tags').val()
    };

    $.post('../manager/training_manager.php', formData, function(response) {
        if (response.result === 'success') {
            toastr.success('Módulo criado com sucesso!');
            $('#addModuleModal').modal('hide');
            $('#addModuleForm')[0].reset();
            $('#content_data').summernote('reset');
            $('#quiz_section').hide();
            $('#questions_container').empty();
            moduleQuestionCount = 0;
            loadModules();
            loadTrainingStats();
        } else {
            toastr.error('Erro ao criar módulo: ' + (response.error || 'Erro desconhecido'));
        }
    }, 'json').fail(function() {
        toastr.error('Erro de comunicação com o servidor');
    });
}

function quickAssignTraining() {
    var moduleId = $('#quick_module_id').val();
    var userEmails = $('#quick_user_emails').val().trim();
    
    if (!moduleId || !userEmails) {
        toastr.error('Módulo e emails dos usuários são obrigatórios');
        return;
    }

    // Parse user emails
    var emails = userEmails.split(',').map(email => email.trim()).filter(email => email);
    var users = emails.map(email => ({ email: email, name: email.split('@')[0] }));

    var formData = {
        action_type: 'assign_training',
        module_id: moduleId,
        client_id: $('#quick_client_id').val(),
        assigned_users: JSON.stringify(users),
        assignment_type: 'manual'
    };

    $.post('../manager/training_manager.php', formData, function(response) {
        if (response.result === 'success') {
            toastr.success('Treinamento atribuído com sucesso!');
            $('#quickAssignForm')[0].reset();
            loadTrainingStats();
        } else {
            toastr.error('Erro ao atribuir treinamento: ' + (response.error || 'Erro desconhecido'));
        }
    }, 'json').fail(function() {
        toastr.error('Erro de comunicação com o servidor');
    });
}

function addQuizQuestion() {
    moduleQuestionCount++;
    var questionHtml = `
        <div class="quiz-question-card" id="question_${moduleQuestionCount}">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Pergunta ${moduleQuestionCount}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuizQuestion(${moduleQuestionCount})">
                        <i class="mdi mdi-delete"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Pergunta</label>
                        <input type="text" class="form-control form-control-modern question-text" required>
                    </div>
                    <div class="form-group">
                        <label>Tipo de Resposta</label>
                        <select class="form-control form-control-modern question-type" onchange="updateAnswerOptions(${moduleQuestionCount})">
                            <option value="multiple_choice">Múltipla Escolha</option>
                            <option value="true_false">Verdadeiro/Falso</option>
                            <option value="text">Texto Livre</option>
                        </select>
                    </div>
                    <div class="answer-options" id="answers_${moduleQuestionCount}">
                        <label>Opções de Resposta</label>
                        <div class="option-group">
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_${moduleQuestionCount}" value="0" checked>
                                    </div>
                                </div>
                                <input type="text" class="form-control answer-option" placeholder="Opção A" required>
                            </div>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <input type="radio" name="correct_${moduleQuestionCount}" value="1">
                                    </div>
                                </div>
                                <input type="text" class="form-control answer-option" placeholder="Opção B" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addAnswerOption(${moduleQuestionCount})">
                            <i class="mdi mdi-plus"></i> Adicionar Opção
                        </button>
                    </div>
                    <div class="form-group mt-3">
                        <label>Explicação (opcional)</label>
                        <textarea class="form-control form-control-modern question-explanation" rows="2" placeholder="Explique por que esta é a resposta correta..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#questions_container').append(questionHtml);
}

function removeQuizQuestion(questionId) {
    $(`#question_${questionId}`).remove();
}

function addAnswerOption(questionId) {
    var container = $(`#answers_${questionId} .option-group`);
    var optionCount = container.find('.input-group').length;
    var optionLetter = String.fromCharCode(65 + optionCount); // A, B, C, D...
    
    var optionHtml = `
        <div class="input-group mb-2">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <input type="radio" name="correct_${questionId}" value="${optionCount}">
                </div>
            </div>
            <input type="text" class="form-control answer-option" placeholder="Opção ${optionLetter}" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="$(this).closest('.input-group').remove()">
                    <i class="mdi mdi-delete"></i>
                </button>
            </div>
        </div>
    `;
    
    container.append(optionHtml);
}

function updateAnswerOptions(questionId) {
    var questionType = $(`#question_${questionId} .question-type`).val();
    var answersContainer = $(`#answers_${questionId}`);
    
    if (questionType === 'true_false') {
        answersContainer.html(`
            <label>Resposta Correta</label>
            <div class="option-group">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="radio" name="correct_${questionId}" value="0" checked>
                        </div>
                    </div>
                    <input type="text" class="form-control answer-option" value="Verdadeiro" readonly>
                </div>
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <input type="radio" name="correct_${questionId}" value="1">
                        </div>
                    </div>
                    <input type="text" class="form-control answer-option" value="Falso" readonly>
                </div>
            </div>
        `);
    } else if (questionType === 'text') {
        answersContainer.html(`
            <label>Resposta Esperada</label>
            <input type="text" class="form-control form-control-modern answer-option" placeholder="Resposta esperada...">
        `);
    }
}

function collectQuizData() {
    // If module configured to use question bank, return config instead
    var source = $('input[name="question_source"]:checked').val();
    if (source === 'bank') {
        // check for composition by difficulty
        var comp_basic = parseInt($('#bank_comp_basic').val() || 0, 10);
        var comp_intermediate = parseInt($('#bank_comp_intermediate').val() || 0, 10);
        var comp_advanced = parseInt($('#bank_comp_advanced').val() || 0, 10);
        var compositionTotal = comp_basic + comp_intermediate + comp_advanced;

        var result = {
            use_bank: true,
            bank_category: $('#bank_category').val() || '',
            bank_difficulty: $('#bank_difficulty').val() || '',
            bank_count: parseInt($('#bank_count').val() || 10, 10),
            passing_score: parseInt($('#passing_score').val() || 70, 10)
        };

        if (compositionTotal > 0) {
            result.bank_composition = {
                basic: comp_basic,
                intermediate: comp_intermediate,
                advanced: comp_advanced
            };
            // bank_count becomes total of composition
            result.bank_count = compositionTotal;
        }

        return result;
    }

    var questions = [];

    $('.quiz-question-card').each(function() {
        var questionText = $(this).find('.question-text').val().trim();
        var questionType = $(this).find('.question-type').val();
        var explanation = $(this).find('.question-explanation').val().trim();
        
        if (!questionText) return;
        
        var answers = [];
        var correctAnswer = null;
        
        if (questionType === 'text') {
            var expectedAnswer = $(this).find('.answer-option').val().trim();
            answers = [expectedAnswer];
            correctAnswer = 0;
        } else {
            $(this).find('.answer-option').each(function(index) {
                var answerText = $(this).val().trim();
                if (answerText) {
                    answers.push(answerText);
                }
            });
            
            var checkedRadio = $(this).find('input[type="radio"]:checked');
            if (checkedRadio.length > 0) {
                correctAnswer = parseInt(checkedRadio.val());
            }
        }
        
        if (answers.length > 0) {
            questions.push({
                question: questionText,
                type: questionType,
                answers: answers,
                correct_answer: correctAnswer,
                explanation: explanation
            });
        }
    });
    
    return questions.length > 0 ? {
        use_bank: false,
        questions: questions,
        total_questions: questions.length,
        passing_score: parseInt($('#passing_score').val() || 70, 10)
    } : null;
}

function getModuleTypeBadge(type) {
    var badges = {
        'video': '<span class="badge badge-primary">Vídeo</span>',
        'quiz': '<span class="badge badge-success">Quiz</span>',
        'interactive': '<span class="badge badge-info">Interativo</span>',
        'mixed': '<span class="badge badge-warning">Misto</span>'
    };
    return badges[type] || '<span class="badge badge-secondary">N/A</span>';
}

function getDifficultyLevelBadge(level) {
    var badges = {
        'basic': '<span class="badge badge-success">Básico</span>',
        'intermediate': '<span class="badge badge-warning">Intermediário</span>',
        'advanced': '<span class="badge badge-danger">Avançado</span>'
    };
    return badges[level] || '<span class="badge badge-secondary">N/A</span>';
}

// Module action functions
function viewModule(moduleId) {
    window.location.href = 'TrainingModule.php?module_id=' + moduleId;
}

function editModule(moduleId) {
    // Implementation for editing module
    toastr.info('Funcionalidade de edição em desenvolvimento');
}

function assignModule(moduleId) {
    // Pre-fill the quick assign form
    $('#quick_module_id').val(moduleId);
    $('html, body').animate({
        scrollTop: $('#quickAssignForm').offset().top - 100
    }, 1000);
}

function previewModule(moduleId) {
    window.open('TrainingPreview.php?module_id=' + moduleId, '_blank');
}

function deleteModule(moduleId) {
    if (confirm('Tem certeza que deseja excluir este módulo? Esta ação não pode ser desfeita.')) {
        // Implementation for deleting module
        toastr.info('Funcionalidade de exclusão em desenvolvimento');
    }
}

// Initialize training system (run once)
function initializeTrainingSystem() {
    if (confirm('Isso irá criar as tabelas necessárias para o sistema de treinamentos. Continuar?')) {
        $.post('../manager/training_manager.php', {
            action_type: 'create_tables'
        }, function(response) {
            if (response.result === 'success') {
                toastr.success('Sistema de treinamentos inicializado com sucesso!');
                location.reload();
            } else {
                toastr.error('Erro ao inicializar sistema: ' + (response.error || 'Erro desconhecido'));
            }
        }, 'json').fail(function() {
            toastr.error('Erro de comunicação com o servidor');
        });
    }
}

// Export functions to global scope
window.addQuizQuestion = addQuizQuestion;
window.removeQuizQuestion = removeQuizQuestion;
window.addAnswerOption = addAnswerOption;
window.updateAnswerOptions = updateAnswerOptions;
window.saveModule = saveModule;
window.viewModule = viewModule;
window.editModule = editModule;
window.assignModule = assignModule;
window.previewModule = previewModule;
window.deleteModule = deleteModule;
window.initializeTrainingSystem = initializeTrainingSystem;

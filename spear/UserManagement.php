<?php require_once "manager/session_manager.php"; ?>
<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Gestão de Usuários - Loophish</title>
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <link rel="stylesheet" type="text/css" href="css/style.min.css">
    <link rel="stylesheet" type="text/css" href="css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="css/dataTables.foundation.min.css">
    <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
    <link rel="stylesheet" type="text/css" href="css/loophish-theme.css">
    <style>
        /* Correções para abas e modais */
        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block !important;
        }
        .nav-link.active {
            background-color: #007bff !important;
            color: white !important;
            border-color: #007bff !important;
        }
        .modal.show {
            display: block !important;
        }
        .modal-backdrop {
            background-color: rgba(0,0,0,0.5);
        }
        /* Corrigir z-index dos modais */
        .modal {
            z-index: 1050;
        }
        .modal-backdrop {
            z-index: 1040;
        }
    </style>
</head>
<body>
    <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div>
    
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        
        <?php require_once "z_menu.php"; ?>
        
        <div class="page-wrapper">
            <div class="page-breadcrumb bg-white">
                <div class="row align-items-center">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Gestão de Usuários</h4>
                    </div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <div class="d-md-flex">
                            <ol class="breadcrumb ms-auto">
                                <li><a href="#" class="fw-normal">Dashboard</a></li>
                                <li class="active">Gestão de Usuários</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="container-fluid">
                <!-- Cards de Estatísticas -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-users f-s-40 color-primary"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <h2 id="stat_total_users">0</h2>
                                    <p class="m-b-0">Total de Usuários</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-building f-s-40 text-info"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <h2 id="stat_total_departments">0</h2>
                                    <p class="m-b-0">Departamentos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-envelope f-s-40 text-warning"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <h2 id="stat_active_campaigns">0</h2>
                                    <p class="m-b-0">Campanhas Ativas</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card p-30">
                            <div class="media">
                                <div class="media-left meida media-middle">
                                    <span><i class="fas fa-chart-line f-s-40 text-success"></i></span>
                                </div>
                                <div class="media-body media-text-right">
                                    <h2 id="stat_avg_participation">0%</h2>
                                    <p class="m-b-0">Participação Média</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#usuarios" role="tab">
                                            <span class="hidden-sm-up"><i class="ti-user"></i></span>
                                            <span class="hidden-xs-down">Usuários</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#importacao" role="tab">
                                            <span class="hidden-sm-up"><i class="ti-upload"></i></span>
                                            <span class="hidden-xs-down">Importar CSV</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#departamentos" role="tab">
                                            <span class="hidden-sm-up"><i class="ti-home"></i></span>
                                            <span class="hidden-xs-down">Departamentos</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#historico" role="tab">
                                            <span class="hidden-sm-up"><i class="ti-time"></i></span>
                                            <span class="hidden-xs-down">Histórico</span>
                                        </a>
                                    </li>
                                </ul>

                                <div class="tab-content tabcontent-border">
                                    <!-- Tab Usuários -->
                                    <div class="tab-pane active" id="usuarios" role="tabpanel">
                                        <div class="p-20">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddUser">
                                                        <i class="fas fa-plus"></i> Adicionar Usuário
                                                    </button>
                                                </div>
                                                <div class="col-md-6 text-end">
                                                    <div class="input-group" style="width: 300px; display: inline-flex;">
                                                        <input type="text" class="form-control" id="searchUsers" placeholder="Pesquisar usuários...">
                                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-striped table-bordered" id="tableUsers">
                                                    <thead>
                                                        <tr>
                                                            <th>Nome</th>
                                                            <th>Email</th>
                                                            <th>Departamento</th>
                                                            <th>Campanhas</th>
                                                            <th>Última Campanha</th>
                                                            <th>Status</th>
                                                            <th>Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Importação -->
                                    <div class="tab-pane" id="importacao" role="tabpanel">
                                        <div class="p-20">
                                            <div class="alert alert-info">
                                                <h5><i class="fas fa-info-circle"></i> Formato do CSV</h5>
                                                <p>O arquivo CSV deve conter as colunas: <strong>First Name, Last Name, Email, Notes</strong></p>
                                                <p>O campo <strong>Notes</strong> será usado como nome do departamento.</p>
                                                <p>Exemplo: <code>david,"damasceno da frota",davidddf.frota@gmail.com,TI</code></p>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        <label for="csvFile">Selecionar Arquivo CSV:</label>
                                                        <input type="file" class="form-control" id="csvFile" accept=".csv" />
                                                    </div>
                                                    
                                                    <div class="form-group mt-3">
                                                        <button type="button" class="btn btn-primary" id="btnImportCSV">
                                                            <i class="fas fa-upload"></i> Importar Usuários
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" id="btnPreviewCSV">
                                                            <i class="fas fa-eye"></i> Visualizar Dados
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card bg-light">
                                                        <div class="card-body">
                                                            <h6>Resultados da Importação:</h6>
                                                            <div id="importResults">
                                                                <p class="text-muted">Nenhuma importação realizada ainda.</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Preview dos dados -->
                                            <div class="mt-4" id="csvPreview" style="display: none;">
                                                <h5>Visualização dos Dados:</h5>
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered" id="previewTable">
                                                        <thead></thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Departamentos -->
                                    <div class="tab-pane" id="departamentos" role="tabpanel">
                                        <div class="p-20">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAddDepartment">
                                                        <i class="fas fa-plus"></i> Criar Departamento
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="row" id="departmentCards">
                                                <!-- Departamentos serão carregados via JavaScript -->
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab Histórico -->
                                    <div class="tab-pane" id="historico" role="tabpanel">
                                        <div class="p-20">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <h5>Top 10 Usuários com Mais Campanhas</h5>
                                                </div>
                                            </div>
                                            
                                            <div class="table-responsive">
                                                <table class="table table-striped" id="tableTopUsers">
                                                    <thead>
                                                        <tr>
                                                            <th>Posição</th>
                                                            <th>Nome</th>
                                                            <th>Email</th>
                                                            <th>Departamento</th>
                                                            <th>Campanhas</th>
                                                            <th>Última Participação</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php require_once "z_footer.php"; ?>
        </div>
    </div>

    <!-- Modal Adicionar Usuário -->
    <div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Usuário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAddUser">
                        <div class="form-group mb-3">
                            <label for="userFirstName">Nome:</label>
                            <input type="text" class="form-control" id="userFirstName" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="userLastName">Sobrenome:</label>
                            <input type="text" class="form-control" id="userLastName" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="userEmail">Email:</label>
                            <input type="email" class="form-control" id="userEmail" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="userDepartment">Departamento:</label>
                            <select class="form-control" id="userDepartment">
                                <option value="">Selecionar Departamento</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSaveUser">Salvar Usuário</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Departamento -->
    <div class="modal fade" id="modalAddDepartment" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Criar Departamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formAddDepartment">
                        <div class="form-group mb-3">
                            <label for="deptName">Nome do Departamento:</label>
                            <input type="text" class="form-control" id="deptName" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="deptDescription">Descrição:</label>
                            <textarea class="form-control" id="deptDescription" rows="3"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="deptColor">Cor:</label>
                            <input type="color" class="form-control" id="deptColor" value="#007bff">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btnSaveDepartment">Criar Departamento</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Carregamento sequencial das bibliotecas para garantir ordem correta
        function loadScript(src, callback) {
            var script = document.createElement('script');
            script.src = src;
            script.onload = callback;
            script.onerror = function() {
                console.error('Erro ao carregar:', src);
                callback();
            };
            document.head.appendChild(script);
        }
        
        function loadCSS(href) {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = href;
            document.head.appendChild(link);
        }
        
        // Carregar bibliotecas em sequência
        loadScript('js/libs/jquery/jquery-3.6.0.min.js', function() {
            console.log('jQuery carregado');
            loadScript('js/libs/perfect-scrollbar.jquery.min.js', function() {
                console.log('Perfect Scrollbar carregado');
                loadScript('js/libs/bootstrap.min.js', function() {
                    console.log('Bootstrap carregado');
                    loadScript('js/libs/custom.min.js', function() {
                        console.log('Custom carregado');
                        loadScript('js/libs/select2.min.js', function() {
                            console.log('Select2 carregado');
                            loadScript('js/libs/jquery/datatables.js', function() {
                                console.log('DataTables carregado');
                                loadScript('js/libs/toastr.min.js', function() {
                                    console.log('Toastr carregado');
                                    loadScript('js/user_management.js', function() {
                                        console.log('UserManagement carregado');
                                        
                                        // Verificar se script foi carregado corretamente
                                        console.log('Window.loadCSVFile:', typeof window.loadCSVFile);
                                        console.log('Global loadCSVFile:', typeof loadCSVFile);
                                        
                                        // Configurar eventos após carregamento completo
                                        setTimeout(function() {
                                            // Aguardar mais um pouco para garantir que tudo foi carregado
                                            var checkReady = function() {
                                                if (typeof $ !== 'undefined' && document.readyState === 'complete') {
                                                    setupUserManagementEvents();
                                                } else {
                                                    console.log('Aguardando carregamento completo...');
                                                    setTimeout(checkReady, 200);
                                                }
                                            };
                                            checkReady();
                                        }, 300);
                                    });
                                });
                            });
                        });
                    });
                });
            });
        });
        
        // Função para configurar eventos após carregamento
        function setupUserManagementEvents() {
            console.log('Configurando eventos do UserManagement...');
            
            // Verificar se jQuery está disponível
            if (typeof $ === 'undefined') {
                console.error('jQuery não está disponível!');
                return;
            }
            
            // Verificar se elementos existem
            console.log('Elemento #btnSaveUser existe:', $('#btnSaveUser').length > 0);
            console.log('Elemento #btnSaveDepartment existe:', $('#btnSaveDepartment').length > 0);
            console.log('Elemento #btnImportCSV existe:', $('#btnImportCSV').length > 0);
            console.log('Elemento #csvFile existe:', $('#csvFile').length > 0);
            
            // Inicializar botões de CSV como desabilitados
            $('#btnImportCSV, #btnPreviewCSV').prop('disabled', true);
            $('#btnImportCSV').html('<i class="fas fa-upload"></i> Aguarde - sem dados');
            $('#btnPreviewCSV').html('<i class="fas fa-eye"></i> Aguarde - sem dados');
            
            try {
                // Configurar navegação por abas manualmente
                $('.nav-link[data-bs-toggle="tab"]').off('click').on('click', function(e) {
                    e.preventDefault();
                    console.log('Clique na aba:', $(this).attr('href'));
                    
                    // Remover classes ativas
                    $('.nav-link').removeClass('active');
                    $('.tab-pane').removeClass('active show');
                    
                    // Adicionar classe ativa na aba clicada
                    $(this).addClass('active');
                    
                    // Mostrar conteúdo da aba
                    const target = $(this).attr('href');
                    if (target && target !== '#' && target.length > 1) {
                        $(target).addClass('active show');
                    }
                });
                
                // Configurar modais manualmente
                $('[data-bs-toggle="modal"]').off('click').on('click', function(e) {
                    e.preventDefault();
                    const target = $(this).attr('data-bs-target');
                    console.log('Abrindo modal:', target);
                    
                    if (target && target !== '#' && target.length > 1) {
                        $(target).modal('show');
                    }
                });
                
                console.log('Navegação por abas e modais configurada');
            } catch (error) {
                console.error('Erro ao configurar navegação:', error);
            }
            
            try {
                // Eventos de botões
                $('#btnSaveUser').off('click').on('click', function() {
                    console.log('Clique em Salvar Usuário');
                    try {
                        if (typeof saveUser === 'function') {
                            saveUser();
                        } else if (typeof window.saveUser === 'function') {
                            window.saveUser();
                        } else {
                            console.error('Função saveUser não encontrada');
                            eval('saveUser()');
                        }
                    } catch (error) {
                        console.error('Erro ao executar saveUser:', error);
                    }
                });
                
                $('#btnSaveDepartment').off('click').on('click', function() {
                    console.log('Clique em Salvar Departamento');
                    try {
                        if (typeof saveDepartment === 'function') {
                            saveDepartment();
                        } else if (typeof window.saveDepartment === 'function') {
                            window.saveDepartment();
                        } else {
                            console.error('Função saveDepartment não encontrada');
                            eval('saveDepartment()');
                        }
                    } catch (error) {
                        console.error('Erro ao executar saveDepartment:', error);
                    }
                });
                
                $('#btnImportCSV').off('click').on('click', function() {
                    console.log('Clique em Importar CSV');
                    try {
                        if (typeof importCSVData === 'function') {
                            importCSVData();
                        } else if (typeof window.importCSVData === 'function') {
                            window.importCSVData();
                        } else {
                            console.error('Função importCSVData não encontrada');
                            eval('importCSVData()');
                        }
                    } catch (error) {
                        console.error('Erro ao executar importCSVData:', error);
                    }
                });
                
                $('#btnPreviewCSV').off('click').on('click', function() {
                    console.log('Clique em Visualizar CSV');
                    try {
                        if (typeof previewCSVData === 'function') {
                            previewCSVData();
                        } else if (typeof window.previewCSVData === 'function') {
                            window.previewCSVData();
                        } else {
                            console.error('Função previewCSVData não encontrada');
                            eval('previewCSVData()');
                        }
                    } catch (error) {
                        console.error('Erro ao executar previewCSVData:', error);
                    }
                });
                
                // Evento de upload de arquivo
                $('#csvFile').off('change').on('change', function() {
                    console.log('Arquivo CSV selecionado');
                    try {
                        if (typeof loadCSVFile === 'function') {
                            loadCSVFile();
                        } else if (typeof window.loadCSVFile === 'function') {
                            window.loadCSVFile();
                        } else {
                            console.error('Função loadCSVFile não encontrada');
                            // Fallback: tentar executar mesmo assim
                            eval('loadCSVFile()');
                        }
                    } catch (error) {
                        console.error('Erro ao executar loadCSVFile:', error);
                    }
                });
                
                console.log('Eventos de botões configurados');
            } catch (error) {
                console.error('Erro ao configurar eventos de botões:', error);
            }
            
            // Verificar se DataTable existe
            if (typeof dt_users !== 'undefined' && dt_users) {
                console.log('DataTable de usuários disponível');
            } else {
                console.log('DataTable de usuários não disponível ainda');
            }
            
            // Verificar funções do user_management.js
            console.log('Função initializeUserManagement existe:', typeof initializeUserManagement === 'function');
            console.log('Função loadUsers existe:', typeof loadUsers === 'function');
            console.log('Função loadUserStats existe:', typeof loadUserStats === 'function');
            console.log('Função saveUser existe:', typeof saveUser === 'function');
            console.log('Função saveDepartment existe:', typeof saveDepartment === 'function');
            console.log('Função importCSVData existe:', typeof importCSVData === 'function');
            console.log('Função previewCSVData existe:', typeof previewCSVData === 'function');
            console.log('Função loadCSVFile existe:', typeof loadCSVFile === 'function');
            
            // Teste simples de clique
            $('body').off('click.test').on('click.test', function(e) {
                console.log('Clique detectado em:', e.target);
            });
            
            console.log('Configuração de eventos finalizada!');
        }
    </script>
</body>
</html>
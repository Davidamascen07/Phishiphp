<?php
   require_once(dirname(__FILE__) . '/manager/session_manager.php');
   isSessionValid(true);
?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Importação de Usuários e Departamentos via CSV">
    <meta name="author" content="LoophishX">
    
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
    <title>Importação CSV - LoophishX</title>
    
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="css/style.min.css">
    <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
    
    <style>
        .import-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .step-wizard {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        
        .step-wizard::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 40px;
            right: 40px;
            height: 2px;
            background: #e9ecef;
            z-index: 1;
        }
        
        .step-item {
            text-align: center;
            position: relative;
            z-index: 2;
            background: white;
            padding: 0 15px;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .step-item.active .step-number {
            background: #007bff;
            color: white;
        }
        
        .step-item.completed .step-number {
            background: #28a745;
            color: white;
        }
        
        .step-title {
            font-size: 14px;
            color: #6c757d;
            margin: 0;
        }
        
        .step-item.active .step-title {
            color: #007bff;
            font-weight: bold;
        }
        
        .csv-preview {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            background: #f8f9fa;
            padding: 15px;
        }
        
        .department-preview {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .department-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            margin: 2px;
            color: white;
            font-weight: bold;
        }
        
        .department-new {
            background: #28a745;
        }
        
        .department-existing {
            background: #6c757d;
        }
        
        .upload-zone {
            border: 2px dashed #007bff;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .upload-zone:hover {
            border-color: #0056b3;
            background: #e3f2fd;
        }
        
        .upload-zone.dragover {
            border-color: #28a745;
            background: #d4edda;
        }
        
        .progress-bar-custom {
            height: 25px;
            border-radius: 12px;
            font-size: 12px;
            line-height: 25px;
        }
        
        .result-summary {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            border-radius: 5px;
        }
        
        .hidden {
            display: none;
        }
        
        .file-info {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 5px;
            padding: 15px;
            margin: 15px 0;
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

    <div id="main-wrapper">
        <?php include_once 'z_menu.php' ?>
        
        <div class="page-wrapper">
            <div class="page-breadcrumb">
                <div class="row">
                    <div class="col-12 d-flex no-block align-items-center">
                        <h4 class="page-title">Importação de Usuários e Departamentos</h4>
                        <div class="ml-auto text-right">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Usuários</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Importação CSV</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid import-container">
                <!-- Step Wizard -->
                <div class="step-wizard">
                    <div class="step-item active" id="step1">
                        <div class="step-number">1</div>
                        <p class="step-title">Upload do Arquivo</p>
                    </div>
                    <div class="step-item" id="step2">
                        <div class="step-number">2</div>
                        <p class="step-title">Preview dos Dados</p>
                    </div>
                    <div class="step-item" id="step3">
                        <div class="step-number">3</div>
                        <p class="step-title">Configurações</p>
                    </div>
                    <div class="step-item" id="step4">
                        <div class="step-number">4</div>
                        <p class="step-title">Importação</p>
                    </div>
                    <div class="step-item" id="step5">
                        <div class="step-number">5</div>
                        <p class="step-title">Resultado</p>
                    </div>
                </div>

                <!-- Step 1: Upload -->
                <div class="card" id="card-step1">
                    <div class="card-body">
                        <h5 class="card-title">📁 Upload do Arquivo CSV</h5>
                        <p class="text-muted">Faça upload do arquivo CSV com os dados dos usuários. O formato esperado é: <strong>First Name, Last Name, Email, Notes (Departamento)</strong></p>
                        
                        <div class="upload-zone" id="uploadZone">
                            <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                            <h5>Arraste o arquivo CSV aqui ou clique para selecionar</h5>
                            <p class="text-muted">Formatos aceitos: .csv, .txt (máx. 10MB)</p>
                            <input type="file" id="csvFile" accept=".csv,.txt" class="d-none">
                        </div>
                        
                        <div class="file-info hidden" id="fileInfo">
                            <h6><i class="fas fa-file-csv text-success mr-2"></i>Arquivo Selecionado:</h6>
                            <p id="fileName" class="mb-2 font-weight-bold"></p>
                            <small class="text-muted">
                                <span id="fileSize"></span> • 
                                <span id="fileDate"></span>
                            </small>
                        </div>
                        
                        <div class="text-right mt-3">
                            <button type="button" class="btn btn-primary" id="btnNext1" disabled>
                                <i class="fas fa-arrow-right mr-2"></i>Próximo: Preview dos Dados
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Preview -->
                <div class="card hidden" id="card-step2">
                    <div class="card-body">
                        <h5 class="card-title">👁️ Preview dos Dados</h5>
                        <p class="text-muted">Confira os dados que serão importados e os departamentos que serão criados.</p>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h6><i class="fas fa-users mr-2"></i>Resumo de Usuários</h6>
                                <div class="alert alert-info">
                                    <strong id="totalUsers">0</strong> usuários serão processados
                                </div>
                                
                                <h6><i class="fas fa-eye mr-2"></i>Amostra dos Dados:</h6>
                                <div class="csv-preview" id="csvPreview">
                                    <!-- Preview data will be inserted here -->
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <h6><i class="fas fa-building mr-2"></i>Departamentos Identificados</h6>
                                <div id="departmentPreview">
                                    <!-- Department preview will be inserted here -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-secondary mr-2" id="btnBack2">
                                <i class="fas fa-arrow-left mr-2"></i>Voltar
                            </button>
                            <button type="button" class="btn btn-primary" id="btnNext2">
                                <i class="fas fa-arrow-right mr-2"></i>Próximo: Configurações
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Configuration -->
                <div class="card hidden" id="card-step3">
                    <div class="card-body">
                        <h5 class="card-title">⚙️ Configurações da Importação</h5>
                        <p class="text-muted">Configure como os dados serão importados no sistema.</p>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h6><i class="fas fa-building mr-2"></i>Departamentos</h6>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="createDepartments" checked>
                                        <label class="custom-control-label" for="createDepartments">
                                            <strong>Criar departamentos automaticamente</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Departamentos que não existem serão criados automaticamente</small>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <h6><i class="fas fa-users mr-2"></i>Usuários</h6>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="createUsers" checked>
                                        <label class="custom-control-label" for="createUsers">
                                            <strong>Criar usuários no sistema</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Usuários serão adicionados à tabela de usuários do cliente</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6">
                                <h6><i class="fas fa-envelope mr-2"></i>Grupo de Email (Opcional)</h6>
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="updateUserGroup">
                                        <label class="custom-control-label" for="updateUserGroup">
                                            <strong>Adicionar aos grupos de email</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Usuários serão adicionados aos grupos para campanhas de email</small>
                                </div>
                                
                                <div class="form-group" id="userGroupSelector" style="display: none;">
                                    <label for="userGroupSelect">Selecionar Grupo Existente:</label>
                                    <select class="form-control" id="userGroupSelect">
                                        <option value="">Selecionar grupo...</option>
                                    </select>
                                    <small class="text-muted">Ou deixe em branco para criar novo grupo</small>
                                </div>
                            </div>
                            
                            <div class="col-lg-6">
                                <h6><i class="fas fa-info-circle mr-2"></i>Informações Adicionais</h6>
                                <div class="alert alert-warning">
                                    <h6>⚠️ Importante:</h6>
                                    <ul class="mb-0">
                                        <li>Usuários existentes serão <strong>atualizados</strong></li>
                                        <li>Emails duplicados serão <strong>ignorados</strong></li>
                                        <li>Departamentos são criados com <strong>cores aleatórias</strong></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-secondary mr-2" id="btnBack3">
                                <i class="fas fa-arrow-left mr-2"></i>Voltar
                            </button>
                            <button type="button" class="btn btn-success" id="btnStartImport">
                                <i class="fas fa-play mr-2"></i>Iniciar Importação
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Import Progress -->
                <div class="card hidden" id="card-step4">
                    <div class="card-body">
                        <h5 class="card-title">🔄 Importação em Andamento</h5>
                        <p class="text-muted">Aguarde enquanto processamos os dados...</p>
                        
                        <div class="progress mb-3">
                            <div class="progress-bar progress-bar-striped progress-bar-animated progress-bar-custom" 
                                 role="progressbar" style="width: 0%" id="importProgress">
                                <span id="progressText">Iniciando...</span>
                            </div>
                        </div>
                        
                        <div id="importStatus">
                            <p><i class="fas fa-spinner fa-spin mr-2"></i>Preparando importação...</p>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Results -->
                <div class="card hidden" id="card-step5">
                    <div class="card-body">
                        <h5 class="card-title">✅ Importação Concluída</h5>
                        
                        <div class="result-summary" id="resultSummary">
                            <!-- Results will be inserted here -->
                        </div>
                        
                        <div class="text-center mt-4">
                            <button type="button" class="btn btn-success mr-2" onclick="window.location.href='UserManagement.php'">
                                <i class="fas fa-users mr-2"></i>Ver Usuários
                            </button>
                            <button type="button" class="btn btn-info mr-2" onclick="window.location.href='MailUserGroup.php'">
                                <i class="fas fa-envelope mr-2"></i>Ver Grupos de Email
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="window.location.reload()">
                                <i class="fas fa-redo mr-2"></i>Nova Importação
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include_once 'z_footer.php' ?>
    </div>

    <!-- Scripts -->
    <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
    <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
    <script src="js/libs/sidebarmenu.js"></script>
    <script src="js/libs/custom.min.js"></script>
    <script src="js/libs/toastr.min.js"></script>
    <script defer src="js/libs/popper.min.js"></script>
    <script defer src="js/libs/bootstrap.min.js"></script>
    <script defer src="js/libs/select2.min.js"></script>
    
    <script src="js/csv_import.js"></script>
</body>
</html>
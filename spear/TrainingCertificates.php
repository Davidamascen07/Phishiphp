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
      <meta name="description" content="LooPhish - Certificate Management">
      <meta name="author" content="">
      <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
      <title>LooPhish - Gestão de Certificados</title>
      
      <!-- Custom CSS -->
      <link rel="stylesheet" type="text/css" href="css/style.min.css">
      <link rel="stylesheet" type="text/css" href="css/loophish-theme.css">
      <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
      <link rel="stylesheet" type="text/css" href="css/dataTables.foundation.min.css">
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
                     <h4 class="page-title">Gestão de Certificados</h4>
                     <div class="ml-auto text-right">
                        <nav aria-label="breadcrumb">
                           <ol class="breadcrumb">
                              <li class="breadcrumb-item"><a href="Home">Home</a></li>
                              <li class="breadcrumb-item"><a href="TrainingManagement">Treinamentos</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Certificados</li>
                           </ol>
                        </nav>
                     </div>
                  </div>
               </div>
            </div>
            
            <div class="container-fluid">
               <!-- Certificate Statistics -->
               <div class="row">
                  <div class="col-md-3">
                     <div class="dashboard-card" style="background: linear-gradient(135deg, #3498DB 0%, #2ECC71 100%);">
                        <div class="icon">
                           <i class="mdi mdi-certificate"></i>
                        </div>
                        <h3 id="total-certificates">0</h3>
                        <p>Certificados Emitidos</p>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="dashboard-card" style="background: linear-gradient(135deg, #E74C3C 0%, #F39C12 100%);">
                        <div class="icon">
                           <i class="mdi mdi-calendar-month"></i>
                        </div>
                        <h3 id="monthly-certificates">0</h3>
                        <p>Este Mês</p>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="dashboard-card" style="background: linear-gradient(135deg, #9B59B6 0%, #8E44AD 100%);">
                        <div class="icon">
                           <i class="mdi mdi-check-circle"></i>
                        </div>
                        <h3 id="valid-certificates">0</h3>
                        <p>Certificados Válidos</p>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="dashboard-card" style="background: linear-gradient(135deg, #1ABC9C 0%, #16A085 100%);">
                        <div class="icon">
                           <i class="mdi mdi-download"></i>
                        </div>
                        <h3 id="downloaded-certificates">0</h3>
                        <p>Downloads Realizados</p>
                     </div>
                  </div>
               </div>

               <!-- Certificate Template Management -->
               <div class="row">
                  <div class="col-md-4">
                     <div class="modern-card">
                        <div class="card-body">
                           <h5 class="card-title">Templates de Certificado</h5>
                           <p class="text-muted mb-4">Gerencie os layouts dos certificados</p>
                           
                           <div id="template-list">
                              <div class="template-item active mb-3" data-template="default">
                                 <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                       <strong>Template Padrão</strong>
                                       <br><small class="text-muted">Layout clássico com logo</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate('default')">
                                       <i class="mdi mdi-eye"></i>
                                    </button>
                                 </div>
                              </div>
                              
                              <div class="template-item mb-3" data-template="modern">
                                 <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                       <strong>Template Moderno</strong>
                                       <br><small class="text-muted">Design minimalista</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate('modern')">
                                       <i class="mdi mdi-eye"></i>
                                    </button>
                                 </div>
                              </div>
                              
                              <div class="template-item mb-3" data-template="corporate">
                                 <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                       <strong>Template Corporativo</strong>
                                       <br><small class="text-muted">Para empresas</small>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary" onclick="previewTemplate('corporate')">
                                       <i class="mdi mdi-eye"></i>
                                    </button>
                                 </div>
                              </div>
                           </div>
                           
                           <button type="button" class="btn btn-modern btn-modern-primary btn-block" data-toggle="modal" data-target="#templateModal">
                              <i class="mdi mdi-plus"></i> Novo Template
                           </button>
                        </div>
                     </div>
                  </div>
                  
                  <div class="col-md-8">
                     <div class="modern-card">
                        <div class="card-body">
                           <h5 class="card-title">Pré-visualização do Certificado</h5>
                           <div id="certificate-preview" class="certificate-preview">
                              <!-- Certificate preview will be loaded here -->
                              <div class="text-center p-5">
                                 <i class="mdi mdi-certificate mdi-96px text-muted"></i>
                                 <p class="text-muted mt-3">Selecione um template para visualizar</p>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Certificate List -->
               <div class="row">
                  <div class="col-12">
                     <div class="modern-card">
                        <div class="card-body">
                           <div class="d-flex justify-content-between align-items-center mb-4">
                              <h5 class="card-title mb-0">Certificados Emitidos</h5>
                              <div>
                                 <button type="button" class="btn btn-outline-success btn-sm" onclick="bulkIssue()">
                                    <i class="mdi mdi-certificate"></i> Emissão em Lote
                                 </button>
                                 <button type="button" class="btn btn-outline-primary btn-sm" onclick="validateCertificate()">
                                    <i class="mdi mdi-check-circle"></i> Validar Certificado
                                 </button>
                              </div>
                           </div>
                           
                           <!-- Filters -->
                           <div class="row mb-3">
                              <div class="col-md-3">
                                 <select class="form-control form-control-modern" id="filter_client" onchange="loadCertificates()">
                                    <option value="">Todos os clientes</option>
                                 </select>
                              </div>
                              <div class="col-md-3">
                                 <select class="form-control form-control-modern" id="filter_module" onchange="loadCertificates()">
                                    <option value="">Todos os módulos</option>
                                 </select>
                              </div>
                              <div class="col-md-3">
                                 <select class="form-control form-control-modern" id="filter_status" onchange="loadCertificates()">
                                    <option value="">Todos os status</option>
                                    <option value="1">Válidos</option>
                                    <option value="0">Revogados</option>
                                 </select>
                              </div>
                              <div class="col-md-3">
                                 <input type="text" class="form-control form-control-modern" id="search_validation" placeholder="Código de validação" onkeyup="searchByValidation()">
                              </div>
                           </div>
                           
                           <div class="table-responsive">
                              <table id="certificatesTable" class="table table-modern table-hover">
                                 <thead>
                                    <tr>
                                       <th>Usuário</th>
                                       <th>Módulo</th>
                                       <th>Cliente</th>
                                       <th>Pontuação</th>
                                       <th>Data de Conclusão</th>
                                       <th>Código de Validação</th>
                                       <th>Status</th>
                                       <th>Ações</th>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    <!-- Data loaded via AJAX -->
                                 </tbody>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- Template Management Modal -->
      <div class="modal fade modal-modern" id="templateModal" tabindex="-1" role="dialog">
         <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Gerenciar Template de Certificado</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form id="templateForm">
                     <div class="form-group">
                        <label for="template_name">Nome do Template</label>
                        <input type="text" class="form-control form-control-modern" id="template_name" name="template_name" required>
                     </div>
                     
                     <div class="form-group">
                        <label for="template_description">Descrição</label>
                        <input type="text" class="form-control form-control-modern" id="template_description" name="template_description">
                     </div>
                     
                     <div class="form-group">
                        <label>Configurações do Template</label>
                        <div class="row">
                           <div class="col-md-6">
                              <label for="bg_color">Cor de Fundo</label>
                              <input type="color" class="form-control" id="bg_color" name="bg_color" value="#ffffff">
                           </div>
                           <div class="col-md-6">
                              <label for="text_color">Cor do Texto</label>
                              <input type="color" class="form-control" id="text_color" name="text_color" value="#000000">
                           </div>
                        </div>
                     </div>
                     
                     <div class="form-group">
                        <label for="logo_upload">Logo do Certificado</label>
                        <input type="file" class="form-control-file" id="logo_upload" name="logo_upload" accept="image/*">
                        <small class="text-muted">Recomendado: PNG ou SVG, máximo 2MB</small>
                     </div>
                     
                     <div class="form-group">
                        <label for="certificate_text">Texto do Certificado</label>
                        <textarea class="form-control form-control-modern" id="certificate_text" name="certificate_text" rows="4" placeholder="Use {user_name}, {module_name}, {completion_date}, {score} como marcadores"></textarea>
                     </div>
                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                  <button type="button" class="btn btn-modern btn-modern-primary" onclick="saveTemplate()">Salvar Template</button>
               </div>
            </div>
         </div>
      </div>

      <!-- Certificate Validation Modal -->
      <div class="modal fade modal-modern" id="validationModal" tabindex="-1" role="dialog">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Validar Certificado</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <div class="form-group">
                     <label for="validation_code">Código de Validação</label>
                     <input type="text" class="form-control form-control-modern" id="validation_code" placeholder="Digite o código do certificado">
                  </div>
                  <div id="validation_result" style="display: none;">
                     <!-- Validation result will be displayed here -->
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                  <button type="button" class="btn btn-modern btn-modern-primary" onclick="performValidation()">Validar</button>
               </div>
            </div>
         </div>
      </div>

      <!-- JavaScript -->
   <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
   <script src="js/libs/jquery/jquery-ui.min.js"></script>
   <script src="js/libs/js.cookie.min.js"></script>
   <script src="js/libs/popper.min.js"></script>
   <script src="js/libs/bootstrap.min.js"></script>
   <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
   <script src="js/libs/custom.min.js"></script>
   <script src="js/libs/jquery/datatables.js"></script>
   <script src="js/libs/jquery/dataTables.buttons.min.js"></script>
   <script src="js/libs/toastr.min.js"></script>
   <script src="js/common_scripts.js"></script>
   <script src="js/training_certificates.js"></script>
   <script defer src="js/libs/sidebarmenu.js"></script>
   </body>
</html>

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
   <meta name="description" content="LooPhish - Training Management">
   <meta name="author" content="">
   <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
   <title>LooPhish - Gestão de Treinamentos</title>

   <!-- Custom CSS -->
   <link rel="stylesheet" type="text/css" href="css/style.min.css">
   <link rel="stylesheet" type="text/css" href="css/loophish-theme.css">
   <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
   <link rel="stylesheet" type="text/css" href="css/dataTables.foundation.min.css">
   <link rel="stylesheet" type="text/css" href="css/summernote-lite.min.css">
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
                  <h4 class="page-title">Gestão de Treinamentos</h4>
                  <div class="ml-auto text-right">
                     <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                           <li class="breadcrumb-item"><a href="Home">Home</a></li>
                           <li class="breadcrumb-item active" aria-current="page">Treinamentos</li>
                        </ol>
                     </nav>
                  </div>
               </div>
            </div>
         </div>

         <div class="container-fluid">
            <!-- Training Statistics Cards -->
            <div class="row">
               <div class="col-md-3">
                  <div class="dashboard-card" style="background: linear-gradient(135deg, #3498DB 0%, #2ECC71 100%);">
                     <div class="icon">
                        <i class="mdi mdi-school"></i>
                     </div>
                     <h3 id="total-modules">0</h3>
                     <p>Módulos de Treinamento</p>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="dashboard-card" style="background: linear-gradient(135deg, #E74C3C 0%, #F39C12 100%);">
                     <div class="icon">
                        <i class="mdi mdi-account-group"></i>
                     </div>
                     <h3 id="active-assignments">0</h3>
                     <p>Atribuições Ativas</p>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="dashboard-card" style="background: linear-gradient(135deg, #9B59B6 0%, #8E44AD 100%);">
                     <div class="icon">
                        <i class="mdi mdi-certificate"></i>
                     </div>
                     <h3 id="certificates-issued">0</h3>
                     <p>Certificados Emitidos</p>
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="dashboard-card" style="background: linear-gradient(135deg, #1ABC9C 0%, #16A085 100%);">
                     <div class="icon">
                        <i class="mdi mdi-chart-line"></i>
                     </div>
                     <h3 id="completed-trainings">0</h3>
                     <p>Treinamentos Concluídos</p>
                  </div>
               </div>
            </div>

            <!-- Training Modules Management -->
            <div class="row">
               <div class="col-12">
                  <div class="modern-card">
                     <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                           <h5 class="card-title mb-0">Módulos de Treinamento</h5>
                           <button type="button" class="btn btn-modern btn-modern-primary" data-toggle="modal" data-target="#addModuleModal">
                              <i class="mdi mdi-plus"></i> Criar Módulo
                           </button>
                        </div>

                        <div class="table-responsive">
                           <table id="modulesTable" class="table table-modern table-hover">
                              <thead>
                                 <tr>
                                    <th>Nome do Módulo</th>
                                    <th>Tipo</th>
                                    <th>Categoria</th>
                                    <th>Duração</th>
                                    <th>Nível</th>
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

            <!-- Quick Training Assignment -->
            <div class="row mt-4">
               <div class="col-md-6">
                  <div class="modern-card">
                     <div class="card-body">
                        <h5 class="card-title">Atribuição Rápida</h5>
                        <p class="text-muted">Atribua treinamentos rapidamente a usuários ou departamentos</p>
                        <form id="quickAssignForm">
                           <div class="form-group">
                              <label>Módulo de Treinamento</label>
                              <select class="form-control form-control-modern" id="quick_module_id" name="module_id" required>
                                 <option value="">Selecione um módulo...</option>
                              </select>
                           </div>
                           <div class="form-group">
                              <label>Cliente</label>
                              <select class="form-control form-control-modern" id="quick_client_id" name="client_id">
                                 <option value="">Todos os clientes</option>
                              </select>
                           </div>
                           <div class="form-group">
                              <label>Emails dos Usuários (separados por vírgula)</label>
                              <textarea class="form-control form-control-modern" id="quick_user_emails" name="user_emails" rows="3" placeholder="usuario1@empresa.com, usuario2@empresa.com"></textarea>
                           </div>
                           <button type="submit" class="btn btn-modern btn-modern-primary btn-block">
                              <i class="mdi mdi-send"></i> Atribuir Treinamento
                           </button>
                        </form>
                     </div>
                  </div>
               </div>

               <div class="col-md-6">
                  <div class="modern-card">
                     <div class="card-body">
                        <h5 class="card-title">Ranking de Gamificação</h5>
                        <p class="text-muted">Top 5 usuários com melhor desempenho</p>
                        <div id="rankingList">
                           <!-- Ranking loaded via AJAX -->
                        </div>
                        <div class="text-center mt-3">
                           <a href="TrainingRankings.php" class="btn btn-outline-primary btn-sm">Ver Ranking Completo</a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Add Module Modal -->
   <div class="modal fade modal-modern" id="addModuleModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-xl" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title">Criar Novo Módulo de Treinamento</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <form id="addModuleForm">
                  <div class="row">
                     <div class="col-md-8">
                        <div class="form-group">
                           <label for="module_name">Nome do Módulo *</label>
                           <input type="text" class="form-control form-control-modern" id="module_name" name="module_name" required>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="module_type">Tipo do Módulo *</label>
                           <select class="form-control form-control-modern" id="module_type" name="module_type" required>
                              <option value="">Selecione...</option>
                              <option value="video">Vídeo</option>
                              <option value="quiz">Quiz</option>
                              <option value="interactive">Interativo</option>
                              <option value="mixed">Misto</option>
                           </select>
                        </div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label for="module_description">Descrição</label>
                     <textarea class="form-control form-control-modern" id="module_description" name="module_description" rows="3"></textarea>
                  </div>

                  <div class="row">
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="category">Categoria</label>
                           <select class="form-control form-control-modern" id="category" name="category">
                              <option value="">Selecione...</option>
                              <option value="phishing">Phishing</option>
                              <option value="password_security">Segurança de Senhas</option>
                              <option value="social_engineering">Engenharia Social</option>
                              <option value="data_protection">Proteção de Dados</option>
                              <option value="general_security">Segurança Geral</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="difficulty_level">Nível de Dificuldade</label>
                           <select class="form-control form-control-modern" id="difficulty_level" name="difficulty_level">
                              <option value="basic">Básico</option>
                              <option value="intermediate">Intermediário</option>
                              <option value="advanced">Avançado</option>
                           </select>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                           <label for="estimated_duration">Duração (min)</label>
                           <input type="number" class="form-control form-control-modern" id="estimated_duration" name="estimated_duration" value="15" min="1">
                        </div>
                     </div>
                  </div>

                  <div class="form-group">
                     <label for="tags">Tags (separadas por vírgula)</label>
                     <input type="text" class="form-control form-control-modern" id="tags" name="tags" placeholder="segurança, phishing, email">
                  </div>

                  <!-- Content Editor -->
                  <div class="form-group">
                     <label for="content_data">Conteúdo do Treinamento</label>
                     <textarea class="form-control" id="content_data" name="content_data"></textarea>
                  </div>

                  <!-- Quiz Builder -->
                  <div id="quiz_section" style="display: none;">
                     <h6>Configuração do Quiz</h6>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label for="passing_score">Pontuação Mínima (%)</label>
                              <input type="number" class="form-control form-control-modern" id="passing_score" name="passing_score" value="70" min="0" max="100">
                           </div>
                        </div>
                     </div>

                     <div id="quiz_questions">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                           <h6>Perguntas do Quiz</h6>
                           <button type="button" class="btn btn-sm btn-outline-primary" onclick="addQuizQuestion()">
                              <i class="mdi mdi-plus"></i> Adicionar Pergunta
                           </button>
                        </div>
                        <div id="questions_container">
                           <!-- Questions will be added dynamically -->
                        </div>
                     </div>
                     <hr>
                     <div class="form-group">
                        <label>Fonte de Perguntas</label>
                        <div>
                           <label class="mr-3"><input type="radio" name="question_source" value="module" checked> Perguntas do Módulo</label>
                           <label><input type="radio" name="question_source" value="bank"> Usar Banco de Perguntas</label>
                        </div>
                     </div>
                     <div id="bank_config" style="display: none;">
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Categoria do Banco</label>
                                 <select id="bank_category" class="form-control">
                                    <option value="">Qualquer categoria</option>
                                    <option value="Phishing e Engenharia Social">Phishing e Engenharia Social</option>
                                    <option value="Proteção de Dados e LGPD">Proteção de Dados e LGPD</option>
                                    <option value="Cibersegurança no Dia a Dia">Cibersegurança no Dia a Dia</option>
                                    <option value="Uso Seguro de Ferramentas Corporativas">Uso Seguro de Ferramentas Corporativas</option>
                                    <option value="Fraudes e Golpes Digitais">Fraudes e Golpes Digitais</option>
                                    <option value="Compliance e Cultura de Segurança">Compliance e Cultura de Segurança</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label>Dificuldade</label>
                                 <select id="bank_difficulty" class="form-control">
                                    <option value="">Qualquer</option>
                                    <option value="basic">Iniciante</option>
                                    <option value="intermediate">Intermediário</option>
                                    <option value="advanced">Avançado</option>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-3">
                              <div class="form-group">
                                 <label>Nº de Perguntas</label>
                                 <input type="number" id="bank_count" class="form-control" value="10" min="1">
                              </div>
                           </div>
                        </div>
                        <div class="row mt-2">
                           <div class="col-12">
                              <small class="text-muted">Ou defina uma composição por dificuldade (opcional). Se preencher, o total será a soma dos campos abaixo.</small>
                           </div>
                        </div>
                        <div class="row mt-1">
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label>Iniciante</label>
                                 <input type="number" id="bank_comp_basic" class="form-control" value="0" min="0">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label>Intermediário</label>
                                 <input type="number" id="bank_comp_intermediate" class="form-control" value="0" min="0">
                              </div>
                           </div>
                           <div class="col-md-4">
                              <div class="form-group">
                                 <label>Avançado</label>
                                 <input type="number" id="bank_comp_advanced" class="form-control" value="0" min="0">
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
               <button type="button" class="btn btn-modern btn-modern-primary" onclick="saveModule()">Salvar Módulo</button>
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
   <script src="js/libs/summernote-bs4.min.js"></script>
   <script src="js/common_scripts.js"></script>
   <script src="js/training_management.js"></script>
   <script defer src="js/libs/sidebarmenu.js"></script>
</body>

</html>
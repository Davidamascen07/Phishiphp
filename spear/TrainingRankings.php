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
      <meta name="description" content="LooPhish - Training Rankings">
      <meta name="author" content="">
      <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
      <title>LooPhish - Rankings de Treinamento</title>
      
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
                     <h4 class="page-title">Rankings de Treinamento</h4>
                     <div class="ml-auto text-right">
                        <nav aria-label="breadcrumb">
                           <ol class="breadcrumb">
                              <li class="breadcrumb-item"><a href="Home">Home</a></li>
                              <li class="breadcrumb-item"><a href="TrainingManagement">Treinamentos</a></li>
                              <li class="breadcrumb-item active" aria-current="page">Rankings</li>
                           </ol>
                        </nav>
                     </div>
                  </div>
               </div>
            </div>
            
            <div class="container-fluid">
               <!-- Filter Controls -->
               <div class="row">
                  <div class="col-12">
                     <div class="modern-card">
                        <div class="card-body">
                           <div class="row">
                              <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="filter_client">Filtrar por Cliente</label>
                                    <select class="form-control form-control-modern" id="filter_client" onchange="loadRankings()">
                                       <option value="">Todos os clientes</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="filter_period">Período</label>
                                    <select class="form-control form-control-modern" id="filter_period" onchange="loadRankings()">
                                       <option value="all">Todo o período</option>
                                       <option value="30">Últimos 30 dias</option>
                                       <option value="90">Últimos 90 dias</option>
                                       <option value="365">Último ano</option>
                                    </select>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="form-group">
                                    <label for="ranking_type">Tipo de Ranking</label>
                                    <select class="form-control form-control-modern" id="ranking_type" onchange="updateRankingView()">
                                       <option value="overall">Geral</option>
                                       <option value="department">Por Departamento</option>
                                       <option value="module">Por Módulo</option>
                                    </select>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Podium for Top 3 -->
               <div class="row">
                  <div class="col-12">
                     <div class="modern-card">
                        <div class="card-body text-center">
                           <h5 class="card-title mb-4">🏆 Pódio dos Campeões</h5>
                           <div class="row justify-content-center" id="podium">
                              <!-- Podium loaded via AJAX -->
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Complete Rankings Table -->
               <div class="row">
                  <div class="col-12">
                     <div class="modern-card">
                        <div class="card-body">
                           <div class="d-flex justify-content-between align-items-center mb-4">
                              <h5 class="card-title mb-0">Ranking Completo</h5>
                              <div>
                                 <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportRanking()">
                                    <i class="mdi mdi-download"></i> Exportar
                                 </button>
                                 <button type="button" class="btn btn-outline-info btn-sm" onclick="updateAllRankings()">
                                    <i class="mdi mdi-refresh"></i> Atualizar
                                 </button>
                              </div>
                           </div>
                           
                           <div class="table-responsive">
                              <table id="rankingsTable" class="table table-modern table-hover">
                                 <thead>
                                    <tr>
                                       <th>Posição</th>
                                       <th>Usuário</th>
                                       <th>Cliente/Departamento</th>
                                       <th>Pontuação Total</th>
                                       <th>Módulos Concluídos</th>
                                       <th>Certificados</th>
                                       <th>Média de Score</th>
                                       <th>Tempo Total</th>
                                       <th>Última Atividade</th>
                                       <th>Badges</th>
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

               <!-- Statistics Cards -->
               <div class="row">
                  <div class="col-md-3">
                     <div class="modern-card">
                        <div class="card-body text-center">
                           <div class="icon text-primary mb-3">
                              <i class="mdi mdi-account-group mdi-48px"></i>
                           </div>
                           <h4 id="total_participants">0</h4>
                           <p class="text-muted">Total de Participantes</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="modern-card">
                        <div class="card-body text-center">
                           <div class="icon text-success mb-3">
                              <i class="mdi mdi-trophy mdi-48px"></i>
                           </div>
                           <h4 id="avg_score">0%</h4>
                           <p class="text-muted">Pontuação Média</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="modern-card">
                        <div class="card-body text-center">
                           <div class="icon text-warning mb-3">
                              <i class="mdi mdi-certificate mdi-48px"></i>
                           </div>
                           <h4 id="total_certificates">0</h4>
                           <p class="text-muted">Certificados Emitidos</p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-3">
                     <div class="modern-card">
                        <div class="card-body text-center">
                           <div class="icon text-info mb-3">
                              <i class="mdi mdi-clock mdi-48px"></i>
                           </div>
                           <h4 id="avg_time">0h</h4>
                           <p class="text-muted">Tempo Médio</p>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- Department Rankings -->
               <div class="row" id="department_rankings" style="display: none;">
                  <div class="col-12">
                     <div class="modern-card">
                        <div class="card-body">
                           <h5 class="card-title mb-4">Ranking por Departamentos</h5>
                           <div class="table-responsive">
                              <table id="departmentTable" class="table table-modern table-hover">
                                 <thead>
                                    <tr>
                                       <th>Posição</th>
                                       <th>Departamento</th>
                                       <th>Cliente</th>
                                       <th>Participantes</th>
                                       <th>Pontuação Média</th>
                                       <th>Taxa de Conclusão</th>
                                       <th>Nível de Risco</th>
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

      <!-- Badge Details Modal -->
      <div class="modal fade modal-modern" id="badgeModal" tabindex="-1" role="dialog">
         <div class="modal-dialog" role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title">Badges Conquistadas</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body" id="badgeContent">
                  <!-- Badge details loaded dynamically -->
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
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
   <script src="js/training_rankings.js"></script>
   <script defer src="js/libs/sidebarmenu.js"></script>
   </body>
</html>

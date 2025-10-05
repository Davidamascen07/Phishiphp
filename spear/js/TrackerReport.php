<?php
   require_once(dirname(__FILE__) . '/manager/session_manager.php');
   isSessionValid(true);
?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-BR">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- Favicon icon -->
      <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
   <title>Loophish - Kit de Ferramentas de Spear Phishing por E-mail</title>
      <!-- Custom CSS -->
      <link rel="stylesheet" type="text/css" href="css/select2.min.css">
      <link rel="stylesheet" type="text/css" href="css/style.min.css">
      <link rel="stylesheet" type="text/css" href="css/dataTables.foundation.min.css">
      <link rel="stylesheet" type="text/css" href="css/loophish-modern.css">
      <style> 
         .tab-header{ list-style-type: none; }
      </style>
      <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
   </head>
   <body>
      <!-- ============================================================== -->
      <!-- Preloader - style you can find in spinners.css -->
      <!-- ============================================================== -->
      <div class="preloader">
         <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
         </div>
      </div>
      <!-- ============================================================== -->
      <!-- Main wrapper - style you can find in pages.scss -->
      <!-- ============================================================== -->
      <div id="main-wrapper">
         <!-- ============================================================== -->
         <!-- Topbar header - style you can find in pages.scss -->
         <!-- ============================================================== -->
         <?php include_once 'z_menu.php' ?>
         <!-- ============================================================== -->
         <!-- End Left Sidebar - style you can find in sidebar.scss  -->
         <!-- ============================================================== -->
         <!-- ============================================================== -->
         <!-- Page wrapper  -->
         <!-- ============================================================== -->
         <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Modern Page Header -->
            <!-- ============================================================== -->
            <div class="modern-page-header">
               <div class="page-header-content">
                  <div class="page-header-text">
                     <h1 class="page-title">
                        <i class="mdi mdi-chart-line icon-gradient"></i>
                        Relatórios do Rastreador Web
                     </h1>
                     <p class="page-subtitle">Visualize e analise dados coletados pelos seus rastreadores</p>
                  </div>
                  <div class="page-header-actions">
                     <button type="button" class="modern-btn modern-btn-primary modern-btn-icon" data-toggle="modal" data-target="#ModalTracker">
                        <i class="mdi mdi-target"></i>
                        Selecionar Rastreador
                     </button>
                  </div>
               </div>
               <div class="animated-bg">
                  <div class="shape shape-1"></div>
                  <div class="shape shape-2"></div>
                  <div class="shape shape-3"></div>
               </div>
            </div>

            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid modern-container">
               <!-- ============================================================== -->
               <!-- Info Card -->
               <!-- ============================================================== -->
               <div class="row mb-4">
                  <div class="col-12">
                     <div class="modern-card modern-card-info">
                        <div class="modern-card-body">
                           <div class="row align-items-center">
                              <div class="col-md-4">
                                 <div class="info-group">
                                    <i class="mdi mdi-target text-primary"></i>
                                    <div class="info-content">
                                       <label class="info-label">Rastreador:</label>
                                       <span class="info-value" id="disp_web_tracker_name">Não selecionado</span>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-4">
                                 <div class="info-group" id="disp_tracker_status">
                                    <!-- Status será inserido aqui -->
                                 </div>
                              </div>
                              <div class="col-md-4 text-end">
                                 <div class="info-group">
                                    <i class="mdi mdi-clock-outline text-success"></i>
                                    <div class="info-content">
                                       <label class="info-label">Iniciado em:</label>
                                       <span class="info-value" id="disp_tracker_start">-</span>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <!-- ============================================================== -->
               <!-- Report Control Card -->
               <!-- ============================================================== -->
               <div class="row">
                  <div class="col-12">
                     <div class="modern-card">
                        <div class="modern-card-header">
                           <h3 class="modern-card-title">
                              <i class="mdi mdi-table text-primary me-2"></i>
                              Configurações do Relatório
                           </h3>
                        </div>
                        <div class="modern-card-body">
                           <div class="row g-3">
                              <div class="col-md-3">
                                 <label class="modern-form-label">Tipo de Relatório</label>
                                 <select class="modern-form-input modern-select" id="reportTypeSelector">
                                 </select>
                              </div>
                              <div class="col-md-6">
                                 <label class="modern-form-label">Colunas Visíveis</label>
                                 <select class="modern-form-input modern-select" multiple id="tb_report_colums_list">
                                 </select>
                              </div>
                              <div class="col-md-3">
                                 <label class="modern-form-label">Ações</label>
                                 <div class="d-flex gap-2">
                                    <button type="button" class="modern-btn modern-btn-success modern-btn-icon" data-toggle="tooltip" title="Atualizar tabela" onclick="loadTableWebTrackerResult(g_tracker_id)">
                                       <i class="mdi mdi-refresh"></i>
                                    </button>
                                    <button type="button" class="modern-btn modern-btn-info modern-btn-icon" onclick="exportReport()">
                                       <i class="mdi mdi-download"></i>
                                       Exportar
                                    </button>
                                 </div>
                              </div>
                           </div>

                           <div class="row mt-4">
                              <div class="col-12">
                                 <div class="table-responsive">
                                    <table id="table_tracker_report" class="modern-table table-striped">
                                       <thead>
                                       </thead>
                                       <tbody>
                                       </tbody>
                                    </table>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- ============================================================== -->
               <!-- End PAge Content -->
               <!-- ============================================================== -->
               <!-- ============================================================== -->
               <!-- Right sidebar -->
               <!-- ============================================================== -->
               <!-- .right-sidebar -->
               <!-- ============================================================== -->
               <!-- End Right sidebar -->
               <!-- ============================================================== -->
            </div>
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
            <!-- Modal de Seleção de Rastreador -->
            <div class="modal fade" id="ModalTracker" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                  <div class="modal-content modern-modal">
                     <div class="modal-header modern-modal-header bg-gradient-primary">
                        <h5 class="modal-title text-white">
                           <i class="mdi mdi-target me-2"></i>
                           Selecionar Rastreador
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <div class="modal-body">
                        <div class="table-responsive">
                           <table id="Modal_table_tracker_list" class="modern-table table-hover">
                              <thead>
                                 <tr>
                                    <th class="text-center" width="50">#</th>
                                    <th class="text-center">ID</th>
                                    <th>Nome do Rastreador</th>
                                    <th class="text-center">Data de Criação</th>
                                    <th class="text-center" width="100">Ações</th>
                                 </tr>
                              </thead>
                              <tbody>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- Modal de Exportação -->
            <div class="modal fade" id="ModalExport" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content modern-modal">
                     <div class="modal-header modern-modal-header bg-gradient-success">
                        <h5 class="modal-title text-white">
                           <i class="mdi mdi-download me-2"></i>
                           Exportar Relatório
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-dismiss="modal" aria-label="Close"></button>
                     </div>
                     <div class="modal-body">
                        <div class="modern-form-group">
                           <label for="Modal_export_file_name" class="modern-form-label">Nome do Arquivo</label>
                           <input type="text" class="modern-form-input" id="Modal_export_file_name" placeholder="Digite o nome do arquivo">
                        </div>
                        <div class="modern-form-group">
                           <label for="modal_export_report_selector" class="modern-form-label">Formato do Arquivo</label>
                           <select class="modern-form-input modern-select" id="modal_export_report_selector">
                              <option value="csv">Exportar como CSV</option>
                              <option value="pdf">Exportar como PDF</option>
                              <option value="html">Exportar como HTML</option>
                           </select>
                        </div>
                     </div>
                     <div class="modal-footer modern-modal-footer">
                        <button type="button" class="modern-btn modern-btn-secondary" data-dismiss="modal">
                           <i class="mdi mdi-close"></i>
                           Cancelar
                        </button>
                        <button type="button" class="modern-btn modern-btn-success" onclick="exportReportAction($(this))">
                           <i class="mdi mdi-download"></i>
                           Exportar
                        </button>
                     </div>
                  </div>
               </div>
            </div>
            <?php include_once 'z_footer.php' ?>
            <!-- ============================================================== -->
            <!-- End footer -->
            <!-- ============================================================== -->
         </div>
         <!-- ============================================================== -->
         <!-- End Page wrapper  -->
         <!-- ============================================================== -->
      </div>
      <!-- ============================================================== -->
      <!-- End Wrapper -->
      <!-- ============================================================== -->
      <!-- ============================================================== -->
      <!-- All Jquery -->
      <!-- ============================================================== -->
      <script src="js/libs/jquery/jquery-3.6.0.min.js"></script> 
      <!-- Bootstrap tether Core JavaScript -->
      <script src="js/libs/jquery/jquery-ui.min.js"></script>
      <script src="js/libs/js.cookie.min.js"></script>
      <script src="js/libs/popper.min.js"></script>      
      <script src="js/libs/bootstrap.min.js"></script>
      
      <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
      <!--Custom JavaScript -->
      <script src="js/libs/custom.min.js"></script>
      <script src="js/loophish-modern.js"></script>
      <!-- this page js -->
      <script src="js/libs/jquery/datatables.js"></script>
      <script src="js/libs/select2.min.js"></script>
      <script src="js/common_scripts.js"></script>  
      <script src="js/web_tracker_report_functions.js"></script>
      <script>
         <?php 
            if(isset($_GET['tracker']))
               echo ('webTrackerSelected("'.doFilter($_GET['tracker'],'ALPHA_NUM').'")');  
            else
               echo '$(function() {$("#ModalTracker").modal("toggle"); });';         
         ?>     
      </script>  
      <script defer src="js/libs/moment.min.js"></script>
      <script defer src="js/libs/sidebarmenu.js"></script>
      <script defer src="js/libs/toastr.min.js"></script>
   </body>
</html>
<?php
   require_once(dirname(__FILE__) . '/manager/session_manager.php');
   
   // Detectar se é um acesso público (link compartilhado)
   $isPublicView = isset($_GET['mcamp']) && isset($_GET['tk']);
?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-br">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <!-- Tell the browser to be responsive to screen width -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- Favicon icon -->
      <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
      <title>LooPhish - Dashboard de Campanhas de Email</title>
      <link rel="stylesheet" type="text/css" href="css/select2.min.css"> 
      <link rel="stylesheet" type="text/css" href="css/prism.css"/>
      <link rel="stylesheet" type="text/css" href="css/style.min.css">
      <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
      <link rel="stylesheet" type="text/css" href="css/dataTables.foundation.min.css">
      <link rel="stylesheet" type="text/css" href="css/summernote-lite.min.css"> 
      <style type="text/css">
         .note-editable { background-color: white !important; } /*Disabled background colour*/
         
         <?php if ($isPublicView): ?>
         /* Estilos para modo público compartilhado */
         #main-wrapper {
             margin-left: 0 !important;
             padding-top: 0 !important;
         }
         .page-wrapper {
             margin-left: 0 !important;
             padding-top: 20px !important;
         }
         .container-fluid {
             padding: 15px !important;
         }
         /* Ocultar elementos desnecessários no modo público */
         .topbar,
         .left-sidebar,
         .navbar,
         .breadcrumb {
             display: none !important;
         }
         /* Ajustar título para modo público */
         body::before {
             content: "📊 Dashboard Público - LooPhish (Campanhas de Email)";
             display: block;
             background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
             color: white;
             padding: 15px 20px;
             margin: 0;
             font-weight: bold;
             font-size: 16px;
             text-align: center;
             box-shadow: 0 2px 4px rgba(0,0,0,0.1);
         }
         <?php endif; ?>
      </style> 
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
      <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
         <!-- ============================================================== -->
         <!-- Topbar header - style you can find in pages.scss -->
         <!-- ============================================================== -->
         <?php if (!$isPublicView) { include_once 'z_menu.php'; } ?>
         <!-- ============================================================== -->
         <!-- End Left Sidebar - style you can find in sidebar.scss  -->
         <!-- ============================================================== -->
         <!-- ============================================================== -->
         <!-- Page wrapper  -->
         <!-- ============================================================== -->
         <div class="page-wrapper">
            <!-- ============================================================== -->
            <!-- Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <div class="page-breadcrumb breadcrumb-withbutton">
               <div class="row">
                  <div class="col-12 d-flex no-block align-items-center">
                     <h4 class="page-title">Dashboard de Campanha de Email</h4>
                     <div class="ml-auto text-right">
                        <button type="button" class="btn btn-info btn-sm item_private" data-toggle="modal" data-target="#ModalCampaignList"><i class="mdi mdi-hand-pointing-right" title="Selecionar campanha de email" data-toggle="tooltip" data-placement="bottom"></i> Selecionar Campanha</button>
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-sm" onclick="refreshDashboard()" title="Atualizar dashboard" data-toggle="tooltip" data-placement="bottom"><i class="mdi mdi-refresh"></i></button>
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_settings">Configurações de Exibição</a>
                                <a class="dropdown-item item_private" href="#" data-toggle="modal" data-target="#modal_dashboard_link">Obter Link do Dashboard</a>
                            </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- ============================================================== -->
            <!-- End Bread crumb and right sidebar toggle -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
               <!-- ============================================================== -->
               <!-- Sales Cards  -->
               <!-- ============================================================== -->
               <div class="row">
                    <div class="col-12">
                        <div class="card">
                              <div class="card-body">
                                 <div class="row">
                                    <div class="align-items-left col-12 d-flex no-block row">
                                       <div class="col-md-4">
                                          <span><strong>Campanha: </strong></span><span id="disp_camp_name">N/A</span>
                                       </div>  
                                       <div class="col-md-4 text-center m-l-5" id="disp_camp_status">                            
                                       </div>  
                                       <div class="align-items-right ml-auto row">                                  
                                          <div>
                                             <span><strong>Início: </strong></span><span id="disp_camp_start">N/A</span>
                                          </div> 
                                       </div>
                                    </div>                                    
                                 </div>
                                 <div class="progress m-t-15" title="Status de envio" data-toggle="tooltip" data-placement="top" id="progressbar_status" style="height:20px; background-color:#ccccff;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width:0%"></div>
                                 </div>
                              </div>
                        </div>
                    </div>
               </div>
               <div class="row">
                  <div class="col-md-12">
                     <div class="card">
                        <div class="card-body">
                           <h5 class="card-title "><span>Linha do Tempo da Campanha</span></h5>
                           <div id="chart_live_mailcamp">                           
                              <apexchart type="scatter" height="350"/>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="row">
                  <div class="col-md-12">   
                     <div class="card">
                        <div class="card-body">     
                           <div class="align-items-left col-12 d-flex no-block">             
                              <div class="col-md-3">                             
                                 <h5 class="card-title text-center"><span>Visão Geral - Email</span></h5> 
                                 <div id="radialchart_overview_mailcamp" ></div>
                              </div>
                                 
                              <div class="col-md-3">    
                                 <h5 class="card-title text-center"><span>Emails Enviados</span></h5>
                                 <div id="piechart_mail_total_sent" ></div>
                              </div>
                              <div class="col-md-3">
                                 <h5 class="card-title text-center"><span>Emails Abertos</span></h5>
                                 <div id="piechart_mail_total_mail_open" ></div>
                              </div>
                              <div class="col-md-3">                           
                                 <h5 class="card-title text-center"><span>Emails Respondidos</span></h5>
                                 <div id="piechart_mail_total_replied" class="center"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <div class="row">
                    <div class="col-12">
                        <div class="card">
                              <div class="card-body">
                                 <div class="form-group align-items-left col-12 d-flex no-block">
                                    <div class="col-md-0 row">
                                       <label class="m-t-10 m-r-5"> Colunas:</label>
                                    </div>  
                                    <div class="col-md-9">  
                                       <select class="select2 form-control m-t-16" style="width: 100%;" multiple="multiple"  id="tb_camp_result_colums_list_mcamp">
                                          <optgroup label="Informações do Usuário">
                                             <option value="rid" selected>RID</option>
                                             <option value="user_name" selected>Nome</option>
                                             <option value="user_email" selected>Email</option>
                                             <option value="sending_status" selected>Status</option>
                                             <option value="send_time" selected>Hora de Envio</option>
                                             <option value="send_error" selected>Erro de Envio</option>
                                             <option value="mail_open" selected>Email Aberto</option>
                                             <option value="mail_open_count">Email (contador de aberturas)</option>
                                             <option value="mail_first_open">Email (primeira abertura)</option>
                                             <option value="mail_last_open">Email (última abertura)</option>
                                             <option value="mail_open_times">Email (todos os horários de abertura)</option>
                                             <option value="public_ip" selected>IP Público</option>
                                             <option value="user_agent">Agente do Usuário</option>
                                             <option value="mail_client" selected>Cliente de Email</option>
                                             <option value="platform" selected>Plataforma</option>
                                             <option value="device_type" selected>Tipo de Dispositivo</option>
                                             <option value="all_headers">Cabeçalhos HTTP</option>
                                             <option value="mail_reply" selected>Resposta de Email</option>
                                             <option value="mail_reply_count">Email (contador de respostas)</option>
                                             <option value="mail_reply_content">Email (conteúdo da resposta)</option>
                                          </optgroup>
                                          <optgroup label="Informações de IP do Usuário/Servidor de Email">
                                             <option value="country" selected>País</option>
                                             <option value="city">Cidade</option>
                                             <option value="zip">CEP</option>
                                             <option value="isp">ISP</option>
                                             <option value="timezone">Fuso Horário</option>
                                             <option value="coordinates">Coordenadas</option>
                                          </optgroup>
                                       </select>                                     
                                    </div>  
                                    <div class="col-md-1">
                                       <button type="button" class="btn btn-success mdi mdi-reload " data-toggle="tooltip" data-placement="top" title="Atualizar tabela" onclick="loadTableCampaignResult()"></button>
                                    </div>
                                    <div class="align-items-right ml-auto">
                                       <div class="row">                                  
                                          <button type="button" class="btn btn-success item_private" data-toggle="modal" data-target="#ModalExport"><i class="m-r-10 fas fa-file-export"></i> Exportar</button>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="form-group row">
                                    <div class="table-responsive">
                                       <table id="table_mail_campaign_result" class="table table-striped table-bordered">
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
            <!-- Modal -->
            <div class="modal fade" id="ModalCampaignList" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true ">
               <div class="modal-dialog modal-large" role="document ">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Selecionar Campanha de Email</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                     </div>
                     <div class="modal-body">
                        <div class="form-group row">
                           <div class="table-responsive">
                              <table id="table_mail_campaign_list" class="table table-striped table-bordered">
                                 <thead>
                                    <tr>
                                       <th>#</th>
                                       <th>Nome do Rastreador</th>
                                       <th>Data de Criação</th>
                                       <th>Status</th>
                                       <th>Ação</th>
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
            </div>
            <!-- Modal -->
            <div class="modal fade" id="ModalExport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Exportar Relatório</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                     </div>
                     <div class="modal-body">
                        <div class="form-group row">
                           <label for="Modal_export_file_name" class="col-sm-3 text-left control-label col-form-label">Nome do Arquivo: </label>
                           <div class="col-sm-9 custom-control">
                              <input type="text" class="form-control" id="Modal_export_file_name">
                           </div>
                        </div>
                        <div class="form-group row">
                           <label for="modal_export_report_selector" class="col-sm-3 text-left control-label col-form-label">Formato do Arquivo: </label>
                           <div class="col-sm-9 custom-control">
                              <select class="select2 form-control"  style="height: 36px;width: 100%;" id="modal_export_report_selector">
                                 <option value="csv">Exportar como CSV</option>
                                 <option value="pdf">Exportar como PDF</option>
                                 <option value="html">Exportar como HTML</option>
                              </select>
                           </div>
                        </div>
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-success" onclick="exportReportAction($(this))"><i class="fas fa-file-export"></i> Exportar</button>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modal_reply_mails" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog modal-large" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title">Emails de Resposta</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                     </div>
                     <div class="modal-body" id="modal_reply_mails_body" >
                        <ul class="nav nav-tabs" role="tablist">  
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content tabcontent-border">
                           
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modal_settings" tabindex="-1" role="dialog" aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content" style="width: 120%;">
                     <div class="modal-header">
                        <h5 class="modal-title">Configurações de Exibição do Dashboard</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                     </div>
                     <div class="modal-body">
                        <div class="form-group row">
                           <label class="col-md-3">Dados da tabela:</label>
                           <div class="col-md-9">
                              <div class="custom-control custom-radio">
                                 <input type="radio" class="custom-control-input" id="rb1" value="radio_table_data_single" name="radio_table_data" required checked>
                                 <label class="custom-control-label" for="rb1">Mostrar apenas primeira entrada</label>
                                 <i class="mdi mdi-information cursor-pointer" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Apenas os primeiros dados rastreados dos usuários são exibidos. Ex: exibe apenas a primeira visita do usuário"></i>
                              </div>
                              <div class="custom-control custom-radio">
                                 <input type="radio" class="custom-control-input" id="rb2" value="radio_table_data_all" name="radio_table_data" required>
                                 <label class="custom-control-label" for="rb2">Mostrar todas as entradas</label>
                                 <i class="mdi mdi-information cursor-pointer" tabindex="0" data-toggle="popover" data-trigger="focus" data-placement="top" data-content="Todos os dados rastreados dos usuários são exibidos. Ex: exibe todas as visitas de um usuário"></i>
                              </div> 
                           </div>                           
                        </div>
                        <div class="form-group row">
                           <label class="col-md-3">Mail reply check:</label>
                           <div class="col-md-9">
                              <div class="custom-control custom-radio">
                                 <input type="radio" class="custom-control-input" id="rb3" value="reply_yes" name="radio_mail_reply_check" required checked>
                                 <label class="custom-control-label" for="rb3">Check mail replies</label>
                              </div>
                              <div class="custom-control custom-radio">
                                 <input type="radio" class="custom-control-input" id="rb4" value="reply_no" name="radio_mail_reply_check" required>
                                 <label class="custom-control-label" for="rb4">Do not check mail replies</label>
                              </div> 
                           </div>                           
                        </div>                        
                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-info" onclick="$('#modal_settings').modal('toggle');refreshDashboard();">Apply</button>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="modal_dashboard_link" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
               <div class="modal-dialog modal-large" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Link de Acesso ao Dashboard</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
                     </div>
                     <div class="modal-body">
                        <div class="form-group row">
                           <label for="Modal_export_file_name" class="col-sm-4 text-left control-label col-form-label">Acesso público: </label>
                           <div class="custom-control custom-switch col-sm-2 m-t-5 text-right">
                              <label class="switch">
                                 <input type="checkbox" id="cb_act_dashboard_link">
                                 <span class="slider round"></span>
                              </label>
                           </div>
                        </div>
                        <label for="Modal_export_file_name" class=" text-left control-label col-form-label">Link compartilhável do dashboard (público):</label>
                        <pre><code class="language-html" id="dashboard_link_url">Erro: Por favor, selecione uma campanha primeiro</code></pre>
                        <span class="prism_side float-right">
                           <button type="button" id="btn_copy_quick_tracker" class="btn waves-effect waves-light btn-xs btn-dark mdi mdi-content-copy" data-toggle="tooltip" title="Copiar Link" data-placement="bottom"></button><button type="button" class="btn waves-effect waves-light btn-xs btn-dark mdi mdi-reload" data-toggle="tooltip" title="Regenerar Link" data-placement="bottom" onclick="enableDisablePublicAccess(true)"></button>
                        </span>
                     </div>
                     <div class="modal-footer col-md-12">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal_dashboard_link">Fechar</button>  
                     </div>
                  </div>
               </div>
            </div>
            <!-- Modal -->
            <!-- ============================================================== -->
            <!-- End Container fluid  -->
            <!-- ============================================================== -->
            <!-- ============================================================== -->
            <!-- footer -->
            <!-- ============================================================== -->
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
      <script src="js/libs/jquery/jquery-ui.min.js"></script>
      <script src="js/libs/js.cookie.min.js"></script>
      <!-- Bootstrap tether Core JavaScript -->
      <script src="js/libs/popper.min.js"></script>
      <script src="js/libs/bootstrap.min.js"></script>
      <!--Menu sidebar -->
      <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
      <!--Custom JavaScript -->
      <script src="js/libs/custom.min.js"></script>
      <!--This page JavaScript --> 
      <script src="js/libs/select2.min.js"></script>
      <script src="js/libs/clipboard.min.js"></script> 
      <script src="js/libs/jquery/datatables.js"></script>
      <script src="js/libs/moment.min.js"></script>
      <script src="js/libs/moment-timezone-with-data.min.js"></script>
      <script src="js/libs/apexcharts.js"></script>
      <script src="js/common_scripts.js"></script>
      <script src="js/mail_campaign_dashboard.js"></script>
      <?php
         if(isset($_GET['tk']) && isset($_GET['mcamp']) && amIPublic($_GET['tk'],$_GET['mcamp']) == true)
            echo '<script>
                     var g_tk_id = "'.$_GET['tk'].'"; 
                     hideMeFromPublic(); 
                  </script>';
         else{
             echo '<script>var g_tk_id = getRandomId();</script>';            
            isSessionValid(true);
         }
      //------------------------------------------
         echo '<script>';
         
         if(isset($_GET['mcamp']))
            echo 'var g_campaign_id ="'.doFilter($_GET['mcamp'],'ALPHA_NUM').'";
                  campaignSelected("' . doFilter($_GET['mcamp'],'ALPHA_NUM') . '");';
         else
            echo 'var g_campaign_id ="", g_tracker_id="";
                  $(function() {$("#ModalCampaignList").modal("toggle");});';
         
         echo '</script>';
      ?>

      <?php if (!$isPublicView) { ?>
      <script defer src="js/libs/sidebarmenu.js"></script>
      <?php } ?>
      <script defer src="js/libs/toastr.min.js"></script>
      <script defer src="js/libs/summernote-bs4.min.js"></script>
      <script defer src="js/libs/prism.js"></script> 
   </body>
</html>
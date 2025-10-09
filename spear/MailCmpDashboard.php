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
      <!-- Modern CSS Framework -->
      <link rel="stylesheet" type="text/css" href="css/style.min.css">
      <link rel="stylesheet" type="text/css" href="css/loophish-modern.css">
      <link rel="stylesheet" type="text/css" href="css/loophish-theme-2025.css">
      <link rel="stylesheet" type="text/css" href="css/select2.min.css">
      <link rel="stylesheet" type="text/css" href="css/prism.css"/>
      <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
      <link rel="stylesheet" type="text/css" href="css/dataTables.foundation.min.css">
      <link rel="stylesheet" type="text/css" href="css/summernote-lite.min.css">
      <!-- Material Design Icons -->
      <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.0.96/css/materialdesignicons.min.css" rel="stylesheet"> 
      <style type="text/css">
         .note-editable { background-color: white !important; } /*Disabled background colour*/
         
         /* Dashboard Header Moderno */
         .dashboard-header {
            background: var(--gradient-primary);
            color: white;
            border-radius: var(--border-radius-xl);
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
            position: relative;
            overflow: hidden;
         }
         
         .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
         }
         
         .dashboard-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
         }
         
         .dashboard-subtitle {
            opacity: 0.9;
            font-size: 1.2rem;
            position: relative;
            z-index: 1;
         }
         
         /* Cards Modernos */
         .card {
            background: #fdfdfd;
            border-radius: var(--border-radius-xl);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: var(--transition-normal);
            margin-bottom: 2rem;
         }
         
         .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
         }
         
         .card-body {
            padding: 2rem;
         }
         
         .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
            margin-bottom: 1.5rem;
         }
         
         /* Cards de Métricas Modernos */
         .metric-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            position: relative;
            overflow: hidden;
         }
         
         .metric-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
         }
         
         .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-primary);
         }
         
         .metric-number {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.5rem;
            font-family: 'Inter', sans-serif;
         }
         
         .metric-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
         }
         
         .metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
         }
         
         .progress-wrapper {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
         }
         
         .progress {
            flex: 1;
            height: 8px;
            background-color: #f3f4f6;
            border-radius: 50px;
            overflow: hidden;
         }
         
         .progress-bar {
            height: 100%;
            border-radius: 50px;
            transition: width 0.6s ease;
         }
         
         .progress-text {
            font-size: 0.875rem;
            font-weight: 600;
            color: #374151;
            min-width: 40px;
            text-align: right;
         }
         
         .chart-container {
            background: #ffffff;
            border-radius: 16px;
            padding: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.05);
         }
         
         /* Tipografia Moderna */
         .fw-bold {
            font-weight: 700 !important;
         }
         
         .text-primary {
            color: var(--primary-color) !important;
         }
         
         /* Cores de Background para Ícones */
         .bg-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
         }
         
         .bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
         }
         
         .bg-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
         }
         
         .bg-info {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
         }
         
         .bg-primary {
            background: var(--gradient-primary) !important;
         }
         
         /* Grid System Responsivo */
         .g-3 {
            gap: 1rem !important;
         }
         
         .g-4 {
            gap: 1.5rem !important;
         }
         
         .mb-4 {
            margin-bottom: 1.5rem !important;
         }
         
         .p-4 {
            padding: 1.5rem !important;
         }
         
         /* Botões Modernos */
         .btn {
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: var(--transition-normal);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
         }
         
         .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
         }
         
         .btn-info {
            background: var(--gradient-primary);
            border: none;
         }
         
         .page-wrapper {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
         }
         
         /* Responsividade */
         @media (max-width: 768px) {
            .dashboard-header {
               padding: 1.5rem;
            }
            
            .dashboard-title {
               font-size: 2rem;
            }
            
            .card-body {
               padding: 1.5rem;
            }
         }
         
         /* Animações suaves */
         @keyframes fadeInUp {
            from {
               opacity: 0;
               transform: translateY(30px);
            }
            to {
               opacity: 1;
               transform: translateY(0);
            }
         }
         
         .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
         }
         
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
             background: var(--gradient-primary);
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
               <!-- Dashboard Header Moderno -->
               <div class="dashboard-header animate-fadeInUp">
                  <div class="dashboard-title">Dashboard de Campanhas de Email</div>
                  <div class="dashboard-subtitle">Análise detalhada e relatórios de campanhas de phishing por email</div>
               </div>
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
               <!-- Seção de Visão Geral dos Emails -->
               <div class="row mb-4">
                  <div class="col-12">
                     <div class="card">
                        <div class="card-body p-4">
                           <h3 class="mb-4 fw-bold text-primary">📧 Visão Geral dos Emails</h3>
                           <div class="row g-4">
                              <!-- Gráfico Circular Email -->
                              <div class="col-lg-4">
                                 <div class="chart-container">
                                    <div id="radialchart_overview_mailcamp" style="min-height: 320px;"></div>
                                 </div>
                              </div>
                              
                              <!-- Cards de Métricas Email -->
                              <div class="col-lg-8">
                                 <div class="row g-3">
                                    <!-- Card Emails Enviados -->
                                    <div class="col-md-4">
                                       <div class="metric-card bg-white">
                                          <div class="d-flex justify-content-between align-items-start mb-3">
                                             <div>
                                                <div class="metric-number text-success" id="metric_emails_sent">0</div>
                                                <div class="metric-label">Total de Emails Enviados</div>
                                             </div>
                                             <div class="metric-icon bg-success">
                                                <i class="mdi mdi-email-outline"></i>
                                             </div>
                                          </div>
                                          <div class="progress-wrapper">
                                             <div class="progress">
                                                <div class="progress-bar bg-success" id="progress_emails_sent" style="width: 0%;"></div>
                                             </div>
                                             <span class="progress-text" id="percent_emails_sent">0%</span>
                                          </div>
                                          <!-- Gráfico original oculto para manter compatibilidade -->
                                          <div id="piechart_mail_total_sent" style="display: none;"></div>
                                       </div>
                                    </div>
                                    
                                    <!-- Card Emails Abertos -->
                                    <div class="col-md-4">
                                       <div class="metric-card bg-white">
                                          <div class="d-flex justify-content-between align-items-start mb-3">
                                             <div>
                                                <div class="metric-number text-warning" id="metric_emails_opened">0</div>
                                                <div class="metric-label">Emails Abertos</div>
                                             </div>
                                             <div class="metric-icon bg-warning">
                                                <i class="mdi mdi-email-open-outline"></i>
                                             </div>
                                          </div>
                                          <div class="progress-wrapper">
                                             <div class="progress">
                                                <div class="progress-bar bg-warning" id="progress_emails_opened" style="width: 0%;"></div>
                                             </div>
                                             <span class="progress-text" id="percent_emails_opened">0%</span>
                                          </div>
                                          <!-- Gráfico original oculto para manter compatibilidade -->
                                          <div id="piechart_mail_total_mail_open" style="display: none;"></div>
                                       </div>
                                    </div>
                                    
                                    <!-- Card Respostas -->
                                    <div class="col-md-4">
                                       <div class="metric-card bg-white">
                                          <div class="d-flex justify-content-between align-items-start mb-3">
                                             <div>
                                                <div class="metric-number text-danger" id="metric_emails_replied">0</div>
                                                <div class="metric-label">Respostas a Emails</div>
                                             </div>
                                             <div class="metric-icon bg-danger">
                                                <i class="mdi mdi-reply"></i>
                                             </div>
                                          </div>
                                          <div class="progress-wrapper">
                                             <div class="progress">
                                                <div class="progress-bar bg-danger" id="progress_emails_replied" style="width: 0%;"></div>
                                             </div>
                                             <span class="progress-text" id="percent_emails_replied">0%</span>
                                          </div>
                                          <!-- Gráfico original oculto para manter compatibilidade -->
                                          <div id="piechart_mail_total_replied" style="display: none;"></div>
                                       </div>
                                    </div>
                                    
                                    <!-- Card Taxa de Abertura -->
                                    <div class="col-md-6">
                                       <div class="metric-card bg-white">
                                          <div class="d-flex justify-content-between align-items-start mb-3">
                                             <div>
                                                <div class="metric-number text-info" id="metric_open_rate">0%</div>
                                                <div class="metric-label">Taxa de Abertura</div>
                                             </div>
                                             <div class="metric-icon bg-info">
                                                <i class="mdi mdi-chart-line"></i>
                                             </div>
                                          </div>
                                          <div class="progress-wrapper">
                                             <div class="progress">
                                                <div class="progress-bar bg-info" id="progress_open_rate" style="width: 0%;"></div>
                                             </div>
                                             <span class="progress-text" id="percent_open_rate_display">0%</span>
                                          </div>
                                       </div>
                                    </div>
                                    
                                    <!-- Card Taxa de Resposta -->
                                    <div class="col-md-6">
                                       <div class="metric-card bg-white">
                                          <div class="d-flex justify-content-between align-items-start mb-3">
                                             <div>
                                                <div class="metric-number text-primary" id="metric_reply_rate">0%</div>
                                                <div class="metric-label">Taxa de Resposta</div>
                                             </div>
                                             <div class="metric-icon bg-primary">
                                                <i class="mdi mdi-trending-up"></i>
                                             </div>
                                          </div>
                                          <div class="progress-wrapper">
                                             <div class="progress">
                                                <div class="progress-bar bg-primary" id="progress_reply_rate" style="width: 0%;"></div>
                                             </div>
                                             <span class="progress-text" id="percent_reply_rate_display">0%</span>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
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
      <script src="js/dashboard_modern_integration.js"></script>
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
<?php
require_once(dirname(__FILE__) . '/manager/session_manager.php');
isSessionValid(true);

$current_client_id = getCurrentClientId();
$accessible_clients = getUserAccessibleClients();

// Definir cliente padrão se não estiver definido e houver clientes disponíveis
if (!$current_client_id && !empty($accessible_clients)) {
    setClientContext($accessible_clients[0]['client_id']);
    $current_client_id = $accessible_clients[0]['client_id'];
}
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
   <title>Loophish - Plataforma de Awareness em Segurança Cibernética</title>
   <!-- Modern CSS Framework -->
   <link rel="stylesheet" type="text/css" href="css/style.min.css">
   <link rel="stylesheet" type="text/css" href="css/loophish-modern.css">
   <link rel="stylesheet" type="text/css" href="css/loophish-theme-2025.css">
   <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
   <link rel="stylesheet" type="text/css" href="css/select2.min.css">
   <!-- Material Design Icons (fallback CDN caso o arquivo local não carregue) -->
   <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.0.96/css/materialdesignicons.min.css" rel="stylesheet">
   <style>
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
      
      .client-selector {
         position: absolute;
         top: 1rem;
         right: 1rem;
         z-index: 2;
      }
      
      .client-selector select {
         background: rgba(255, 255, 255, 0.2);
         border: 1px solid rgba(255, 255, 255, 0.3);
         color: white;
         border-radius: var(--border-radius);
         padding: 0.5rem 1rem;
         min-width: 200px;
      }
      
      .client-selector select option {
         background: var(--gray-800);
         color: white;
      }
      
      /* Stats Cards Modernos */
      .stats-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }
      
      .stat-card-modern {
         background: #fdfdfd;
         border-radius: var(--border-radius-xl);
         padding: 2rem;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
         border: 1px solid rgba(255, 255, 255, 0.2);
         transition: var(--transition-normal);
         position: relative;
         overflow: hidden;
      }
      
      .stat-card-modern::before {
         content: '';
         position: absolute;
         top: 0;
         left: 0;
         right: 0;
         height: 4px;
      }
      
      .stat-card-modern:hover {
         transform: translateY(-8px);
         box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
      }
      
      .stat-card-modern.gradient-email::before {
         background: linear-gradient(135deg, #a0c4ff 0%, #003566 100%);
      }
      
      .stat-card-modern.gradient-web::before {
         background: linear-gradient(135deg, #b9fbc0 0%, #1b4332 100%);
      }
      
      .stat-card-modern.gradient-quick::before {
         background: linear-gradient(135deg, #f8d7da 0%, #a71d2a 100%);
      }
      
      .stat-card-modern.gradient-clients::before {
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      }
      
      .stat-card-header {
         display: flex;
         align-items: center;
         justify-content: space-between;
         margin-bottom: 1.5rem;
      }
      
      .stat-icon-modern {
         width: 70px;
         height: 70px;
         border-radius: var(--border-radius-lg);
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 2rem;
         color: white;
         background: var(--gradient-primary);
         box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
      }
      
      .stat-value-modern {
         font-size: 3rem;
         font-weight: 800;
         margin: 0;
         background: var(--gradient-primary);
         -webkit-background-clip: text;
         -webkit-text-fill-color: transparent;
         background-clip: text;
         line-height: 1;
      }
      
      .stat-label-modern {
         font-size: 1rem;
         font-weight: 600;
         color: var(--gray-600);
         text-transform: uppercase;
         letter-spacing: 0.5px;
         margin-bottom: 0.5rem;
      }
      
      .stat-description {
         font-size: 0.875rem;
         color: var(--gray-500);
         margin-top: 0.5rem;
      }
      
      /* Action Cards */
      .action-cards {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }
      
      .action-card {
         background: #fdfdfd;
         border-radius: var(--border-radius-xl);
         padding: 2rem;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
         transition: var(--transition-normal);
         position: relative;
         overflow: hidden;
         border: 1px solid rgba(255, 255, 255, 0.2);
         display: flex;
         flex-direction: column;
         justify-content: flex-start;
      }
      
      .action-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
      }
      
      .action-card-icon {
         width: 60px;
         height: 60px;
         border-radius: var(--border-radius-lg);
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.5rem;
         color: white;
         margin-bottom: 1.5rem;
         box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      }
      
      .action-card-title {
         font-size: 1.25rem;
         font-weight: 600;
         color: var(--gray-600);
         margin-bottom: 0.75rem;
      }
      
      .action-card-description {
         color: var(--gray-600);
         margin-bottom: 1.5rem;
         line-height: 1.6;
      }
      
      .action-card-button {
         background: var(--gradient-primary);
         color: white;
         border: none;
         border-radius: var(--border-radius);
         padding: 0.75rem 1.5rem;
         font-weight: 500;
         cursor: pointer;
         transition: var(--transition-normal);
         width: 100%;
         box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            /* push the button to the bottom of the card */
            margin-top: auto;
      }
      
      .action-card-button:hover {
         transform: translateY(-2px);
         box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
      }
      
      /* Recent Activity */
      .recent-activity {
         background: var(--bg-card);
         border-radius: var(--border-radius-xl);
         padding: 2rem;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
         border: 1px solid rgba(255, 255, 255, 0.2);
      }
      
      .activity-header {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin-bottom: 2rem;
         padding-bottom: 1rem;
         border-bottom: 1px solid var(--gray-100);
      }
      
      .activity-title {
         font-size: 1.5rem;
         font-weight: 600;
         color: var(--gray-800);
         margin: 0;
      }
      
      .activity-item {
         display: flex;
         align-items: center;
         gap: 1rem;
         padding: 1rem 0;
         border-bottom: 1px solid var(--gray-100);
      }
      
      .activity-item:last-child {
         border-bottom: none;
      }
      
      .activity-icon {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         background: var(--gradient-primary);
         color: white;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1rem;
      }
      
      .activity-content {
         flex: 1;
      }
      
      .activity-text {
         font-weight: 500;
         color: var(--gray-800);
         margin-bottom: 0.25rem;
      }
      
      .activity-time {
         font-size: 0.875rem;
         color: var(--gray-500);
      }
      
      /* Responsive */
      @media (max-width: 768px) {
         .dashboard-header {
            padding: 1.5rem;
         }
         
         .dashboard-title {
            font-size: 2rem;
         }
         
         .client-selector {
            position: static;
            margin-top: 1rem;
         }
         
         .stats-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
         }
         
         .action-cards {
            grid-template-columns: 1fr;
         }
      }
   </style>
         <!-- background: linear-gradient(90deg, #667eea, #764ba2);
         border-radius: 2px;
      }

      .graph-container {
         background: #ffffff;
         border-radius: 12px;
         padding: 20px;
         box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
      }

      .page-wrapper {
         background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
         min-height: 100vh;
      }

      .card-body {
         padding: 2rem;
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

      .animate-delay-1 {
         animation-delay: 0.1s;
      }

      .animate-delay-2 {
         animation-delay: 0.2s;
      }

      .animate-delay-3 {
         animation-delay: 0.3s;
      }

      /* Responsividade melhorada */
      @media (max-width: 768px) {
         .modern-card {
            margin-bottom: 1.5rem;
         }

         .card-body {
            padding: 1.5rem;
         }
      }
   </style> -->
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
         <!-- Bread crumb and right sidebar toggle -->
         <!-- ============================================================== -->
         <div class="page-breadcrumb">
            <div class="row">
               <div class="col-12 d-flex no-block align-items-center">
                  <!--<h4 class="page-title">Home</h4> -->
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
            <!-- Dashboard Header -->
            <div class="dashboard-header animate-fadeInUp">
               <div class="dashboard-title">
                  <i class="mdi mdi-view-dashboard"></i>
                  Dashboard Loophish
               </div>
               <div class="dashboard-subtitle">
                  Plataforma de Awareness em Segurança Cibernética
               </div>
               
               <!-- Seletor de Cliente -->
               <div class="client-selector">
                  <select class="form-control" id="clientSelector">
                     <option value="">Selecionar Cliente</option>
                     <?php foreach ($accessible_clients as $client): ?>
                        <option value="<?= $client['client_id'] ?>" <?= $client['client_id'] == $current_client_id ? 'selected' : '' ?>>
                           <?= htmlspecialchars($client['client_name']) ?>
                        </option>
                     <?php endforeach; ?>
                  </select>
               </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
               <div class="stat-card-modern gradient-email animate-fadeInUp animate-delay-1">
                  <div class="stat-card-header">
                     <div>
                        <div class="stat-label-modern">Campanhas de Email</div>
                        <div class="stat-value-modern" id="lb_mailcamp_total">0</div>
                        <div class="stat-description">Total de campanhas criadas</div>
                     </div>
                     <div class="stat-icon-modern" style="background: linear-gradient(135deg, #a0c4ff 0%, #003566 100%);">
                        <i class="mdi mdi-email-multiple"></i>
                     </div>
                  </div>
                  <div class="d-flex justify-content-between text-sm">
                     <span>Ativas: <strong id="lb_mailcamp_active">0</strong></span>
                     <span>Concluídas: <strong id="lb_mailcamp_completed">0</strong></span>
                  </div>
               </div>
               
               <div class="stat-card-modern gradient-web animate-fadeInUp animate-delay-2">
                  <div class="stat-card-header">
                     <div>
                        <div class="stat-label-modern">Rastreadores Web</div>
                        <div class="stat-value-modern" id="lb_webtracker_total">0</div>
                        <div class="stat-description">Páginas de phishing criadas</div>
                     </div>
                     <div class="stat-icon-modern" style="background: linear-gradient(135deg, #b9fbc0 0%, #1b4332 100%);">
                        <i class="mdi mdi-web"></i>
                     </div>
                  </div>
                  <div class="d-flex justify-content-between text-sm">
                     <span>Ativos: <strong id="lb_webtracker_active">0</strong></span>
                     <span>Visitas: <strong id="lb_webtracker_visits">0</strong></span>
                  </div>
               </div>
               
               <div class="stat-card-modern gradient-quick animate-fadeInUp animate-delay-3">
                  <div class="stat-card-header">
                     <div>
                        <div class="stat-label-modern">Trackers Rápidos</div>
                        <div class="stat-value-modern" id="lb_quicktracker_total">0</div>
                        <div class="stat-description">Links de rastreamento</div>
                     </div>
                     <div class="stat-icon-modern" style="background: linear-gradient(135deg, #f8d7da 0%, #a71d2a 100%);">
                        <i class="mdi mdi-flash"></i>
                     </div>
                  </div>
                  <div class="d-flex justify-content-between text-sm">
                     <span>Ativos: <strong id="lb_quicktracker_active">0</strong></span>
                     <span>Cliques: <strong id="lb_quicktracker_clicks">0</strong></span>
                  </div>
               </div>
               
               <div class="stat-card-modern gradient-clients animate-fadeInUp animate-delay-4">
                  <div class="stat-card-header">
                     <div>
                        <div class="stat-label-modern">Clientes Ativos</div>
                        <div class="stat-value-modern" id="lb_clients_total"><?= count($accessible_clients) ?></div>
                        <div class="stat-description">Organizações gerenciadas</div>
                     </div>
                     <div class="stat-icon-modern" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <i class="mdi mdi-domain"></i>
                     </div>
                  </div>
                  <div class="d-flex justify-content-between text-sm">
                     <span>Usuários: <strong id="lb_total_users">0</strong></span>
                     <span>Departamentos: <strong id="lb_total_departments">0</strong></span>
                  </div>
               </div>
            </div>

            <!-- Action Cards -->
            <div class="action-cards">
               <div class="action-card animate-fadeInUp animate-delay-1">
                  <div class="action-card-icon" style="background: linear-gradient(135deg, #a0c4ff 0%, #003566 100%);">
                     <i class="mdi mdi-email-plus"></i>
                  </div>
                  <div class="action-card-title">Nova Campanha de Email</div>
                  <div class="action-card-description">
                     Crie uma nova campanha de phishing por email para testar a conscientização dos usuários.
                  </div>
                  <button class="action-card-button" onclick="window.location.href='MailCampaignList'">
                     <i class="mdi mdi-plus"></i>
                     Criar Campanha
                  </button>
               </div>
               
               <div class="action-card animate-fadeInUp animate-delay-2">
                  <div class="action-card-icon" style="background: linear-gradient(135deg, #b9fbc0 0%, #1b4332 100%);">
                     <i class="mdi mdi-web-plus"></i>
                  </div>
                  <div class="action-card-title">Novo Rastreador Web</div>
                  <div class="action-card-description">
                     Desenvolva páginas de phishing personalizadas para capturar interações dos usuários.
                  </div>
                  <button class="action-card-button" onclick="window.location.href='TrackerGenerator'">
                     <i class="mdi mdi-plus"></i>
                     Criar Rastreador
                  </button>
               </div>
               
               <div class="action-card animate-fadeInUp animate-delay-3">
                  <div class="action-card-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                     <i class="mdi mdi-account-group"></i>
                  </div>
                  <div class="action-card-title">Gerenciar Clientes</div>
                  <div class="action-card-description">
                     Administre organizações, usuários e configure parâmetros personalizados.
                  </div>
                  <button class="action-card-button" onclick="window.location.href='ClientList'">
                     <i class="mdi mdi-cog"></i>
                     Gerenciar
                  </button>
               </div>
            </div>
            <div class="row mt-4">
               <div class="col-md-12">
                  <div class="modern-graph-card animate-fadeInUp animate-delay-1">
                     <div class="card-body">
                        <h5 class="graph-title">
                           <i class="mdi mdi-chart-bar mr-2"></i>
                           Visão Geral das Campanhas
                        </h5>
                        <div class="graph-container">
                           <div id="graph_overview" style="height: 300px;"></div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row mt-4">
               <div class="col-md-12">
                  <div class="modern-graph-card animate-fadeInUp animate-delay-2">
                     <div class="card-body">
                        <h5 class="graph-title">
                           <i class="mdi mdi-timeline mr-2"></i>
                           Linha do Tempo das Campanhas
                        </h5>
                        <div class="graph-container">
                           <div id="graph_timeline_all" style="height: 300px;"></div>
                        </div>
                     </div>
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
   <script src="js/libs/js.cookie.min.js"></script>
   <!-- Bootstrap tether Core JavaScript -->
   <script src="js/libs/bootstrap.min.js"></script>
   <!--Wave Effects -->
   <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
   <!--Custom JavaScript -->
   <script src="js/libs/custom.min.js"></script>
   <!--This page JavaScript -->
   <!-- Charts js Files -->
   <script src="js/libs/apexcharts.js"></script>
   <script src="js/libs/moment.min.js"></script>
   <script src="js/libs/moment-timezone-with-data.min.js"></script>
   <script src="js/libs/select2.min.js"></script>
   <script src="js/common_scripts.js"></script>
   <script src="js/home_functions.js"></script>
   <script defer src="js/libs/sidebarmenu.js"></script>
   <script defer src="js/libs/toastr.min.js"></script>
   
   <script>
      $(document).ready(function() {
         // Inicializar Select2 no seletor de cliente
         $('#clientSelector').select2({
            theme: 'default',
            minimumResultsForSearch: -1
         });
         
         // Evento de mudança de cliente
         $('#clientSelector').on('change', function() {
            const clientId = $(this).val();
            if (clientId) {
               setClientContext(clientId);
            }
         });
         
         // Carregar estatísticas do cliente atual
         loadClientStats();
         
         // Atualizar dados a cada 30 segundos
         setInterval(loadClientStats, 30000);
         
         // Sincronizar com o header quando cliente mudar globalmente
         window.addEventListener('clientChanged', function(event) {
            $('#clientSelector').val(event.detail.clientId).trigger('change.select2');
            loadClientStats();
         });
      }); // Fechar $(document).ready()
      
      function setClientContext(clientId) {
         $.post({
            url: "manager/session_api.php",
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ 
               action: "setClientContext",
               clientId: clientId 
            })
         }).done(function(response) {
            if (response.success === true) {
               toastr.success('Cliente selecionado com sucesso!');
               loadClientStats();
            } else {
               toastr.error('Erro ao selecionar cliente: ' + (response.message || 'Erro desconhecido'));
            }
         }).fail(function(xhr, status, error) {
            console.error('Erro AJAX:', xhr.responseText);
            toastr.error('Erro de comunicação com o servidor: ' + error);
         });
      }
      
      function loadClientStats() {
         let currentClientId = $('#clientSelector').val();
         
         // Se não conseguir do selector, tentar obter da sessão
         if (!currentClientId || currentClientId === '') {
            // Fazer requisição para obter cliente atual da sessão
            $.post({
               url: "manager/session_api.php",
               contentType: 'application/json; charset=utf-8',
               data: JSON.stringify({ 
                  action: "getCurrentClientContext"
               })
            }).done(function(sessionData) {
               if (sessionData.success && sessionData.clientId) {
                  currentClientId = sessionData.clientId;
                  // Atualizar o selector se necessário
                  if ($('#clientSelector').val() !== currentClientId) {
                     $('#clientSelector').val(currentClientId).trigger('change.select2');
                  }
                  loadStatsForClient(currentClientId);
               } else {
                  console.warn('Não foi possível obter cliente da sessão');
                  currentClientId = 'default_org';
                  loadStatsForClient(currentClientId);
               }
            }).fail(function() {
               console.warn('Erro ao obter cliente da sessão, usando default');
               currentClientId = 'default_org';
               loadStatsForClient(currentClientId);
            });
            return;
         }
         
         loadStatsForClient(currentClientId);
      }
      
      function loadStatsForClient(clientId) {
         if (!clientId) {
            console.warn('ID do cliente não fornecido');
            return;
         }
         
         // Carregar estatísticas de campanhas de email
         $.post({
            url: "manager/home_stats_manager",
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ 
               action_type: "get_email_campaigns_stats",
               client_id: clientId 
            })
         }).done(function(data) {
            if (data.total !== undefined) {
               $('#lb_mailcamp_total').text(data.total);
               $('#lb_mailcamp_active').text(data.active || 0);
               $('#lb_mailcamp_completed').text(data.completed || 0);
            }
         });
         
         // Carregar estatísticas de rastreadores web
         $.post({
            url: "manager/home_stats_manager",
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ 
               action_type: "get_web_trackers_stats",
               client_id: clientId 
            })
         }).done(function(data) {
            if (data.total !== undefined) {
               $('#lb_webtracker_total').text(data.total);
               $('#lb_webtracker_active').text(data.active || 0);
               $('#lb_webtracker_visits').text(data.visits || 0);
            }
         });
         
         // Carregar estatísticas de trackers rápidos
         $.post({
            url: "manager/home_stats_manager",
            contentType: 'application/json; charset=utf-8',
            data: JSON.stringify({ 
               action_type: "get_quick_trackers_stats",
               client_id: clientId 
            })
         }).done(function(data) {
            if (data.total !== undefined) {
               $('#lb_quicktracker_total').text(data.total);
               $('#lb_quicktracker_active').text(data.active || 0);
               $('#lb_quicktracker_clicks').text(data.clicks || 0);
            }
         });
         
         // Carregar estatísticas gerais do cliente
         if (clientId !== 'default_org') {
            $.post({
               url: "manager/client_manager",
               contentType: 'application/json; charset=utf-8',
               data: JSON.stringify({ 
                  action_type: "get_client_stats",
                  client_id: clientId 
               })
            }).done(function(data) {
               if (data.total_users !== undefined) {
                  $('#lb_total_users').text(data.total_users);
                  $('#lb_total_departments').text(data.top_departments ? data.top_departments.length : 0);
               }
            });
         }
      }
   </script>
</body>

</html>
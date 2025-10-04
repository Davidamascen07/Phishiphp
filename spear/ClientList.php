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
      <title>Loophish - Gerenciamento de Clientes</title>
      <!-- Modern CSS Framework -->
      <link rel="stylesheet" type="text/css" href="css/loophish-modern.css">
      <link rel="stylesheet" type="text/css" href="css/loophish-theme-2025.css">
      <link rel="stylesheet" type="text/css" href="css/select2.min.css">
      <link rel="stylesheet" type="text/css" href="css/style.min.css">
      <link rel="stylesheet" type="text/css" href="css/dataTables.foundation.min.css">
      <link rel="stylesheet" type="text/css" href="css/toastr.min.css">
      <style>
         .clients-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
         }
         
         .clients-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
         }
         
         .clients-subtitle {
            opacity: 0.9;
            font-size: 1.1rem;
         }
         
         .client-card {
            background: white;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            overflow: hidden;
            position: relative;
         }
         
         .client-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
         }
         
         .client-card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
         }
         
         .client-logo {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
         }
         
         .client-status {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
         }
         
         .status-active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
         }
         
         .status-inactive {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
         }
         
         .client-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            padding: 1.5rem;
         }
         
         .stat-item {
            text-align: center;
         }
         
         .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
         }
         
         .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
         }
         
         .client-actions {
            padding: 1rem 1.5rem;
            background: #f8fafc;
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
         }
         
         .action-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            border: none;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
         }
         
         .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
         }
         
         .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
         }
         
         .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
         }
         
         .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
         }
         
         .floating-add-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            z-index: 1000;
         }
         
         .floating-add-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.6);
         }
         
         .modal-modern .modal-content {
            border-radius: var(--border-radius-lg);
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
         }
         
         .modal-modern .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
            border: none;
         }
         
         .modal-modern .modal-title {
            font-weight: 600;
         }
         
         .form-control-modern {
            border-radius: var(--border-radius);
            border: 2px solid #e2e8f0;
            padding: 0.00rem 1rem;
            transition: all 0.3s ease;
         }
         
         .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
         }
         
         .color-picker {
            width: 50px;
            height: 40px;
            border-radius: var(--border-radius);
            border: 2px solid #e2e8f0;
            cursor: pointer;
         }
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
            <!-- Container fluid  -->
            <!-- ============================================================== -->
            <div class="container-fluid">
               <!-- Header da página -->
               <div class="clients-header animate-fadeInUp">
                  <div class="clients-title">
                     <i class="mdi mdi-domain"></i>
                     Gerenciamento de Clientes
                  </div>
                  <div class="clients-subtitle">
                     Gerencie organizações e seus usuários de forma centralizada
                  </div>
               </div>

               <!-- Filtros e Busca -->
               <div class="row mb-4">
                  <div class="col-md-4">
                     <div class="input-group">
                        <div class="input-group-prepend">
                           <span class="input-group-text">
                              <i class="mdi mdi-magnify"></i>
                           </span>
                        </div>
                        <input type="text" class="form-control form-control-modern" 
                               id="searchClients" placeholder="Buscar clientes...">
                     </div>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control form-control-modern" id="filterStatus">
                        <option value="">Todos os Status</option>
                        <option value="1">Ativos</option>
                        <option value="0">Inativos</option>
                     </select>
                  </div>
                  <div class="col-md-3">
                     <select class="form-control form-control-modern" id="sortBy">
                        <option value="client_name">Nome</option>
                        <option value="created_date">Data de Criação</option>
                        <option value="user_count">Quantidade de Usuários</option>
                     </select>
                  </div>
               </div>

               <!-- Lista de Clientes -->
               <div id="clientsList" class="row">
                  <!-- Cards dos clientes serão carregados aqui via JavaScript -->
               </div>

               <!-- Loading -->
               <div id="loadingClients" class="text-center" style="display: none;">
                  <div class="spinner-border text-primary" role="status">
                     <span class="sr-only">Carregando...</span>
                  </div>
                  <p class="mt-2">Carregando clientes...</p>
               </div>

               <!-- Mensagem vazia -->
               <div id="emptyMessage" class="text-center" style="display: none;">
                  <div class="py-5">
                     <i class="mdi mdi-domain" style="font-size: 4rem; color: #e2e8f0;"></i>
                     <h4 class="text-muted mt-3">Nenhum cliente encontrado</h4>
                     <p class="text-muted">Clique no botão + para adicionar seu primeiro cliente</p>
                  </div>
               </div>
            </div>
         </div>

         <!-- Botão flutuante adicionar -->
         <button class="floating-add-btn" onclick="openClientModal()" title="Adicionar Cliente">
            <i class="mdi mdi-plus"></i>
         </button>

         <!-- Modal de Cliente -->
         <div class="modal fade modal-modern" id="clientModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title" id="clientModalTitle">Adicionar Cliente</h5>
                     <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <form id="clientForm">
                        <input type="hidden" id="clientId" name="client_id">
                        <input type="hidden" id="isUpdate" name="is_update" value="false">
                        
                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="clientName">Nome da Organização *</label>
                                 <input type="text" class="form-control form-control-modern" 
                                        id="clientName" name="client_name" required>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="clientDomain">Domínio</label>
                                 <input type="text" class="form-control form-control-modern" 
                                        id="clientDomain" name="client_domain" 
                                        placeholder="exemplo.com.br">
                              </div>
                           </div>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="contactEmail">Email de Contato</label>
                                 <input type="email" class="form-control form-control-modern" 
                                        id="contactEmail" name="contact_email">
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="contactPhone">Telefone de Contato</label>
                                 <input type="text" class="form-control form-control-modern" 
                                        id="contactPhone" name="contact_phone">
                              </div>
                           </div>
                        </div>

                        <div class="form-group">
                           <label for="address">Endereço</label>
                           <textarea class="form-control form-control-modern" id="address" 
                                   name="address" rows="3"></textarea>
                        </div>

                        <div class="row">
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label>Cores da Marca</label>
                                 <div class="d-flex gap-2">
                                    <div>
                                       <label class="small">Primária</label>
                                       <input type="color" class="color-picker" 
                                              id="primaryColor" value="#667eea">
                                    </div>
                                    <div>
                                       <label class="small">Secundária</label>
                                       <input type="color" class="color-picker" 
                                              id="secondaryColor" value="#764ba2">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <div class="form-group">
                                 <label for="status">Status</label>
                                 <select class="form-control form-control-modern" id="status" name="status">
                                    <option value="1">Ativo</option>
                                    <option value="0">Inativo</option>
                                 </select>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                     <button type="button" class="btn btn-primary" onclick="saveClient()">Salvar</button>
                  </div>
               </div>
            </div>
         </div>

         <!-- Modal de Confirmação de Exclusão -->
         <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h5 class="modal-title">Confirmar Exclusão</h5>
                     <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <p>Tem certeza que deseja excluir este cliente?</p>
                     <p class="text-danger small">
                        <strong>Atenção:</strong> Esta ação irá desativar o cliente e pode afetar campanhas associadas.
                     </p>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                     <button type="button" class="btn btn-danger" onclick="confirmDelete()">Excluir</button>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- JavaScript libraries -->
      <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
      <script src="js/libs/popper.min.js"></script>
      <script src="js/libs/bootstrap.min.js"></script>
      <script src="js/libs/select2.min.js"></script>
      <script src="js/libs/toastr.min.js"></script>
      <script src="js/common_scripts.js"></script>

      <script>
         let currentClients = [];
         let clientToDelete = null;

         $(document).ready(function() {
            loadClients();
            
            // Eventos de filtro e busca
            $('#searchClients').on('keyup', filterClients);
            $('#filterStatus').on('change', filterClients);
            $('#sortBy').on('change', sortClients);
         });

         function loadClients() {
            $('#loadingClients').show();
            $('#clientsList').empty();
            $('#emptyMessage').hide();

            $.post({
               url: "manager/client_manager",
               contentType: 'application/json; charset=utf-8',
               data: JSON.stringify({ action_type: "get_client_list" })
            }).done(function(data) {
               console.log('Dados recebidos do servidor:', data);
               $('#loadingClients').hide();
               
               // Verificar se houve erro
               if (data && data.result === 'error') {
                  console.error('Erro do servidor:', data.message);
                  toastr.error('Erro ao carregar clientes: ' + data.message);
                  $('#emptyMessage').show();
                  return;
               }
               
               if (data && Array.isArray(data) && data.length > 0) {
                  currentClients = data;
                  renderClients(data);
               } else {
                  console.log('Nenhum cliente encontrado');
                  $('#emptyMessage').show();
               }
            }).fail(function(xhr, status, error) {
               console.error('Erro na requisição:', xhr.responseText);
               $('#loadingClients').hide();
               toastr.error('Erro ao carregar clientes: ' + error);
               $('#emptyMessage').show();
            });
         }

         function renderClients(clients) {
            const container = $('#clientsList');
            container.empty();

            clients.forEach(client => {
               const statusClass = client.status == 1 ? 'status-active' : 'status-inactive';
               const statusText = client.status == 1 ? 'Ativo' : 'Inativo';
               const logoLetter = client.client_name.charAt(0).toUpperCase();

               const card = `
                  <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                     <div class="client-card">
                        <div class="client-card-header">
                           <div class="d-flex justify-content-between align-items-start">
                              <div>
                                 <div class="client-logo">${logoLetter}</div>
                                 <h5 class="mb-1">${client.client_name}</h5>
                                 <p class="text-muted small mb-2">${client.client_domain || 'Sem domínio'}</p>
                                 <span class="client-status ${statusClass}">
                                    <i class="mdi mdi-circle-small"></i>
                                    ${statusText}
                                 </span>
                              </div>
                           </div>
                        </div>
                        
                        <div class="client-stats">
                           <div class="stat-item">
                              <div class="stat-number">${client.user_count || 0}</div>
                              <div class="stat-label">Usuários</div>
                           </div>
                           <div class="stat-item">
                              <div class="stat-number">${client.campaign_count || 0}</div>
                              <div class="stat-label">Campanhas</div>
                           </div>
                           <div class="stat-item">
                              <div class="stat-number">0</div>
                              <div class="stat-label">Treinamentos</div>
                           </div>
                        </div>
                        
                        <div class="client-actions">
                           <button class="action-btn btn-primary" onclick="openClientModal('${client.client_id}')" title="Editar">
                              <i class="mdi mdi-pencil"></i>
                           </button>
                           <button class="action-btn btn-warning" onclick="manageUsers('${client.client_id}')" title="Gerenciar Usuários">
                              <i class="mdi mdi-account-group"></i>
                           </button>
                           <button class="action-btn btn-danger" onclick="deleteClient('${client.client_id}')" title="Excluir">
                              <i class="mdi mdi-delete"></i>
                           </button>
                        </div>
                     </div>
                  </div>
               `;
               
               container.append(card);
            });
         }

         function openClientModal(clientId = null) {
            if (clientId) {
               // Modo edição
               $('#clientModalTitle').text('Editar Cliente');
               $('#isUpdate').val('true');
               loadClientDetails(clientId);
            } else {
               // Modo criação
               $('#clientModalTitle').text('Adicionar Cliente');
               $('#isUpdate').val('false');
               $('#clientForm')[0].reset();
               $('#primaryColor').val('#667eea');
               $('#secondaryColor').val('#764ba2');
            }
            
            $('#clientModal').modal('show');
         }

         function loadClientDetails(clientId) {
            $.post({
               url: "manager/client_manager",
               contentType: 'application/json; charset=utf-8',
               data: JSON.stringify({ 
                  action_type: "get_client_details",
                  client_id: clientId 
               })
            }).done(function(data) {
               if (data.client_id) {
                  $('#clientId').val(data.client_id);
                  $('#clientName').val(data.client_name);
                  $('#clientDomain').val(data.client_domain);
                  $('#contactEmail').val(data.contact_email);
                  $('#contactPhone').val(data.contact_phone);
                  $('#address').val(data.address);
                  $('#status').val(data.status);
                  
                  if (data.brand_colors) {
                     $('#primaryColor').val(data.brand_colors.primary || '#667eea');
                     $('#secondaryColor').val(data.brand_colors.secondary || '#764ba2');
                  }
               }
            }).fail(function() {
               toastr.error('Erro ao carregar dados do cliente');
            });
         }

         function saveClient() {
            // Validar campos obrigatórios
            const clientName = $('#clientName').val().trim();
            if (!clientName) {
               toastr.error('Nome da organização é obrigatório');
               return;
            }

            const isUpdate = $('#isUpdate').val() === 'true';
            const clientData = {
               action_type: "save_client",
               is_update: isUpdate ? 'true' : 'false',
               client_name: clientName,
               client_domain: $('#clientDomain').val() || '',
               contact_email: $('#contactEmail').val() || '',
               contact_phone: $('#contactPhone').val() || '',
               address: $('#address').val() || '',
               status: parseInt($('#status').val()) || 1,
               brand_colors: {
                  primary: $('#primaryColor').val() || '#667eea',
                  secondary: $('#secondaryColor').val() || '#764ba2'
               }
            };

            // Se for atualização, incluir o ID do cliente
            if (isUpdate) {
               const clientId = $('#clientId').val();
               if (!clientId) {
                  toastr.error('ID do cliente não encontrado para atualização');
                  return;
               }
               clientData.client_id = clientId;
            }

            console.log('Dados a serem enviados:', clientData);

            $.post({
               url: "manager/client_manager",
               contentType: 'application/json; charset=utf-8',
               data: JSON.stringify(clientData)
            }).done(function(response) {
               console.log('Resposta do servidor:', response);
               if (response.result === 'success') {
                  toastr.success(isUpdate ? 'Cliente atualizado com sucesso!' : 'Cliente criado com sucesso!');
                  $('#clientModal').modal('hide');
                  loadClients();
               } else {
                  toastr.error(response.message || 'Erro ao salvar cliente');
               }
            }).fail(function(xhr, status, error) {
               console.error('Erro na requisição:', xhr.responseText);
               toastr.error('Erro de comunicação com o servidor: ' + error);
            });
         }

         function deleteClient(clientId) {
            clientToDelete = clientId;
            $('#deleteModal').modal('show');
         }

         function confirmDelete() {
            if (clientToDelete) {
               $.post({
                  url: "manager/client_manager",
                  contentType: 'application/json; charset=utf-8',
                  data: JSON.stringify({ 
                     action_type: "delete_client",
                     client_id: clientToDelete 
                  })
               }).done(function(response) {
                  if (response.result === 'success') {
                     toastr.success('Cliente excluído com sucesso!');
                     loadClients();
                  } else {
                     // Mostrar mensagem de erro detalhada
                     const message = response.message || 'Erro ao excluir cliente';
                     toastr.error(message, 'Não foi possível excluir', {
                        timeOut: 8000,
                        extendedTimeOut: 3000
                     });
                  }
                  
                  $('#deleteModal').modal('hide');
                  clientToDelete = null;
               }).fail(function(xhr, status, error) {
                  console.error('Erro na requisição:', xhr.responseText);
                  toastr.error('Erro de comunicação com o servidor: ' + error);
                  $('#deleteModal').modal('hide');
               });
            }
         }

         function manageUsers(clientId) {
            // Redirecionar para página de gerenciamento de usuários
            window.location.href = `ClientUsers?client_id=${clientId}`;
         }

         function filterClients() {
            const searchTerm = $('#searchClients').val().toLowerCase();
            const statusFilter = $('#filterStatus').val();
            
            let filteredClients = currentClients.filter(client => {
               const matchesSearch = client.client_name.toLowerCase().includes(searchTerm) ||
                                   (client.client_domain && client.client_domain.toLowerCase().includes(searchTerm));
               const matchesStatus = statusFilter === '' || client.status == statusFilter;
               
               return matchesSearch && matchesStatus;
            });

            if (filteredClients.length > 0) {
               renderClients(filteredClients);
               $('#emptyMessage').hide();
            } else {
               $('#clientsList').empty();
               $('#emptyMessage').show();
            }
         }

         function sortClients() {
            const sortBy = $('#sortBy').val();
            
            currentClients.sort((a, b) => {
               if (sortBy === 'client_name') {
                  return a.client_name.localeCompare(b.client_name);
               } else if (sortBy === 'created_date') {
                  return new Date(b.created_date) - new Date(a.created_date);
               } else if (sortBy === 'user_count') {
                  return (b.user_count || 0) - (a.user_count || 0);
               }
               return 0;
            });

            filterClients(); // Re-aplicar filtros após ordenar
         }
      </script>
   </body>
</html>
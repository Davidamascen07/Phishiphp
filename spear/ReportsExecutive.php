<?php
session_start();
include_once 'manager/session_manager.php';
include_once 'config/db.php';

// Verificar sessão
if (!isSessionValid()) {
    header("Location: index.php");
    exit;
}

$currentClientId = getCurrentClientId();

// Função para obter nome do cliente com fallback seguro
function getCurrentClientName() {
    global $conn;
    $currentClientId = getCurrentClientId();
    
    if (empty($currentClientId) || $currentClientId === 'default_org') {
        return 'Cliente Padrão';
    }
    
    try {
        // Verificar se a tabela tb_clients existe
        $checkTable = mysqli_query($conn, "SHOW TABLES LIKE 'tb_clients'");
        if (mysqli_num_rows($checkTable) == 0) {
            return 'Cliente ' . $currentClientId;
        }
        
        // Verificar se as colunas existem
        $checkColumns = mysqli_query($conn, "SHOW COLUMNS FROM tb_clients");
        $columns = [];
        while ($row = mysqli_fetch_assoc($checkColumns)) {
            $columns[] = $row['Field'];
        }
        
        // Determinar a coluna de nome e ID
        $nameColumn = 'client_name';
        $idColumn = 'client_id';
        
        if (in_array('name', $columns)) {
            $nameColumn = 'name';
        }
        if (in_array('id', $columns)) {
            $idColumn = 'id';
        }
        
        // Buscar o nome do cliente
        $stmt = $conn->prepare("SELECT `{$nameColumn}` FROM tb_clients WHERE `{$idColumn}` = ? AND status = 1 LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('s', $currentClientId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                return $row[$nameColumn];
            }
        }
    } catch (Exception $e) {
        error_log('Erro ao buscar nome do cliente: ' . $e->getMessage());
    }
    
    return 'Cliente ' . $currentClientId;
}

$currentClientName = getCurrentClientName();
?>
<!DOCTYPE html>
<html dir="ltr" lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Relatórios Executivos - Loophish">
    <title>Relatórios Executivos | Loophish</title>
    
    <!-- CSS Files -->
    <link href="css/loophish-theme-2025.css" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .client-selector-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(79, 172, 254, 0.1);
        }
        
        .client-selector-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .client-selector-label {
            font-weight: 600;
            color: #2c3e50;
            min-width: 120px;
        }
        
        .client-selector {
            flex: 1;
            max-width: 400px;
        }
        
        .select2-container--default .select2-selection--single {
            height: 45px;
            border: 2px solid rgba(79, 172, 254, 0.2);
            border-radius: 10px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 41px;
            padding-left: 15px;
            color: #2c3e50;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 41px;
            right: 10px;
        }
        
        .select2-dropdown {
            border: 2px solid rgba(79, 172, 254, 0.2);
            border-radius: 10px;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }        
        
        .client-selector-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(79, 172, 254, 0.1);
        }
        
        .client-selector-wrapper {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .client-selector-label {
            font-weight: 600;
            color: #2c3e50;
            min-width: 120px;
        }
        
        .client-selector {
            flex: 1;
            max-width: 400px;
        }
        
        .select2-container--default .select2-selection--single {
            height: 45px;
            border: 2px solid rgba(79, 172, 254, 0.2);
            border-radius: 10px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 41px;
            padding-left: 15px;
            color: #2c3e50;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 41px;
            right: 10px;
        }
        
        .select2-dropdown {
            border: 2px solid rgba(79, 172, 254, 0.2);
            border-radius: 10px;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .executive-report-container {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: calc(100vh - 120px);
            padding: 20px;
        }
        
        .report-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(79, 172, 254, 0.1);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .report-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .report-header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 20px 25px;
            border-radius: 20px 20px 0 0;
            position: relative;
            overflow: hidden;
        }
        
        .report-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="40" r="3" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="2" fill="rgba(255,255,255,0.1)"/></svg>');
        }
        
        .report-header h4 {
            margin: 0;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .report-header .report-icon {
            position: absolute;
            right: 25px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
            opacity: 0.7;
        }
        
        .report-content {
            padding: 25px;
        }
        
        .prompt-display {
            background: linear-gradient(135deg, #f8f9fa 0%, #fff 100%);
            border: 2px solid rgba(79, 172, 254, 0.2);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            font-size: 16px;
            line-height: 1.7;
            color: #2c3e50;
            position: relative;
            overflow: hidden;
        }
        
        .prompt-display::before {
            content: '💡';
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 24px;
            opacity: 0.3;
        }
        
        .chart-container {
            position: relative;
            height: 350px;
            margin: 20px 0;
            background: #fafafa;
            border-radius: 15px;
            padding: 15px;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .metric-card {
            background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            border: 1px solid rgba(79, 172, 254, 0.2);
        }
        
        .metric-value {
            font-size: 28px;
            font-weight: 700;
            color: #4facfe;
            margin-bottom: 5px;
        }
        
        .metric-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }
        
        .filters-panel {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid rgba(79, 172, 254, 0.1);
        }
        
        .filter-group {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-item {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-item label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .modern-select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(79, 172, 254, 0.2);
            border-radius: 10px;
            background: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .modern-select:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
        }
        
        .btn-generate {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 24px;
        }
        
        .btn-generate:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 172, 254, 0.3);
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 40px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4facfe;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .risk-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .risk-alto {
            background: #dc3545;
            color: white;
        }
        
        .risk-medio {
            background: #ffc107;
            color: #000;
        }
        
        .risk-baixo {
            background: #28a745;
            color: white;
        }
        
        .critical-users-list,
        .critical-departments-list {
            list-style: none;
            padding: 0;
        }
        
        .critical-users-list li,
        .critical-departments-list li {
            background: #f8f9fa;
            margin: 10px 0;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #dc3545;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .user-info,
        .dept-info {
            flex: 1;
        }
        
        .user-name,
        .dept-name {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .user-details,
        .dept-details {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .recommendations-list {
            list-style: none;
            padding: 0;
        }
        
        .recommendations-list li {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
            margin: 15px 0;
            padding: 20px;
            border-radius: 15px;
            border-left: 5px solid #28a745;
            position: relative;
        }
        
        .recommendations-list li::before {
            content: '✓';
            position: absolute;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }
        
        .no-data-message {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .no-data-message i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .filter-group {
                flex-direction: column;
            }
            
            .filter-item {
                min-width: 100%;
            }
            
            .metrics-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            }
            
            .chart-container {
                height: 250px;
            }
        }
    </style>
</head>

<body>
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full"
        data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">
        
        <!-- Menu Superior e Lateral -->
        <?php include_once 'z_menu.php'; ?>
        
        <!-- Page Wrapper -->
        <div class="page-wrapper">
            <!-- Container Principal -->
            <div class="container-fluid executive-report-container">
                
                <!-- Cabeçalho da Página -->
                <div class="row">
                    <div class="col-12">
                        <div class="card modern-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h3 class="card-title mb-1">
                                            <i class="mdi mdi-chart-line-variant"></i>
                                            Relatórios Executivos
                                        </h3>
                                        <p class="text-muted mb-0">
                                            Sistema de analytics avançado e executivos
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-info">Fase 2 - Analytics</span>
                                        <br>
                                        <small class="text-muted">Dados em tempo real</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seletor de Clientes -->
                <div class="row">
                    <div class="col-12">
                        <div class="client-selector-container">
                            <div class="client-selector-wrapper">
                                <label class="client-selector-label">
                                    <i class="mdi mdi-office-building"></i>
                                    Cliente Ativo:
                                </label>
                                <div class="client-selector">
                                    <select id="clientSelector" class="form-control" style="width: 100%;">
                                        <option value="<?php echo htmlspecialchars($currentClientId); ?>" selected>
                                            <?php echo htmlspecialchars($currentClientName); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="client-actions">
                                    <button id="refreshClients" class="btn btn-outline-primary btn-sm" title="Atualizar lista de clientes">
                                        <i class="mdi mdi-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seletor de Clientes -->
                <div class="row">
                    <div class="col-12">
                        <div class="client-selector-container">
                            <div class="client-selector-wrapper">
                                <label class="client-selector-label">
                                    <i class="mdi mdi-office-building"></i>
                                    Cliente Ativo:
                                </label>
                                <div class="client-selector">
                                    <select id="clientSelector" class="form-control" style="width: 100%;">
                                        <option value="<?php echo htmlspecialchars($currentClientId); ?>" selected>
                                            <?php echo htmlspecialchars($currentClientName); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="client-actions">
                                    <button id="refreshClients" class="btn btn-outline-primary btn-sm" title="Atualizar lista de clientes">
                                        <i class="mdi mdi-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Painel de Filtros -->
                <div class="row">
                    <div class="col-12">
                        <div class="filters-panel">
                            <h5 class="mb-3">
                                <i class="mdi mdi-filter-variant"></i>
                                Filtros e Configurações
                            </h5>
                            <div class="filter-group">
                                <div class="filter-item">
                                    <label for="periodSelect">Período de Análise</label>
                                    <select id="periodSelect" class="modern-select">
                                        <option value="<?php echo date('Y-m'); ?>">Mês Atual</option>
                                        <option value="<?php echo date('Y-m', strtotime('-1 month')); ?>">Mês Anterior</option>
                                        <option value="<?php echo date('Y-m', strtotime('-2 months')); ?>">2 Meses Atrás</option>
                                        <option value="<?php echo date('Y-m', strtotime('-3 months')); ?>">3 Meses Atrás</option>
                                    </select>
                                </div>
                                <div class="filter-item">
                                    <label for="reportTypeSelect">Tipo de Relatório</label>
                                    <select id="reportTypeSelect" class="modern-select">
                                        <option value="all">Todos os Relatórios</option>
                                        <option value="resumo_executivo">Resumo Executivo</option>
                                        <option value="metricas_principais">Métricas Principais</option>
                                        <option value="analise_progresso">Análise de Progresso</option>
                                        <option value="usuarios_criticos">Usuários Críticos</option>
                                        <option value="departamentos_criticos">Departamentos Críticos</option>
                                        <option value="recomendacoes">Recomendações</option>
                                    </select>
                                </div>
                                <div class="filter-item">
                                    <label for="departmentFilter">Departamento (Opcional)</label>
                                    <select id="departmentFilter" class="modern-select">
                                        <option value="">Todos os Departamentos</option>
                                        <option value="TI">Tecnologia da Informação</option>
                                        <option value="RH">Recursos Humanos</option>
                                        <option value="Financeiro">Financeiro</option>
                                        <option value="Vendas">Vendas</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Operacoes">Operações</option>
                                    </select>
                                </div>
                                <div class="filter-item">
                                    <button id="generateReports" class="btn-generate">
                                        <i class="mdi mdi-chart-box"></i>
                                        Gerar Relatórios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Loading Spinner -->
                <div id="loadingSpinner" class="loading-spinner">
                    <div class="spinner"></div>
                    <p>Gerando relatórios executivos...</p>
                </div>
                
                <!-- Container dos Relatórios -->
                <div id="reportsContainer">
                    <!-- Os relatórios serão carregados dinamicamente aqui -->
                </div>
                
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="js/libs/jquery/jquery-3.6.0.min.js"></script>
    <script src="js/libs/bootstrap.min.js"></script>
    <script src="js/libs/perfect-scrollbar.jquery.min.js"></script>
    <script src="js/libs/custom.min.js"></script>
    <script src="js/libs/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- scripo bugado dando logout  -->
    <!-- <script src="js/common_scripts.js"></script> -->

    <script>
        // Executive Reports Manager
        class ExecutiveReportsManager {
            constructor() {
                this.currentClientId = <?php echo json_encode($currentClientId); ?>;
                this.chartInstances = {};
                this.init();
            }

            init() {
                this.initializeSelects();
                this.bindEvents();
                this.loadDefaultReports();
            }

            initializeSelects() {
                $('#periodSelect, #reportTypeSelect, #departmentFilter').select2({
                    minimumResultsForSearch: -1,
                    theme: 'default'
                });
            }

            bindEvents() {
                $('#generateReports').on('click', () => this.generateReports());
                
                // Auto-generate on filter change
                $('#periodSelect, #reportTypeSelect').on('change', () => {
                    if ($('#reportTypeSelect').val() !== 'all') {
                        this.generateReports();
                    }
                });
            }

            async loadDefaultReports() {
                await this.generateReports();
            }

            async generateReports() {
                const period = $('#periodSelect').val();
                const reportType = $('#reportTypeSelect').val();
                const department = $('#departmentFilter').val();

                this.showLoading();

                try {
                    if (reportType === 'all') {
                        await this.generateAllReports(period, department);
                    } else {
                        await this.generateSingleReport(reportType, period, department);
                    }
                } catch (error) {
                    console.error('Erro ao gerar relatórios:', error);
                    toastr.error('Erro ao gerar relatórios: ' + error.message);
                } finally {
                    this.hideLoading();
                }
            }

            async generateAllReports(period, department) {
                const reportTypes = [
                    'resumo_executivo',
                    'metricas_principais', 
                    'analise_progresso',
                    'usuarios_criticos',
                    'departamentos_criticos',
                    'recomendacoes'
                ];

                const container = $('#reportsContainer');
                container.empty();

                for (const type of reportTypes) {
                    try {
                        const data = await this.fetchReportData(type, period, department);
                        const reportHtml = this.generateReportHTML(type, data);
                        container.append(reportHtml);
                        
                        // Initialize chart if data contains chartData
                        if (data.chartData) {
                            setTimeout(() => {
                                this.initializeChart(type, data.chartData);
                            }, 100);
                        }
                    } catch (error) {
                        console.error(`Erro ao gerar relatório ${type}:`, error);
                        const errorHtml = this.generateErrorHTML(type, error.message);
                        container.append(errorHtml);
                    }
                }
            }

            async generateSingleReport(reportType, period, department) {
                const container = $('#reportsContainer');
                container.empty();

                try {
                    const data = await this.fetchReportData(reportType, period, department);
                    const reportHtml = this.generateReportHTML(reportType, data);
                    container.html(reportHtml);
                    
                    if (data.chartData) {
                        setTimeout(() => {
                            this.initializeChart(reportType, data.chartData);
                        }, 100);
                    }
                } catch (error) {
                    const errorHtml = this.generateErrorHTML(reportType, error.message);
                    container.html(errorHtml);
                }
            }

            async fetchReportData(reportType, period, department) {
                const actionMap = {
                    'resumo_executivo': 'getExecutiveSummary',
                    'metricas_principais': 'getMainMetrics',
                    'analise_progresso': 'getProgressAnalysis',
                    'usuarios_criticos': 'getCriticalUsers',
                    'departamentos_criticos': 'getCriticalDepartments',
                    'recomendacoes': 'getRecommendations'
                };

                const action = actionMap[reportType];
                if (!action) {
                    throw new Error('Tipo de relatório não reconhecido');
                }

                const response = await fetch('manager/advanced_analytics_manager_mock.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: action,
                        period: period,
                        department: department,
                        limit: reportType.includes('criticos') ? 5 : undefined
                    })
                });

                if (!response.ok) {
                    throw new Error('Erro na requisição: ' + response.status);
                }

                const result = await response.json();
                
                if (result.result !== 'success') {
                    throw new Error(result.error || 'Erro desconhecido');
                }

                return result.data;
            }

            generateReportHTML(reportType, data) {
                const titles = {
                    'resumo_executivo': { icon: 'chart-pie', title: '📊 Resumo Executivo' },
                    'metricas_principais': { icon: 'chart-bar', title: '📈 Métricas Principais' },
                    'analise_progresso': { icon: 'chart-line', title: '📋 Análise de Progresso' },
                    'usuarios_criticos': { icon: 'account-alert', title: '⚠️ Usuários Críticos' },
                    'departamentos_criticos': { icon: 'office-building', title: '🏢 Departamentos Críticos' },
                    'recomendacoes': { icon: 'lightbulb', title: '💡 Recomendações' }
                };

                const reportInfo = titles[reportType];
                
                let html = `
                    <div class="report-card" id="report-${reportType}">
                        <div class="report-header">
                            <h4>${reportInfo.title}</h4>
                            <i class="mdi mdi-${reportInfo.icon} report-icon"></i>
                        </div>
                        <div class="report-content">
                `;

                // Prompt Display
                if (data.prompt) {
                    html += `
                        <div class="prompt-display">
                            ${data.prompt}
                        </div>
                    `;
                }

                // Chart Container
                if (data.chartData) {
                    html += `
                        <div class="chart-container">
                            <canvas id="chart-${reportType}"></canvas>
                        </div>
                    `;
                }

                // Specific content based on report type
                html += this.generateSpecificContent(reportType, data);

                html += `
                        </div>
                    </div>
                `;

                return html;
            }

            generateSpecificContent(reportType, data) {
                switch (reportType) {
                    case 'resumo_executivo':
                        return this.generateExecutiveSummaryContent(data);
                    case 'metricas_principais':
                        return this.generateMainMetricsContent(data);
                    case 'analise_progresso':
                        return this.generateProgressAnalysisContent(data);
                    case 'usuarios_criticos':
                        return this.generateCriticalUsersContent(data);
                    case 'departamentos_criticos':
                        return this.generateCriticalDepartmentsContent(data);
                    case 'recomendacoes':
                        return this.generateRecommendationsContent(data);
                    default:
                        return '';
                }
            }

            generateExecutiveSummaryContent(data) {
                if (!data.metrics) return '';
                
                return `
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-value">${data.metrics.totalEmails}</div>
                            <div class="metric-label">E-mails Enviados</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.metrics.clickRate}%</div>
                            <div class="metric-label">Taxa de Clique</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">
                                <span class="risk-badge risk-${data.metrics.riskLevel}">${data.metrics.riskLevel.toUpperCase()}</span>
                            </div>
                            <div class="metric-label">Nível de Risco</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.metrics.awarenessScore}%</div>
                            <div class="metric-label">Score de Conscientização</div>
                        </div>
                    </div>
                `;
            }

            generateMainMetricsContent(data) {
                if (!data.metrics) return '';
                
                return `
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-value">${data.metrics.total_emails || 0}</div>
                            <div class="metric-label">E-mails Enviados</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.metrics.open_rate || 0}%</div>
                            <div class="metric-label">Taxa de Abertura</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.metrics.click_rate || 0}%</div>
                            <div class="metric-label">Taxa de Clique</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.metrics.submission_rate || 0}%</div>
                            <div class="metric-label">Taxa de Submissão</div>
                        </div>
                    </div>
                    ${data.trends ? `
                        <div class="alert alert-info mt-3">
                            <i class="mdi mdi-trending-up"></i>
                            <strong>Tendência:</strong> ${data.trends.direction} de ${data.trends.percentage}% comparado ao período anterior
                        </div>
                    ` : ''}
                `;
            }

            generateProgressAnalysisContent(data) {
                if (!data.currentMetrics) return '';
                
                return `
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-value">${data.currentMetrics.open_rate || 0}%</div>
                            <div class="metric-label">Abertura Atual</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.currentMetrics.click_rate || 0}%</div>
                            <div class="metric-label">Cliques Atuais</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">
                                <span class="risk-badge risk-${data.riskLevel}">${data.riskLevel.toUpperCase()}</span>
                            </div>
                            <div class="metric-label">Risco Atual</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.improvementTrend}</div>
                            <div class="metric-label">Tendência</div>
                        </div>
                    </div>
                `;
            }

            generateCriticalUsersContent(data) {
                if (!data.criticalUsers || data.criticalUsers.length === 0) {
                    return `
                        <div class="no-data-message">
                            <i class="mdi mdi-account-check"></i>
                            <h5>Nenhum usuário crítico identificado</h5>
                            <p>Excelente! Todos os usuários estão dentro dos parâmetros aceitáveis de segurança.</p>
                        </div>
                    `;
                }

                let html = '<ul class="critical-users-list">';
                data.criticalUsers.forEach(user => {
                    html += `
                        <li>
                            <div class="user-info">
                                <div class="user-name">${user.name}</div>
                                <div class="user-details">${user.department} • ${user.email}</div>
                            </div>
                            <div class="risk-badge risk-${user.riskLevel.toLowerCase()}">${user.riskLevel}</div>
                        </li>
                    `;
                });
                html += '</ul>';
                
                return html;
            }

            generateCriticalDepartmentsContent(data) {
                if (!data.criticalDepartments || data.criticalDepartments.length === 0) {
                    return `
                        <div class="no-data-message">
                            <i class="mdi mdi-office-building-check"></i>
                            <h5>Nenhum departamento crítico identificado</h5>
                            <p>Todos os departamentos estão performando adequadamente em segurança.</p>
                        </div>
                    `;
                }

                let html = '<ul class="critical-departments-list">';
                data.criticalDepartments.forEach(dept => {
                    html += `
                        <li>
                            <div class="dept-info">
                                <div class="dept-name">${dept.name}</div>
                                <div class="dept-details">${dept.totalUsers} usuários • ${dept.vulnerabilityPercent}% vulnerabilidade</div>
                            </div>
                            <div class="risk-badge risk-${dept.riskClassification.toLowerCase()}">${dept.riskClassification}</div>
                        </li>
                    `;
                });
                html += '</ul>';
                
                return html;
            }

            generateRecommendationsContent(data) {
                if (!data.recommendations || data.recommendations.length === 0) {
                    return `
                        <div class="no-data-message">
                            <i class="mdi mdi-check-all"></i>
                            <h5>Sistema otimizado</h5>
                            <p>Não há recomendações pendentes no momento. Continue o excelente trabalho!</p>
                        </div>
                    `;
                }

                let html = '<ul class="recommendations-list">';
                data.recommendations.forEach(rec => {
                    html += `
                        <li>
                            <div>
                                <strong>${this.formatRecommendationType(rec.type)}:</strong>
                                ${rec.text}
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">
                                    Prioridade: <span class="badge badge-${this.getPriorityBadgeClass(rec.priority)}">${rec.priority}</span>
                                    ${rec.expectedImprovement ? ` • Impacto: ${rec.expectedImprovement}` : ''}
                                </small>
                            </div>
                        </li>
                    `;
                });
                html += '</ul>';
                
                return html;
            }

            formatRecommendationType(type) {
                const types = {
                    'treinamento_recorrente': 'Treinamento Recorrente',
                    'reforco_politicas': 'Reforço de Políticas',
                    'aumento_simulacoes': 'Aumento de Simulações',
                    'canal_reporte': 'Canal de Reporte',
                    'segmentacao_usuarios': 'Segmentação de Usuários'
                };
                return types[type] || type;
            }

            getPriorityBadgeClass(priority) {
                const classes = {
                    'critica': 'danger',
                    'alta': 'warning',
                    'media': 'info',
                    'baixa': 'secondary'
                };
                return classes[priority] || 'secondary';
            }

            generateErrorHTML(reportType, errorMessage) {
                const titles = {
                    'resumo_executivo': '📊 Resumo Executivo',
                    'metricas_principais': '📈 Métricas Principais',
                    'analise_progresso': '📋 Análise de Progresso',
                    'usuarios_criticos': '⚠️ Usuários Críticos',
                    'departamentos_criticos': '🏢 Departamentos Críticos',
                    'recomendacoes': '💡 Recomendações'
                };

                return `
                    <div class="report-card">
                        <div class="report-header">
                            <h4>${titles[reportType]}</h4>
                        </div>
                        <div class="report-content">
                            <div class="alert alert-warning">
                                <i class="mdi mdi-alert-circle"></i>
                                <strong>Erro ao gerar relatório:</strong> ${errorMessage}
                            </div>
                        </div>
                    </div>
                `;
            }

            initializeChart(reportType, chartData) {
                const ctx = document.getElementById(`chart-${reportType}`);
                if (!ctx) return;

                // Destroy existing chart if exists
                if (this.chartInstances[reportType]) {
                    this.chartInstances[reportType].destroy();
                }

                // Chart.js configuration
                const config = {
                    type: chartData.type === 'horizontalBar' ? 'bar' : chartData.type,
                    data: {
                        labels: chartData.labels,
                        datasets: chartData.datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: chartData.type === 'horizontalBar' ? 'y' : 'x',
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                };

                // Additional configurations based on chart type
                if (chartData.type === 'line') {
                    config.options.scales = {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    };
                }

                this.chartInstances[reportType] = new Chart(ctx, config);
            }

            showLoading() {
                $('#loadingSpinner').show();
                $('#reportsContainer').hide();
            }

            hideLoading() {
                $('#loadingSpinner').hide();
                $('#reportsContainer').show();
            }
        }

        // Initialize when document is ready
        $(document).ready(function() {
            window.executiveReportsManager = new ExecutiveReportsManager();
        });
    </script>

    <!-- Client Selector Script -->
    <script src="js/client_selector.js"></script>
</body>
</html>
/**
 * ReportsExecutive JavaScript Functions
 * Gerenciamento de relatórios executivos com Chart.js e analytics avançados
 * LooPhish - Fase 2 Implementation
 */

// Executive Reports Manager Class
class AdvancedExecutiveReports {
    constructor() {
        this.charts = {};
        this.currentFilters = {
            period: new Date().toISOString().slice(0, 7),
            department: '',
            reportType: 'all'
        };
        this.apiEndpoint = 'manager/advanced_analytics_manager.php';
        this.chartColors = {
            primary: '#4facfe',
            secondary: '#00f2fe',
            success: '#28a745',
            warning: '#ffc107',
            danger: '#dc3545',
            info: '#17a2b8',
            light: '#f8f9fa',
            dark: '#343a40'
        };
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeFilters();
        this.setupChartDefaults();
    }

    bindEvents() {
        // Filter change events
        $('#periodSelect').on('change', (e) => {
            this.currentFilters.period = e.target.value;
            this.onFilterChange();
        });

        $('#reportTypeSelect').on('change', (e) => {
            this.currentFilters.reportType = e.target.value;
            this.onFilterChange();
        });

        $('#departmentFilter').on('change', (e) => {
            this.currentFilters.department = e.target.value;
            this.onFilterChange();
        });

        // Generate button
        $('#generateReports').on('click', () => {
            this.generateAllReports();
        });

        // Export functionality
        $(document).on('click', '.export-btn', (e) => {
            const format = $(e.target).data('format');
            const reportType = $(e.target).data('report-type');
            this.exportReport(reportType, format);
        });

        // Refresh individual reports
        $(document).on('click', '.refresh-report-btn', (e) => {
            const reportType = $(e.target).data('report-type');
            this.refreshSingleReport(reportType);
        });
    }

    initializeFilters() {
        // Initialize Select2 dropdowns
        $('#periodSelect, #reportTypeSelect, #departmentFilter').select2({
            minimumResultsForSearch: -1,
            theme: 'default'
        });

        // Set default period (current month)
        const currentMonth = new Date().toISOString().slice(0, 7);
        $('#periodSelect').val(currentMonth).trigger('change');
    }

    setupChartDefaults() {
        // Global Chart.js defaults
        Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#6c757d';
        Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
        Chart.defaults.plugins.tooltip.cornerRadius = 8;
        Chart.defaults.plugins.legend.labels.usePointStyle = true;
    }

    onFilterChange() {
        // Auto-refresh if specific report type is selected
        if (this.currentFilters.reportType !== 'all') {
            this.debounce(() => {
                this.generateSingleReport(this.currentFilters.reportType);
            }, 300);
        }
    }

    async generateAllReports() {
        this.showLoading();
        
        try {
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

            // Generate reports sequentially with progress indication
            for (let i = 0; i < reportTypes.length; i++) {
                const reportType = reportTypes[i];
                this.updateLoadingProgress((i / reportTypes.length) * 100, `Gerando ${this.getReportTitle(reportType)}...`);
                
                try {
                    await this.generateSingleReportCard(reportType);
                } catch (error) {
                    console.error(`Erro ao gerar ${reportType}:`, error);
                    this.appendErrorCard(reportType, error.message);
                }
            }

            this.hideLoading();
            this.showSuccessMessage('Relatórios gerados com sucesso!');

        } catch (error) {
            console.error('Erro geral:', error);
            this.hideLoading();
            this.showErrorMessage('Erro ao gerar relatórios: ' + error.message);
        }
    }

    async generateSingleReport(reportType) {
        this.showLoading();
        
        try {
            const container = $('#reportsContainer');
            container.empty();
            
            await this.generateSingleReportCard(reportType);
            
            this.hideLoading();
            
        } catch (error) {
            console.error(`Erro ao gerar ${reportType}:`, error);
            this.hideLoading();
            this.showErrorMessage('Erro ao gerar relatório: ' + error.message);
        }
    }

    async generateSingleReportCard(reportType) {
        try {
            const data = await this.fetchReportData(reportType);
            const reportHtml = this.buildReportHTML(reportType, data);
            
            $('#reportsContainer').append(reportHtml);
            
            // Initialize chart if data exists
            if (data.chartData) {
                await this.initializeChart(reportType, data.chartData);
            }
            
            // Add report-specific functionality
            this.initializeReportSpecificFeatures(reportType, data);
            
        } catch (error) {
            throw new Error(`Falha ao gerar ${reportType}: ${error.message}`);
        }
    }

    async fetchReportData(reportType) {
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
            throw new Error('Tipo de relatório não reconhecido: ' + reportType);
        }

        const requestData = {
            action: action,
            period: this.currentFilters.period,
            department: this.currentFilters.department
        };

        // Add specific parameters for certain report types
        if (reportType.includes('criticos')) {
            requestData.limit = 10;
        }

        const response = await fetch(this.apiEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        const result = await response.json();
        
        if (result.result !== 'success') {
            throw new Error(result.error || 'Erro desconhecido na API');
        }

        return result.data;
    }

    buildReportHTML(reportType, data) {
        const reportInfo = this.getReportInfo(reportType);
        
        return `
            <div class="report-card animate-fadeInUp" id="report-${reportType}">
                <div class="report-header">
                    <h4>${reportInfo.title}</h4>
                    <div class="report-actions">
                        <button class="btn btn-sm btn-outline-light refresh-report-btn" data-report-type="${reportType}" title="Atualizar">
                            <i class="mdi mdi-refresh"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-light export-btn" data-report-type="${reportType}" data-format="pdf" title="Exportar PDF">
                            <i class="mdi mdi-file-pdf"></i>
                        </button>
                    </div>
                    <i class="mdi mdi-${reportInfo.icon} report-icon"></i>
                </div>
                <div class="report-content">
                    ${this.buildPromptSection(data.prompt)}
                    ${this.buildChartSection(reportType, data.chartData)}
                    ${this.buildContentSection(reportType, data)}
                    ${this.buildInsightsSection(data.insights)}
                </div>
            </div>
        `;
    }

    buildPromptSection(prompt) {
        if (!prompt) return '';
        
        return `
            <div class="prompt-display">
                <div class="prompt-content">${prompt}</div>
                <div class="prompt-actions">
                    <button class="btn btn-sm btn-outline-primary copy-prompt-btn" title="Copiar Prompt">
                        <i class="mdi mdi-content-copy"></i> Copiar
                    </button>
                </div>
            </div>
        `;
    }

    buildChartSection(reportType, chartData) {
        if (!chartData) return '';
        
        return `
            <div class="chart-container">
                <canvas id="chart-${reportType}" width="400" height="200"></canvas>
            </div>
        `;
    }

    buildContentSection(reportType, data) {
        switch (reportType) {
            case 'resumo_executivo':
                return this.buildExecutiveSummaryContent(data);
            case 'metricas_principais':
                return this.buildMainMetricsContent(data);
            case 'analise_progresso':
                return this.buildProgressAnalysisContent(data);
            case 'usuarios_criticos':
                return this.buildCriticalUsersContent(data);
            case 'departamentos_criticos':
                return this.buildCriticalDepartmentsContent(data);
            case 'recomendacoes':
                return this.buildRecommendationsContent(data);
            default:
                return '';
        }
    }

    buildInsightsSection(insights) {
        if (!insights || insights.length === 0) return '';
        
        return `
            <div class="insights-section mt-4">
                <h6><i class="mdi mdi-lightbulb-outline"></i> Insights Principais</h6>
                <ul class="insights-list">
                    ${insights.map(insight => `<li>${insight}</li>`).join('')}
                </ul>
            </div>
        `;
    }

    buildExecutiveSummaryContent(data) {
        if (!data.metrics) return this.buildNoDataMessage();
        
        const metrics = data.metrics;
        return `
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">${this.formatNumber(metrics.totalEmails || 0)}</div>
                    <div class="metric-label">E-mails Enviados</div>
                    <div class="metric-trend ${metrics.emailTrend >= 0 ? 'positive' : 'negative'}">
                        <i class="mdi mdi-trending-${metrics.emailTrend >= 0 ? 'up' : 'down'}"></i>
                        ${Math.abs(metrics.emailTrend || 0)}%
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">${metrics.clickRate || 0}%</div>
                    <div class="metric-label">Taxa de Clique</div>
                    <div class="metric-trend ${metrics.clickTrend >= 0 ? 'negative' : 'positive'}">
                        <i class="mdi mdi-trending-${metrics.clickTrend >= 0 ? 'up' : 'down'}"></i>
                        ${Math.abs(metrics.clickTrend || 0)}%
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">
                        <span class="risk-badge risk-${metrics.riskLevel || 'medio'}">${(metrics.riskLevel || 'medio').toUpperCase()}</span>
                    </div>
                    <div class="metric-label">Nível de Risco</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">${metrics.awarenessScore || 0}%</div>
                    <div class="metric-label">Score de Conscientização</div>
                    <div class="metric-trend ${metrics.awarenessTrend >= 0 ? 'positive' : 'negative'}">
                        <i class="mdi mdi-trending-${metrics.awarenessTrend >= 0 ? 'up' : 'down'}"></i>
                        ${Math.abs(metrics.awarenessTrend || 0)}%
                    </div>
                </div>
            </div>
        `;
    }

    buildMainMetricsContent(data) {
        if (!data.metrics) return this.buildNoDataMessage();
        
        const metrics = data.metrics;
        return `
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-value">${this.formatNumber(metrics.total_emails || 0)}</div>
                    <div class="metric-label">Total de E-mails</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">${metrics.open_rate || 0}%</div>
                    <div class="metric-label">Taxa de Abertura</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">${metrics.click_rate || 0}%</div>
                    <div class="metric-label">Taxa de Clique</div>
                </div>
                <div class="metric-card">
                    <div class="metric-value">${metrics.submission_rate || 0}%</div>
                    <div class="metric-label">Taxa de Submissão</div>
                </div>
            </div>
            ${data.trends ? this.buildTrendsSection(data.trends) : ''}
        `;
    }

    buildProgressAnalysisContent(data) {
        if (!data.currentMetrics) return this.buildNoDataMessage();
        
        return `
            <div class="progress-analysis">
                <div class="current-state">
                    <h6>Estado Atual</h6>
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <div class="metric-value">${data.currentMetrics.open_rate || 0}%</div>
                            <div class="metric-label">Taxa de Abertura</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">${data.currentMetrics.click_rate || 0}%</div>
                            <div class="metric-label">Taxa de Clique</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">
                                <span class="risk-badge risk-${data.riskLevel || 'medio'}">${(data.riskLevel || 'MEDIO').toUpperCase()}</span>
                            </div>
                            <div class="metric-label">Nível de Risco</div>
                        </div>
                        <div class="metric-card">
                            <div class="metric-value">
                                <i class="mdi mdi-trending-${data.improvementTrend === 'Melhorando' ? 'up text-success' : data.improvementTrend === 'Piorando' ? 'down text-danger' : 'neutral text-warning'}"></i>
                                ${data.improvementTrend || 'Estável'}
                            </div>
                            <div class="metric-label">Tendência</div>
                        </div>
                    </div>
                </div>
                ${data.historicalComparison ? this.buildHistoricalComparison(data.historicalComparison) : ''}
            </div>
        `;
    }

    buildCriticalUsersContent(data) {
        if (!data.criticalUsers || data.criticalUsers.length === 0) {
            return this.buildNoDataMessage('Excelente! Nenhum usuário crítico identificado.', 'account-check');
        }

        return `
            <div class="critical-users-section">
                <div class="critical-users-summary mb-3">
                    <div class="alert alert-warning">
                        <i class="mdi mdi-alert-triangle"></i>
                        <strong>${data.criticalUsers.length}</strong> usuário(s) crítico(s) identificado(s) que requerem atenção imediata.
                    </div>
                </div>
                <div class="critical-users-list">
                    ${data.criticalUsers.map(user => this.buildCriticalUserCard(user)).join('')}
                </div>
            </div>
        `;
    }

    buildCriticalUserCard(user) {
        return `
            <div class="critical-user-card">
                <div class="user-avatar">
                    <i class="mdi mdi-account-circle"></i>
                </div>
                <div class="user-info">
                    <div class="user-name">${user.name}</div>
                    <div class="user-details">
                        <span class="department">${user.department}</span> • 
                        <span class="email">${user.email}</span>
                    </div>
                    <div class="user-stats">
                        <span class="stat">Cliques: ${user.clickCount || 0}</span>
                        <span class="stat">Submissões: ${user.submissionCount || 0}</span>
                    </div>
                </div>
                <div class="user-risk">
                    <span class="risk-badge risk-${user.riskLevel.toLowerCase()}">${user.riskLevel}</span>
                    <div class="risk-score">${user.riskScore || 0}%</div>
                </div>
            </div>
        `;
    }

    buildCriticalDepartmentsContent(data) {
        if (!data.criticalDepartments || data.criticalDepartments.length === 0) {
            return this.buildNoDataMessage('Todos os departamentos estão performando adequadamente.', 'office-building-check');
        }

        return `
            <div class="critical-departments-section">
                <div class="departments-summary mb-3">
                    <div class="alert alert-danger">
                        <i class="mdi mdi-office-building-remove"></i>
                        <strong>${data.criticalDepartments.length}</strong> departamento(s) com vulnerabilidades críticas.
                    </div>
                </div>
                <div class="critical-departments-list">
                    ${data.criticalDepartments.map(dept => this.buildCriticalDepartmentCard(dept)).join('')}
                </div>
            </div>
        `;
    }

    buildCriticalDepartmentCard(dept) {
        return `
            <div class="critical-department-card">
                <div class="dept-icon">
                    <i class="mdi mdi-office-building"></i>
                </div>
                <div class="dept-info">
                    <div class="dept-name">${dept.name}</div>
                    <div class="dept-stats">
                        <div class="stat-item">
                            <span class="stat-label">Usuários:</span>
                            <span class="stat-value">${dept.totalUsers}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Vulnerabilidade:</span>
                            <span class="stat-value">${dept.vulnerabilityPercent}%</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Taxa de Cliques:</span>
                            <span class="stat-value">${dept.clickRate}%</span>
                        </div>
                    </div>
                </div>
                <div class="dept-risk">
                    <span class="risk-badge risk-${dept.riskClassification.toLowerCase()}">${dept.riskClassification}</span>
                </div>
            </div>
        `;
    }

    buildRecommendationsContent(data) {
        if (!data.recommendations || data.recommendations.length === 0) {
            return this.buildNoDataMessage('Sistema otimizado! Não há recomendações pendentes.', 'check-all');
        }

        return `
            <div class="recommendations-section">
                <div class="recommendations-summary mb-3">
                    <div class="alert alert-info">
                        <i class="mdi mdi-lightbulb-outline"></i>
                        <strong>${data.recommendations.length}</strong> recomendação(ões) para melhorar a segurança.
                    </div>
                </div>
                <div class="recommendations-list">
                    ${data.recommendations.map((rec, index) => this.buildRecommendationCard(rec, index)).join('')}
                </div>
            </div>
        `;
    }

    buildRecommendationCard(recommendation, index) {
        const priorityColors = {
            'critica': 'danger',
            'alta': 'warning',
            'media': 'info',
            'baixa': 'secondary'
        };

        return `
            <div class="recommendation-card priority-${recommendation.priority}">
                <div class="recommendation-header">
                    <div class="recommendation-number">${index + 1}</div>
                    <div class="recommendation-priority">
                        <span class="badge badge-${priorityColors[recommendation.priority] || 'secondary'}">
                            ${recommendation.priority.toUpperCase()}
                        </span>
                    </div>
                </div>
                <div class="recommendation-content">
                    <h6 class="recommendation-title">${this.formatRecommendationType(recommendation.type)}</h6>
                    <p class="recommendation-text">${recommendation.text}</p>
                    ${recommendation.expectedImprovement ? `
                        <div class="expected-improvement">
                            <i class="mdi mdi-trending-up text-success"></i>
                            Impacto esperado: ${recommendation.expectedImprovement}
                        </div>
                    ` : ''}
                </div>
                <div class="recommendation-actions">
                    <button class="btn btn-sm btn-outline-primary implement-btn" data-rec-id="${recommendation.id || index}">
                        <i class="mdi mdi-check"></i> Implementar
                    </button>
                </div>
            </div>
        `;
    }

    async initializeChart(reportType, chartData) {
        // Wait for DOM element to be ready
        await this.waitForElement(`#chart-${reportType}`);
        
        const ctx = document.getElementById(`chart-${reportType}`);
        if (!ctx) {
            console.warn(`Canvas element chart-${reportType} not found`);
            return;
        }

        // Destroy existing chart
        if (this.charts[reportType]) {
            this.charts[reportType].destroy();
        }

        // Configure chart based on type
        const config = this.getChartConfig(reportType, chartData);
        
        try {
            this.charts[reportType] = new Chart(ctx, config);
        } catch (error) {
            console.error(`Erro ao criar gráfico ${reportType}:`, error);
        }
    }

    getChartConfig(reportType, chartData) {
        const baseConfig = {
            type: chartData.type || 'doughnut',
            data: {
                labels: chartData.labels || [],
                datasets: this.formatDatasets(chartData.datasets || [], chartData.type)
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: (context) => {
                                return this.formatTooltipLabel(context, chartData.type);
                            }
                        }
                    }
                }
            }
        };

        // Add specific configurations
        switch (chartData.type) {
            case 'line':
                baseConfig.options.scales = {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: (value) => value + '%'
                        }
                    }
                };
                break;
            case 'bar':
                baseConfig.options.scales = {
                    y: {
                        beginAtZero: true
                    }
                };
                break;
        }

        return baseConfig;
    }

    formatDatasets(datasets, chartType) {
        return datasets.map((dataset, index) => {
            const formattedDataset = { ...dataset };
            
            // Add colors if not provided
            if (!formattedDataset.backgroundColor) {
                if (chartType === 'doughnut' || chartType === 'pie') {
                    formattedDataset.backgroundColor = this.getColorPalette(dataset.data.length);
                } else {
                    formattedDataset.backgroundColor = this.chartColors.primary;
                    formattedDataset.borderColor = this.chartColors.primary;
                }
            }

            // Line chart specific styling
            if (chartType === 'line') {
                formattedDataset.fill = false;
                formattedDataset.tension = 0.4;
                formattedDataset.pointBackgroundColor = formattedDataset.borderColor;
                formattedDataset.pointBorderColor = '#fff';
                formattedDataset.pointBorderWidth = 2;
                formattedDataset.pointRadius = 4;
            }

            return formattedDataset;
        });
    }

    getColorPalette(count) {
        const colors = [
            this.chartColors.primary,
            this.chartColors.secondary,
            this.chartColors.success,
            this.chartColors.warning,
            this.chartColors.danger,
            this.chartColors.info,
            '#6f42c1', // Purple
            '#fd7e14', // Orange
            '#20c997', // Teal
            '#e83e8c'  // Pink
        ];

        return colors.slice(0, count);
    }

    formatTooltipLabel(context, chartType) {
        const label = context.dataset.label || '';
        const value = context.parsed;
        
        switch (chartType) {
            case 'doughnut':
            case 'pie':
                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                const percentage = ((value / total) * 100).toFixed(1);
                return `${label}: ${value} (${percentage}%)`;
            case 'line':
                return `${label}: ${value}%`;
            default:
                return `${label}: ${value}`;
        }
    }

    initializeReportSpecificFeatures(reportType, data) {
        // Copy prompt functionality
        $(document).off('click', '.copy-prompt-btn').on('click', '.copy-prompt-btn', (e) => {
            const promptText = $(e.target).closest('.prompt-display').find('.prompt-content').text();
            this.copyToClipboard(promptText);
        });

        // Implement recommendation functionality
        $(document).off('click', '.implement-btn').on('click', '.implement-btn', (e) => {
            const recId = $(e.target).data('rec-id');
            this.implementRecommendation(recId);
        });
    }

    // Utility methods
    getReportInfo(reportType) {
        const reportInfoMap = {
            'resumo_executivo': { icon: 'chart-pie', title: '📊 Resumo Executivo' },
            'metricas_principais': { icon: 'chart-bar', title: '📈 Métricas Principais' },
            'analise_progresso': { icon: 'chart-line', title: '📋 Análise de Progresso' },
            'usuarios_criticos': { icon: 'account-alert', title: '⚠️ Usuários Críticos' },
            'departamentos_criticos': { icon: 'office-building', title: '🏢 Departamentos Críticos' },
            'recomendacoes': { icon: 'lightbulb', title: '💡 Recomendações' }
        };

        return reportInfoMap[reportType] || { icon: 'chart', title: 'Relatório' };
    }

    getReportTitle(reportType) {
        return this.getReportInfo(reportType).title;
    }

    formatRecommendationType(type) {
        const types = {
            'treinamento_recorrente': 'Treinamento Recorrente',
            'reforco_politicas': 'Reforço de Políticas',
            'aumento_simulacoes': 'Aumento de Simulações',
            'canal_reporte': 'Canal de Reporte',
            'segmentacao_usuarios': 'Segmentação de Usuários'
        };
        return types[type] || type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        } else if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toString();
    }

    buildNoDataMessage(message = 'Nenhum dado disponível para o período selecionado.', icon = 'chart-box-outline') {
        return `
            <div class="no-data-message">
                <i class="mdi mdi-${icon}"></i>
                <h5>Sem Dados</h5>
                <p>${message}</p>
            </div>
        `;
    }

    buildTrendsSection(trends) {
        if (!trends) return '';
        
        return `
            <div class="trends-section mt-3">
                <div class="alert alert-${trends.direction === 'up' ? 'success' : trends.direction === 'down' ? 'warning' : 'info'}">
                    <i class="mdi mdi-trending-${trends.direction === 'up' ? 'up' : trends.direction === 'down' ? 'down' : 'neutral'}"></i>
                    <strong>Tendência:</strong> ${trends.description || `${trends.direction} de ${trends.percentage}% comparado ao período anterior`}
                </div>
            </div>
        `;
    }

    buildHistoricalComparison(comparison) {
        return `
            <div class="historical-comparison mt-3">
                <h6>Comparação Histórica</h6>
                <div class="comparison-grid">
                    ${Object.entries(comparison).map(([key, value]) => `
                        <div class="comparison-item">
                            <div class="comparison-label">${key}</div>
                            <div class="comparison-value">${value}</div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    }

    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            this.showSuccessMessage('Prompt copiado para a área de transferência!');
        } catch (error) {
            console.error('Erro ao copiar:', error);
            this.showErrorMessage('Erro ao copiar prompt');
        }
    }

    async implementRecommendation(recId) {
        // Implementation placeholder
        this.showInfoMessage('Funcionalidade de implementação será adicionada na próxima versão.');
    }

    async refreshSingleReport(reportType) {
        try {
            $(`#report-${reportType}`).addClass('refreshing');
            
            // Add refresh animation
            const reportCard = $(`#report-${reportType}`);
            reportCard.css('opacity', '0.6');
            
            await this.generateSingleReportCard(reportType);
            
            reportCard.css('opacity', '1').removeClass('refreshing');
            this.showSuccessMessage('Relatório atualizado com sucesso!');
            
        } catch (error) {
            console.error(`Erro ao atualizar ${reportType}:`, error);
            this.showErrorMessage('Erro ao atualizar relatório: ' + error.message);
        }
    }

    exportReport(reportType, format) {
        // Export functionality placeholder
        this.showInfoMessage(`Exportação em ${format.toUpperCase()} será implementada na próxima versão.`);
    }

    // Loading and message methods
    showLoading() {
        $('#loadingSpinner').show();
        $('#reportsContainer').hide();
    }

    hideLoading() {
        $('#loadingSpinner').hide();
        $('#reportsContainer').show();
    }

    updateLoadingProgress(percentage, message) {
        // Update loading progress if progress bar exists
        const progressBar = $('#loadingProgress');
        if (progressBar.length) {
            progressBar.css('width', percentage + '%');
        }
        
        const loadingMessage = $('#loadingSpinner p');
        if (loadingMessage.length && message) {
            loadingMessage.text(message);
        }
    }

    showSuccessMessage(message) {
        if (typeof toastr !== 'undefined') {
            toastr.success(message);
        } else {
            console.log('Success:', message);
        }
    }

    showErrorMessage(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            console.error('Error:', message);
        }
    }

    showInfoMessage(message) {
        if (typeof toastr !== 'undefined') {
            toastr.info(message);
        } else {
            console.log('Info:', message);
        }
    }

    // Utility functions
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    async waitForElement(selector, timeout = 5000) {
        return new Promise((resolve, reject) => {
            const element = document.querySelector(selector);
            if (element) {
                resolve(element);
                return;
            }

            const observer = new MutationObserver(() => {
                const element = document.querySelector(selector);
                if (element) {
                    observer.disconnect();
                    resolve(element);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });

            setTimeout(() => {
                observer.disconnect();
                reject(new Error(`Element ${selector} not found within ${timeout}ms`));
            }, timeout);
        });
    }
}

// Initialize when document is ready
$(document).ready(function() {
    window.advancedExecutiveReports = new AdvancedExecutiveReports();
});

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AdvancedExecutiveReports;
}
# JavaScript para Atualização dos Gráficos - Dashboard Moderno

## 📊 Configuração dos Gráficos ApexCharts

### 🔧 Função para Atualizar Métricas dos Cards

```javascript
// Função para atualizar as métricas dos cards modernos
function updateMetricCards(data) {
    // Métricas de Email
    if (data.email) {
        // Total de emails enviados
        $('#metric_emails_sent').text(data.email.sent || 0);
        $('#progress_emails_sent').css('width', '100%');
        $('#percent_emails_sent').text('100%');
        
        // Emails abertos
        const openRate = data.email.sent > 0 ? Math.round((data.email.opened / data.email.sent) * 100) : 0;
        $('#metric_emails_opened').text(data.email.opened || 0);
        $('#progress_emails_opened').css('width', openRate + '%');
        $('#percent_emails_opened').text(openRate + '%');
        
        // Taxa de abertura
        $('#metric_open_rate').text(openRate + '%');
        $('#progress_open_rate').css('width', openRate + '%');
        $('#percent_open_rate_display').text(openRate + '%');
        
        // Respostas
        const replyRate = data.email.sent > 0 ? Math.round((data.email.replied / data.email.sent) * 100) : 0;
        $('#metric_emails_replied').text(data.email.replied || 0);
        $('#progress_emails_replied').css('width', replyRate + '%');
        $('#percent_emails_replied').text(replyRate + '%');
        
        // Taxa de resposta (se existir)
        if ($('#metric_reply_rate').length) {
            $('#metric_reply_rate').text(replyRate + '%');
            $('#progress_reply_rate').css('width', replyRate + '%');
            $('#percent_reply_rate_display').text(replyRate + '%');
        }
    }
    
    // Métricas Web (apenas para WebMailCmpDashboard)
    if (data.web) {
        // Visitas à página
        $('#metric_page_visits').text(data.web.visits || 0);
        const visitRate = data.email && data.email.sent > 0 ? Math.round((data.web.visits / data.email.sent) * 100) : 0;
        $('#progress_page_visits').css('width', visitRate + '%');
        $('#percent_page_visits').text(visitRate + '%');
        
        // Envios de formulário
        $('#metric_form_submissions').text(data.web.submissions || 0);
        const submissionRate = data.web.visits > 0 ? Math.round((data.web.submissions / data.web.visits) * 100) : 0;
        $('#progress_form_submissions').css('width', submissionRate + '%');
        $('#percent_form_submissions').text(submissionRate + '%');
        
        // Entradas suspeitas
        $('#metric_suspicious_entries').text(data.web.suspicious || 0);
        const suspiciousRate = data.web.visits > 0 ? Math.round((data.web.suspicious / data.web.visits) * 100) : 0;
        $('#progress_suspicious_entries').css('width', suspiciousRate + '%');
        $('#percent_suspicious_entries').text(suspiciousRate + '%');
        
        // Taxa de conversão
        $('#metric_conversion_rate').text(submissionRate + '%');
        $('#progress_conversion_rate').css('width', submissionRate + '%');
        $('#percent_conversion_rate_display').text(submissionRate + '%');
    }
}

// Configuração do gráfico radial para visão geral de emails
function createEmailOverviewChart(data) {
    const options = {
        series: [data.openRate || 0],
        chart: {
            height: 320,
            type: 'radialBar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 225,
                hollow: {
                    margin: 0,
                    size: '70%',
                    background: '#fff',
                    image: undefined,
                    position: 'front',
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 0,
                        blur: 4,
                        opacity: 0.24
                    }
                },
                track: {
                    background: '#fff',
                    strokeWidth: '67%',
                    margin: 0,
                    dropShadow: {
                        enabled: true,
                        top: -3,
                        left: 0,
                        blur: 4,
                        opacity: 0.35
                    }
                },
                dataLabels: {
                    show: true,
                    name: {
                        offsetY: -10,
                        show: true,
                        color: '#888',
                        fontSize: '17px'
                    },
                    value: {
                        formatter: function(val) {
                            return parseInt(val) + '%';
                        },
                        color: '#111',
                        fontSize: '36px',
                        show: true,
                    }
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                shadeIntensity: 0.5,
                gradientToColors: ['#ABE5A1'],
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            }
        },
        stroke: {
            lineCap: 'round'
        },
        labels: ['Taxa de Abertura'],
        colors: ['#667eea']
    };

    const chart = new ApexCharts(document.querySelector("#radialchart_overview_mailcamp"), options);
    chart.render();
    return chart;
}

// Configuração do gráfico radial para visão geral web
function createWebOverviewChart(data) {
    const options = {
        series: [data.conversionRate || 0],
        chart: {
            height: 320,
            type: 'radialBar',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 225,
                hollow: {
                    margin: 0,
                    size: '70%',
                    background: '#fff',
                    image: undefined,
                    position: 'front',
                    dropShadow: {
                        enabled: true,
                        top: 3,
                        left: 0,
                        blur: 4,
                        opacity: 0.24
                    }
                },
                track: {
                    background: '#fff',
                    strokeWidth: '67%',
                    margin: 0,
                    dropShadow: {
                        enabled: true,
                        top: -3,
                        left: 0,
                        blur: 4,
                        opacity: 0.35
                    }
                },
                dataLabels: {
                    show: true,
                    name: {
                        offsetY: -10,
                        show: true,
                        color: '#888',
                        fontSize: '17px'
                    },
                    value: {
                        formatter: function(val) {
                            return parseInt(val) + '%';
                        },
                        color: '#111',
                        fontSize: '36px',
                        show: true,
                    }
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'dark',
                type: 'horizontal',
                shadeIntensity: 0.5,
                gradientToColors: ['#11998e'],
                inverseColors: true,
                opacityFrom: 1,
                opacityTo: 1,
                stops: [0, 100]
            }
        },
        stroke: {
            lineCap: 'round'
        },
        labels: ['Taxa de Conversão'],
        colors: ['#38ef7d']
    };

    const chart = new ApexCharts(document.querySelector("#radialchart_overview_webcamp"), options);
    chart.render();
    return chart;
}
```

## 🎯 Instruções de Implementação

### 1. Integração com JavaScript Existente

Para integrar essas funções com o JavaScript existente dos dashboards:

1. **No `web_mail_campaign_dashboard.js`** e **`mail_campaign_dashboard.js`**:
   - Adicionar as funções acima
   - Chamar `updateMetricCards(data)` quando os dados forem carregados
   - Substituir os gráficos de pizza antigos pelos novos gráficos radiais

### 2. Exemplo de Uso

```javascript
// Quando os dados são carregados via AJAX
function loadCampaignData() {
    $.ajax({
        url: 'manager/campaign_data.php',
        type: 'POST',
        data: { campaign_id: g_campaign_id },
        success: function(response) {
            const data = {
                email: {
                    sent: response.emails_sent,
                    opened: response.emails_opened,
                    replied: response.emails_replied
                },
                web: {
                    visits: response.page_visits,
                    submissions: response.form_submissions,
                    suspicious: response.suspicious_entries
                }
            };
            
            // Atualizar cards de métricas
            updateMetricCards(data);
            
            // Criar/atualizar gráficos
            createEmailOverviewChart({
                openRate: data.email.sent > 0 ? (data.email.opened / data.email.sent) * 100 : 0
            });
            
            if (data.web) {
                createWebOverviewChart({
                    conversionRate: data.web.visits > 0 ? (data.web.submissions / data.web.visits) * 100 : 0
                });
            }
        }
    });
}
```

### 3. Animações de Entrada

```javascript
// Adicionar animações quando os cards carregam
function animateMetricCards() {
    $('.metric-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(30px)'
        }).delay(index * 100).animate({
            'opacity': '1',
            'transform': 'translateY(0)'
        }, 600);
    });
}
```

## ✅ Resultado Final

A nova interface moderna oferece:

- **Cards de métricas** com ícones coloridos e barras de progresso
- **Gráficos radiais** ao invés de múltiplos gráficos de pizza
- **Layout responsivo** que se adapta a diferentes tamanhos de tela
- **Animações suaves** para melhor experiência do usuário
- **Tipografia moderna** e hierarquia visual clara
- **Cores consistentes** com o design system da aplicação
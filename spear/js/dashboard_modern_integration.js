/**
 * Dashboard Moderno - Integração com dados reais
 * Sincroniza os cards modernos com os dados que já existem
 */

// Função para atualizar cards modernos com dados reais do JavaScript existente
function updateModernMetrics() {
    // Esta função será chamada após os dados serem carregados
    console.log('Atualizando cards modernos...');
}

// ========== FUNÇÕES PARA WebMailCmpDashboard ==========

// 1. Estender updatePieEmailSent para atualizar card de emails enviados
if (typeof updatePieEmailSent !== 'undefined') {
    const originalUpdatePieEmailSent = updatePieEmailSent;
    updatePieEmailSent = function(total_user_email_count, sent_success_count, sent_failed_count) {
        // Chamar função original
        originalUpdatePieEmailSent(total_user_email_count, sent_success_count, sent_failed_count);
        
        // Atualizar card moderno
        updateMetricCard('metric_emails_sent', 'progress_emails_sent', 'percent_emails_sent', total_user_email_count || 0);
        console.log('Card emails enviados atualizado:', total_user_email_count);
    };
}

// 2. Estender updatePieEmailOpen para atualizar card de emails abertos
if (typeof updatePieEmailOpen !== 'undefined') {
    const originalUpdatePieEmailOpen = updatePieEmailOpen;
    updatePieEmailOpen = function(total_user_email_count, mail_open_count, open_percent) {
        // Chamar função original
        originalUpdatePieEmailOpen(total_user_email_count, mail_open_count, open_percent);
        
        // Atualizar cards modernos
        updateMetricCard('metric_emails_opened', 'progress_emails_opened', 'percent_emails_opened', 
                        mail_open_count || 0, total_user_email_count);
        updateMetricCard('metric_open_rate', 'progress_open_rate', 'percent_open_rate_display', 
                        Math.round(open_percent || 0), 100, true);
        
        console.log('Cards emails abertos atualizados:', mail_open_count, open_percent + '%');
    };
}

// 3. Estender updatePieEmailReply para atualizar card de respostas
if (typeof updatePieEmailReply !== 'undefined') {
    const originalUpdatePieEmailReply = updatePieEmailReply;
    updatePieEmailReply = function(total_user_email_count, mail_reply_count, reply_percent) {
        // Chamar função original
        originalUpdatePieEmailReply(total_user_email_count, mail_reply_count, reply_percent);
        
        // Atualizar cards modernos
        updateMetricCard('metric_emails_replied', 'progress_emails_replied', 'percent_emails_replied', 
                        mail_reply_count || 0, total_user_email_count);
        
        // Verificar se existe card de taxa de resposta (apenas em MailCmpDashboard)
        if ($('#metric_reply_rate').length) {
            updateMetricCard('metric_reply_rate', 'progress_reply_rate', 'percent_reply_rate_display', 
                            Math.round(reply_percent || 0), 100, true);
        }
        
        console.log('Cards emails respondidos atualizados:', mail_reply_count, reply_percent + '%');
    };
}

// 4. Estender updatePieTotalPV para atualizar card de visitas (apenas WebMailCmpDashboard)
if (typeof updatePieTotalPV !== 'undefined') {
    const originalUpdatePieTotalPV = updatePieTotalPV;
    updatePieTotalPV = function(total_user_email_count, total_pv, pv_percent) {
        // Chamar função original
        originalUpdatePieTotalPV(total_user_email_count, total_pv, pv_percent);
        
        // Atualizar card moderno
        updateMetricCard('metric_page_visits', 'progress_page_visits', 'percent_page_visits', 
                        total_pv || 0, total_user_email_count);
        
        console.log('Card visitas à página atualizado:', total_pv, pv_percent + '%');
    };
}

// 5. Estender updatePieTotalFS para atualizar card de formulários
if (typeof updatePieTotalFS !== 'undefined') {
    const originalUpdatePieTotalFS = updatePieTotalFS;
    updatePieTotalFS = function(total_user_email_count, lps_count) {
        // Chamar função original
        originalUpdatePieTotalFS(total_user_email_count, lps_count);
        
        // Calcular taxa de conversão
        const submissionRate = total_user_email_count > 0 ? Math.round((lps_count / total_user_email_count) * 100) : 0;
        
        // Atualizar cards modernos
        updateMetricCard('metric_form_submissions', 'progress_form_submissions', 'percent_form_submissions', 
                        lps_count || 0, total_user_email_count);
        updateMetricCard('metric_conversion_rate', 'progress_conversion_rate', 'percent_conversion_rate_display', 
                        submissionRate, 100, true);
        
        console.log('Cards formulários atualizados:', lps_count, submissionRate + '%');
    };
}

// 6. Estender updatePieTotalSuspected para atualizar card de suspeitas
if (typeof updatePieTotalSuspected !== 'undefined') {
    const originalUpdatePieTotalSuspected = updatePieTotalSuspected;
    updatePieTotalSuspected = function(total_pv_nonmatch, total_fs_nonmatch, pv_nonmatch_percent, fs_nonmatch_percent) {
        // Chamar função original
        originalUpdatePieTotalSuspected(total_pv_nonmatch, total_fs_nonmatch, pv_nonmatch_percent, fs_nonmatch_percent);
        
        // Atualizar card moderno
        const suspicious = (total_pv_nonmatch || 0) + (total_fs_nonmatch || 0);
        const suspiciousRate = Math.round(pv_nonmatch_percent || 0);
        updateMetricCard('metric_suspicious_entries', 'progress_suspicious_entries', 'percent_suspicious_entries', 
                        suspicious, 100);
        
        console.log('Card entradas suspeitas atualizado:', suspicious, suspiciousRate + '%');
    };
}

// ========== FUNÇÕES PARA MailCmpDashboard ==========

// 7. Estender updatePieTotalSent para atualizar card de emails enviados (MailCmpDashboard)
if (typeof updatePieTotalSent !== 'undefined') {
    const originalUpdatePieTotalSent = updatePieTotalSent;
    updatePieTotalSent = function(total_user_email_count, sent_mail_count, sent_failed_count) {
        // Chamar função original
        originalUpdatePieTotalSent(total_user_email_count, sent_mail_count, sent_failed_count);
        
        // Atualizar card moderno
        updateMetricCard('metric_emails_sent', 'progress_emails_sent', 'percent_emails_sent', total_user_email_count || 0);
        console.log('Card emails enviados atualizado (MailCmp):', total_user_email_count);
    };
}

// 8. Estender updatePieTotalOpen para atualizar card de emails abertos (MailCmpDashboard)
if (typeof updatePieTotalOpen !== 'undefined') {
    const originalUpdatePieTotalOpen = updatePieTotalOpen;
    updatePieTotalOpen = function(total_user_email_count, mail_open_count, open_percent) {
        // Chamar função original
        originalUpdatePieTotalOpen(total_user_email_count, mail_open_count, open_percent);
        
        // Atualizar cards modernos
        updateMetricCard('metric_emails_opened', 'progress_emails_opened', 'percent_emails_opened', 
                        mail_open_count || 0, total_user_email_count);
        updateMetricCard('metric_open_rate', 'progress_open_rate', 'percent_open_rate_display', 
                        Math.round(open_percent || 0), 100, true);
        
        console.log('Cards emails abertos atualizados (MailCmp):', mail_open_count, open_percent + '%');
    };
}

// 9. Estender updatePieTotalReply para atualizar card de respostas (MailCmpDashboard)
if (typeof updatePieTotalReply !== 'undefined') {
    const originalUpdatePieTotalReply = updatePieTotalReply;
    updatePieTotalReply = function(total_user_email_count, mail_reply_count, reply_percent) {
        // Chamar função original
        originalUpdatePieTotalReply(total_user_email_count, mail_reply_count, reply_percent);
        
        // Atualizar cards modernos
        updateMetricCard('metric_emails_replied', 'progress_emails_replied', 'percent_emails_replied', 
                        mail_reply_count || 0, total_user_email_count);
        updateMetricCard('metric_reply_rate', 'progress_reply_rate', 'percent_reply_rate_display', 
                        Math.round(reply_percent || 0), 100, true);
        
        console.log('Cards emails respondidos atualizados (MailCmp):', mail_reply_count, reply_percent + '%');
    };
}

// ========== FUNÇÕES AUXILIARES E VARIAÇÕES ==========

// 10. Estender updatePieTotalMailOpen para atualizar card de emails abertos (variação do nome)
if (typeof updatePieTotalMailOpen !== 'undefined') {
    const originalUpdatePieTotalMailOpen = updatePieTotalMailOpen;
    updatePieTotalMailOpen = function(total_user_email_count, open_mail_count, open_mail_percent) {
        // Chamar função original
        originalUpdatePieTotalMailOpen(total_user_email_count, open_mail_count, open_mail_percent);
        
        // Atualizar cards modernos
        updateMetricCard('metric_emails_opened', 'progress_emails_opened', 'percent_emails_opened', 
                        open_mail_count || 0, total_user_email_count);
        updateMetricCard('metric_open_rate', 'progress_open_rate', 'percent_open_rate_display', 
                        Math.round(open_mail_percent || 0), 100, true);
        
        console.log('Cards emails abertos atualizados (variação):', open_mail_count, open_mail_percent + '%');
    };
}

// 11. Estender updateProgressbar para atualizar múltiplos cards
if (typeof updateProgressbar !== 'undefined') {
    const originalUpdateProgressbar = updateProgressbar;
    updateProgressbar = function(mailcamp_status, sender_list_id, user_group_id, mail_template_id, sent_mail_count, sent_success_count, sent_failed_count, mail_open_count) {
        // Chamar função original
        originalUpdateProgressbar(mailcamp_status, sender_list_id, user_group_id, mail_template_id, sent_mail_count, sent_success_count, sent_failed_count, mail_open_count);
        
        // Atualizar cards modernos com dados da barra de progresso
        if (sent_mail_count) {
            updateMetricCard('metric_emails_sent', 'progress_emails_sent', 'percent_emails_sent', sent_mail_count);
        }
        
        if (mail_open_count && sent_mail_count > 0) {
            const openRate = Math.round((mail_open_count / sent_mail_count) * 100);
            updateMetricCard('metric_emails_opened', 'progress_emails_opened', 'percent_emails_opened', 
                            mail_open_count, sent_mail_count);
            updateMetricCard('metric_open_rate', 'progress_open_rate', 'percent_open_rate_display', 
                            openRate, 100, true);
        }
        
        console.log('Cards atualizados via updateProgressbar:', {
            sent: sent_mail_count, 
            success: sent_success_count, 
            failed: sent_failed_count, 
            opened: mail_open_count
        });
    };
}

// Função para resetar todos os cards quando uma nova campanha é selecionada
function resetModernCards() {
    console.log('Resetando cards modernos...');
    
    // Lista de IDs dos elementos métricos
    const metricIds = [
        'metric_emails_sent', 'metric_emails_opened', 'metric_emails_replied',
        'metric_open_rate', 'metric_reply_rate', 'metric_page_visits',
        'metric_form_submissions', 'metric_suspicious_entries', 'metric_conversion_rate'
    ];
    
    // Lista de IDs das barras de progresso
    const progressIds = [
        'progress_emails_sent', 'progress_emails_opened', 'progress_emails_replied',
        'progress_open_rate', 'progress_reply_rate', 'progress_page_visits',
        'progress_form_submissions', 'progress_suspicious_entries', 'progress_conversion_rate'
    ];
    
    // Lista de IDs dos textos de porcentagem
    const percentIds = [
        'percent_emails_sent', 'percent_emails_opened', 'percent_emails_replied',
        'percent_open_rate_display', 'percent_reply_rate_display', 'percent_page_visits',
        'percent_form_submissions', 'percent_suspicious_entries', 'percent_conversion_rate_display'
    ];
    
    // Resetar métricas
    metricIds.forEach(id => {
        const element = $('#' + id);
        if (element.length) {
            if (id.includes('rate') || id.includes('conversion')) {
                element.text('0%');
            } else {
                element.text('0');
            }
        }
    });
    
    // Resetar barras de progresso
    progressIds.forEach(id => {
        const element = $('#' + id);
        if (element.length) {
            element.css('width', '0%');
        }
    });
    
    // Resetar textos de porcentagem
    percentIds.forEach(id => {
        const element = $('#' + id);
        if (element.length) {
            element.text('0%');
        }
    });
    
    console.log('Cards modernos resetados com sucesso');
}

// Função utilitária para atualizar card com animação
function updateMetricCard(metricId, progressId, percentId, value, maxValue = 100, isPercentage = false) {
    const metricElement = $('#' + metricId);
    const progressElement = $('#' + progressId);
    const percentElement = $('#' + percentId);
    
    if (metricElement.length) {
        // Atualizar valor principal com animação
        metricElement.fadeOut(150, function() {
            $(this).text(isPercentage ? value + '%' : value).fadeIn(150);
        });
    }
    
    if (progressElement.length && maxValue > 0) {
        // Calcular porcentagem se não for já uma porcentagem
        const percentage = isPercentage ? value : Math.round((value / maxValue) * 100);
        
        // Animar barra de progresso
        progressElement.animate({
            width: percentage + '%'
        }, 300);
        
        // Atualizar texto da porcentagem
        if (percentElement.length) {
            percentElement.text(percentage + '%');
        }
    }
}

// Interceptar a função campaignSelected para resetar cards
if (typeof campaignSelected !== 'undefined') {
    const originalCampaignSelected = campaignSelected;
    campaignSelected = function(campaign_id, tracker_id, f_refresh) {
        // Resetar cards antes de carregar novos dados
        resetModernCards();
        
        // Chamar função original
        return originalCampaignSelected(campaign_id, tracker_id, f_refresh);
    };
}

// Adicionar animações suaves aos cards quando carregam
function animateMetricCards() {
    $('.metric-card').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        });
        
        setTimeout(() => {
            $(this).animate({
                'opacity': '1',
                'transform': 'translateY(0)'
            }, 400);
        }, index * 100);
    });
}

// Inicializar quando a página carregar
$(document).ready(function() {
    console.log('=== Dashboard Moderno Inicializado ===');
    
    // Verificar quais elementos estão disponíveis na página
    const availableMetrics = [];
    const metricSelectors = [
        'metric_emails_sent', 'metric_emails_opened', 'metric_emails_replied',
        'metric_open_rate', 'metric_reply_rate', 'metric_page_visits',
        'metric_form_submissions', 'metric_suspicious_entries', 'metric_conversion_rate'
    ];
    
    metricSelectors.forEach(id => {
        if ($('#' + id).length) {
            availableMetrics.push(id);
        }
    });
    
    console.log('Cards modernos disponíveis:', availableMetrics);
    
    // Animar cards na primeira carga
    setTimeout(() => {
        animateMetricCards();
        console.log('Animações dos cards iniciadas');
    }, 500);
    
    // Verificar se existem dados na sessão ou contexto global para popular cards iniciais
    if (typeof window.dashboard_data !== 'undefined') {
        updateModernCardsFromGlobalData();
        console.log('Dados globais carregados nos cards');
    }
    
    // Executar debug das funções após 1 segundo
    setTimeout(() => {
        debugAvailableFunctions();
    }, 1000);
    
    console.log('Dashboard moderno pronto para receber dados reais');
});

// Função para atualizar cards a partir de dados globais se disponíveis
function updateModernCardsFromGlobalData() {
    if (window.dashboard_data) {
        const data = window.dashboard_data;
        
        // Emails enviados
        if (data.emails_sent) {
            $('#metric_emails_sent').text(data.emails_sent);
        }
        
        // Emails abertos
        if (data.emails_opened) {
            $('#metric_emails_opened').text(data.emails_opened);
            if (data.open_rate) {
                $('#metric_open_rate').text(data.open_rate + '%');
                $('#progress_open_rate').css('width', data.open_rate + '%');
            }
        }
        
        // Emails respondidos
        if (data.emails_replied) {
            $('#metric_emails_replied').text(data.emails_replied);
            if (data.reply_rate) {
                $('#metric_reply_rate').text(data.reply_rate + '%');
                $('#progress_reply_rate').css('width', data.reply_rate + '%');
            }
        }
        
        // Visitas à página
        if (data.page_visits) {
            $('#metric_page_visits').text(data.page_visits);
        }
        
        // Formulários enviados
        if (data.form_submissions) {
            $('#metric_form_submissions').text(data.form_submissions);
            if (data.conversion_rate) {
                $('#metric_conversion_rate').text(data.conversion_rate + '%');
                $('#progress_conversion_rate').css('width', data.conversion_rate + '%');
            }
        }
    }
}

// Função para debugging - mostrar quais funções estão disponíveis
function debugAvailableFunctions() {
    const functions = [
        'updatePieEmailSent', 'updatePieEmailOpen', 'updatePieEmailReply',
        'updatePieTotalPV', 'updatePieTotalFS', 'updatePieTotalSuspected',
        'updatePieTotalSent', 'updatePieTotalOpen', 'updatePieTotalReply',
        'updatePieTotalMailOpen', 'updatePieTotalMailReplied', 'updatePieOverViewEmail',
        'updateProgressbar', 'campaignSelected'
    ];
    
    console.log('=== Dashboard Integration Debug ===');
    functions.forEach(func => {
        if (typeof window[func] !== 'undefined') {
            console.log('✓ ' + func + ' - disponível e interceptada');
        } else {
            console.log('✗ ' + func + ' - não encontrada');
        }
    });
    console.log('===================================');
}

// Expor funções utilitárias globalmente para debug e testes
window.dashboardIntegration = {
    debug: debugAvailableFunctions,
    reset: resetModernCards,
    updateCard: updateMetricCard,
    updateFromGlobal: updateModernCardsFromGlobalData,
    
    // Função para testar cards com dados fictícios
    testCards: function() {
        console.log('Testando cards com dados fictícios...');
        updateMetricCard('metric_emails_sent', 'progress_emails_sent', 'percent_emails_sent', 100);
        updateMetricCard('metric_emails_opened', 'progress_emails_opened', 'percent_emails_opened', 75, 100);
        updateMetricCard('metric_open_rate', 'progress_open_rate', 'percent_open_rate_display', 75, 100, true);
        updateMetricCard('metric_emails_replied', 'progress_emails_replied', 'percent_emails_replied', 25, 100);
        updateMetricCard('metric_reply_rate', 'progress_reply_rate', 'percent_reply_rate_display', 25, 100, true);
        
        if ($('#metric_page_visits').length) {
            updateMetricCard('metric_page_visits', 'progress_page_visits', 'percent_page_visits', 50, 100);
        }
        
        if ($('#metric_form_submissions').length) {
            updateMetricCard('metric_form_submissions', 'progress_form_submissions', 'percent_form_submissions', 30, 100);
            updateMetricCard('metric_conversion_rate', 'progress_conversion_rate', 'percent_conversion_rate_display', 30, 100, true);
        }
        
        if ($('#metric_suspicious_entries').length) {
            updateMetricCard('metric_suspicious_entries', 'progress_suspicious_entries', 'percent_suspicious_entries', 5, 100);
        }
        
        console.log('Teste dos cards concluído!');
    }
};

// Interceptar possíveis erros de JavaScript e continuar funcionando
window.addEventListener('error', function(e) {
    if (e.message && e.message.includes('piechart_')) {
        console.log('Erro de gráfico interceptado e ignorado:', e.message);
        e.preventDefault();
        return false;
    }
});

console.log('Dashboard Integration carregado - use window.dashboardIntegration.debug() para verificar status');
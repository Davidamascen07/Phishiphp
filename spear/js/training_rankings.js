// LooPhish - Training Rankings JavaScript
$(document).ready(function() {
    // Initialize DataTables
    var rankingsTable = $('#rankingsTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        order: [[0, 'asc']], // Order by position
        language: {
            url: '/SniperPhish-main/spear/js/libs/Portuguese-Brasil.json'
        },
        columnDefs: [
            { targets: [0], width: '60px' },
            { targets: [3,4,5,6], className: 'text-center' },
            { targets: -1, orderable: false }
        ]
    });

    var departmentTable = $('#departmentTable').DataTable({
        processing: true,
        serverSide: false,
        responsive: true,
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.18/i18n/Portuguese-Brasil.json'
        }
    });

    // Load initial data
    loadClients();
    loadRankings();
    loadStatistics();

    // Auto-refresh every 60 seconds
    setInterval(function() {
        loadRankings();
        loadStatistics();
    }, 60000);
});

function loadClients() {
    $.post('manager/client_manager.php', {
        action_type: 'get_clients'
    }, function(response) {
        if (response.result === 'success') {
            var select = $('#filter_client');
            select.empty().append('<option value="">Todos os clientes</option>');
            
            response.data.forEach(function(client) {
                select.append(`<option value="${client.client_id}">${client.client_name}</option>`);
            });
        }
    }, 'json').fail(function() {
        // Client management might not be available
        $('#filter_client').closest('.form-group').hide();
    });
}

function loadRankings() {
    var clientId = $('#filter_client').val();
    
    $.post('manager/training_manager.php', {
        action_type: 'get_rankings',
        client_id: clientId
    }, function(response) {
        if (response.result === 'success') {
            updatePodium(response.data.slice(0, 3));
            populateRankingsTable(response.data);
        } else {
            toastr.error('Erro ao carregar rankings: ' + (response.error || 'Erro desconhecido'));
        }
    }, 'json').fail(function() {
        toastr.error('Erro de comunicação com o servidor');
    });
}

function updatePodium(topThree) {
    var podium = $('#podium');
    podium.empty();
    
    if (topThree.length === 0) {
        podium.html('<p class="text-muted">Nenhum dado de ranking disponível</p>');
        return;
    }
    
    // Reorder for podium display (2nd, 1st, 3rd)
    var podiumOrder = [];
    if (topThree[1]) podiumOrder.push({...topThree[1], position: 2, height: '120px', color: '#C0C0C0'});
    if (topThree[0]) podiumOrder.push({...topThree[0], position: 1, height: '150px', color: '#FFD700'});
    if (topThree[2]) podiumOrder.push({...topThree[2], position: 3, height: '100px', color: '#CD7F32'});
    
    podiumOrder.forEach(function(user) {
        var podiumItem = `
            <div class="col-md-4">
                <div class="text-center">
                    <div class="podium-stand" style="height: ${user.height}; background: ${user.color}; margin: 0 auto 15px; width: 80px; border-radius: 10px 10px 0 0; position: relative; display: flex; align-items: flex-end; justify-content: center; padding-bottom: 10px;">
                        <div style="background: white; border-radius: 50%; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 24px; color: ${user.color}; box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                            ${user.position}°
                        </div>
                    </div>
                    <div class="podium-info">
                        <h6 class="mb-1">${user.user_name}</h6>
                        <small class="text-muted">${user.user_email}</small>
                        <div class="mt-2">
                            <div class="badge badge-primary">${user.total_score} pts</div>
                            <div class="badge badge-success">${user.modules_completed} módulos</div>
                        </div>
                        <div class="mt-1">
                            ${getBadgesDisplay(user.badges_earned)}
                        </div>
                    </div>
                </div>
            </div>
        `;
        podium.append(podiumItem);
    });
}

function populateRankingsTable(rankings) {
    var table = $('#rankingsTable').DataTable();
    table.clear();

    rankings.forEach(function(rank, index) {
        var position = index + 1;
        var positionBadge = getPositionBadge(position);
        var userInfo = `
            <div>
                <strong>${rank.user_name}</strong><br>
                <small class="text-muted">${rank.user_email}</small>
            </div>
        `;
        
        var clientDept = rank.department ? 
            `${rank.client_name || 'N/A'}<br><small>${rank.department}</small>` : 
            (rank.client_name || 'N/A');
            
        var badges = getBadgesDisplay(rank.badges_earned);
        var lastActivity = formatDate(rank.last_activity);
        var totalTime = formatTime(rank.total_time_spent);
        
        table.row.add([
            positionBadge,
            userInfo,
            clientDept,
            `<span class="badge badge-primary">${rank.total_score}</span>`,
            `<span class="badge badge-success">${rank.modules_completed}</span>`,
            `<span class="badge badge-warning">${rank.certificates_earned}</span>`,
            `${parseFloat(rank.average_score || 0).toFixed(1)}%`,
            totalTime,
            lastActivity,
            badges
        ]);
    });

    table.draw();
}

function loadStatistics() {
    // Calculate statistics from rankings
    $.post('manager/training_manager.php', {
        action_type: 'get_rankings',
        client_id: $('#filter_client').val()
    }, function(response) {
        if (response.result === 'success') {
            var rankings = response.data;
            
            $('#total_participants').text(rankings.length);
            
            if (rankings.length > 0) {
                var totalScore = rankings.reduce((sum, rank) => sum + parseFloat(rank.average_score || 0), 0);
                var avgScore = (totalScore / rankings.length).toFixed(1);
                $('#avg_score').text(avgScore + '%');
                
                var totalCertificates = rankings.reduce((sum, rank) => sum + parseInt(rank.certificates_earned || 0), 0);
                $('#total_certificates').text(totalCertificates);
                
                var totalTime = rankings.reduce((sum, rank) => sum + parseInt(rank.total_time_spent || 0), 0);
                var avgTime = Math.round(totalTime / rankings.length / 60); // Convert to hours
                $('#avg_time').text(avgTime + 'h');
            } else {
                $('#avg_score, #total_certificates, #avg_time').text('0');
            }
        }
    }, 'json');
}

function updateRankingView() {
    var rankingType = $('#ranking_type').val();
    
    if (rankingType === 'department') {
        $('#department_rankings').show();
        loadDepartmentRankings();
    } else {
        $('#department_rankings').hide();
    }
}

function loadDepartmentRankings() {
    // Placeholder for department rankings
    var departmentData = [
        {
            position: 1,
            department: 'TI',
            client: 'Empresa ABC',
            participants: 25,
            average_score: 87.5,
            completion_rate: 92,
            risk_level: 'low'
        },
        {
            position: 2,
            department: 'Financeiro',
            client: 'Empresa XYZ',
            participants: 18,
            average_score: 79.2,
            completion_rate: 85,
            risk_level: 'medium'
        }
    ];
    
    var table = $('#departmentTable').DataTable();
    table.clear();
    
    departmentData.forEach(function(dept) {
        var riskBadge = getRiskLevelBadge(dept.risk_level);
        
        table.row.add([
            getPositionBadge(dept.position),
            dept.department,
            dept.client,
            dept.participants,
            dept.average_score.toFixed(1) + '%',
            dept.completion_rate + '%',
            riskBadge
        ]);
    });
    
    table.draw();
}

function getPositionBadge(position) {
    if (position === 1) {
        return '<span class="ranking-position first">1°</span>';
    } else if (position === 2) {
        return '<span class="ranking-position second">2°</span>';
    } else if (position === 3) {
        return '<span class="ranking-position third">3°</span>';
    } else {
        return `<span class="ranking-position">${position}°</span>`;
    }
}

function getBadgesDisplay(badgesString) {
    if (!badgesString) return '<span class="text-muted">-</span>';
    
    try {
        var badges = JSON.parse(badgesString);
        var badgeHtml = '';
        
        badges.forEach(function(badge) {
            badgeHtml += `<span class="badge badge-sm badge-info mr-1" title="${badge.description || badge.name}">${badge.icon || '🏆'}</span>`;
        });
        
        return badgeHtml || '<span class="text-muted">-</span>';
    } catch (e) {
        return '<span class="text-muted">-</span>';
    }
}

function getRiskLevelBadge(level) {
    var badges = {
        'low': '<span class="badge badge-success">Baixo</span>',
        'medium': '<span class="badge badge-warning">Médio</span>',
        'high': '<span class="badge badge-danger">Alto</span>'
    };
    return badges[level] || '<span class="badge badge-secondary">N/A</span>';
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    
    try {
        // Assuming date format is 'd-m-Y h:i A'
        return dateString;
    } catch (e) {
        return 'N/A';
    }
}

function formatTime(minutes) {
    if (!minutes || minutes === 0) return '0min';
    
    var hours = Math.floor(minutes / 60);
    var mins = minutes % 60;
    
    if (hours > 0) {
        return `${hours}h ${mins}min`;
    } else {
        return `${mins}min`;
    }
}

function showBadgeDetails(badges) {
    try {
        var badgeList = JSON.parse(badges);
        var content = '<div class="row">';
        
        badgeList.forEach(function(badge) {
            content += `
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <div style="font-size: 3rem;">${badge.icon || '🏆'}</div>
                            <h6>${badge.name}</h6>
                            <p class="text-muted small">${badge.description || 'Badge conquistada'}</p>
                            <small class="text-info">${badge.earned_date || 'Data não disponível'}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        
        content += '</div>';
        
        $('#badgeContent').html(content);
        $('#badgeModal').modal('show');
    } catch (e) {
        toastr.error('Erro ao exibir detalhes das badges');
    }
}

function updateAllRankings() {
    toastr.info('Atualizando rankings...');
    
    // Placeholder for updating all rankings
    $.post('manager/training_manager.php', {
        action_type: 'update_rankings'
    }, function(response) {
        if (response.result === 'success') {
            toastr.success('Rankings atualizados com sucesso!');
            loadRankings();
            loadStatistics();
        } else {
            toastr.error('Erro ao atualizar rankings: ' + (response.error || 'Erro desconhecido'));
        }
    }, 'json').fail(function() {
        toastr.error('Erro de comunicação com o servidor');
    });
}

function exportRanking() {
    var clientId = $('#filter_client').val();
    var period = $('#filter_period').val();
    
    // Create export URL
    var exportUrl = 'manager/export_rankings.php?';
    if (clientId) exportUrl += 'client_id=' + clientId + '&';
    if (period !== 'all') exportUrl += 'period=' + period + '&';
    exportUrl += 'format=csv';
    
    // Trigger download
    window.open(exportUrl, '_blank');
}

// Export functions to global scope
window.loadRankings = loadRankings;
window.updateRankingView = updateRankingView;
window.showBadgeDetails = showBadgeDetails;
window.updateAllRankings = updateAllRankings;
window.exportRanking = exportRanking;

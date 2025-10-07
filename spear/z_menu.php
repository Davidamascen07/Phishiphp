<!-- Bootstrap 5 CSS -->
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
<!-- Material Design Icons -->
<!-- <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet"> -->
<!-- Bootstrap 5 JS -->


<!-- Custom Dropdown JavaScript for Header -->
<script>
    // Custom dropdown functionality to replace Bootstrap's dropdown behavior
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all dropdowns
        initializeDropdowns();
    });

    function initializeDropdowns() {
        // Get all dropdown toggles
        const dropdownToggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');

        dropdownToggles.forEach(function(toggle) {
            const dropdown = toggle.closest('.dropdown');
            const menu = dropdown.querySelector('.dropdown-menu');

            if (!menu) return;

            // Add click handler to toggle
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Close other dropdowns first
                closeAllDropdowns();

                // Toggle current dropdown
                const isOpen = menu.classList.contains('show');
                if (!isOpen) {
                    menu.classList.add('show');
                    dropdown.classList.add('show');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });

            // Prevent menu from closing when clicking inside
            menu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            closeAllDropdowns();
        });

        // Close dropdowns on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllDropdowns();
            }
        });
    }

    function closeAllDropdowns() {
        const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
        openDropdowns.forEach(function(menu) {
            menu.classList.remove('show');
            const dropdown = menu.closest('.dropdown');
            if (dropdown) {
                dropdown.classList.remove('show');
                const toggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');
                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    }

    // Add CSS for dropdown animations and positioning
    const customDropdownCSS = `
.dropdown-menu {
    display: block;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.2s ease;
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    min-width: 10rem;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    font-size: 0.875rem;
    color: #212529;
    text-align: left;
    list-style: none;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}

.dropdown-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-menu-end {
    right: 0;
    left: auto;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.25rem 1rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    text-decoration: none;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    cursor: pointer;
}

.dropdown-item:hover,
.dropdown-item:focus {
    color: #1e2125;
    background-color: #e9ecef;
    text-decoration: none;
}

.dropdown-divider {
    height: 0;
    margin: 0.5rem 0;
    overflow: hidden;
    border-top: 1px solid rgba(0,0,0,.15);
}

/* Position adjustments for header dropdowns */
.header-actions .dropdown {
    position: relative;
}

.create-new-dropdown .dropdown-menu {
    min-width: 200px;
    left: 0;
    right: auto;
}

.user-dropdown .dropdown-menu {
    min-width: 180px;
}

/* Client Selector Styles */
.client-selector-dropdown {
    position: relative;
    display: inline-block;
}

.client-selector-btn {
    background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
    border: 1px solid rgba(79, 172, 254, 0.3);
    color: #fff;
    padding: 8px 15px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    min-width: 140px;
    text-align: left;
}

.client-selector-btn:hover {
    background: linear-gradient(135deg, rgba(79, 172, 254, 0.2) 0%, rgba(0, 242, 254, 0.2) 100%);
    border-color: rgba(79, 172, 254, 0.5);
    color: #fff;
    transform: translateY(-1px);
}

.client-dropdown-menu {
    min-width: 250px;
    max-height: 300px;
    overflow-y: auto;
    border: 1px solid rgba(79, 172, 254, 0.3);
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
}

.client-dropdown-menu .dropdown-header {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: #fff;
    font-weight: 600;
    padding: 10px 15px;
    margin: 0;
    border-radius: 10px 10px 0 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.client-dropdown-menu .dropdown-item {
    padding: 10px 15px;
    color: #2c3e50;
    font-weight: 500;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.client-dropdown-menu .dropdown-item:hover {
    background: linear-gradient(135deg, rgba(79, 172, 254, 0.1) 0%, rgba(0, 242, 254, 0.1) 100%);
    border-left-color: #4facfe;
    color: #2980b9;
    transform: translateX(3px);
}

.client-dropdown-menu .dropdown-item.active {
    background: linear-gradient(135deg, rgba(79, 172, 254, 0.2) 0%, rgba(0, 242, 254, 0.2) 100%);
    border-left-color: #4facfe;
    color: #2980b9;
    font-weight: 600;
}

.client-dropdown-menu .dropdown-item i {
    color: #4facfe;
    margin-right: 8px;
    width: 16px;
}

.client-dropdown-menu .dropdown-divider {
    margin: 5px 0;
    border-top-color: rgba(79, 172, 254, 0.2);
}

/* Notification dropdown specific styles */
#top_notifier + .dropdown-menu {
    min-width: 250px;
    max-width: 300px;
}
`;

    // Inject custom CSS
    const styleSheet = document.createElement('style');
    styleSheet.textContent = customDropdownCSS;
    document.head.appendChild(styleSheet);
</script>


<style>
    :root {
        --primary-color: #0c0c0cff;
        --sidebar-bg: #1e293b;
        --sidebar-hover: #334155;
        --sidebar-active: #4361ee;
        --header-height: 70px;
    }

    body {
        background-color: #f8fafc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        overflow-x: hidden;
    }

    .modern-sidebar {
        background: var(--sidebar-bg);
        width: 280px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .sidebar-brand {
        padding: 15px;
        color: white;
        border-bottom: 1px solid #374151;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .sidebar-brand:hover {
        color: white;
        text-decoration: none;
    }

    .sidebar-brand img {
        width: 40px;
        height: 40px;
        margin-right: 12px;
    }

    .sidebar-brand h4 {
        margin: 0;
        font-weight: 700;
        font-size: 1.4rem;
    }

    .sidebar-menu {
        padding: 10px 0;
        flex: 1;
        overflow-y: auto;
    }

    .menu-item {
        color: #cbd5e1;
        padding: 12px 20px;
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
    }

    .menu-item:hover,
    .menu-item.active {
        background: var(--sidebar-hover);
        color: white;
        border-left-color: var(--primary-color);
        text-decoration: none;
    }

    .menu-item i {
        margin-right: 12px;
        font-size: 18px;
        width: 20px;
    }

    .submenu {
        background: #1a2436;
        padding-left: 20px;
        display: none;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .submenu.show {
        display: block;
        max-height: 500px;
    }

    .submenu-item {
        color: #94a3b8;
        padding: 10px 20px;
        display: block;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.9rem;
    }

    .submenu-item:hover {
        color: white;
        background: rgba(67, 97, 238, 0.1);
        text-decoration: none;
    }

    .menu-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
    }

    .menu-toggle::after {
        content: '▼';
        font-size: 10px;
        transition: transform 0.3s ease;
    }

    .menu-toggle.active::after {
        transform: rotate(180deg);
    }

    .modern-header {
        background: linear-gradient(135deg, var(--primary-color), #3a56d4);
        border-bottom: none;
        position: fixed;
        top: 0;
        right: 0;
        left: 280px;
        height: var(--header-height);
        z-index: 999;
        transition: all 0.3s ease;
        padding: 0 25px;
        box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
    }

    .header-content {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* split header into two halves so we can push half the content to the right */
    .header-half {
        width: 50%;
        display: flex;
        align-items: center;
    }

    .header-half.right {
        justify-content: flex-end;
        /* push items to the far right */
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .header-title {
        color: white;
        font-weight: 600;
        font-size: 1.2rem;
        margin: 0;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-btn {
        background: rgba(255, 255, 255, 0);
        border: none;
        color: white;
        border-radius: 8px;
        padding: 8px 12px;
        transition: all 0.3s ease;
    }

    .header-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-1px);
        color: white;
    }

    .header-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-dropdown .dropdown-toggle::after {
        display: none;
    }

    .user-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
    }

    /* Ensure compatibility with existing page layouts */
    .main-content {
        margin-left: 280px;
        padding: 25px;
        min-height: 100vh;
        padding-top: calc(var(--header-height) + 20px);
        transition: margin-left 0.3s ease;
    }

    /* Adjust existing page-wrapper if it exists */
    .page-wrapper {
        margin-left: 280px !important;
        padding-top: var(--header-height) !important;
        transition: margin-left 0.1s ease !important;
    }

    .page-wrapper.hidden {
        margin-left: 0 !important;
    }

    .menu-toggle-btn {
        display: block;
        background: rgba(255, 255, 255, 0.15);
        color: white;
        border: none;
        padding: 10px;
        border-radius: 8px;
        transition: all 0.2s ease, transform 0.2s ease;
    }

    .menu-toggle-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-1px);
        color: white;
    }

    .modern-sidebar.hidden {
        transform: translateX(-100%);
    }

    .modern-header.hidden {
        left: 0;
    }

    .main-content.hidden {
        margin-left: 0;
        padding-top: calc(var(--header-height) + 20px);
    }

    @media (min-width: 763px) {
        .modern-sidebar {
            transform: translateX(0);
        }
    }

    @media (max-width: 762px) {
        .modern-sidebar {
            transform: translateX(-100%);
        }

        .modern-sidebar.show {
            transform: translateX(0);
        }

        .modern-header {
            left: 0;
        }

        .main-content {
            margin-left: 0;
        }

        .menu-toggle-btn {
            display: block;
        }

        .header-title {
            font-size: 1.1rem;
        }
    }

    @media (max-width: 761px) {
        .modern-header {
            padding: 0 15px;
        }

        .header-title {
            font-size: 1rem;
        }

        .header-btn {
            padding: 6px 10px;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
        }
    }

    .sidebar-menu::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-menu::-webkit-scrollbar-track {
        background: #1a2436;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background: #4b5563;
        border-radius: 2px;
    }

    .sidebar-menu::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    /* Dropdown customizations */
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
    }

    .dropdown-item:hover {
        background-color: #f3f4f6;
    }

    .create-new-dropdown .dropdown-menu {
        min-width: 200px;
    }

    .create-new-btn {
        background: rgba(255, 255, 255, 0.15) !important;
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white !important;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
    }

    .create-new-btn:hover {
        background: rgba(255, 255, 255, 0.25) !important;
        color: white !important;
        transform: translateY(-1px);
    }
</style>

<header class="modern-header">
    <div class="header-content">
        <div class="header-half left">
            <div class="header-left">
                <button class="menu-toggle-btn" onclick="toggleSidebar()">
                    <i class="mdi mdi-menu"></i>
                </button>

                <!-- Client Selector -->
                <div class="dropdown client-selector-dropdown">
                    <button class="btn client-selector-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="headerClientSelector">
                        <i class="mdi mdi-office-building me-2"></i>
                        <span id="currentClientName">Carregando...</span>
                    </button>
                    <ul class="dropdown-menu client-dropdown-menu" id="headerClientDropdown">
                        <li>
                            <h6 class="dropdown-header">Selecionar Cliente</h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="text-center">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Create New Dropdown -->
                <div class="dropdown create-new-dropdown">
                    <button class="btn create-new-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-md-inline">Criar Novo</span>
                        <span class="d-inline d-md-none"><i class="mdi mdi-plus"></i></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="/spear/QuickTracker"><i class="mdi mdi-watch-vibrate me-2"></i>Rastreador Rápido</a></li>
                        <li><a class="dropdown-item" href="/spear/TrackerGenerator"><i class="mdi mdi-web me-2"></i>Rastreador Web</a></li>
                        <li><a class="dropdown-item" href="/spear/MailCampaignList?action=add&campaign=new"><i class="mdi mdi-email me-2"></i>Campanha de Email</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/spear/MailUserGroup?action=add&user=new"><i class="mdi mdi-account-group me-2"></i>Grupo de Usuários</a></li>
                        <li><a class="dropdown-item" href="/spear/MailTemplate?action=add&template=new"><i class="mdi mdi-email-edit me-2"></i>Modelo de Email</a></li>
                        <li><a class="dropdown-item" href="/spear/MailSender?action=add&sender=new"><i class="mdi mdi-account-tie me-2"></i>Lista de Remetentes</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="header-half right">
            <div class="header-actions">
                <!-- Last Login (Hidden by default) -->
                <div class="d-none lb-login">
                    <span class="text-white-50">Last login: <span></span></span>
                </div>

                <!-- Notifications -->
                <div class="dropdown">
                    <button class="header-btn position-relative" type="button" data-bs-toggle="dropdown" id="top_notifier">
                        <i class="mdi mdi-bell"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Dynamic notifications will be added here by JS -->
                    </ul>
                </div>

                <!-- User Dropdown -->
                <div class="dropdown user-dropdown">
                    <button class="header-btn d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                        <img src="/spear/images/users/1.png" alt="user" class="rounded-circle pro-pic" width="31">
                        <span class="d-none d-md-inline text-white profile-name"></span>
                        <i class="mdi mdi-chevron-down d-none d-md-inline"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/spear/SettingsUser">
                                <i class="mdi mdi-account me-2"></i>My Profile <span class="text-muted profile-name"></span>
                            </a></li>
                        <li><a class="dropdown-item" href="/spear/SettingsGeneral">
                                <i class="mdi mdi-cog me-2"></i>General Setting
                            </a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="/spear/logout">
                                <i class="mdi mdi-logout me-2"></i>Logout
                            </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- ============================================================== -->
<!-- End Topbar header -->
<!-- ============================================================== -->
<?php
// compute project root path (e.g. /SniperPhishmain) so JS can rewrite legacy "/spear/..." URLs
$sp_root = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), "\\/");
?>
<script>
    // Preserve original path fixing functionality
    (function() {
        var ROOT = '<?php echo $sp_root; ?>'; // e.g. /SniperPhishmain
        // On DOM ready, fix image src attributes that start with /spear/
        function fixImages() {
            try {
                document.querySelectorAll('img').forEach(function(img) {
                    var s = img.getAttribute('src');
                    if (s && s.indexOf('/spear/') === 0) {
                        img.src = ROOT + s; // -> /SniperPhishmain/spear/...
                    }
                });
            } catch (e) {
                /* ignore */
            }
        }
        // If jQuery is present, add ajaxPrefilter to rewrite AJAX urls starting with /spear/
        function installAjaxFix() {
            if (window.jQuery) {
                $.ajaxPrefilter(function(options) {
                    if (!options || !options.url) return;
                    // absolute origin + /spear/... or path starting with /spear/
                    try {
                        var originPrefix = window.location.origin + '/spear/';
                        if (options.url.indexOf(originPrefix) === 0) {
                            options.url = window.location.origin + ROOT + options.url.substring(window.location.origin.length);
                        } else if (options.url.indexOf('/spear/') === 0) {
                            options.url = ROOT + options.url; // prefix with project
                        }
                    } catch (e) {
                        /* ignore */
                    }
                });

                // also fix any .pro-pic already set by scripts
                $(function() {
                    $('.pro-pic').each(function() {
                        var $t = $(this);
                        var s = $t.attr('src');
                        if (s && s.indexOf('/spear/') === 0) $t.attr('src', ROOT + s);
                    });
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                fixImages();
                installAjaxFix();
                initModernSidebar();
            });
        } else {
            fixImages();
            installAjaxFix();
            initModernSidebar();
        }

        // Modern sidebar functionality
        function initModernSidebar() {
            // Restore sidebar state on page load
            if (localStorage.getItem('sidebar-hidden') && window.innerWidth >= 762) {
                const sidebar = document.getElementById('sidebar');
                const header = document.querySelector('.modern-header');
                const main = document.querySelector('.main-content');
                const pageWrapper = document.querySelector('.page-wrapper');
                if (sidebar && header) {
                    sidebar.classList.add('hidden');
                    header.style.setProperty('left', '0', 'important');
                    header.style.setProperty('width', '100%', 'important');
                    if (main) main.style.setProperty('margin-left', '0', 'important');
                    if (pageWrapper) {
                        pageWrapper.style.setProperty('margin-left', '0', 'important');
                        pageWrapper.style.setProperty('padding-left', '0rem', 'important');
                    }
                }
            }

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                const sidebar = document.getElementById('sidebar');
                const toggleBtn = document.querySelector('.menu-toggle-btn');

                if (window.innerWidth < 762 && sidebar && toggleBtn &&
                    !sidebar.contains(event.target) &&
                    !toggleBtn.contains(event.target) &&
                    sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                }
            });
        }
        // Adicionar funcionalidade de seletor de cliente
        loadHeaderClientSelector();

        function loadHeaderClientSelector() {
            // Carregar clientes disponíveis
            fetch('/spear/manager/session_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'getUserAccessibleClients'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateHeaderClientSelector(data.clients, data.currentClientId, data.currentClientName);
                    } else {
                        console.error('Erro ao carregar clientes:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Erro na requisição:', error);
                });
        }

        function updateHeaderClientSelector(clients, currentClientId, currentClientName) {
            const clientNameSpan = document.getElementById('currentClientName');
            const dropdown = document.getElementById('headerClientDropdown');

            if (clientNameSpan) {
                clientNameSpan.textContent = currentClientName || 'Nenhum cliente';
            }

            if (dropdown && clients) {
                let dropdownHtml = '<li><h6 class="dropdown-header">Selecionar Cliente</h6></li>';
                dropdownHtml += '<li><hr class="dropdown-divider"></li>';

                clients.forEach(client => {
                    // Validar dados do cliente antes de gerar HTML
                    if (!client.client_id || !client.client_name) {
                        console.warn('Cliente com dados inválidos ignorado:', client);
                        return;
                    }

                    const clientId = String(client.client_id).replace(/'/g, "\\'");
                    const clientName = String(client.client_name).replace(/'/g, "\\'");
                    const isActive = client.client_id === currentClientId ? 'active' : '';

                    dropdownHtml += `
                        <li>
                            <a class="dropdown-item ${isActive}" href="#" onclick="changeHeaderClient('${clientId}', '${clientName}')">
                                <i class="mdi mdi-office-building"></i>
                                ${client.client_name}
                            </a>
                        </li>
                    `;
                });

                dropdown.innerHTML = dropdownHtml;
            }
        }
    })();

    // Função global para troca de cliente (usada em onclick)
    function changeHeaderClient(clientId, clientName) {
        // Verificar se já está no cliente selecionado
        const currentClientEl = document.getElementById('currentClientName');
        if (currentClientEl && currentClientEl.textContent.trim() === clientName) {
            if (typeof toastr !== 'undefined') {
                toastr.info('Cliente já selecionado: ' + clientName);
            }
            return;
        }

        // Mostrar loading
        if (typeof toastr !== 'undefined') {
            toastr.info('Alterando cliente...');
        }

        fetch('manager/session_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'setClientContext',
                    clientId: clientId
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro na resposta do servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Verificar se a sessão foi realmente alterada
                    return fetch('manager/session_api.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                action: 'getCurrentClientContext'
                            })
                        })
                        .then(response => response.json())
                        .then(currentData => {
                            console.log('Verificação da troca:', currentData);
                            console.log('Cliente esperado:', clientId);
                            console.log('Cliente atual:', currentData.clientId);

                            if (currentData.success && currentData.clientId === clientId) {
                                // Sucesso confirmado - atualizar interface
                                const currentClientNameEl = document.getElementById('currentClientName');
                                if (currentClientNameEl) {
                                    currentClientNameEl.textContent = clientName;
                                }

                                // Atualizar classes ativas no dropdown
                                const dropdownItems = document.querySelectorAll('#headerClientDropdown .dropdown-item');
                                dropdownItems.forEach(item => {
                                    item.classList.remove('active');
                                    if (item.textContent.trim().includes(clientName)) {
                                        item.classList.add('active');
                                    }
                                });

                                // Fechar dropdown
                                if (typeof closeAllDropdowns === 'function') {
                                    closeAllDropdowns();
                                }

                                // Disparar evento para sincronizar outras partes da página
                                const event = new CustomEvent('clientChanged', {
                                    detail: {
                                        clientId: clientId,
                                        clientName: clientName
                                    }
                                });
                                window.dispatchEvent(event);

                                // Mostrar mensagem de sucesso
                                if (typeof toastr !== 'undefined') {
                                    toastr.success(`Cliente alterado para: ${clientName}`);
                                }

                                // Recarregar dados imediatamente sem recarregar a página
                                if (typeof window.reloadClientData === 'function') {
                                    window.reloadClientData();
                                }

                                // Recarregar página como fallback após um tempo menor
                                setTimeout(() => {
                                    window.location.reload();
                                }, 800);
                            } else {
                                console.error('Falha na verificação da troca de cliente:');
                                console.error('- currentData.success:', currentData.success);
                                console.error('- currentData.clientId:', currentData.clientId);
                                console.error('- clientId esperado:', clientId);
                                console.error('- currentData completa:', currentData);
                                throw new Error('Falha na verificação da troca de cliente: esperado=' + clientId + ', atual=' + (currentData.clientId || 'undefined'));
                            }
                        });
                } else {
                    throw new Error(data.message || 'Erro desconhecido ao alterar cliente');
                }
            })
            .catch(error => {
                console.error('Erro na requisição de troca de cliente:', error);
                if (typeof toastr !== 'undefined') {
                    toastr.error('Erro ao alterar cliente: ' + error.message);
                } else {
                    alert('Erro ao alterar cliente: ' + error.message);
                }
            });
    }

    // Tornar função disponível globalmente
    window.changeHeaderClient = changeHeaderClient;

    // Função de debug para troca de cliente
    window.debugClientChange = function() {
        console.log('=== DEBUG TROCA DE CLIENTE ===');
        fetch('manager/session_api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    action: 'getCurrentClientContext'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Cliente atual na sessão:', data);
                const selector = document.getElementById('currentClientName');
                console.log('Elemento currentClientName:', selector);
                console.log('Texto atual do elemento:', selector ? selector.textContent : 'não encontrado');
            })
            .catch(error => console.error('Erro no debug:', error));
    };

    // Global function to reload client data
    window.reloadClientData = function() {
        if (typeof loadClientStats === 'function') {
            loadClientStats();
        }
        if (typeof loadHeaderClientSelector === 'function') {
            loadHeaderClientSelector();
        }
    };

    // Toggle submenu functionality
    function toggleSubmenu(id) {
        // Validar ID antes de usar no querySelector
        if (!id || id.trim() === '') {
            console.warn('toggleSubmenu: ID não fornecido ou vazio');
            return;
        }

        const submenu = document.getElementById('submenu-' + id);
        const menuItem = event.currentTarget;

        if (submenu && menuItem) {
            submenu.classList.toggle('show');
            menuItem.classList.toggle('active');
        } else {
            console.warn('toggleSubmenu: Elemento não encontrado para ID:', id);
        }
    }

    // Toggle sidebar functionality
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const header = document.querySelector('.modern-header');
        const main = document.querySelector('.main-content');
        const pageWrapper = document.querySelector('.page-wrapper');

        if (!sidebar || !header) return;

        // Mobile behavior
        if (window.innerWidth < 762) {
            sidebar.classList.toggle('show');
            return;
        }

        // Get sidebar width from CSS variable
        const sidebarWidth = getComputedStyle(document.documentElement).getPropertyValue('--sidebar-width').trim();

        // Desktop behavior
        const isHidden = sidebar.classList.toggle('hidden');
        if (isHidden) {
            // Sidebar hidden - expand content
            header.style.setProperty('left', '0', 'important');
            header.style.setProperty('width', '100%', 'important');
            if (main) main.style.setProperty('margin-left', '0', 'important');
            if (pageWrapper) {
                pageWrapper.style.setProperty('margin-left', '0', 'important');
                pageWrapper.style.setProperty('padding-left', '0rem', 'important');
            }
            localStorage.setItem('sidebar-hidden', '1');
        } else {
            // Sidebar visible - make space for sidebar
            header.style.setProperty('left', sidebarWidth, 'important');
            header.style.setProperty('width', `calc(100% - ${sidebarWidth})`, 'important');
            if (main) main.style.setProperty('margin-left', sidebarWidth, 'important');
            if (pageWrapper) {
                pageWrapper.style.setProperty('margin-left', sidebarWidth, 'important');
                pageWrapper.style.setProperty('padding-left', '0rem', 'important');
            }
            localStorage.removeItem('sidebar-hidden');
        }
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar && window.innerWidth >= 762) {
            sidebar.classList.remove('show'); // Remove mobile show class on desktop
        }
    });
</script>
<!-- ============================================================== -->
<!-- Left Sidebar - Modern Design  -->
<!-- ============================================================== -->
<div class="modern-sidebar" id="sidebar">
    <a href="/spear/Home" class="sidebar-brand">
        <img src="/spear/images/logo-icon.png" alt="Logo">
        <h4>Loophish</h4>
    </a>

    <div class="sidebar-menu">
        <a href="/spear/Home" class="menu-item">
            <i class="mdi mdi-home"></i>
            <span>Início</span>
        </a>

        <div class="menu-item menu-toggle" onclick="toggleSubmenu('quick-tracker')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-watch-vibrate"></i>
                <span>Rastreador Rápido</span>
            </div>
        </div>
        <div class="submenu" id="submenu-quick-tracker">
            <a href="/spear/QuickTracker" class="submenu-item">
                <i class="mdi mdi-playlist-plus"></i> Lista de Rastreadores
            </a>
            <a href="/spear/QuickTrackerReport" class="submenu-item">
                <i class="mdi mdi-book-open"></i> Relatórios
            </a>
        </div>

        <div class="menu-item menu-toggle" onclick="toggleSubmenu('web-tracker')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-web"></i>
                <span>Rastreador Web</span>
            </div>
        </div>
        <div class="submenu" id="submenu-web-tracker">
            <a href="/spear/TrackerList" class="submenu-item">
                <i class="mdi mdi-format-list-bulleted"></i> Lista de Rastreadores
            </a>
            <a href="/spear/TrackerGenerator" class="submenu-item">
                <i class="mdi mdi-plus"></i> Novo Rastreador
            </a>
        </div>

        <a href="/spear/TrackerReport" class="menu-item">
            <i class="mdi mdi-laptop-windows"></i>
            <span>Relatório do Rastreador Web</span>
        </a>

        <div class="menu-item menu-toggle" onclick="toggleSubmenu('email-campaign')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-email"></i>
                <span>Campanha de Email</span>
            </div>
        </div>
        <div class="submenu" id="submenu-email-campaign">
            <a href="/spear/MailCampaignList" class="submenu-item">
                <i class="mdi mdi-playlist-plus"></i> Lista de Campanhas
            </a>
            <a href="/spear/MailUserGroup" class="submenu-item">
                <i class="mdi mdi-account-group"></i> Grupo de Usuários
            </a>
            <a href="/spear/MailTemplate" class="submenu-item">
                <i class="mdi mdi-email-edit"></i> Modelo de Email
            </a>
            <a href="/spear/MailSender" class="submenu-item">
                <i class="mdi mdi-account-tie"></i> Lista de Remetentes
            </a>
            <a href="/spear/MailConfig" class="submenu-item">
                <i class="mdi mdi-cog"></i> Configuração
            </a>
        </div>

        <a href="/spear/MailCmpDashboard" class="menu-item">
            <i class="mdi mdi-view-dashboard"></i>
            <span>Painel de Campanha de Email</span>
        </a>

        <a href="/spear/WebMailCmpDashboard" class="menu-item">
            <i class="mdi mdi-view-dashboard"></i>
            <span>Painel Web-MailCamp</span>
        </a>

        <div class="menu-item menu-toggle" onclick="toggleSubmenu('sniperhost')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-cloud"></i>
                <span>Host de Arquivos</span>
            </div>
        </div>
        <div class="submenu" id="submenu-sniperhost">
            <a href="/spear/sniperhost/PlainText" class="submenu-item">
                <i class="mdi mdi-format-text"></i> Texto Simples
            </a>
            <a href="/spear/sniperhost/FileHost" class="submenu-item">
                <i class="mdi mdi-file-multiple"></i> Arquivos
            </a>
            <a href="/spear/sniperhost/LandingPage" class="submenu-item">
                <i class="mdi mdi-google-pages"></i> Página de Destino
            </a>
        </div>

        <!-- Módulo de Gestão de Clientes -->
        <div class="menu-item menu-toggle" onclick="toggleSubmenu('clients')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-office-building"></i>
                <span>Gestão de Clientes</span>
            </div>
        </div>
        <div class="submenu" id="submenu-clients">
            <a href="/spear/ClientList" class="submenu-item">
                <i class="mdi mdi-format-list-bulleted"></i> Lista de Clientes
            </a>
            <a href="/spear/UserManagement" class="submenu-item">
                <i class="mdi mdi-account-multiple"></i> Gestão de Usuários
            </a>
        </div>

        <!-- Módulo de Treinamentos -->
        <div class="menu-item menu-toggle" onclick="toggleSubmenu('training')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-school"></i>
                <span>Treinamentos</span>
            </div>
        </div>
        <div class="submenu" id="submenu-training">
            <a href="/spear/TrainingManagement" class="submenu-item">
                <i class="mdi mdi-book-open"></i> Gestão de Treinamentos
            </a>
            <a href="/spear/TrainingRankings" class="submenu-item">
                <i class="mdi mdi-trophy"></i> Rankings
            </a>
            <a href="/spear/TrainingCertificates" class="submenu-item">
                <i class="mdi mdi-certificate"></i> Certificados
            </a>
            <a href="/spear/TrainingQuestions" class="submenu-item">
                <i class="mdi mdi-certificate"></i> Questão
            </a>
        </div>

        <!-- Módulo de Relatórios -->
        <div class="menu-item menu-toggle" onclick="toggleSubmenu('reports')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-chart-line"></i>
                <span>Relatórios</span>
            </div>
        </div>
        <div class="submenu" id="submenu-reports">
            <a href="/spear/ReportsExecutive" class="submenu-item">
                <i class="mdi mdi-file-chart"></i> Relatórios Executivos
            </a>
            <a href="/spear/ReportsTechnical" class="submenu-item">
                <i class="mdi mdi-file-table"></i> Relatórios Técnicos
            </a>
        </div>

        <div class="menu-item menu-toggle" onclick="toggleSubmenu('settings')">
            <div style="display: flex; align-items: center;">
                <i class="mdi mdi-settings"></i>
                <span>Configurações</span>
            </div>
        </div>
        <div class="submenu" id="submenu-settings">
            <a href="/spear/SettingsGeneral" class="submenu-item">
                <i class="mdi mdi-settings"></i> Configurações Gerais
            </a>
            <a href="/spear/SettingsUser" class="submenu-item">
                <i class="mdi mdi-account-settings-variant"></i> Configurações do Usuário
            </a>
            <a href="/spear/SPLogs" class="submenu-item">
                <i class="mdi mdi-note-text"></i> Registros
            </a>
            <a href="/spear/SPAbout" class="submenu-item">
                <i class="mdi mdi-information"></i> Sobre
            </a>
        </div>
    </div>
</div>

<!-- Client Context Management Script -->
<script src="/spear/js/client-context-manager.js"></script>
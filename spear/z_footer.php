<footer class="footer">
    <div class="footer-content">
        <div class="footer-info">
            <div class="footer-logo">
                <img src="images/logo-icon.png" alt="Loophish Logo" width="24" height="24">
                <span>Loophish</span>
            </div>
            <div class="footer-text">
                Kit de Ferramentas de Spear Phishing Educacional
            </div>
        </div>
        <div class="footer-links">
            <!-- <a href="#" class="footer-link">
                <i class="mdi mdi-shield-check"></i>
                Segurança
            </a>
            <a href="#" class="footer-link">
                <i class="mdi mdi-help-circle"></i>
                Suporte
            </a>
            <a href="/spear/SPAbout" class="footer-link">
                <i class="mdi mdi-information"></i>
                Sobre
            </a> -->
        </div>
        <div class="footer-copyright">
            <div class="copyright-text">
                © 2025 <strong>Loophish</strong>. Todos os direitos reservados.
            </div>
            <div class="version-info">
                Versão 2.0
            </div>
        </div>
    </div>
</footer>

<style>
.footer {
    background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-900) 100%);
    color: var(--gray-300);
    padding: 2rem 0 1rem;
    margin-top: 3rem;
    border-top: 3px solid var(--primary-color);
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr auto 1fr;
    gap: 2rem;
    align-items: center;
}

.footer-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--white);
    margin-bottom: 0.5rem;
}

.footer-text {
    font-size: 0.875rem;
    color: var(--gray-400);
    line-height: 1.6;
}

.footer-links {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
}

.footer-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--gray-300);
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius-sm);
    transition: var(--transition-fast);
}

.footer-link:hover {
    color: var(--primary-color);
    background: rgba(67, 97, 238, 0.1);
    text-decoration: none;
    transform: translateY(-2px);
}

.footer-copyright {
    text-align: right;
}

.copyright-text {
    font-size: 0.875rem;
    color: var(--gray-300);
    margin-bottom: 0.25rem;
}

.version-info {
    font-size: 0.75rem;
    color: var(--gray-500);
}

/* Tema toggle button */
.theme-toggle {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    z-index: 1000;
    width: 3.5rem;
    height: 3.5rem;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: var(--white);
    border: none;
    cursor: pointer;
    box-shadow: var(--shadow-lg);
    transition: var(--transition-normal);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.theme-toggle:hover {
    transform: scale(1.1) rotate(15deg);
    box-shadow: var(--shadow-xl);
}

/* Scroll to top button */
.scroll-top {
    position: fixed;
    bottom: 7rem;
    right: 2rem;
    z-index: 1000;
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.7);
    color: var(--white);
    border: none;
    cursor: pointer;
    transition: var(--transition-fast);
    display: none;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.scroll-top.show {
    display: flex;
}

.scroll-top:hover {
    background: var(--primary-color);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 1.5rem;
    }
    
    .footer-links {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .footer-copyright {
        text-align: center;
    }
    
    .theme-toggle,
    .scroll-top {
        right: 1rem;
    }
}
</style>

<!-- Theme toggle button -->
<button class="theme-toggle" onclick="toggleTheme()" title="Alternar tema">
    <i class="mdi mdi-theme-light-dark"></i>
</button>

<!-- Scroll to top button -->
<button class="scroll-top" onclick="scrollToTop()" title="Voltar ao topo">
    <i class="mdi mdi-chevron-up"></i>
</button>

<script>
// Theme toggle functionality
function toggleTheme() {
    const html = document.documentElement;
    const currentTheme = html.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Add a subtle animation
    document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
    setTimeout(() => {
        document.body.style.transition = '';
    }, 300);
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
});

// Scroll to top functionality
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Show/hide scroll to top button
window.addEventListener('scroll', function() {
    const scrollTop = document.querySelector('.scroll-top');
    if (window.pageYOffset > 300) {
        scrollTop.classList.add('show');
    } else {
        scrollTop.classList.remove('show');
    }
});

// Add smooth scrolling to all internal links
document.addEventListener('DOMContentLoaded', function() {
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>
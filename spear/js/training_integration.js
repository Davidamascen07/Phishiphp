/**
 * Training Integration Script
 * Automatically triggers training modules based on user interactions
 * 
 * This script should be included in phishing pages to enable automatic
 * redirection to training content after form submissions or other interactions.
 */

(function() {
    'use strict';
    
    // Configuration - these will be set by the server when generating the page
    const TrainingConfig = {
        trackerId: window.TRACKER_ID || '',
        sessionId: window.SESSION_ID || '',
        userId: window.USER_ID || '',
        baseUrl: window.BASE_URL || '',
        
        // Training triggers
        triggers: {
            onFormSubmit: true,
            onLinkClick: true,
            onButtonClick: true,
            onFileDownload: true
        },
        
        // Redirect settings
        redirectDelay: 2000, // 2 seconds
        showWarningMessage: true,
        
        // Debug mode
        debug: false
    };
    
    // State tracking
    let trainingTriggered = false;
    let interactionCount = 0;
    
    /**
     * Initialize training integration
     */
    function initTrainingIntegration() {
        if (!TrainingConfig.trackerId) {
            console.warn('Training Integration: No tracker ID found');
            return;
        }
        
        setupEventListeners();
        
        if (TrainingConfig.debug) {
            console.log('Training Integration initialized', TrainingConfig);
        }
    }
    
    /**
     * Setup event listeners for various interactions
     */
    function setupEventListeners() {
        // Form submission tracking
        if (TrainingConfig.triggers.onFormSubmit) {
            document.addEventListener('submit', handleFormSubmit, true);
        }
        
        // Link click tracking
        if (TrainingConfig.triggers.onLinkClick) {
            document.addEventListener('click', handleLinkClick, true);
        }
        
        // Button click tracking
        if (TrainingConfig.triggers.onButtonClick) {
            document.addEventListener('click', handleButtonClick, true);
        }
        
        // File download tracking
        if (TrainingConfig.triggers.onFileDownload) {
            document.addEventListener('click', handleFileDownload, true);
        }
        
        // Page unload tracking (for analytics)
        window.addEventListener('beforeunload', handlePageUnload);
    }
    
    /**
     * Handle form submission
     */
    function handleFormSubmit(event) {
        if (trainingTriggered) return;
        
        const form = event.target;
        
        // Skip if this is a training-related form
        if (form.classList.contains('training-form') || form.id.includes('training')) {
            return;
        }
        
        logInteraction('form_submit', {
            formId: form.id,
            formAction: form.action,
            formMethod: form.method,
            fieldCount: form.elements.length
        });
        
        // Delay form submission to allow training trigger
        event.preventDefault();
        
        // Capture form data first
        const formData = new FormData(form);
        const formDataObj = {};
        for (let [key, value] of formData.entries()) {
            formDataObj[key] = value;
        }
        
        // Show warning message if enabled
        if (TrainingConfig.showWarningMessage) {
            showPhishingWarning();
        }
        
        // Trigger training after delay
        setTimeout(() => {
            triggerTraining('on_interaction', {
                interaction_type: 'form_submit',
                form_data: formDataObj
            });
        }, TrainingConfig.redirectDelay);
    }
    
    /**
     * Handle link clicks
     */
    function handleLinkClick(event) {
        const target = event.target.closest('a');
        if (!target || trainingTriggered) return;
        
        // Skip internal training links
        if (target.href.includes('training') || target.classList.contains('training-link')) {
            return;
        }
        
        // Check for suspicious link patterns
        if (isSuspiciousLink(target.href)) {
            event.preventDefault();
            
            logInteraction('suspicious_link_click', {
                href: target.href,
                linkText: target.textContent.trim(),
                linkClass: target.className
            });
            
            if (TrainingConfig.showWarningMessage) {
                showPhishingWarning();
            }
            
            setTimeout(() => {
                triggerTraining('on_interaction', {
                    interaction_type: 'link_click',
                    link_url: target.href,
                    link_text: target.textContent.trim()
                });
            }, TrainingConfig.redirectDelay);
        }
    }
    
    /**
     * Handle button clicks
     */
    function handleButtonClick(event) {
        const target = event.target;
        if (!target.matches('button, input[type="button"], input[type="submit"]') || trainingTriggered) {
            return;
        }
        
        // Skip training-related buttons
        if (target.classList.contains('training-btn') || target.id.includes('training')) {
            return;
        }
        
        logInteraction('button_click', {
            buttonId: target.id,
            buttonText: target.textContent || target.value,
            buttonType: target.type
        });
        
        // Trigger training for certain button types
        if (target.type === 'submit' || target.classList.contains('primary-action')) {
            setTimeout(() => {
                triggerTraining('on_interaction', {
                    interaction_type: 'button_click',
                    button_text: target.textContent || target.value
                });
            }, TrainingConfig.redirectDelay);
        }
    }
    
    /**
     * Handle file download attempts
     */
    function handleFileDownload(event) {
        const target = event.target.closest('a');
        if (!target || trainingTriggered) return;
        
        const href = target.href.toLowerCase();
        const downloadExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.zip', '.exe', '.dmg'];
        
        if (downloadExtensions.some(ext => href.includes(ext)) || target.hasAttribute('download')) {
            event.preventDefault();
            
            logInteraction('file_download_attempt', {
                fileName: target.textContent.trim(),
                fileUrl: target.href
            });
            
            if (TrainingConfig.showWarningMessage) {
                showPhishingWarning();
            }
            
            setTimeout(() => {
                triggerTraining('on_interaction', {
                    interaction_type: 'file_download',
                    file_url: target.href,
                    file_name: target.textContent.trim()
                });
            }, TrainingConfig.redirectDelay);
        }
    }
    
    /**
     * Check if a link appears suspicious
     */
    function isSuspiciousLink(url) {
        if (!url) return false;
        
        const suspiciousPatterns = [
            /bit\.ly/i,
            /tinyurl/i,
            /t\.co/i,
            /goo\.gl/i,
            /ow\.ly/i,
            /short\.link/i,
            /tiny\.cc/i,
            /urgentupdate/i,
            /securityalert/i,
            /verifyaccount/i,
            /accountsuspended/i,
            /clickhere/i,
            /updatepayment/i
        ];
        
        return suspiciousPatterns.some(pattern => pattern.test(url));
    }
    
    /**
     * Show phishing warning message
     */
    function showPhishingWarning() {
        // Create warning overlay
        const overlay = document.createElement('div');
        overlay.id = 'phishing-warning-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 10000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        `;
        
        const warningBox = document.createElement('div');
        warningBox.style.cssText = `
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        `;
        
        warningBox.innerHTML = `
            <div style="color: #e74c3c; font-size: 48px; margin-bottom: 20px;">⚠️</div>
            <h2 style="color: #e74c3c; margin: 0 0 15px 0;">Security Alert!</h2>
            <p style="color: #333; margin: 0 0 20px 0; line-height: 1.5;">
                You just interacted with a simulated phishing attack. 
                In a real scenario, this could have compromised your security.
            </p>
            <p style="color: #666; margin: 0 0 20px 0; font-size: 14px;">
                Redirecting to security training in a few seconds...
            </p>
            <div style="width: 100%; height: 4px; background: #eee; border-radius: 2px; overflow: hidden;">
                <div id="warning-progress" style="width: 0%; height: 100%; background: #3498db; transition: width 2s linear;"></div>
            </div>
        `;
        
        overlay.appendChild(warningBox);
        document.body.appendChild(overlay);
        
        // Animate progress bar
        setTimeout(() => {
            document.getElementById('warning-progress').style.width = '100%';
        }, 100);
        
        // Auto-remove after delay
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, TrainingConfig.redirectDelay + 500);
    }
    
    /**
     * Trigger training module
     */
    function triggerTraining(triggerType, additionalData = {}) {
        if (trainingTriggered) return;
        trainingTriggered = true;
        
        const requestData = {
            tracker_id: TrainingConfig.trackerId,
            user_id: TrainingConfig.userId,
            session_id: TrainingConfig.sessionId,
            trigger_type: triggerType,
            ...additionalData
        };
        
        // Make request to training redirect handler
        fetch('training_redirect.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.result === 'success' && data.training_redirect) {
                if (TrainingConfig.debug) {
                    console.log('Training redirect triggered:', data);
                }
                
                // Redirect to training
                setTimeout(() => {
                    window.location.href = data.training_url;
                }, data.delay_seconds * 1000 || 0);
            } else {
                if (TrainingConfig.debug) {
                    console.log('No training redirect configured');
                }
            }
        })
        .catch(error => {
            console.error('Training trigger error:', error);
        });
    }
    
    /**
     * Log interaction for analytics
     */
    function logInteraction(type, data = {}) {
        interactionCount++;
        
        const logData = {
            interaction_type: type,
            interaction_count: interactionCount,
            timestamp: Date.now(),
            page_url: window.location.href,
            user_agent: navigator.userAgent,
            ...data
        };
        
        if (TrainingConfig.debug) {
            console.log('Interaction logged:', logData);
        }
        
        // Send to analytics endpoint (non-blocking)
        fetch('spear/manager/training_analytics.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                tracker_id: TrainingConfig.trackerId,
                session_id: TrainingConfig.sessionId,
                log_data: logData
            })
        }).catch(() => {
            // Silently fail - analytics shouldn't break the experience
        });
    }
    
    /**
     * Handle page unload
     */
    function handlePageUnload() {
        if (interactionCount > 0) {
            logInteraction('page_unload', {
                time_on_page: Date.now() - (window.pageLoadTime || Date.now()),
                total_interactions: interactionCount
            });
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTrainingIntegration);
    } else {
        initTrainingIntegration();
    }
    
    // Track page load time
    window.pageLoadTime = Date.now();
    
    // Public API for manual triggering
    window.TrainingIntegration = {
        trigger: triggerTraining,
        log: logInteraction,
        config: TrainingConfig
    };
    
})();
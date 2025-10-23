// PWA Install Modal - Simple and Reliable
(function () {
    'use strict';

    let deferredPrompt = null;
    let installModal = null;

    // Check if app can be installed
    function canShowModal() {
        // Don't show if already installed
        if (window.matchMedia('(display-mode: standalone)').matches) return false;
        if (window.navigator.standalone === true) return false;

        // Don't show if user dismissed this session
        if (sessionStorage.getItem('pwa-dismissed') === 'true') return false;

        return true;
    }

    // Create install modal
    function createInstallModal() {
        if (!canShowModal()) return;
        
        // Remove any existing modal
        const existing = document.getElementById('pwa-install-modal');
        if (existing) existing.remove();

        installModal = document.createElement('div');
        installModal.id = 'pwa-install-modal';
        installModal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
            padding: 20px;
            box-sizing: border-box;
        `;

        installModal.innerHTML = `
            <div style="
                background: white;
                border-radius: 15px;
                padding: 30px;
                text-align: center;
                max-width: 400px;
                width: 100%;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                position: relative;
            ">
                <div style="
                    width: 80px;
                    height: 80px;
                    background: linear-gradient(135deg, #914738, #b85c4a);
                    border-radius: 50%;
                    margin: 0 auto 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 32px;
                ">📱</div>
                
                <h2 style="
                    color: #914738;
                    margin: 0 0 15px 0;
                    font-size: 24px;
                    font-weight: bold;
                ">Install 12Week App</h2>
                
                <p style="
                    color: #666;
                    margin: 0 0 25px 0;
                    line-height: 1.5;
                    font-size: 16px;
                ">Get quick access, offline features, and a better experience with our mobile app!</p>
                
                <div style="margin-bottom: 20px;">
                    <button id="modal-install-btn" style="
                        background: linear-gradient(135deg, #914738, #b85c4a);
                        color: white;
                        border: none;
                        padding: 15px 30px;
                        border-radius: 30px;
                        font-size: 16px;
                        font-weight: bold;
                        cursor: pointer;
                        margin-right: 10px;
                        box-shadow: 0 4px 15px rgba(145, 71, 56, 0.3);
                        transition: transform 0.2s ease;
                    " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                        Install Now
                    </button>
                </div>
                
                <button id="modal-dismiss-btn" style="
                    background: transparent;
                    color: #999;
                    border: 1px solid #ddd;
                    padding: 10px 20px;
                    border-radius: 20px;
                    font-size: 14px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                " onmouseover="this.style.borderColor='#914738'; this.style.color='#914738'" onmouseout="this.style.borderColor='#ddd'; this.style.color='#999'">
                    Maybe Later
                </button>
                
                <button id="modal-close-btn" style="
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: none;
                    border: none;
                    font-size: 24px;
                    color: #ccc;
                    cursor: pointer;
                    width: 30px;
                    height: 30px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                " onmouseover="this.style.color='#914738'" onmouseout="this.style.color='#ccc'">×</button>
            </div>
        `;

        // Add event listeners
        const installBtn = installModal.querySelector('#modal-install-btn');
        const dismissBtn = installModal.querySelector('#modal-dismiss-btn');
        const closeBtn = installModal.querySelector('#modal-close-btn');

        installBtn.onclick = function(e) {
            e.preventDefault();
            handleInstallClick();
        };

        dismissBtn.onclick = function(e) {
            e.preventDefault();
            sessionStorage.setItem('pwa-dismissed', 'true');
            closeModal();
        };

        closeBtn.onclick = function(e) {
            e.preventDefault();
            sessionStorage.setItem('pwa-dismissed', 'true');
            closeModal();
        };

        // Close modal when clicking outside
        installModal.onclick = function(e) {
            if (e.target === installModal) {
                sessionStorage.setItem('pwa-dismissed', 'true');
                closeModal();
            }
        };

        // Add to page
        document.body.appendChild(installModal);

        // Auto-close after 15 seconds
        setTimeout(function() {
            if (document.getElementById('pwa-install-modal')) {
                console.log('Auto-closing PWA modal after 15 seconds');
                closeModal();
            }
        }, 15000);

        console.log('PWA install modal created and displayed');
    }

    // Handle install click for different platforms
    function handleInstallClick() {
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);

        if (isIOS) {
            showIOSInstructions();
        } else if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function (result) {
                console.log('Install result:', result.outcome);
                if (result.outcome === 'accepted') {
                    closeModal();
                }
                deferredPrompt = null;
            });
        } else {
            showGeneralInstructions();
        }
    }

    // Show iOS-specific install instructions
    function showIOSInstructions() {
        // Update modal content for iOS instructions
        const modalContent = installModal.querySelector('div');
        modalContent.innerHTML = `
            <div style="
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #914738, #b85c4a);
                border-radius: 50%;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
            ">📱</div>
            
            <h2 style="
                color: #914738;
                margin: 0 0 15px 0;
                font-size: 24px;
                font-weight: bold;
            ">Install on iOS</h2>
            
            <div style="
                color: #666;
                margin: 0 0 25px 0;
                line-height: 1.6;
                font-size: 16px;
                text-align: left;
            ">
                <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                    <strong>Step 1:</strong> Tap the <strong>Share</strong> button ⬆️ at the bottom of Safari
                </div>
                <div style="margin-bottom: 15px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                    <strong>Step 2:</strong> Scroll down and select <strong>"Add to Home Screen"</strong>
                </div>
                <div style="padding: 15px; background: #f8f9fa; border-radius: 10px;">
                    <strong>Step 3:</strong> Tap <strong>"Add"</strong> to install the app
                </div>
            </div>
            
            <button onclick="document.getElementById('pwa-install-modal').remove();" style="
                background: linear-gradient(135deg, #914738, #b85c4a);
                color: white;
                border: none;
                padding: 15px 30px;
                border-radius: 30px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(145, 71, 56, 0.3);
            ">Got it!</button>
        `;
    }

    // Show general install instructions
    function showGeneralInstructions() {
        const modalContent = installModal.querySelector('div');
        modalContent.innerHTML = `
            <div style="
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, #914738, #b85c4a);
                border-radius: 50%;
                margin: 0 auto 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 32px;
            ">📱</div>
            
            <h2 style="
                color: #914738;
                margin: 0 0 15px 0;
                font-size: 24px;
                font-weight: bold;
            ">Install App</h2>
            
            <p style="
                color: #666;
                margin: 0 0 25px 0;
                line-height: 1.5;
                font-size: 16px;
            ">Look for the install option in your browser menu, or bookmark this page for quick access.</p>
            
            <button onclick="document.getElementById('pwa-install-modal').remove();" style="
                background: linear-gradient(135deg, #914738, #b85c4a);
                color: white;
                border: none;
                padding: 15px 30px;
                border-radius: 30px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                box-shadow: 0 4px 15px rgba(145, 71, 56, 0.3);
            ">OK</button>
        `;
    }

    // Close modal
    function closeModal() {
        if (installModal) {
            installModal.remove();
            installModal = null;
        }
        
        const existing = document.getElementById('pwa-install-modal');
        if (existing) existing.remove();
    }

    // Show modal after page loads
    function showModalAfterLoad() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(createInstallModal, 1000);
            });
        } else {
            setTimeout(createInstallModal, 1000);
        }
    }

    // Listen for install prompt availability (Android/Chrome)
    window.addEventListener('beforeinstallprompt', function (e) {
        console.log('PWA install prompt available');
        e.preventDefault();
        deferredPrompt = e;
        showModalAfterLoad();
    });

    // Handle successful installation
    window.addEventListener('appinstalled', function () {
        console.log('PWA installed successfully');
        closeModal();

        // Show success modal
        const successModal = document.createElement('div');
        successModal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        `;

        successModal.innerHTML = `
            <div style="
                background: white;
                border-radius: 15px;
                padding: 30px;
                text-align: center;
                max-width: 300px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            ">
                <div style="font-size: 48px; margin-bottom: 15px;">✅</div>
                <h3 style="color: #4CAF50; margin: 0 0 10px 0;">Success!</h3>
                <p style="color: #666; margin: 0;">App installed successfully!</p>
            </div>
        `;

        document.body.appendChild(successModal);
        setTimeout(() => successModal.remove(), 3000);
    });

    // For iOS, show modal immediately
    if (/iPad|iPhone|iPod/.test(navigator.userAgent) && !window.navigator.standalone) {
        showModalAfterLoad();
    }

})();
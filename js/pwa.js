if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        fetch('/wp-admin/admin-ajax.php?action=get_manifest')
            .then(response => response.json())
            .then(manifest => {
                if (manifest.success === false) {
                    console.log('PWA not enabled');
                    return;
                }
                // Register service worker with manifest data
                navigator.serviceWorker.register(swData.themeUrl + 'js/service-worker.js')
                    .then(function (registration) {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                        if (registration.active) {
                            registration.active.postMessage({
                                type: 'SET_THEME_URL',
                                themeUrl: swData.themeUrl,
                                manifest: manifest
                            });
                        }
                    })
                    .catch(function (err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            })
            .catch(error => {
                console.error('Error loading manifest:', error);
            });
    });
}

// Check if the app is already installed
window.addEventListener('appinstalled', (evt) => {
    console.log('NiertoCube-PWA was installed successfully');
});

// 'beforeinstallprompt' event to handle installation
let deferredPrompt;
window.addEventListener('beforeinstallprompt', (e) => {
    // Prevent Chrome 67 and earlier from automatically showing the prompt
    e.preventDefault();
    // Stash the event so it can be triggered later
    deferredPrompt = e;
    // Update UI to notify the user they can add to home screen
    showInstallPromotion();
});

function showInstallPromotion() {
    if (!document.cookie.includes('nierto_cube_cookie_notice_accepted=1')) {
        return; // Don't show install promotion if cookies haven't been accepted
    }

    const bannerImage = niertoCubePWA.installBanner;
    const banner = document.createElement('div');
    banner.id = 'DownloadAsApp';
    banner.style.position = 'fixed';
    banner.style.bottom = '0';
    banner.style.left = '0';
    banner.style.right = '0';
    banner.style.padding = '10px';
    banner.style.backgroundColor = '#f0f0f0';
    banner.style.textAlign = 'center';
    banner.style.zIndex = '9998'; // Set z-index lower than cookie notice but higher than other elements

    if (bannerImage) {
        const img = document.createElement('img');
        img.src = bannerImage;
        img.alt = 'Install this website as App';
        img.style.maxWidth = '100%';
        img.style.cursor = 'pointer';
        img.onclick = installApp;
        banner.appendChild(img);
    } else {
        banner.innerHTML = `
            <p id="installNotice">Install this Site as an App</p>
            <button id="installNoticeButton" onclick="installApp()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; cursor: pointer;">Install</button>
        `; // the banner uses a p with id installNotice and a button with id installNoticeButton
    }

    const closeButton = document.createElement('button');
    closeButton.innerHTML = '&times;';
    closeButton.style.position = 'absolute';
    closeButton.style.top = '5px';
    closeButton.style.right = '5px';
    closeButton.style.background = 'none';
    closeButton.style.border = 'none';
    closeButton.style.fontSize = '20px';
    closeButton.style.cursor = 'pointer';
    closeButton.onclick = function () {
        document.body.removeChild(banner);
    };
    banner.appendChild(closeButton);

    document.body.appendChild(banner);
}

// Function to be called when the user wants to install the app
function installApp() {
    // Hide the app provided install promotion
    hideInstallPromotion();
    // Show the install prompt
    deferredPrompt.prompt();
    // Wait for the user to respond to the prompt
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
            console.log('User accepted the install prompt');
        } else {
            console.log('User dismissed the install prompt');
        }
        deferredPrompt = null;
    });
}

// Function to hide the install promotion
function hideInstallPromotion() {
    const banner = document.getElementById('DownloadAsApp');
    if (banner) {
        banner.style.display = 'none';
    }
}
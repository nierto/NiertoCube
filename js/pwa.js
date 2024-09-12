if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/wp-content/themes/nierto-cube/service-worker.js').then(function (registration) {
            console.log('ServiceWorker registration successful with scope: ', registration.scope);
        }, function (err) {
            console.log('ServiceWorker registration failed: ', err);
        });
    });
}

// Check if the app is already installed
window.addEventListener('appinstalled', (evt) => {
    console.log('NiertoCube is installed');
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
    banner.style.zIndex = '9999';

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
            <p>Install this Site as an App for a better experience!</p>
            <button onclick="installApp()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; cursor: pointer;">Install</button>
        `;
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
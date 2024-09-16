if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register(swData.themeUrl + 'service-worker.js')
            .then(function (registration) {
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
                if (registration.active) {
                    registration.active.postMessage({
                        type: 'SET_THEME_URL',
                        themeUrl: swData.themeUrl
                    });
                }
            }, function (err) {
                console.log('ServiceWorker registration failed: ', err);
            });
    });
}
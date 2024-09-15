// clear-cache.js
(function () {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.ready.then(function (registration) {
            registration.active.postMessage({ action: 'clearCache' });
        });
    }
})();
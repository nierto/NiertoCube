// service-worker.js
const CACHE_NAME = 'nierto-cube-cache-v1';
const themeUrl = getThemeUrl();

const urlsToCache = [
    '/',
    themeUrl + 'css/all-styles.css',
    themeUrl + 'css/cube.css',
    themeUrl + 'css/keyframes.css',
    themeUrl + 'css/logo.css',
    themeUrl + 'css/navigation.css',
    themeUrl + 'css/rootstyle.css',
    themeUrl + 'css/screensizes.css',
    themeUrl + 'js/config.js',
    themeUrl + 'js/cube.js',
    themeUrl + 'js/service-worker.js',
    themeUrl + 'js/pwa.js',
    themeUrl + 'style.css',
    themeUrl + 'inc/aria-functionality.php',
    themeUrl + 'inc/caching-functionality.php',
    themeUrl + 'inc/cookies-functionality.php',
    themeUrl + 'inc/google-functionality.php',
    themeUrl + 'inc/metatags-functionality.php',
    themeUrl + 'inc/santitation-functionality.php',
    themeUrl + 'inc/structureddata-functionality.php',
    themeUrl + 'inc/valkey-functionality.php',
    themeUrl + 'inc/errors-functionality.php',
    themeUrl + 'inc/security-functionality.php',
    themeUrl + 'footer.php',
    themeUrl + 'functions.php',
    themeUrl + 'header.php',
    themeUrl + 'index.php',
    themeUrl + 'page-template-iframe.php',
];

self.addEventListener('install', function (event) {
    event.waitUntil(
        fetch('/wp-admin/admin-ajax.php?action=is_pwa_enabled')
            .then(response => response.json())
            .then(data => {
                if (!data.enabled) {
                    self.skipWaiting();
                    return;
                }
                // Rest of your service worker installation code...
            })
    );
});



self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function (cache) {
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('fetch', function (event) {
    if (event.request.headers.get('DNT') === '1') {
        // Do not cache if "Do Not Track" is enabled
        return fetch(event.request);
    }    
    event.respondWith(
        caches.match(event.request)
            .then(function (response) {
                if (response) {
                    return response;
                }

                return fetch(event.request).then(
                    function (response) {
                        // Check if we received a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // IMPORTANT: Clone the response. A response is a stream
                        // and because we want the browser to consume the response
                        // as well as the cache consuming the response, we need
                        // to clone it so we have two streams.
                        var responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then(function (cache) {
                                // Cache any successful requests
                                if (event.request.url.indexOf('/wp-json/nierto-cube/v1/face-content/') !== -1) {
                                    cache.put(event.request, responseToCache);
                                }
                            });

                        return response;
                    }
                );
            })
    );
});

// Clean up old caches
self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.map(function (cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// get the theme url
function getThemeUrl() {
    return '/wp-content/themes/' + window.themeData.themeName + '/';
}
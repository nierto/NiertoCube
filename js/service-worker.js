// service-worker.js
const CACHE_NAME = 'nierto-cube-cache-v1';
const urlsToCache = [
    '/',
    '/wp-content/themes/nierto-cube/css/all-styles.css',
    '/wp-content/themes/nierto-cube/css/cube.css',
    '/wp-content/themes/nierto-cube/css/keyframes.css',
    '/wp-content/themes/nierto-cube/css/logo.css',
    '/wp-content/themes/nierto-cube/css/navigation.css',
    '/wp-content/themes/nierto-cube/css/rootstyle.css',
    '/wp-content/themes/nierto-cube/css/screensizes.css',
    '/wp-content/themes/nierto-cube/js/config/config.js.php',
    '/wp-content/themes/nierto-cube/js/cube.js',
    '/wp-content/themes/nierto-cube/js/service-worker.js',
    '/wp-content/themes/nierto-cube/style.css',
    '/wp-content/themes/nierto-cube/inc/caching-functionality.php',
    '/wp-content/themes/nierto-cube/inc/google-functionality.php',
    '/wp-content/themes/nierto-cube/inc/metatags-functionality.php',
    '/wp-content/themes/nierto-cube/inc/santitation-functionality.php',
    '/wp-content/themes/nierto-cube/inc/structureddata-functionality.php',
    '/wp-content/themes/nierto-cube/inc/valkey-functionality.php',
    '/wp-content/themes/nierto-cube/footer.php',
    '/wp-content/themes/nierto-cube/functions.php',
    '/wp-content/themes/nierto-cube/header.php',
    '/wp-content/themes/nierto-cube/index.php',
    '/wp-content/themes/nierto-cube/page-template-iframe.php',
];

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
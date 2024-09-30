// service-worker.js
const CACHE_NAME = 'nierto-cube-cache-v1';
let themeUrl;

self.addEventListener('message', function (event) {
    if (event.data.action === 'clearCache') {
        caches.keys().then(function (names) {
            for (let name of names) {
                caches.delete(name);
            }
        });
    } else if (event.data.type === 'SET_THEME_URL') {
        themeUrl = event.data.themeUrl;
    }
});

self.addEventListener('install', function (event) {
    event.waitUntil(
        fetch('/wp-admin/admin-ajax.php?action=is_pwa_enabled')
            .then(response => response.json())
            .then(data => {
                if (!data.enabled) {
                    return self.skipWaiting();
                }

                return caches.open(CACHE_NAME).then(function (cache) {
                    console.log('Opened cache');

                    // Fetch and cache the theme URL dynamically
                    return fetch('/wp-admin/admin-ajax.php?action=get_theme_url')
                        .then(response => response.json())
                        .then(data => {
                            const themeUrl = data.theme_url;

                            // Define the URLs to cache
                            const urlsToCache = [
                                '/',
                                themeUrl + 'css/all-styles.css',
                                themeUrl + 'css/cube.css',
                                themeUrl + 'css/keyframes.css',
                                themeUrl + 'css/logo.css',
                                themeUrl + 'css/navigation.css',
                                themeUrl + 'css/rootstyle.css',
                                themeUrl + 'css/screensizes.css',
                                themeUrl + 'js/cube.js',
                                themeUrl + 'js/pwa.js',
                                themeUrl + 'js/clear-cache.js',
                                themeUrl + 'style.css',
                                themeUrl + 'index.php',
                                themeUrl + 'page-template-iframe.php',
                            ];

                            // Cache all the URLs
                            return Promise.all(
                                urlsToCache.map(function (url) {
                                    return fetch(url, {
                                        credentials: 'same-origin',
                                        headers: {
                                            'X-WP-Nonce': data.nonce // Use the nonce provided by WordPress
                                        }
                                    }).then(function (response) {
                                        // Check if we received a valid response
                                        if (!response || response.status !== 200 || response.type !== 'basic') {
                                            return;
                                        }

                                        return cache.put(url, response);
                                    }).catch(function (error) {
                                        console.error('Caching failed for', url, error);
                                    });
                                })
                            );
                        });
                });
            })
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('message', function (event) {
    if (event.data.action === 'clearCache') {
        caches.keys().then(function (names) {
            for (let name of names) {
                caches.delete(name);
            }
        });
    }
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
// performance-optimizer.js

// Prevent double initialization
if (typeof window.niertoCubePerformance === 'undefined') {

    class NiertoCubePerformance {
        constructor() {
            // Preserve any existing instance's cache if it exists
            this.contentCache = window.niertoCubePerf?.contentCache || new Map();
            this.pendingFetches = window.niertoCubePerf?.pendingFetches || new Map();
            this.resourceVersion = window.niertoCubeData?.resourceVersion || '1.0';
            this._initialized = false;

            // Check for existing functionality before initializing
            if (!window.niertoCubePerf) {
                this.init();
            }
        }

        init() {
            // Only set up if not already initialized
            if (!this._initialized) {
                this.setupResourceLoading();
                this.setupLazyLoading();
                this.setupPerformanceMonitoring();
                this.setupServiceWorkerSupport();
                this._initialized = true;
            }
        }

        setupResourceLoading() {
            // Preload critical resources
            document.querySelectorAll('link[rel="preload"]:not([data-nierto-loaded])').forEach(link => {
                link.setAttribute('data-nierto-loaded', 'true');
                if (link.as === 'style') {
                    this.loadStyle(link.href);
                } else if (link.as === 'script') {
                    this.loadScript(link.href);
                }
            });

            // Add version to dynamic resource loads if not already modified
            if (!window._niertoCubeFetchModified) {
                const originalFetch = window.fetch;
                window.fetch = async (url, options) => {
                    if (url.includes('/wp-json/niertocube/')) {
                        url = this.addVersionToUrl(url);
                    }
                    return originalFetch(url, options);
                };
                window._niertoCubeFetchModified = true;
            }
        }

        async loadStyle(url) {
            try {
                if (!document.querySelector(`link[href*="${url}"]`)) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = this.addVersionToUrl(url);
                    link.media = 'print';
                    link.onload = () => { link.media = 'all'; };
                    document.head.appendChild(link);
                }
            } catch (error) {
                console.error('Error loading style:', error);
            }
        }

        async loadScript(url) {
            try {
                if (!document.querySelector(`script[src*="${url}"]`)) {
                    const script = document.createElement('script');
                    script.src = this.addVersionToUrl(url);
                    script.defer = true;
                    document.head.appendChild(script);
                }
            } catch (error) {
                console.error('Error loading script:', error);
            }
        }

        setupLazyLoading() {
            if (!document.querySelector('[data-nierto-lazy-loaded="true"]')) {
                if ('loading' in HTMLImageElement.prototype) {
                    // Browser supports native lazy loading
                    document.querySelectorAll('img:not([loading]):not([data-nierto-lazy-loaded])').forEach(img => {
                        img.loading = 'lazy';
                        img.setAttribute('data-nierto-lazy-loaded', 'true');
                    });
                } else {
                    // Fallback for browsers that don't support native lazy loading
                    this.setupIntersectionObserver();
                }

                // Add lazy loading to iframes
                document.querySelectorAll('iframe:not([loading]):not([data-nierto-lazy-loaded])').forEach(iframe => {
                    iframe.loading = 'lazy';
                    iframe.setAttribute('data-nierto-lazy-loaded', 'true');
                });
            }
        }

        setupIntersectionObserver() {
            if (!window._niertoIntersectionObserver) {
                const options = {
                    root: null,
                    rootMargin: '50px',
                    threshold: 0.1
                };

                window._niertoIntersectionObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            if (img.dataset.src) {
                                img.src = img.dataset.src;
                                if (img.dataset.srcset) {
                                    img.srcset = img.dataset.srcset;
                                }
                                img.setAttribute('data-nierto-lazy-loaded', 'true');
                                observer.unobserve(img);
                            }
                        }
                    });
                }, options);

                document.querySelectorAll('img[data-src]:not([data-nierto-lazy-loaded])').forEach(img => {
                    window._niertoIntersectionObserver.observe(img);
                });
            }
        }

        setupPerformanceMonitoring() {
            if (!window._niertoPerformanceInitialized && 'PerformanceObserver' in window) {
                // Monitor resource timing
                const resourceObserver = new PerformanceObserver(list => {
                    list.getEntries().forEach(entry => {
                        this.logPerformanceMetric('resource', entry);
                    });
                });
                resourceObserver.observe({ entryTypes: ['resource'] });

                // Monitor layout shifts
                const layoutObserver = new PerformanceObserver(list => {
                    list.getEntries().forEach(entry => {
                        if (entry.value > 0) {
                            this.logPerformanceMetric('layout-shift', entry);
                        }
                    });
                });
                layoutObserver.observe({ entryTypes: ['layout-shift'] });

                // Monitor long tasks
                const longTaskObserver = new PerformanceObserver(list => {
                    list.getEntries().forEach(entry => {
                        this.logPerformanceMetric('long-task', entry);
                    });
                });
                longTaskObserver.observe({ entryTypes: ['longtask'] });

                window._niertoPerformanceInitialized = true;
            }

            // Track user timing
            if (!window._niertoUserTimingInitialized) {
                window.addEventListener('DOMContentLoaded', () => {
                    this.measureTimeToInteractive();
                });
                window._niertoUserTimingInitialized = true;
            }
        }

        setupServiceWorkerSupport() {
            // Check for existing service worker
            if ('serviceWorker' in navigator &&
                window.niertoCubeData?.enablePWA &&
                !navigator.serviceWorker.controller) {

                window.addEventListener('load', async () => {
                    try {
                        const registration = await navigator.serviceWorker.register('/service-worker.js', {
                            scope: '/'
                        });

                        registration.addEventListener('updatefound', () => {
                            this.handleServiceWorkerUpdate(registration);
                        });
                    } catch (error) {
                        console.error('ServiceWorker registration failed:', error);
                    }
                });
            }
        }

        handleServiceWorkerUpdate(registration) {
            const newWorker = registration.installing;

            newWorker.addEventListener('statechange', () => {
                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                    this.showUpdateNotification();
                }
            });
        }

        showUpdateNotification() {
            if (!document.querySelector('.update-notification')) {
                const notification = document.createElement('div');
                notification.className = 'update-notification';
                notification.innerHTML = `
                <p>A new version is available!</p>
                <button onclick="location.reload()">Update</button>
            `;
                document.body.appendChild(notification);
            }
        }

        async fetchContent(slug) {
            // If cube.js's content loading is in progress, defer to it
            if (window.isTransitioning) {
                return new Promise((resolve) => {
                    setTimeout(() => {
                        this.fetchContent(slug).then(resolve);
                    }, 100);
                });
            }

            // Check if we have a pending fetch for this slug
            if (this.pendingFetches.has(slug)) {
                return this.pendingFetches.get(slug);
            }

            // Check cache first
            if (this.contentCache.has(slug)) {
                return this.contentCache.get(slug);
            }

            // Create new fetch promise
            const fetchPromise = (async () => {
                try {
                    const response = await fetch(`/wp-json/niertocube/v1/content/${slug}`, {
                        headers: {
                            'Accept': 'application/json',
                            'Cache-Control': 'max-age=3600'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Content fetch failed');
                    }

                    const data = await response.json();
                    this.contentCache.set(slug, data);
                    this.pendingFetches.delete(slug);
                    return data;
                } catch (error) {
                    this.pendingFetches.delete(slug);
                    throw error;
                }
            })();

            // Store the pending fetch
            this.pendingFetches.set(slug, fetchPromise);
            return fetchPromise;
        }

        addVersionToUrl(url) {
            // Skip if URL already has a version
            if (url.includes('v=')) return url;

            const separator = url.includes('?') ? '&' : '?';
            return `${url}${separator}v=${this.resourceVersion}`;
        }

        logPerformanceMetric(type, entry) {
            if (!window.niertoCubeData?.debugMode) return;

            const metric = {
                type,
                timestamp: performance.now(),
                ...entry.toJSON()
            };

            if (window.niertoCubeData?.logEndpoint) {
                fetch(window.niertoCubeData.logEndpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(metric)
                }).catch(console.error);
            }
        }

        measureTimeToInteractive() {
            const timing = performance.timing;
            const interactive = timing.domInteractive - timing.navigationStart;
            this.logPerformanceMetric('time-to-interactive', { value: interactive });
        }

        clearCache() {
            this.contentCache.clear();
            if ('caches' in window) {
                caches.keys().then(cacheNames => {
                    cacheNames.forEach(cacheName => {
                        if (cacheName.startsWith('niertocube-')) {
                            caches.delete(cacheName);
                        }
                    });
                });
            }
        }
    }

    // Safe initialization that preserves existing instance if it exists
    window.niertoCubePerf = window.niertoCubePerf || new NiertoCubePerformance();

} // end if typeof check
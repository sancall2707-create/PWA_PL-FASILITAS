const CACHE_NAME = 'pwa-peminjaman-v3';

// Hanya cache aset statis publik yang aman & immutable (Vite build assets)
const PUBLIC_STATIC_ASSETS = [
  '/manifest.json',
  '/favicon.ico',
  '/icons/icon-512.svg',
];

// Install Event: Pre-cache static assets & skip waiting
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PUBLIC_STATIC_ASSETS);
    }).then(() => self.skipWaiting())
  );
});

// Activate Event: Clean ALL old caches immediately & claim clients
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            console.log('🧹 Evicting old cache:', cache);
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event: Smart Cache Strategy
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // 1. SECURITY RULE: JANGAN MENG-CACHE API ATAU ROUTE AUTHENTICATION/DATA
  if (
    url.pathname.startsWith('/api/') ||
    url.pathname.includes('/login') ||
    url.pathname.includes('/register') ||
    url.pathname.includes('/booking') ||
    url.pathname.includes('/admin') ||
    event.request.method !== 'GET'
  ) {
    // Network Only
    event.respondWith(fetch(event.request));
    return;
  }

  // 2. HTML PAGE NAVIGATION (Root `/` or HTML pages): NETWORK-FIRST
  // Memastikan versi HTML terbaru selalu diambil dari server (mencegah mixed content/stale HTML)
  if (event.request.mode === 'navigate' || url.pathname === '/') {
    event.respondWith(
      fetch(event.request)
        .then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const copy = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));
          }
          return networkResponse;
        })
        .catch(() => {
          // Fallback to cached HTML if offline
          return caches.match(event.request);
        })
    );
    return;
  }

  // 3. VITE BUILD ASSET HASHES (`/build/assets/*`): CACHE-FIRST WITH NETWORK FALLBACK
  // Aset Vite memiliki hash unik pada filename (misal app-BmlOQHeX.js), aman di-cache
  if (url.pathname.startsWith('/build/assets/')) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) {
          return cachedResponse;
        }
        return fetch(event.request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const copy = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, copy));
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // Default: Network First
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});

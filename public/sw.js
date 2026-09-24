const CACHE_NAME = 'pwa-peminjaman-v2';

// Hanya cache aset statis publik yang aman
const PUBLIC_ASSETS = [
  '/',
  '/manifest.json',
  '/favicon.ico',
];

// Install Event: Pre-cache static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      return cache.addAll(PUBLIC_ASSETS);
    }).then(() => self.skipWaiting())
  );
});

// Activate Event: Clean old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((cache) => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch Event: Network-first for API & Sensitive Routes, Cache-first for Static Assets
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // SECURITY RULE: JANGAN MENG-CACHE RESPONS API ATAU ROUTE SENSITIF!
  if (
    url.pathname.startsWith('/api/') ||
    url.pathname.includes('login') ||
    url.pathname.includes('register') ||
    url.pathname.includes('booking') ||
    url.pathname.includes('admin') ||
    event.request.method !== 'GET'
  ) {
    // Selalu ambil dari jaringan (Network Only) untuk API & Data Sensitif
    event.respondWith(fetch(event.request));
    return;
  }

  // Untuk Aset Statis Publik (.js, .css, images, manifest) -> Cache First dengan Network Fallback
  event.respondWith(
    caches.match(event.request).then((cachedResponse) => {
      if (cachedResponse) {
        return cachedResponse;
      }
      return fetch(event.request).then((networkResponse) => {
        // Hanya cache respons 200 OK untuk aset statis
        if (
          networkResponse &&
          networkResponse.status === 200 &&
          (url.pathname.endsWith('.js') ||
           url.pathname.endsWith('.css') ||
           url.pathname.endsWith('.png') ||
           url.pathname.endsWith('.jpg') ||
           url.pathname.endsWith('.svg') ||
           url.pathname.endsWith('.ico'))
        ) {
          const responseToCache = networkResponse.clone();
          caches.open(CACHE_NAME).then((cache) => {
            cache.put(event.request, responseToCache);
          });
        }
        return networkResponse;
      });
    })
  );
});

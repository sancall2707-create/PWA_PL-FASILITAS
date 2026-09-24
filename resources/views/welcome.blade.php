<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <!-- Primary Meta Tags -->
    <title>Peminjaman Fasilitas PL Deltamas</title>
    <meta name="title" content="Peminjaman Fasilitas PL Deltamas">
    <meta name="description" content="Aplikasi PWA Peminjaman Kendaraan dan Fasilitas Tempat Sekolah Pangudi Luhur Deltamas">
    <meta name="theme-color" content="#1e40af">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Peminjaman Fasilitas PL Deltamas">
    <meta property="og:description" content="Aplikasi PWA Peminjaman Kendaraan dan Fasilitas Tempat Sekolah Pangudi Luhur Deltamas">
    <meta property="og:image" content="/icons/icon-512.svg">

    <!-- iOS Meta Tags & Mobile App Support -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="PL Deltamas">
    <link rel="apple-touch-icon" href="/icons/icon-512.svg">

    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/svg+xml" href="/icons/icon-512.svg">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased selection:bg-blue-600 selection:text-white">
    <div id="app"></div>

    <!-- PWA Installation & Service Worker Registration Script -->
    <script>
      let deferredPrompt;

      // Register Service Worker IMMEDIATELY (not waiting for load event)
      if ('serviceWorker' in navigator) {
        // Cache busting: append version query to sw.js
        const swVersion = '1';
        navigator.serviceWorker.register(`/sw.js?v=${swVersion}`)
          .then((registration) => {
            console.log('✅ ServiceWorker registered with scope:', registration.scope);
            
            // Check for updates every time page loads
            registration.update().catch(err => console.warn('SW update check failed:', err));
          })
          .catch((error) => {
            console.error('❌ ServiceWorker registration failed:', error);
          });
      }

      // Capture install prompt event for "Add to Home Screen"
      window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        // Broadcast custom event so Vue components can render install button
        window.dispatchEvent(new CustomEvent('pwa-install-available'));
      });

      // Global function to trigger install prompt
      window.installPWA = () => {
        if (deferredPrompt) {
          deferredPrompt.prompt();
          deferredPrompt.userChoice.then((choiceResult) => {
            if (choiceResult.outcome === 'accepted') {
              console.log('User accepted the PWA install prompt');
            } else {
              console.log('User dismissed the PWA install prompt');
            }
            deferredPrompt = null;
          });
        }
      };
    </script>
</body>
</html>

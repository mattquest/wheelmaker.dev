<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex">
    <meta name="description" content="Matt Rademacher — software engineer in Austin, Texas. AI product engineering at Meta, and small things on the side.">
    <title>Matt Rademacher — Austin, Texas</title>
    <link rel="icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/site.css">
    @if(config('app.recaptcha_site_key'))
    <meta name="recaptcha-site-key" content="{{ config('app.recaptcha_site_key') }}">
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('app.recaptcha_site_key') }}"></script>
    @endif
</head>
<body>
    <header class="site-header">
        <div class="wrap header-inner">
            <a class="wordmark" href="/">Matt Rademacher</a>
            <nav class="site-nav" aria-label="Sections">
                <a href="#now">Now</a>
                <a href="#work">Work</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </nav>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer">
        <div class="wrap footer-inner">
            <p>© {{ date('Y') }} Matt Rademacher · Austin, Texas · Wheelmaker, LLC</p>
            <p class="footer-note">Built with Laravel. No trackers, no cookies for ads, nothing to accept.</p>
        </div>
    </footer>

    <script src="/js/site.js" defer></script>
</body>
</html>

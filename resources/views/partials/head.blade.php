<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<meta name="theme-color" content="#5e72e4" />
<meta name="msapplication-navbutton-color" content="#5e72e4" />
<meta name="apple-mobile-web-app-status-bar-style" content="#5e72e4" />

<!-- SEO Meta Tags -->
<meta name="description" content="Extract payment codes from DAS and Brazilian bill documents automatically. Fast, secure, and reliable payment code extraction service.">
<meta name="keywords" content="payment code, barcode decoder, DAS, Brazilian bills, payment extraction">
<meta name="author" content="Victor B. Fiamoncini">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:title" content="My Bar Decoder">
<meta property="og:description" content="Extract payment codes from DAS and Brazilian bill documents automatically.">
<meta property="og:image" content="{{ asset('logo.png') }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ url('/') }}">
<meta property="twitter:title" content="My Bar Decoder">
<meta property="twitter:description" content="Extract payment codes from DAS and Brazilian bill documents automatically.">
<meta property="twitter:image" content="{{ asset('logo.png') }}">

<link rel="canonical" href="{{ url('/') }}">

<title>My Bar Decoder</title>

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

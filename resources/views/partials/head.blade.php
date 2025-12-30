<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<meta name="theme-color" content="#5e72e4" />
<meta name="msapplication-navbutton-color" content="#5e72e4" />
<meta name="apple-mobile-web-app-status-bar-style" content="#5e72e4" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ asset('favicon-96x96.png') }}">
<link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<meta name="theme-color" content="#5e72e4" />
<meta name="msapplication-navbutton-color" content="#5e72e4" />
<meta name="apple-mobile-web-app-status-bar-style" content="#5e72e4" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon.png') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

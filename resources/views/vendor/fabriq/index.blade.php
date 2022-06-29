<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fabriq CMS</title>
    <style>
        @font-face {
            font-family: 'Inter var';
            font-weight: 100 900;
            font-display: swap;
            font-style: normal;
            font-named-instance: 'Regular';
            src: url('/fonts/Inter-roman.var.woff2') format("woff2");
        }
        @font-face {
            font-family: 'Inter var';
            font-weight: 100 900;
            font-display: swap;
            font-style: italic;
            font-named-instance: 'Italic';
            src: url('/fonts/Inter-italic.var.woff2') format("woff2");
        }
    </style>
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/images/favicon-16x16.png">
    <link rel="manifest" href="/images/site.webmanifest">
    <link rel="mask-icon" href="/images/safari-pinned-tab.svg" color="#0b3b5b">
    <link rel="shortcut icon" href="/images/favicon.ico">
    <meta name="msapplication-TileColor" content="#0b3b5b">
    <meta name="msapplication-config" content="/images/browserconfig.xml">
    <meta name="theme-color" content="#0b3b5b">
    <meta name="og:image" content="https://media.fabriq-cms.se/public/fabriq-og-image-1200.jpg">
    @vite(['resources/js/main.js'])
    <script>
        window.fabriqCms = {
            pusher: {
                appId: "{{ config('broadcasting.connections.pusher.app_id') }}",
                key: "{{ config('broadcasting.connections.pusher.key') }}",
                ws_prefix: "{{ config('fabriq.ws_prefix') }}"
            },
            @if(Auth::check())
            userRoles: @json(auth()->user()->roles->pluck('name')),
            @endif
        }
    </script>

</head>
<body>
    <div id="app"></div>
</body>
</html>

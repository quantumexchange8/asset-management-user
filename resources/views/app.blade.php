<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        @if (App::environment('production') || App::environment('staging'))
            <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        @endif

        <title inertia>{{ config('app.name', 'Volta Asia') }}</title>

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('img/logo_mark.png')}}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/logo_mark.png') }}" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-surface-50 dark:bg-surface-ground transition-all duration-200">
        @inertia
    </body>
</html>

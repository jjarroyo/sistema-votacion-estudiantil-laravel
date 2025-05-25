<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $header ?? config('app.name', 'Laravel') }} - Sistema de Votación</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
   @fluxAppearance
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-200 bg-gray-100 dark:bg-zinc-900">
    {{ $slot }}
    @fluxScripts
    @stack('scripts')
</body>
</html>
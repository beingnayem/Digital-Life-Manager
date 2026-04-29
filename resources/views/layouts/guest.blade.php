<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Digital Life Manager') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased overflow-x-hidden">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-4 sm:pt-0 bg-slate-50 px-4">
            <div class="text-center">
                <a href="/" class="inline-flex items-center justify-center">
                    <x-application-logo class="app-logo-guest" />
                </a>
                <h1 class="mt-2 text-lg font-bold text-slate-900">{{ config('app.name', 'Digital Life Manager') }}</h1>
                <p class="mt-0.5 text-xs text-slate-600">Your complete personal organizer</p>
            </div>

            <div class="card w-full sm:max-w-md mt-3">
                <div class="card-body">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>

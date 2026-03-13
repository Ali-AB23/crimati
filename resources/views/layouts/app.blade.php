<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CRIMATI - @yield('title', 'Dashboard')</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<!-- Alpine.js gère l'état 'sidebarOpen' pour toute l'application -->
<body x-data="{ sidebarOpen: false }" class="font-sans antialiased text-gray-900 bg-gray-50 flex h-screen overflow-hidden">
    
    <!-- L'OVERLAY (Fond noir semi-transparent) : Apparaît uniquement sur mobile quand le menu est ouvert -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-20 bg-gray-900/80 lg:hidden"
         @click="sidebarOpen = false" 
         style="display: none;">
    </div>

    <!-- SIDEBAR -->
    @include('layouts.partials.sidebar')

    <!-- COLONNE DE DROITE (Topbar + Contenu) -->
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50 w-full">
        
        <!-- TOPBAR -->
        @include('layouts.partials.topbar')

        <!-- CONTENU PRINCIPAL DYNAMIQUE -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-4 sm:p-6 lg:p-8">
            {{ $slot }}
        </main>

    </div>

</body>
</html>
@php
    use App\Enums\UserRole;
    $role = Auth::user()->role->value;
    $isAdminOrInv = in_array($role,[UserRole::ADMIN_IT->value, UserRole::INVENTORISTE->value]);
    $isAdmin = $role === UserRole::ADMIN_IT->value;
@endphp

<!-- La Sidebar : Gère la translation sur mobile, et la largeur (w-64 / w-20) sur Desktop -->
<aside :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'w-64': !sidebarMini, 'w-20': sidebarMini }"
       class="fixed inset-y-0 left-0 z-30 bg-white border-r border-gray-200 flex flex-col h-full shrink-0 transition-all duration-300 ease-in-out lg:static lg:translate-x-0">
    
    <!-- LOGO ET BOUTON FERMER (Mobile) -->
    <div class="h-20 flex items-center px-6" :class="sidebarMini ? 'justify-center px-0' : 'justify-between'">
        <!-- C le logo initial quand réduit, CRIMATI quand ouvert -->
        <span x-show="!sidebarMini" class="text-2xl font-extrabold text-gray-900 tracking-tight transition-opacity duration-300">CRIMATI</span>
        <span x-show="sidebarMini" style="display: none;" class="text-2xl font-extrabold text-green-700 bg-green-50 p-2 rounded-lg tracking-tight">C</span>
        
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <!-- NAVIGATION -->
    <nav class="flex-1 px-3 py-4 space-y-2 overflow-y-auto overflow-x-hidden" :class="sidebarMini ? 'items-center flex flex-col' : ''">
        
        <!-- 1. Dashboard -->
        <a href="{{ route('dashboard') }}" :title="sidebarMini ? 'Tableau de bord' : ''" class="flex items-center py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" :class="sidebarMini ? 'justify-center px-0 w-12' : 'px-4'">
            <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-green-700' : 'text-gray-400' }}" :class="!sidebarMini ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
            <span x-show="!sidebarMini" class="transition-opacity duration-300">Tableau de bord</span>
        </a>

        <!-- 2. Matériels -->
        <a href="{{ route('assets.index') }}" :title="sidebarMini ? 'Matériels' : ''" class="flex items-center py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('assets.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" :class="sidebarMini ? 'justify-center px-0 w-12' : 'px-4'">
            <svg class="w-5 h-5 {{ request()->routeIs('assets.*') ? 'text-green-700' : 'text-gray-400' }}" :class="!sidebarMini ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span x-show="!sidebarMini" class="transition-opacity duration-300">Matériels</span>
        </a>

        <!-- 3. Mouvements (Admin & Inventoriste) -->
        @if($isAdminOrInv)
            <a href="{{ route('movements.index') }}" :title="sidebarMini ? 'Historique (Mouvements)' : ''" class="flex items-center py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('movements.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" :class="sidebarMini ? 'justify-center px-0 w-12' : 'px-4'">
                <svg class="w-5 h-5 {{ request()->routeIs('movements.*') ? 'text-green-700' : 'text-gray-400' }}" :class="!sidebarMini ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                <span x-show="!sidebarMini" class="transition-opacity duration-300 whitespace-nowrap">Historique (Mouv.)</span>
            </a>
        @endif

        <!-- 4. Réclamations -->
        @if($role !== \App\Enums\UserRole::INVENTORISTE->value)
            <a href="{{ route('tickets.index') }}" :title="sidebarMini ? 'Réclamations' : ''" class="flex items-center py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('tickets.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" :class="sidebarMini ? 'justify-center px-0 w-12' : 'px-4'">
                <svg class="w-5 h-5 {{ request()->routeIs('tickets.*') ? 'text-green-700' : 'text-gray-400' }}" :class="!sidebarMini ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <span x-show="!sidebarMini" class="transition-opacity duration-300">Réclamations</span>
            </a>
        @endif

        <!-- ========================================== -->
        <!-- ZONE ADMIN (Référentiels & Utilisateurs)   -->
        <!-- ========================================== -->
        @if($isAdmin)
            <div class="pt-4 pb-2 w-full"><hr class="border-gray-100"></div>

            <!-- 5. Référentiels -->
            @php
                $isReferentielsActive = request()->routeIs('org-units.*') || request()->routeIs('locations.*') || request()->routeIs('asset-categories.*') || request()->routeIs('asset-types.*') || request()->routeIs('ticket-categories.*');
            @endphp
            
            <!-- x-data imbriqué pour l'accordéon -->
            <div x-data="{ open: {{ $isReferentielsActive ? 'true' : 'false' }} }" class="pt-2 w-full">
                <!-- Bouton Accordéon : Si on est en mode mini, on désactive l'ouverture du menu et on met une tooltip -->
                <button @click="if(!sidebarMini) open = !open" :title="sidebarMini ? 'Référentiels' : ''" class="flex items-center py-2.5 text-sm font-medium rounded-lg transition-colors w-full {{ $isReferentielsActive ? 'text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" :class="sidebarMini ? 'justify-center px-0' : 'justify-between px-4'">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 {{ $isReferentielsActive ? 'text-gray-600' : 'text-gray-400' }}" :class="!sidebarMini ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                        <span x-show="!sidebarMini" class="transition-opacity duration-300">Référentiels</span>
                    </div>
                    <!-- Chevron de l'accordéon caché si sidebarMini -->
                    <svg x-show="!sidebarMini" :class="{'rotate-180': open}" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <!-- Le sous-menu est caché si la sidebar est en mode Mini -->
                <div x-show="open && !sidebarMini" x-collapse class="space-y-1 mt-1 pb-2">
                    <a href="{{ route('org-units.index') }}" class="block pl-12 pr-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('org-units.*') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Unités org.</a>
                    <a href="{{ route('locations.index') }}" class="block pl-12 pr-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('locations.*') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Lieux</a>
                    <a href="{{ route('asset-categories.index') }}" class="block pl-12 pr-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('asset-categories.*') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Catégories matériel</a>
                    <a href="{{ route('asset-types.index') }}" class="block pl-12 pr-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('asset-types.*') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Types de matériel</a>
                    <a href="{{ route('ticket-categories.index') }}" class="block pl-12 pr-4 py-2 text-sm font-medium rounded-lg {{ request()->routeIs('ticket-categories.*') ? 'text-green-700 bg-green-50' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50' }}">Catégories tickets</a>
                </div>
            </div>

            <!-- 6. Utilisateurs -->
            <a href="{{ route('users.index') }}" :title="sidebarMini ? 'Utilisateurs' : ''" class="flex items-center py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('users.*', 'employees.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" :class="sidebarMini ? 'justify-center px-0 w-12' : 'px-4'">
                <svg class="w-5 h-5 {{ request()->routeIs('users.*', 'employees.*') ? 'text-green-700' : 'text-gray-400' }}" :class="!sidebarMini ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span x-show="!sidebarMini" class="transition-opacity duration-300">Utilisateurs</span>
            </a>
        @endif

        <!-- 7. Import Excel -->
        @if($isAdminOrInv)
            <a href="{{ route('import.upload') }}" :title="sidebarMini ? 'Import Excel' : ''" class="flex items-center py-2.5 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('import.*') ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}" :class="sidebarMini ? 'justify-center px-0 w-12' : 'px-4'">
                <svg class="w-5 h-5 {{ request()->routeIs('import.*') ? 'text-green-700' : 'text-gray-400' }}" :class="!sidebarMini ? 'mr-3' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span x-show="!sidebarMini" class="transition-opacity duration-300 whitespace-nowrap">Import Excel</span>
            </a>
        @endif

    </nav>
</aside>
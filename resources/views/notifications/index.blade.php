@section('title', 'Notification inbox')

<x-app-layout>

    <!-- HEADER & BREADCRUMB -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <span class="text-gray-400">Notifications</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Notification inbox</h1>
            
            <div class="flex gap-3">
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 shadow-sm transition">
                        Mark all read
                    </button>
                </form>
                <form action="{{ route('notifications.clearRead') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                        Clear read
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- MESSAGES -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- FILTRES (Barre blanche) -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 p-4">
        <form action="{{ route('notifications.index') }}" method="GET" id="filter-form" class="flex flex-col md:flex-row gap-4 items-center justify-between">
            
            <!-- Recherche textuelle -->
            <div class="relative w-full md:w-1/3">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets, assets, keywords" class="pl-10 w-full border-gray-100 bg-gray-50 rounded-lg text-sm focus:border-green-500 focus:ring-green-500" onchange="document.getElementById('filter-form').submit();">
            </div>

            <!-- Boutons de filtres (Pills) -->
            @php $currentFilter = request('filter', 'all'); @endphp
            <input type="hidden" name="filter" id="filter-input" value="{{ $currentFilter }}">
            <div class="flex flex-wrap gap-2 w-full md:w-auto">
                <button type="button" onclick="document.getElementById('filter-input').value='all'; document.getElementById('filter-form').submit();" class="px-4 py-1.5 rounded-full text-xs font-bold transition {{ $currentFilter == 'all' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">All</button>
                <button type="button" onclick="document.getElementById('filter-input').value='unread'; document.getElementById('filter-form').submit();" class="px-4 py-1.5 rounded-full text-xs font-bold transition {{ $currentFilter == 'unread' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Unread</button>
                <button type="button" onclick="document.getElementById('filter-input').value='tickets'; document.getElementById('filter-form').submit();" class="px-4 py-1.5 rounded-full text-xs font-bold transition {{ $currentFilter == 'tickets' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Tickets</button>
                <button type="button" onclick="document.getElementById('filter-input').value='assets'; document.getElementById('filter-form').submit();" class="px-4 py-1.5 rounded-full text-xs font-bold transition {{ $currentFilter == 'assets' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Assets</button>
                <button type="button" onclick="document.getElementById('filter-input').value='imports'; document.getElementById('filter-form').submit();" class="px-4 py-1.5 rounded-full text-xs font-bold transition {{ $currentFilter == 'imports' ? 'bg-green-700 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' }}">Imports</button>
            </div>

            <!-- Séparateur vertical -->
            <div class="hidden md:block h-6 border-l border-gray-200 mx-2"></div>

            <!-- Tri -->
            <div class="w-full md:w-auto">
                <select name="sort" class="w-full border-transparent bg-transparent text-sm font-bold text-gray-700 focus:ring-0 cursor-pointer" onchange="document.getElementById('filter-form').submit();">
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Sort: Newest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Sort: Oldest</option>
                </select>
            </div>
        </form>
    </div>

    <!-- LISTE DES NOTIFICATIONS (Cartes) -->
    <div class="space-y-4 mb-6">
        @forelse($notifications as $notif)
            @php
                $isUnread = is_null($notif->read_at);
                
                // Détermination des couleurs selon le titre ou l'icône de la notif
                $borderColor = 'border-l-blue-500';
                $iconBg = 'bg-blue-50 text-blue-500';
                $svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                
                $title = strtolower($notif->data['title'] ?? '');
                if (str_contains($title, 'overdue') || str_contains($title, 'red')) {
                    $borderColor = 'border-l-red-500';
                    $iconBg = 'bg-red-50 text-red-500';
                    $svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                } elseif (str_contains($title, 'soon') || str_contains($title, 'yellow')) {
                    $borderColor = 'border-l-yellow-400';
                    $iconBg = 'bg-yellow-50 text-yellow-500';
                    $svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>';
                } elseif (str_contains($title, 'comment')) {
                    $borderColor = 'border-l-blue-400';
                    $iconBg = 'bg-blue-50 text-blue-500';
                    $svgIcon = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>';
                }
            @endphp

<div class="relative bg-white rounded-xl shadow-sm border border-gray-200 border-l-4 {{ $borderColor }} p-5 flex items-start gap-4 transition hover:shadow-md">                
                <!-- Icône ronde -->
                <div class="h-12 w-12 rounded-full flex items-center justify-center shrink-0 {{ $iconBg }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $svgIcon !!}</svg>
                </div>

                <!-- Contenu principal -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-base font-bold text-gray-900">{{ $notif->data['title'] ?? 'Notification' }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $notif->data['message'] ?? '' }}</p>
                    <p class="text-xs text-gray-400 mt-1">Ticket: {{ $notif->data['reference'] ?? 'N/A' }}</p>
                </div>

                <!-- Date & Status -->
                <div class="flex flex-col items-end shrink-0 gap-2">
                    <span class="text-xs font-medium text-gray-400">{{ $notif->created_at->diffForHumans(null, true, true) }}</span>
                    @if($isUnread)
                        <span class="px-2 py-0.5 border border-green-500 text-green-600 bg-white rounded text-[10px] font-bold uppercase tracking-wider">Unread</span>
                    @else
                        <span class="px-2 py-0.5 border border-gray-300 text-gray-500 bg-gray-50 rounded text-[10px] font-bold uppercase tracking-wider">Read</span>
                    @endif
                </div>

                <!-- Lien invisible recouvrant toute la carte -->
                <a href="{{ $notif->data['url'] ?? '#' }}" class="absolute inset-0"></a>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <h3 class="text-lg font-bold text-gray-900">Aucune notification</h3>
                <p class="text-sm text-gray-500 mt-1">Vous êtes à jour !</p>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    @if($notifications->hasPages())
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            {{ $notifications->links() }}
        </div>
    @endif

</x-app-layout>
<!-- Enlevé le lg:justify-end, on garde just justify-between pour avoir Gauche et Droite -->
<header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-20 relative w-full">
    
    <!-- ========================================== -->
    <!-- PARTIE GAUCHE : Boutons de la Sidebar      -->
    <!-- ========================================== -->
    <div class="flex items-center">
        <!-- BOUTON HAMBURGER (Mobile & Tablette : Ouvre/Ferme le menu volant) -->
        <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 mr-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>

        <!-- BOUTON MENU DESKTOP (Grand écran : Réduit/Agrandit la sidebar) -->
        <button @click="sidebarMini = !sidebarMini" class="hidden lg:block p-2 -ml-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg focus:outline-none transition">
            <!-- L'icône change selon l'état -->
            <svg x-show="!sidebarMini" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            <svg x-show="sidebarMini" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
    </div>

    <!-- ========================================== -->
    <!-- PARTIE DROITE : Notifications & Profil     -->
    <!-- ========================================== -->
    <div class="flex items-center space-x-3 sm:space-x-5">
        
        <!-- MENU DES NOTIFICATIONS (Custom Alpine Component) -->
        @php
            $notifications = Auth::user()->notifications()->take(5)->get();
            $unreadCount = Auth::user()->unreadNotifications()->count();
        @endphp

        <!-- On gère l'ouverture (open) et les onglets (tab) nous-mêmes ! -->
        <div x-data="{ open: false, tab: 'all' }" class="relative" @click.outside="open = false">
            
            <!-- TRIGGER (La Cloche) -->
            <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-full focus:outline-none transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                
                @if($unreadCount > 0)
                    <span class="absolute top-1.5 right-1.5 block h-2.5 w-2.5 rounded-full bg-blue-500 ring-2 ring-white"></span>
                @endif
            </button>

            <!-- CONTENT (La fenêtre de notifications) -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                 class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-xl border border-gray-100 z-50 flex flex-col max-h-[80vh]" 
                 style="display: none;">
                
                <!-- Header de la modale Notifs -->
                <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-white shrink-0 rounded-t-lg">
                    <h3 class="text-base font-bold text-gray-900">Notifications</h3>
                    <form action="{{ route('notifications.markAllRead') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-green-600 hover:text-green-800 transition">Mark all read</button>
                    </form>
                </div>

                <!-- Les Tabs (All / Unread) -->
                <div class="px-4 py-2 bg-white shrink-0">
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button type="button" @click="tab = 'all'" :class="tab === 'all' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition">All</button>
                        <button type="button" @click="tab = 'unread'" :class="tab === 'unread' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500 hover:text-gray-700'" class="flex-1 py-1.5 text-xs font-bold rounded-md transition">Unread</button>
                    </div>
                </div>

                <!-- Liste des notifications (Scrollable) -->
                <div class="flex-1 overflow-y-auto overflow-x-hidden bg-white">
                    @forelse($notifications as $notification)
                        <div x-show="tab === 'all' || (tab === 'unread' && {{ is_null($notification->read_at) ? 'true' : 'false' }})" class="group relative block px-4 py-4 hover:bg-gray-50 transition border-b border-gray-50">
                            
                            <div class="flex items-start space-x-4">
                                @php
                                    $iconClass = 'bg-gray-100 text-gray-600';
                                    $svgPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>';

                                    if (isset($notification->data['icon'])) {
                                        if ($notification->data['icon'] === 'red_clock') {
                                            $iconClass = 'bg-red-50 text-red-500';
                                            $svgPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                                        } elseif ($notification->data['icon'] === 'blue_chat') {
                                            $iconClass = 'bg-blue-50 text-blue-500';
                                            $svgPath = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>';
                                        }
                                    }
                                @endphp
                                <div class="flex-shrink-0 h-10 w-10 rounded-full flex items-center justify-center {{ $iconClass }}">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $svgPath !!}</svg>
                                </div>

                                <div class="flex-1 min-w-0 pr-8">
                                    <p class="text-sm font-bold text-gray-900 truncate">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                    <p class="text-sm text-gray-600 mt-0.5 leading-snug">{{ $notification->data['message'] ?? '' }}</p>
                                    
                                    <div class="mt-2 flex items-center gap-2">
                                        <span class="text-[10px] text-gray-500">Ticket: {{ $notification->data['reference'] ?? 'N/A' }}</span>
                                        @if(isset($notification->data['status_badge']))
                                            @php
                                                $badgeColor = $notification->data['status_badge'] === 'OVERDUE' ? 'bg-red-100 text-red-700' : 
                                                            ($notification->data['status_badge'] === 'DUE SOON' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700');
                                            @endphp
                                            <span class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider {{ $badgeColor }}">{{ $notification->data['status_badge'] }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col items-end shrink-0 pt-0.5">
                                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $notification->created_at->diffForHumans(null, true, true) }}</span>
                                    @if(is_null($notification->read_at))
                                        <span class="h-2 w-2 bg-green-500 rounded-full mt-2"></span>
                                    @endif
                                </div>
                                
                                <!-- Lien cliquable -->
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="absolute inset-0 z-10"></a>
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center">
                            <svg class="mx-auto h-8 w-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <p class="mt-2 text-sm font-medium text-gray-900">Pas de notifications</p>
                        </div>
                    @endforelse
                </div>

                <!-- Footer (View All) -->
                <div class="p-3 border-t border-gray-100 bg-gray-50 shrink-0 rounded-b-lg relative z-20">
                    <a href="{{ route('notifications.index') }}" class="block w-full py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 text-center hover:bg-gray-50 transition shadow-sm relative z-20">View all</a>
                </div>

            </div>
        </div>

        <!-- Ligne Séparatrice -->
        <div class="hidden sm:block h-8 border-l border-gray-200"></div>

        <!-- DROPDOWN PROFIL -->
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center space-x-2 sm:space-x-3 focus:outline-none hover:bg-gray-50 p-1 sm:p-1.5 rounded-lg transition-colors">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-bold text-gray-900 leading-tight">
                            {{ Auth::user()->employee->full_name ?? Auth::user()->username }}
                        </div>
                        <div class="inline-block px-1.5 py-0.5 mt-0.5 text-[10px] font-bold text-white bg-green-700 rounded uppercase tracking-wider">
                            {{ str_replace('_', ' ', Auth::user()->role->value) }}
                        </div>
                    </div>
                    <img class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover border border-gray-200" 
                         src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->employee->full_name ?? Auth::user()->username) }}&background=f3f4f6&color=111827&bold=true" 
                         alt="Avatar">
                    <svg class="hidden sm:block w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </x-slot>

            <x-slot name="content">
                <div class="block sm:hidden px-4 py-2 border-b border-gray-100">
                    <div class="font-bold text-gray-800">{{ Auth::user()->employee->full_name ?? Auth::user()->username }}</div>
                    <div class="text-xs text-green-600 font-bold uppercase mt-1">{{ Auth::user()->role->value }}</div>
                </div>
                
                <x-dropdown-link :href="route('profile.edit')">Mon Profil</x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 font-medium">Déconnexion</x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>

    </div>
</header>
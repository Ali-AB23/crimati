<header class="h-20 bg-white border-b border-gray-200 flex items-center justify-between lg:justify-end px-4 sm:px-6 lg:px-8">
    
    <!-- BOUTON HAMBURGER (Mobile & Tablette uniquement) -->
    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ml-2 mr-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </button>

    <div class="flex items-center space-x-3 sm:space-x-5">
        
        <!-- Icône Cloche (Notifications) -->
        <button class="relative p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-full focus:outline-none transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            <span class="absolute top-1.5 right-1.5 block h-2 w-2 rounded-full bg-blue-500 ring-2 ring-white"></span>
        </button>

        <div class="hidden sm:block h-8 border-l border-gray-200"></div>

        <!-- Dropdown Profil -->
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
                    <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600 font-medium">
                        Déconnexion
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>

    </div>
</header>
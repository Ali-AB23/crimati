<x-guest-layout>
    
    <!-- 
      LE FOND (Responsive)
      - bg-cover bg-center : l'image couvre tout l'écran proprement.
      - justify-center (Mobile) -> lg:justify-end (Desktop) : Centre la carte sur mobile, la pousse à droite sur PC.
    -->
    <div class="min-h-screen flex items-center justify-center lg:justify-end bg-gray-50 bg-no-repeat bg-cover bg-center relative w-full"
         style="background-image: url('{{ asset('images/pattern-bg.jpeg') }}');">

        <!-- OVERLAY RESPONSIVE -->
        <!-- Sur mobile/tablette (max lg), on met un voile blanc à 80% d'opacité pour que la carte ressorte bien. -->
        <!-- Sur grand écran (lg:bg-transparent), l'image apparaît normalement. -->
        <div class="absolute inset-0 bg-white/80 lg:bg-transparent z-0"></div>

        <!-- CONTENEUR DE LA CARTE -->
        <!-- w-full px-4 (Mobile) -> lg:mr-24 xl:mr-40 (Marges de droite sur Desktop) -->
        <div class="relative z-10 w-full max-w-md px-4 sm:px-6 lg:px-0 lg:mr-24 xl:mr-40">
            
            <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 border border-gray-100 w-full">

                <!-- LE VRAI LOGO (logo.png) -->
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/cri_logo.png') }}" alt="Logo CRIMATI" class="h-10 w-auto object-contain">
                </div>

                <!-- TITRE -->
                <h1 class="text-3xl font-extrabold text-gray-900 text-center mb-1">Connexion</h1>
                <p class="text-sm text-gray-500 text-center mb-8">Accès sécurisé à la plateforme CRIMATI</p>

                <!-- Affichage des messages de session (Breeze) -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- NOM COMPLET / MATRICULE -->
                    <div>
                        <label for="username" class="block text-sm font-bold text-gray-700 mb-2">Nom complet (Matricule)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                            </div>
                            <input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus
                                   class="pl-10 w-full border-gray-200 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm text-gray-900 bg-gray-50 py-2.5 transition"
                                   placeholder="ex: soufiane ait taleb">
                        </div>
                        @error('username')
                            <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- MOT DE PASSE -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="password" class="block text-sm font-bold text-gray-700">Mot de passe</label>
                            
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" type="password" name="password" required
                                   class="pl-10 w-full border-gray-200 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm text-gray-900 bg-gray-50 py-2.5 transition"
                                   placeholder="••••••••">
                        </div>
                        <div>
                            <span class="text-[10px] text-gray-400 font-medium">
                                Mot de passe oublié ? Contactez l'IT.
                            </span>
                        </div>
                        @error('password')
                            <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- BOUTON SUBMIT -->
                    <div class="pt-3">
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-[#e3342f] hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            Se connecter
                        </button>
                    </div>
                </form>

                <!-- FOOTER -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-center text-sm text-gray-500">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    En cas de problème, contactez le service IT.
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
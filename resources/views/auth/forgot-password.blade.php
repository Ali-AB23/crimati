<x-guest-layout>
    
    <!-- LE MÊME FOND GÉOMÉTRIQUE QUE LA PAGE DE LOGIN -->
    <div class="min-h-screen flex items-center justify-center lg:justify-end bg-gray-50 bg-no-repeat bg-left relative bg-[length:auto_100%]"
         style="background-image: url('{{ asset('images/pattern-bg.jpeg') }}');">

        <!-- OVERLAY RESPONSIVE -->
        <div class="absolute inset-0 bg-white/80 lg:bg-transparent z-0"></div>

        <!-- CONTENEUR DE LA CARTE (Alignement identique au Login) -->
        <div class="relative z-10 w-full max-w-md px-4 sm:px-6 lg:px-0 lg:mr-24 xl:mr-40">
            
            <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 border border-gray-100 w-full">

                <!-- LE VRAI LOGO (logo.png) -->
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('images/cri_logo.png') }}" alt="Logo CRIMATI" class="h-10 w-auto object-contain">
                </div>

                <!-- TITRE ET DESCRIPTION EN FRANÇAIS -->
                <h1 class="text-2xl font-extrabold text-gray-900 text-center mb-2">Mot de passe oublié ?</h1>
                <p class="text-sm text-gray-500 text-center mb-6 leading-relaxed">
                    Aucun problème. Indiquez-nous votre adresse e-mail et nous vous enverrons un lien vous permettant d'en choisir un nouveau.
                </p>

                <!-- Affichage des messages de session (ex: "Lien envoyé !") -->
                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <!-- EMAIL (Design identique aux inputs du login) -->
                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Adresse E-mail</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <!-- Icône Enveloppe -->
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="pl-10 w-full border-gray-200 rounded-xl shadow-sm focus:ring-red-500 focus:border-red-500 sm:text-sm text-gray-900 bg-gray-50 py-2.5 transition"
                                   placeholder="ex: prenom.nom@cri.ma">
                        </div>
                        @error('email')
                            <p class="mt-2 text-xs text-red-600 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- BOUTON SUBMIT (Le beau bouton rouge) -->
                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-[#e3342f] hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            Envoyer le lien de réinitialisation
                        </button>
                    </div>
                </form>

                <!-- FOOTER : Lien pour retourner au Login (Astuce UX) -->
                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col items-center justify-center text-sm text-gray-500">
                    <a href="{{ route('login') }}" class="font-bold text-gray-600 hover:text-red-600 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Retour à la connexion
                    </a>
                </div>

            </div>
        </div>
    </div>

</x-guest-layout>
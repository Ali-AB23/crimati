@section('title', 'Mon Profil')

<x-app-layout>

    <!-- BREADCRUMB & HEADER -->
    <div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
        <div>
            <div class="text-sm text-gray-500 mb-1">
                <span class="text-gray-400">Profil</span> 
                <span class="mx-1">/</span> 
                <span class="text-gray-900 font-medium">Mon profil</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Mon profil</h1>
        </div>
        
    </div>

    <!-- MESSAGES DE SUCCÈS -->
    @if (session('status') === 'profile-updated')
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <p class="text-sm font-medium text-green-800">Profil mis à jour avec succès.</p>
        </div>
    @endif
    @if (session('status') === 'password-updated')
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <p class="text-sm font-medium text-green-800">Mot de passe modifié avec succès.</p>
        </div>
    @endif

    <!-- CARTE D'EN-TÊTE DU PROFIL -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-8 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="shrink-0">
            <img class="h-20 w-20 rounded-full object-cover border border-gray-200" 
                 src="https://ui-avatars.com/api/?name={{ urlencode(optional($user->employee)->full_name ?? $user->username) }}&background=f3f4f6&color=111827&bold=true&size=128" 
                 alt="Avatar">
        </div>
        <div class="text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mb-2">
                <h2 class="text-2xl font-bold text-gray-900">{{ optional($user->employee)->full_name ?? $user->username }}</h2>
                <div class="flex items-center justify-center sm:justify-start gap-2">
                    <span class="px-2 py-0.5 text-[10px] font-bold text-gray-600 bg-gray-100 border border-gray-200 rounded uppercase">{{ str_replace('_', ' ', $user->role->value) }}</span>
                    @if($user->active)
                        <span class="px-2 py-0.5 text-[10px] font-bold text-green-700 bg-green-50 border border-green-100 rounded uppercase">Actif</span>
                    @else
                        <span class="px-2 py-0.5 text-[10px] font-bold text-red-700 bg-red-50 border border-red-100 rounded uppercase">Inactif</span>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap justify-center sm:justify-start items-center text-sm text-gray-500 gap-y-1">
                <span>Dernière connexion : {{ now()->format('Y-m-d H:i') }}</span> <!-- En dur pour l'instant -->
                <span class="hidden sm:inline mx-2 text-gray-300">|</span>
                <span>Compte créé le :{{ $user->created_at->format('Y-m-d') }}</span>
            </div>
        </div>
    </div>

    <!-- FORMULAIRE PRINCIPAL (Infos RH & Affectation) -->
    <form id="profile-form" method="POST" action="{{ route('profile.update') }}" class="mb-8">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            
            <!-- BLOC GAUCHE : EMPLOYEE INFORMATION -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Informations personnelles</h3>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Matricule</label>
                        <!-- Champ désactivé (readonly) : C'est l'Admin qui gère le matricule -->
                        <input type="text" value="{{ optional($user->employee)->matricule }}" readonly class="w-full border-gray-200 bg-gray-50 text-gray-500 rounded-lg shadow-sm sm:text-sm cursor-not-allowed">
                        <p class="mt-1 text-[11px] text-gray-400">Veuillez contacter l'Administrateur IT pour modifier votre matricule.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Nom complet *</label>
                        <input type="text" name="full_name" value="{{ old('full_name', optional($user->employee)->full_name) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', optional($user->employee)->email) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Téléphone</label>
                        <input type="text" name="phone" value="{{ old('phone', optional($user->employee)->phone) }}" placeholder="+212 ..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        <p class="mt-1 text-[11px] text-gray-400">Ex: 06 00 00 00 00 (Format local ou international)..</p>
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>
                </div>
            </div>

            <!-- BLOC DROIT : ORGANIZATION ASSIGNMENT -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Affectation organisationnelle</h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Unité (Service/Pôle) *</label>
                        <select name="org_unit_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm text-gray-700">
                            <option value="" disabled>Sélectionnez une unité organisationnelle...</option>
                            @foreach($orgUnits as $unit)
                                <option value="{{ $unit->id }}" {{ old('org_unit_id', optional($user->employee)->org_unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('org_unit_id')" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Bureau physique</label>
                        <select name="office_location_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm text-gray-700">
                            <option value="">Sélectionnez un bureau (optionnel)</option>
                            @foreach($officeLocations as $location)
                                <option value="{{ $location->id }}" {{ old('office_location_id', optional($user->employee)->office_location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('office_location_id')" />
                    </div>

                    <div class="mt-4 bg-gray-50 border border-gray-200 rounded-lg p-4 flex items-start">
                        <svg class="w-5 h-5 text-blue-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-sm text-gray-600">Renseigner votre bureau est recommandé pour visualiser automatiquement le matériel qui s'y trouve.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- FORMULAIRE SÉPARÉ : SÉCURITÉ (Mot de passe) -->
    <!-- Breeze gère ça avec une route spécifique /password -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8 mb-24"> <!-- mb-24 pour laisser de la place au footer fixe -->
        <h3 class="text-lg font-bold text-gray-900 mb-1">Sécurité</h3>
        <p class="text-sm text-gray-500 mb-6">Laissez les champs vides si vous ne souhaitez pas modifier votre mot de passe actuel.</p>

        <form method="POST" action="{{ route('password.update') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf
            @method('put')

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-1">Mot de passe actuel</label>
                <input type="password" name="current_password" placeholder="********" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-1">Nouveau mot de passe</label>
                <input type="password" name="password" placeholder="********" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-900 mb-1">Confirmer le nouveau mot de passe</label>
                <input type="password" name="password_confirmation" placeholder="********" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="md:col-span-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                    Mettre à jour le mot de passe
                </button>
            </div>
        </form>
    </div>

    <!-- FOOTER FIXE EN BAS (Maquette) -->
    <!-- Le z-index et fixed bottom-0 collent cette barre au bas de l'écran -->
    <div class="fixed bottom-0 left-0 md:left-64 right-0 bg-white border-t border-gray-200 px-6 sm:px-8 py-4 flex flex-col sm:flex-row justify-between items-center gap-4 z-20">
        <p class="text-sm text-gray-500">Les modifications s'appliqueront uniquement à votre compte personnel.</p>
        <div class="flex gap-3 w-full sm:w-auto">
            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Annuler</a>
            <button onclick="document.getElementById('profile-form').submit();" class="w-full sm:w-auto px-6 py-2.5 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 transition shadow-sm">Enregistrer les modifications</button>
        </div>
    </div>

</x-app-layout>
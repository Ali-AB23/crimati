@section('title', 'Nouvel Utilisateur')

<x-app-layout>

    <!-- BREADCRUMB & HEADER -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <a href="{{ route('users.index') }}" class="hover:underline">Utilisateurs</a> 
            <span class="mx-1">&gt;</span> 
            <span class="text-gray-900 font-medium">Nouvel utilisateur</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Nouvel utilisateur</h1>
            <p class="text-sm text-gray-500">Créer un nouveau compte interne. Les champs marqués d'un <span class="text-red-500">*</span> sont obligatoires.</p>
        </div>
    </div>

    <!-- AFFICHAGE DES ERREURS GLOBALES -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.78 7.25a.75.75 0 00-1.06 1.06L9.44 10l-1.72 1.69a.75.75 0 101.06 1.06L10.56 11l1.72 1.69a.75.75 0 101.06-1.06L11.56 10l1.72-1.69a.75.75 0 00-1.06-1.06L10.56 9 8.78 7.25z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <ul class="text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Alpine.js s'occupe de générer les mots de passe et de synchroniser le visuel du toggle avec le champ caché -->
    <div x-data="{ 
            isActive: {{ old('active', 'true') === 'true' ? 'true' : 'false' }},
            password: '',
            
            generatePassword() {
                const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
                let pass = '';
                for (let i = 0; i < 12; i++) {
                    pass += chars.charAt(Math.floor(Math.random() * chars.length));
                }
                this.password = pass;
            }
        }">

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <!-- BARRE D'ACTIONS SUPÉRIEURE (Maquette) -->
            <div class="flex justify-end gap-3 mb-6">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Annuler</a>
                <button type="submit" class="px-6 py-2.5 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 transition shadow-sm">Enregistrer</button>
            </div>

            <!-- GRILLE PRINCIPALE -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
                
                <!-- BLOC GAUCHE : INFOS RH (EMPLOYEES) -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Informations de l'employé</h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Matricule <span class="text-red-500">*</span></label>
                            <!-- La classe d'erreur change la bordure en rouge si le matricule existe déjà -->
                            <input type="text" name="matricule" value="{{ old('matricule') }}" placeholder="e.g. 00123" required 
                                   class="w-full rounded-lg shadow-sm sm:text-sm {{ $errors->has('matricule') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' }}">
                            @error('matricule') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Nom complet <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name') }}" placeholder="e.g. Zineb Belkhyat" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="name@cri.ma" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+212 ..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- BLOC DROIT : INFOS CONNEXION (USERS) -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Compte et accès</h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Identifiant de connexion <span class="text-red-500">*</span></label>
                            <input type="text" name="username" value="{{ old('username') }}" placeholder="firstname.lastname" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            @error('username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm text-gray-700">
                                @foreach($roles as $role)
                                    <option value="{{ $role->value }}" {{ old('role') == $role->value ? 'selected' : '' }}>{{ str_replace('_', ' ', $role->value) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- TOGGLE SWITCH (Remplaçant la checkbox classique) -->
                        <div class="flex items-center space-x-3 pt-2">
                            <!-- Le champ caché envoyé au serveur -->
                            <input type="checkbox" name="active" value="1" class="hidden" :checked="isActive">
                            
                            <!-- Le design Tailwind/Alpine de l'interrupteur -->
                            <button type="button" @click="isActive = !isActive" 
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2" 
                                    :class="isActive ? 'bg-green-600' : 'bg-gray-200'">
                                <span class="sr-only">Use setting</span>
                                <span aria-hidden="true" 
                                      class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" 
                                      :class="isActive ? 'translate-x-5' : 'translate-x-0'"></span>
                            </button>
                            <span class="text-sm font-bold text-gray-900" x-text="isActive ? 'Actif' : 'Inactif'"></span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-1">Nouveau mot de passe <span class="text-red-500">*</span></label>
                                <!-- Alpine lie ce champ à la variable 'password' -->
                                <input type="text" name="password" x-model="password" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-1">Confirmer le mot de passe <span class="text-red-500">*</span></label>
                                <input type="text" name="password_confirmation" x-model="password" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                        </div>

                        <!-- BOUTON GÉNÉRATEUR -->
                        <div>
                            <button type="button" @click="generatePassword()" class="inline-flex items-center px-3 py-1.5 border border-green-600 text-green-700 rounded-lg text-xs font-bold hover:bg-green-50 transition">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                Générer un mot de passe
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOC DU BAS : AFFECTATION (ORG UNITS & LOCATIONS) -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Affectation organisationnelle</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Unité organisationnelle <span class="text-red-500">*</span></label>
                        <select name="org_unit_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm text-gray-700">
                            <option value="" disabled selected>Sélectionner une unité...</option>
                            @foreach($orgUnits as $unit)
                                <option value="{{ $unit->id }}" {{ old('org_unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Bureau physique</label>
                        <select name="office_location_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm text-gray-700">
                            <option value="">Sélectionner un bureau (optionnel)</option>
                            @foreach($officeLocations as $location)
                                <option value="{{ $location->id }}" {{ old('office_location_id') == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Bulle d'information (Maquette) -->
                <div class="mt-6 bg-blue-50 border border-blue-100 rounded-lg p-4 flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <p class="text-sm text-blue-700">Le bureau est optionnel mais recommandé pour une meilleure visibilité du matériel.</p>
                </div>
            </div>

            <!-- BARRE D'ACTIONS INFÉRIEURE -->
            <div class="flex justify-between items-center py-4 border-t border-gray-200">
                <button type="reset" class="text-sm font-bold text-red-600 hover:text-red-800 transition">Réinitialiser le formulaire</button>
                <div class="flex gap-3">
                    <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Annuler</a>
                    <button type="submit" class="px-6 py-2.5 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 transition shadow-sm">Enregistrer</button>
                </div>
            </div>

        </form>
    </div>

</x-app-layout>
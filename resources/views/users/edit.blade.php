@section('title', 'Modifier Utilisateur')

<x-app-layout>

    <!-- BREADCRUMB & HEADER -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            {{-- <span class="text-gray-400">Utilisateurs</span>  --}}
            <a href="{{ route('users.index') }}" class="hover:underline">Utilisateurs</a> 
            <span class="mx-1">&gt;</span> 
            <span class="text-gray-900 font-medium">Modifier utilisateur</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Modifier {{ optional($user->employee)->full_name ?? $user->username }}</h1>
            <p class="text-sm text-gray-500">Modifier les informations du compte interne.</p>
        </div>
    </div>

    <!-- ERREURS GLOBALES -->
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

    <!-- Alpine.js gère le toggle et le générateur de mot de passe -->
    <div x-data="{ 
            isActive: {{ old('active', $user->active) ? 'true' : 'false' }},
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

        <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT') <!-- OBLIGATOIRE POUR LA MISE À JOUR -->

           

            <!-- GRILLE PRINCIPALE -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
                
                <!-- BLOC GAUCHE : INFOS RH -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Informations de l'employé</h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Matricule <span class="text-red-500">*</span></label>
                            <input type="text" name="matricule" value="{{ old('matricule', optional($user->employee)->matricule) }}" required 
                                   class="w-full rounded-lg shadow-sm sm:text-sm {{ $errors->has('matricule') ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' }}">
                            @error('matricule') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Nom complet <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="{{ old('full_name', optional($user->employee)->full_name) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Email</label>
                            <input type="email" name="email" value="{{ old('email', optional($user->employee)->email) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', optional($user->employee)->phone) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <!-- BLOC DROIT : INFOS CONNEXION -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Compte et accès</h2>

                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Identifiant de connexion <span class="text-red-500">*</span></label>
                            <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                            @error('username') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Role <span class="text-red-500">*</span></label>
                            <select name="role" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm text-gray-700">
                                @foreach($roles as $role)
                                    <option value="{{ $role->value }}" {{ old('role', $user->role->value) == $role->value ? 'selected' : '' }}>{{ str_replace('_', ' ', $role->value) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- TOGGLE SWITCH -->
                        <div class="flex items-center space-x-3 pt-2">
                            <input type="checkbox" name="active" value="1" class="hidden" :checked="isActive">
                            
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

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-500 mb-3"><span class="font-bold text-gray-700">Note :</span> Laissez les champs vides si vous ne souhaitez pas modifier le mot de passe actuel.</p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-1">Nouveau mot de passe</label>
                                    <!-- Plus de 'required' ici -->
                                    <input type="text" name="password" x-model="password" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-1">Confirmer le mot de passe</label>
                                    <input type="text" name="password_confirmation" x-model="password" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                </div>
                            </div>

                            <div class="mt-3">
                                <button type="button" @click="generatePassword()" class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 rounded-lg text-xs font-bold hover:bg-gray-50 transition">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                                    Générer un nouveau mot de passe
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOC DU BAS : AFFECTATION -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Affectation organisationnelle</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Unité organisationnelle <span class="text-red-500">*</span></label>
                        <select name="org_unit_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                            <option value="" disabled>Sélectionner une unité...</option>
                            @foreach($orgUnits as $unit)
                                <option value="{{ $unit->id }}" {{ old('org_unit_id', optional($user->employee)->org_unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Bureau physique</label>
                        <select name="office_location_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                            <option value="">Sélectionner un bureau (optionnel)</option>
                            @foreach($officeLocations as $location)
                                <option value="{{ $location->id }}" {{ old('office_location_id', optional($user->employee)->office_location_id) == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

             <!-- BARRE D'ACTIONS SUPÉRIEURE -->
            <div class="flex justify-end gap-3 mb-6">
                <a href="{{ route('users.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Annuler</a>
                <button type="submit" class="px-6 py-2.5 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 transition shadow-sm">Enregistrer les modifications</button>
            </div>
        </form>
    </div>

</x-app-layout>
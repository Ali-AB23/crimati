@section('title', 'Nouvelle Réclamation')

<x-app-layout>

    <!-- BREADCRUMB & HEADER -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <a href="{{ route('tickets.index') }}" class="hover:underline">Reclamations</a> 
            <span class="mx-1">&gt;</span> 
            <span class="text-gray-900 font-medium">Nouvelle</span>
        </div>
        
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle réclamation</h1>
            
            <a href="{{ route('tickets.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- RÈGLE INFO BUBBLE -->
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-start space-x-3 mb-6">
        <svg class="w-5 h-5 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h3 class="text-sm font-bold text-gray-900">Rule</h3>
            <p class="text-sm text-gray-600 mt-1">Vous ne pouvez déclarer un incident que sur un matériel auquel vous avez accès (votre bureau, matériel personnel, ou espaces communs de votre service).</p>
        </div>
    </div>

    <!-- 
        INITIALISATION ALPINE.JS 
        On passe la liste des assets en JSON.
    -->
    <div x-data="{ 
            assets: {{ $assets->toJson() }},
            selectedAssetId: '{{ old('asset_id') }}',
            selectedAsset: null,
            updateSelectedAsset() {
                this.selectedAsset = this.assets.find(a => a.id == this.selectedAssetId) || null;
            }
        }" 
        x-init="updateSelectedAsset()">

        <form action="{{ route('tickets.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                
                <!-- HEADER FORMULAIRE -->
                <div class="p-6 md:p-8 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900">Détails de la demande</h2>
                    <p class="text-sm text-gray-500 mt-1">La référence et le délai de traitement seront générés automatiquement.</p>
                </div>

                <div class="p-6 md:p-8 space-y-8">
                    
                    <!-- SÉLECTION MATÉRIEL -->
                    <div>
                        <label for="asset_id" class="block text-sm font-bold text-gray-900 mb-2">Materiel <span class="text-red-500">*</span></label>
                        <select name="asset_id" id="asset_id" required 
                                x-model="selectedAssetId" @change="updateSelectedAsset()"
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled>Sélectionner un matériel...</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->inventory_code }} - {{ optional($asset->type)->name ?? 'Matériel' }} - {{ optional($asset->currentLocation)->name ?? 'Stock' }}
                                </option>
                            @endforeach
                        </select>
                        @error('asset_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

                        <!-- CARTE "SELECTED MATERIEL" DYNAMIQUE -->
                        <div x-show="selectedAsset" style="display: none;" class="mt-4 bg-gray-50 border border-gray-100 rounded-xl p-5">
                            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-4">Informations du matériel</h4>
                            <div class="grid grid-cols-2 gap-y-4 gap-x-6">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Code d'inventaire</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="selectedAsset?.inventory_code"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Statut</p>
                                    <div class="flex items-center">
                                        <!-- Petit point de couleur simulé -->
                                        <span class="h-2 w-2 rounded-full mr-2" 
                                              :class="{
                                                  'bg-red-500': selectedAsset?.status === 'en_panne',
                                                  'bg-orange-500': selectedAsset?.status === 'en_reparation',
                                                  'bg-green-500': selectedAsset?.status === 'en_service',
                                                  'bg-blue-500': selectedAsset?.status === 'en_stock',
                                                  'bg-gray-500': selectedAsset?.status === 'reforme'
                                              }"></span>
                                        <span class="text-sm font-bold text-gray-900 capitalize" x-text="selectedAsset?.status.replace('_', ' ')"></span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Localisation</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="selectedAsset?.current_location?.name || 'Non définie'"></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Affecte a</p>
                                    <p class="text-sm font-bold text-gray-900" x-text="selectedAsset?.current_employee?.full_name || 'Non affecté'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CATÉGORIE TICKET -->
                    <div>
                        <label for="ticket_category_id" class="block text-sm font-bold text-gray-900 mb-2">Catégorie d'incident <span class="text-red-500">*</span></label>
                        <select name="ticket_category_id" id="ticket_category_id" required 
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="" disabled selected>Sélectionner une catégorie...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('ticket_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('ticket_category_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- PRIORITÉ (Boutons Radios "Chips") -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-3">Niveau d'urgence <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            
                            <!-- Low -->
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="low" class="peer sr-only" {{ old('priority') == 'low' ? 'checked' : '' }} required>
                                <div class="px-5 py-2 rounded-full border border-gray-300 text-sm font-medium text-gray-600 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 hover:bg-gray-50 transition">
                                    Faible
                                </div>
                            </label>

                            <!-- Medium -->
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="medium" class="peer sr-only" {{ old('priority') == 'medium' ? 'checked' : '' }}>
                                <div class="px-5 py-2 rounded-full border border-gray-300 text-sm font-medium text-gray-600 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 hover:bg-gray-50 transition">
                                    Moyen
                                </div>
                            </label>

                            <!-- High -->
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="high" class="peer sr-only" {{ old('priority') == 'high' ? 'checked' : '' }}>
                                <div class="px-5 py-2 rounded-full border border-gray-300 text-sm font-medium text-gray-600 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 hover:bg-gray-50 transition">
                                    Élevé
                                </div>
                            </label>

                            <!-- Urgent -->
                            <label class="cursor-pointer">
                                <input type="radio" name="priority" value="urgent" class="peer sr-only" {{ old('priority') == 'urgent' ? 'checked' : '' }}>
                                <div class="px-5 py-2 rounded-full border border-gray-300 text-sm font-medium text-gray-600 peer-checked:bg-green-600 peer-checked:text-white peer-checked:border-green-600 hover:bg-gray-50 transition">
                                    Urgent
                                </div>
                            </label>

                        </div>
                        @error('priority') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- DESCRIPTION -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-900 mb-2">Description <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="5" required
                                  placeholder="Décrivez précisément le problème rencontré..."
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                </div>

                <!-- FOOTER (BOUTONS) -->
                <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('tickets.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 shadow-sm transition">
                        Envoyer la réclamation
                    </button>
                </div>

            </div>
        </form>
    </div>

</x-app-layout>
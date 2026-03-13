@section('title', 'Nouveau Matériel')

<x-app-layout>

    <!-- BREADCRUMB & HEADER -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <a href="{{ route('assets.index') }}" class="hover:underline">Materiels</a> 
            <span class="mx-1">&gt;</span> 
            <span class="text-gray-900 font-medium">New</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">New materiel</h1>
            <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to list
            </a>
        </div>
    </div>

    <!-- 
        INITIALISATION D'ALPINE.JS
        On injecte les types depuis PHP vers JS. 
        Quand 'selectedTypeId' change, 'updateSchema()' recalcule les champs à afficher.
    -->
    <div x-data="{ 
        types: {{ $assetTypes->map->only(['id', 'spec_schema'])->toJson() }},
        selectedTypeId: '{{ old('asset_type_id') }}',
        schema: {},
        updateSchema() {
            let type = this.types.find(t => t.id == this.selectedTypeId);
            this.schema = type ? (type.spec_schema || {}) : {};
        }
    }" x-init="updateSchema()">

        <!-- FORMULAIRE -->
        <form action="{{ route('assets.store') }}" method="POST">
            @csrf

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 md:p-8 flex-1">
                    
                    <!-- SECTION 1 : REQUIRED DETAILS -->
                    <div class="mb-2">
                        <h2 class="text-lg font-bold text-gray-900">Materiel details</h2>
                        <p class="text-sm text-gray-500 mt-1">Fields marked <span class="text-red-500">*</span> are required.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div>
                            <label for="inventory_code" class="block text-sm font-bold text-gray-900 mb-1">Code inventaire <span class="text-red-500">*</span></label>
                            <input type="text" name="inventory_code" id="inventory_code" value="{{ old('inventory_code') }}" placeholder="Ex: 066/CRI/25" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            @error('inventory_code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <!-- SELECT DYNAMIQUE : TYPE DE MATERIEL -->
                        <div>
                            <label for="asset_type_id" class="block text-sm font-bold text-gray-900 mb-1">Type materiel <span class="text-red-500">*</span></label>
                            <!-- x-model lie ce select à la variable Alpine, @change déclenche le calcul des specs -->
                            <select name="asset_type_id" id="asset_type_id" x-model="selectedTypeId" @change="updateSchema()" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <option value="" disabled>Sélectionner un type...</option>
                                @foreach($assetTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-[11px] text-gray-400">Type defines category and technical specs.</p>
                            @error('asset_type_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-bold text-gray-900 mb-1">Statut <span class="text-red-500">*</span></label>
                            <select name="status" id="status" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                @foreach(\App\Enums\AssetStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ old('status') == $status->value ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucfirst($status->value)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="current_location_id" class="block text-sm font-bold text-gray-900 mb-1">Localisation (Spécifique) <span class="text-red-500">*</span></label>
                            <select name="current_location_id" id="current_location_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <option value="" disabled selected>Sélectionner un lieu exact...</option>
                                @foreach($locations as $location)
                                    <!-- Affiche le nom, ex: "Bureau A101" au lieu de juste "Étage 1" -->
                                    <option value="{{ $location->id }}" {{ old('current_location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('current_location_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- SECTION 2 : OPTIONAL -->
                    <div class="flex items-center mt-10 mb-6">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-4">Optional</span>
                        <div class="flex-grow border-t border-gray-100"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="brand" class="block text-sm font-bold text-gray-900 mb-1">Marque</label>
                            <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="Ex: HP, Dell" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="model" class="block text-sm font-bold text-gray-900 mb-1">Modele</label>
                            <input type="text" name="model" id="model" value="{{ old('model') }}" placeholder="Ex: EliteBook 840" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="serial_number" class="block text-sm font-bold text-gray-900 mb-1">Numero de serie</label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ old('serial_number') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="current_employee_id" class="block text-sm font-bold text-gray-900 mb-1">Affecte a (employe)</label>
                            <select name="current_employee_id" id="current_employee_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                <option value="">Non affecte (En stock)</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('current_employee_id') == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label for="notes" class="block text-sm font-bold text-gray-900 mb-1">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <!-- SECTION 3 : SPECS (Totalement pilotée par la BDD via Alpine) -->
                    <div class="flex items-center mt-10 mb-2">
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mr-4">Specs</span>
                        <div class="flex-grow border-t border-gray-100"></div>
                    </div>
                    
                    <!-- Message si aucun type n'est sélectionné ou si le type n'a pas de specs -->
                    <div x-show="Object.keys(schema).length === 0" class="text-sm text-gray-500 bg-gray-50 p-4 rounded-lg border border-gray-200 text-center mt-4">
                        Sélectionnez un type de matériel pour afficher ses attributs techniques (si disponibles).
                    </div>

                    <!-- Grille des champs générés dynamiquement -->
                    <div x-show="Object.keys(schema).length > 0" style="display: none;" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 p-4 bg-gray-50 border border-gray-100 rounded-xl">
                        
                        <template x-for="(rules, key) in schema" :key="key">
                            <div>
                                <!-- Label formaté proprement (ex: "ram_gb" -> "Ram Gb") -->
                                <label :for="'spec_'+key" class="block text-sm font-bold text-gray-900 mb-1 capitalize" x-text="key.replace(/_/g, ' ')"></label>
                                
                                <!-- Si c'est un SELECT -->
                                <template x-if="rules.type === 'select'">
                                    <select :name="'specs[' + key + ']'" :id="'spec_'+key" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                        <option value="">Sélectionner...</option>
                                        <template x-for="val in rules.values">
                                            <option :value="val" x-text="val"></option>
                                        </template>
                                    </select>
                                </template>

                                <!-- Si c'est un NOMBRE -->
                                <template x-if="rules.type === 'number'">
                                    <input type="number" :name="'specs[' + key + ']'" :id="'spec_'+key" :min="rules.min" :max="rules.max" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </template>

                                <!-- Si c'est du TEXTE (Défaut) -->
                                <template x-if="rules.type === 'text' || !rules.type">
                                    <input type="text" :name="'specs[' + key + ']'" :id="'spec_'+key" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                </template>
                            </div>
                        </template>

                    </div>
                </div>

                <!-- BOUTONS DE SOUMISSION -->
                <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 rounded-b-xl">
                    <a href="{{ route('assets.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-100 transition shadow-sm">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 transition shadow-sm">
                        Create materiel
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
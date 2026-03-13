@section('title', 'Modifier Matériel ' . $asset->inventory_code)

<x-app-layout>
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Modifier {{ $asset->inventory_code }}</h1>
        <a href="{{ route('assets.show', $asset) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
            Annuler
        </a>
    </div>

    <!-- On charge Alpine avec le type actuel ET les specs existantes de la base de données -->
    <div x-data="{ 
        types: {{ $assetTypes->map->only(['id', 'spec_schema'])->toJson() }},
        selectedTypeId: '{{ old('asset_type_id', $asset->asset_type_id) }}',
        schema: {},
        existingSpecs: {{ json_encode(old('specs', $asset->specs ??[])) }},
        updateSchema() {
            let type = this.types.find(t => t.id == this.selectedTypeId);
            this.schema = type ? (type.spec_schema || {}) : {};
        }
    }" x-init="updateSchema()">

        <form action="{{ route('assets.update', $asset) }}" method="POST">
            @csrf
            @method('PUT') <!-- Obligatoire pour l'Update en Laravel -->

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col p-6 md:p-8">
                
                <h2 class="text-lg font-bold text-gray-900 mb-6">Materiel details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Code inventaire *</label>
                        <!-- Code inventaire souvent bloqué en édition pour la traçabilité, mais on le laisse modifiable ici -->
                        <input type="text" name="inventory_code" value="{{ old('inventory_code', $asset->inventory_code) }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Type materiel *</label>
                        <select name="asset_type_id" x-model="selectedTypeId" @change="updateSchema()" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            @foreach($assetTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Statut *</label>
                        <select name="status" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            @foreach(\App\Enums\AssetStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ old('status', $asset->status->value) == $status->value ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($status->value)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Localisation *</label>
                        <select name="current_location_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ old('current_location_id', $asset->current_location_id) == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1">Affecte a</label>
                        <select name="current_employee_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            <option value="">Non affecte</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ old('current_employee_id', $asset->current_employee_id) == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- SPECS DYNAMIQUES PRÉ-REMPLIES -->
                <div class="mt-10 mb-4 border-t border-gray-100 pt-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Specs</h2>
                    <div x-show="Object.keys(schema).length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-gray-50 border border-gray-100 rounded-xl">
                        <template x-for="(rules, key) in schema" :key="key">
                            <div>
                                <label :for="'spec_'+key" class="block text-sm font-bold text-gray-900 mb-1 capitalize" x-text="key.replace(/_/g, ' ')"></label>
                                
                                <template x-if="rules.type === 'select'">
                                    <!-- On pré-sélectionne la valeur existante -->
                                    <select :name="'specs[' + key + ']'" x-model="existingSpecs[key]" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                        <option value="">Sélectionner...</option>
                                        <template x-for="val in rules.values">
                                            <option :value="val" x-text="val"></option>
                                        </template>
                                    </select>
                                </template>

                                <template x-if="rules.type !== 'select'">
                                    <!-- On injecte la valeur existante dans l'input -->
                                    <input :type="rules.type === 'number' ? 'number' : 'text'" :name="'specs[' + key + ']'" x-model="existingSpecs[key]" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-green-700 rounded-lg text-sm font-bold text-white hover:bg-green-800 transition">
                        Enregistrer les modifications
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
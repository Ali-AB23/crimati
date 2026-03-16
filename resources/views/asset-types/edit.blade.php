@section('title', 'Modifier Type : ' . $assetType->name)

<x-app-layout>

    <!-- BREADCRUMB & HEADER -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <span class="text-gray-400">Referentiels</span> 
            <span class="mx-1">/</span> 
            <a href="{{ route('asset-types.index') }}" class="hover:underline text-gray-500">Types materiel</a>
            <span class="mx-1">/</span> 
            <span class="text-gray-900 font-medium">Edit</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Modifier {{ $assetType->name }}</h1>
            
            <a href="{{ route('asset-types.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to list
            </a>
        </div>
    </div>

    <!-- AFFICHAGE DES ERREURS -->
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

    <!-- 
        INITIALISATION ALPINE.JS (MODE ÉDITION)
    -->
    <div x-data="{ 
            name: '{{ old('name', $assetType->name) }}',
            categoryId: '{{ old('asset_category_id', $assetType->asset_category_id) }}',
            
            // On injecte le JSON existant depuis PHP vers JS
            existingSchema: {{ json_encode($assetType->spec_schema ??[]) }},
            schemaRows:[], 
            
            init() {
                // Au chargement, on 'détricote' le JSON pour remplir les lignes du formulaire
                if (Object.keys(this.existingSchema).length > 0) {
                    for (const [key, details] of Object.entries(this.existingSchema)) {
                        this.schemaRows.push({
                            key: key,
                            type: details.type || 'text',
                            options: (details.values && Array.isArray(details.values)) ? details.values.join(', ') : ''
                        });
                    }
                }
            },
            
            generateJsonSchema() {
                let finalSchema = {};
                this.schemaRows.forEach(row => {
                    if (row.key.trim() !== '') {
                        let safeKey = row.key.trim().toLowerCase().replace(/[^a-z0-9]/g, '_');
                        
                        finalSchema[safeKey] = {
                            type: row.type || 'text',
                        };
                        
                        if(row.type === 'select' && row.options.trim() !== '') {
                            finalSchema[safeKey].values = row.options.split(',').map(s => s.trim());
                        }
                    }
                });
                return JSON.stringify(finalSchema);
            }
         }">

        <!-- ATTENTION : Method PUT pour l'Update -->
        <form action="{{ route('asset-types.update', $assetType) }}" method="POST" @submit="document.getElementById('hidden_schema').value = generateJsonSchema();">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="spec_schema" id="hidden_schema" value="">

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 md:p-8 flex-1">
                    
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Type details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="name" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1">Categorie parent <span class="text-red-500">*</span></label>
                            <select name="asset_category_id" x-model="categoryId" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                <option value="" disabled>Sélectionner une catégorie...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- LE CONSTRUCTEUR DE SCHÉMA (Inclus depuis le partial) -->
                    @include('asset-types.partials.spec-schema')

                </div>

                <div class="px-6 md:px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 rounded-b-xl">
                    <a href="{{ route('asset-types.index') }}" class="px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-100 transition shadow-sm">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 transition shadow-sm">
                        Save type
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-app-layout>
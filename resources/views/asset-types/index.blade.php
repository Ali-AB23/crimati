@section('title', 'Types de matériel')

<x-app-layout>

    <!-- HEADER & BREADCRUMB -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <span class="text-gray-400">Référentiels</span> 
            <span class="mx-1">/</span> 
            <span class="text-gray-500 font-medium">Types de matériel</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Types de matériel</h1>
            
            <!-- BOUTON ADD TYPE (Redirige vers la page Create) -->
            <a href="{{ route('asset-types.create') }}" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition w-full sm:w-auto justify-center">
                <span class="mr-2 text-lg leading-none">+</span> Ajouter un type
            </a>
        </div>
    </div>

    <!-- AFFICHAGE DES ERREURS DE SUPPRESSION (try/catch du controller) -->
    @error('delete_error')
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.78 7.25a.75.75 0 00-1.06 1.06L9.44 10l-1.72 1.69a.75.75 0 101.06 1.06L10.56 11l1.72 1.69a.75.75 0 101.06-1.06L11.56 10l1.72-1.69a.75.75 0 00-1.06-1.06L10.56 9 8.78 7.25z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ $message }}</p>
                </div>
            </div>
        </div>
    @enderror

    <!-- ZONE DE RECHERCHE -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="p-5">
            <div class="flex items-center mb-4">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Recherche</h2>
            </div>
            
            <form action="{{ route('asset-types.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                
                <div class="w-full sm:w-1/2 md:w-1/3">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Nom</label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="PC Portable" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm">
                </div>

                <div class="w-full sm:w-1/2 md:w-1/3">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-2">Modèle de spécifications</label>
                    <select name="has_schema" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm text-gray-600">
                        <option value="">Tous</option>
                        <option value="yes" {{ request('has_schema') == 'yes' ? 'selected' : '' }}>Avec modèlee</option>
                        <option value="no" {{ request('has_schema') == 'no' ? 'selected' : '' }}>Sans modèle</option>
                    </select>
                </div>

                <div class="flex space-x-3 w-full sm:w-auto">
                    <a href="{{ route('asset-types.index') }}" class="w-full sm:w-auto px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 text-center shadow-sm transition">Réinitialiser</a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 shadow-sm transition">Rechercher</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLEAU DES TYPES -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Types d’actifs</h2>
            <div class="text-sm text-gray-500">
                Showing {{ $types->firstItem() ?? 0 }}-{{ $types->lastItem() ?? 0 }} of {{ $types->total() }}
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Nom</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Modèle de spécifications</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($types as $type)
                    
                    <!-- X-DATA pour la modale Delete de cette ligne spécifique -->
                    <tr x-data="{ showDeleteModal: false }" class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm font-medium text-gray-900">{{ $type->name }}</td>
                        <td class="p-4 text-sm text-gray-600">{{ optional($type->category)->name ?? 'N/A' }}</td>
                        <td class="p-4">
                            @php
                                // On vérifie si un schéma JSON existe et n'est pas vide
                                $hasSchema = !empty($type->spec_schema);
                            @endphp
                            
                            @if($hasSchema)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                    Avec modèlee
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">
                                    Sans modèle
                                </span>
                            @endif
                        </td>
                        
                        <td class="p-4 text-sm font-medium text-right space-x-3">
                            <!-- REDIRECTION VERS LA PAGE EDIT -->
                            <a href="{{ route('asset-types.edit', $type) }}" class="text-green-600 hover:text-green-800">Modifier</a>
                            
                            <!-- DÉCLENCHEUR MODALE DELETE -->
                            <button @click="showDeleteModal = true" class="text-red-600 hover:text-red-800">Supprimer</button>

                            <!-- ========================================== -->
                            <!-- MODALE DELETE -->
                            <!-- ========================================== -->
                            <template x-teleport="body">
                                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                                    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
                                        <div x-show="showDeleteModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                                        <div x-show="showDeleteModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md w-full p-6 text-center">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Supprimer la catégorie</h3>
                                            <p class="text-sm text-gray-500 mt-2 mb-6">Êtes-vous sûr de vouloir supprimer <strong>{{ $type->name }}</strong> ? Cette action est irréversible.</p>
                                            <form action="{{ route('asset-types.destroy', $type) }}" method="POST" class="flex justify-center gap-3">
                                                @csrf @method('DELETE')
                                                <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 w-full hover:bg-gray-50">Cancel</button>
                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold w-full hover:bg-red-700">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-6 text-center text-gray-500 text-sm">Aucun type d’actif trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($types->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $types->links() }}
        </div>
        @endif
    </div>

</x-app-layout>
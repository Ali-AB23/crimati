@section('title', 'Lieux physiques')

<x-app-layout>

    <!-- HEADER & BREADCRUMB -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <span class="text-gray-400">Référentiels</span> 
            <span class="mx-1">/</span> 
            <span class="text-gray-500 font-medium">Lieux</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Lieux</h1>
            
            <!-- BOUTON Ajouter un lieu (Modale avec Alpine) -->
            <div x-data="{ showCreateModal: false }">
                <button @click="showCreateModal = true" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition w-full sm:w-auto justify-center">
                    <span class="mr-2 text-lg leading-none">+</span> Ajouter un lieu
                </button>

                <!-- MODALE : Ajouter un lieu -->
                <template x-teleport="body">
                    <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
                            <div x-show="showCreateModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCreateModal = false"></div>
                            
                            <div x-show="showCreateModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-lg w-full p-6">
                                
                                <div class="mb-5">
                                    <h3 class="text-xl font-bold text-gray-900">Ajouter un lieu</h3>
                                    <p class="text-sm text-gray-500 mt-1">Créer un nouvel emplacement physique.</p>
                                </div>
                                
                                <form action="{{ route('locations.store') }}" method="POST">
                                    @csrf
                                    <div class="space-y-4 mb-6">
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nom du lieu *</label>
                                            <input type="text" name="name" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Type de lieu *</label>
                                            <select name="type" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                <option value="">Sélectionner un type...</option>
                                                @foreach($types as $type)
                                                    <option value="{{ $type->value }}">{{ $type->value }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Lieu parent (Optionnel)</label>
                                            <select name="parent_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                <option value="">- Aucun parent (Ex: Bâtiment) -</option>
                                                @foreach($potentialParents as $parent)
                                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <!-- BONUS MLD : Unité responsable (pour les lieux communs) -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Unité responsable (optionnel)</label>
                                            <select name="org_unit_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                <option value="">- Aucune unité -</option>
                                                @foreach($orgUnits as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                            <p class="text-[10px] text-gray-400 mt-1 leading-tight">Obligatoire uniquement pour les "Lieux Communs" (salle de réunion, stock) afin que les employés de ce service puissent y créer des tickets.</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Annuler</button>
                                        <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 transition shadow-sm">Créer le lieu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- ZONE DE RECHERCHE -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Recherche</h2>
        </div>
        
        <div class="p-5">
            <form action="{{ route('locations.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                
                <div class="w-full md:w-1/3">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Nom du lieu</label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Bureau 2.14" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm">
                </div>

                <div class="w-full md:w-1/3">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Type du lieu</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm text-gray-600">
                        <option value="">Tous</option>
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:w-1/3">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Parent</label>
                    <select name="parent_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm text-gray-600">
                        <option value="">Tous</option>
                        @foreach($potentialParents as $parent)
                            <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex space-x-3 w-full md:w-auto">
                    <a href="{{ route('locations.index') }}" class="w-full md:w-auto px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 text-center shadow-sm transition">Réinitialiser</a>
                    <button type="submit" class="w-full md:w-auto px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 shadow-sm transition">Recherche</button>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLEAU DES LIEUX -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-900">Liste des lieux</h2>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Nom du lieu</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Unité responsable</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($locations as $loc)
                    
                    <!-- X-DATA pour chaque ligne (Modales Edit / Delete isolées) -->
                    <tr x-data="{ showEditModal: false, showDeleteModal: false }" class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm font-medium text-gray-900">{{ $loc->name }}</td>
                        <td class="p-4">
                            <span class="bg-gray-100 text-gray-700 text-[10px] font-bold px-2 py-1 rounded uppercase tracking-wider">
                                {{ str_replace('_', ' ', $loc->type->value) }}
                            </span>
                        </td>
                        <td class="p-4 text-sm text-gray-600">{{ optional($loc->parent)->name ?? '-' }}</td>
                        <td class="p-4 text-sm text-gray-600">{{ optional($loc->orgUnit)->name ?? '-' }}</td>
                        
                        <td class="p-4 text-sm font-medium text-right space-x-3">
                            <button @click="showEditModal = true" class="text-green-600 hover:text-green-800">Modifier</button>
                            <button @click="showDeleteModal = true" class="text-red-600 hover:text-red-800">Supprimer</button>

                            <!-- ========================================== -->
                            <!-- MODALE EDIT (Propre à cette ligne)       -->
                            <!-- ========================================== -->
                            <template x-teleport="body">
                                <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto text-left">
                                    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
                                        <div x-show="showEditModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showEditModal = false"></div>
                                        <div x-show="showEditModal" class="relative bg-white rounded-2xl overflow-hidden shadow-xl sm:my-8 sm:max-w-lg w-full p-6">
                                            
                                            <div class="mb-5">
                                                <h3 class="text-xl font-bold text-gray-900">Modifier le lieu</h3>
                                            </div>
                                            
                                            <form action="{{ route('locations.update', $loc) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="space-y-4 mb-6">
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nom du lieu</label>
                                                        <input type="text" name="name" value="{{ $loc->name }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Type</label>
                                                        <select name="type" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                            @foreach($types as $type)
                                                                <option value="{{ $type->value }}" {{ $loc->type->value == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Parent</label>
                                                        <select name="parent_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                            <option value="">- Aucun -</option>
                                                            @foreach($potentialParents as $parent)
                                                                <!-- On empêche un lieu d'être son propre parent -->
                                                                @if($parent->id != $loc->id)
                                                                    <option value="{{ $parent->id }}" {{ $loc->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Unite responsable</label>
                                                        <select name="org_unit_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                            <option value="">- Aucune unité -</option>
                                                            @foreach($orgUnits as $unit)
                                                                <option value="{{ $unit->id }}" {{ $loc->org_unit_id == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Cancel</button>
                                                    <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 transition shadow-sm">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- ========================================== -->
                            <!-- MODALE DELETE (Propre à cette ligne)     -->
                            <!-- ========================================== -->
                            <template x-teleport="body">
                                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                                    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
                                        <div x-show="showDeleteModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                                        <div x-show="showDeleteModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md w-full p-6 text-center">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Supprimer le lieu</h3>
                                            <p class="text-sm text-gray-500 mt-2 mb-6">Êtes-vous sûr de vouloir supprimer l'emplacement <strong>{{ $loc->name }}</strong>? Cette action est irréversible et retirera ce lieu de tout l'historique de l'application.</p>
                                            <form action="{{ route('locations.destroy', $loc) }}" method="POST" class="flex justify-center gap-3">
                                                @csrf @method('DELETE')
                                                <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 w-full hover:bg-gray-50">Annuler</button>
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
                        <td colspan="5" class="p-6 text-center text-gray-500 text-sm">Aucun lieu enregistré.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $locations->links() }}
        </div>
        @endif
    </div>

</x-app-layout>
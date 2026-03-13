@section('title', 'Matériels')

<x-app-layout>
    
    @php
        // On utilise le chemin complet (\App\Enums\...) pour éviter le bug du "use" dans Blade
        $role = Auth::user()->role->value;
        $isAdminOrInv = in_array($role,[
            \App\Enums\UserRole::ADMIN_IT->value, 
            \App\Enums\UserRole::INVENTORISTE->value
        ]);
    @endphp

    <!-- EN-TÊTE -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Materiels</h1>
        
        <!-- Boutons d'action (Admin/Inventoriste uniquement) -->
        @if($isAdminOrInv)
        <div class="flex space-x-3">
            <a href="#" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                Import Excel
            </a>
            <a href="{{ route('assets.create') }}" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                New materiel
            </a>
        </div>
        @endif
    </div>

    <!-- ZONE DE RECHERCHE ET FILTRES -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
            <h2 class="text-sm font-bold text-gray-900">Search and filters</h2>
        </div>
        
        <div class="p-5">
            <form action="{{ route('assets.index') }}" method="GET" class="space-y-4">
                
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
                    <!-- Code Inventaire -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Code Inventaire</label>
                        <input type="text" name="code" value="{{ request('code') }}" placeholder="Ex: 066/CRI/25" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 shadow-sm">
                    </div>

                    <!-- Statut -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Statut</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">Tous les statuts</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($status->value)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Localisation -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Localisation</label>
                        <select name="location_id" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">Toutes les localisations</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Catégorie -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Categorie</label>
                        <select name="category_id" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">Toutes les categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Type</label>
                        <select name="type_id" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">Tous les types</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ request('type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-2">
                    <a href="{{ route('assets.index') }}" class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                        Reset
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition">
                        Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- LISTE DES MATÉRIELS -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white">
            <div class="flex items-center space-x-3">
                <h2 class="text-lg font-bold text-gray-900">Materiels list</h2>
                <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded">{{ $assets->total() }} total</span>
            </div>
            <div class="text-sm text-gray-500">
                Showing {{ $assets->firstItem() ?? 0 }}-{{ $assets->lastItem() ?? 0 }} of {{ $assets->total() }} items
            </div>
        </div>
        
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Code Inventaire</th>
                        <th class="p-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="p-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="p-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Localisation</th>
                        <th class="p-5 text-xs font-bold text-gray-500 uppercase tracking-wider">Affecté À</th>
                        <th class="p-5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-5 text-sm font-medium text-gray-900">{{ $asset->inventory_code }}</td>
                        
                        <td class="p-5 text-sm text-gray-700 leading-snug">
                            {{ optional($asset->type)->name ?? 'N/A' }} <br>
                            @if($asset->type && $asset->type->category)
                                <span class="text-xs text-gray-400">({{ $asset->type->category->name }})</span>
                            @endif
                        </td>
                        
                        <td class="p-5">
                            @php
                                $statusColors =[
                                    'en_panne' => 'bg-red-50 text-red-600',
                                    'en_reparation' => 'bg-orange-100 text-orange-700',
                                    'en_stock' => 'bg-blue-50 text-blue-600',
                                    'en_service' => 'bg-green-50 text-green-600',
                                    'reforme' => 'bg-gray-100 text-gray-600',
                                ];
                                $colorClass = $statusColors[$asset->status->value] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium capitalize {{ $colorClass }}">
                                {{ str_replace('_', ' ', $asset->status->value) }}
                            </span>
                        </td>
                        
                        <td class="p-5 text-sm text-gray-600">{{ optional($asset->currentLocation)->name ?? 'N/A' }}</td>
                        
                        <td class="p-5 text-sm">
                            @if($asset->currentEmployee)
                                <span class="text-gray-900 whitespace-normal inline-block w-32 leading-tight">
                                    {{ $asset->currentEmployee->full_name }}
                                </span>
                            @else
                                <span class="text-gray-400">Non affecte</span>
                            @endif
                        </td>
                        
                        <td class="p-5 text-sm font-medium text-right space-x-3">
                            <a href="{{ route('assets.show', $asset->id) }}" class="text-green-700 hover:text-green-900">Voir</a>
                            
                            @if($isAdminOrInv)
                                <a href="{{ route('assets.edit', $asset->id) }}" class="text-green-700 hover:text-green-900">Modifier</a>
                                <a href="{{ route('assets.show', $asset->id) }}" class="text-green-700 hover:text-green-900" title="Allez sur la fiche pour déplacer">Deplacer</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                <span class="text-lg font-medium text-gray-900">Aucun matériel trouvé</span>
                                <p class="text-sm text-gray-500 mt-1">Modifiez vos filtres ou ajoutez un nouveau matériel.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
        <div class="p-4 border-t border-gray-100 bg-white">
            {{ $assets->links() }} 
        </div>
        @endif

    </div>

</x-app-layout>
@section('title', 'Historique des Mouvements')

<x-app-layout>

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Mouvements</h1>
        <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
            Voir les matériels
        </a>
    </div>

    <!-- ZONE DE FILTRES -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6 p-6">
        <form action="{{ route('movements.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                
                <!-- Filtre : Asset Code -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Code du matériel</label>
                    <input type="text" name="asset_code" value="{{ request('asset_code') }}" placeholder="e.g. 066/CRI/25" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 shadow-sm">
                </div>

                <!-- Filtre : Type -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Type</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                        <option value="">Tous les types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                                {{ ucfirst(strtolower($type->value)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre : From Location -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Depuis l'emplacement</label>
                    <select name="from_location_id" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                        <option value="">Choisir un emplacement</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('from_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre : To Location -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Depuis l'emplacement</label>
                    <select name="to_location_id" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                        <option value="">Choisir l’emplacement</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ request('to_location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre : Moved By -->
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Déplacé par</label>
                    <input type="text" name="moved_by_name" value="{{ request('moved_by_name') }}" placeholder="Rechercher un employé" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 shadow-sm">
                </div>

            </div>

            <!-- Boutons du filtre -->
            <div class="mt-5 flex justify-end space-x-3 border-t border-gray-100 pt-4">
                <a href="{{ route('movements.index') }}" class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition">
                    Réinitialiser les filtres
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Rechercher des mouvements
                </button>
            </div>
        </form>
    </div>

    <!-- TABLEAU DES MOUVEMENTS -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Déplacé le</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Matériel</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Depuis l'emplacement</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Vers l'emplacement</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">De l'employé</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">À l'employé</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Déplacé par</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Remarque</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50 transition">
                        
                        <!-- Date et Heure -->
                        <td class="p-5 text-sm text-gray-600">
                            {{ $movement->moved_at->format('Y-m-d H:i') }}
                        </td>
                        
                        <!-- Badges Type de Mouvement -->
                        <td class="p-5">
                            @php
                                $typeColors =[
                                    'AFFECTATION' => 'bg-green-100 text-green-700',
                                    'TRANSFERT'   => 'bg-yellow-100 text-yellow-700',
                                    'RETOUR'      => 'bg-blue-100 text-blue-700',
                                    'DEPLACEMENT' => 'bg-gray-100 text-gray-700',
                                ];
                                $typeClass = $typeColors[$movement->type->value] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $typeClass }}">
                                {{ $movement->type->value }}
                            </span>
                        </td>

                        <!-- Lien vers l'Asset en vert gras (Maquette) -->
                        <td class="p-5 text-sm font-bold text-green-600 hover:underline">
                            <a href="{{ route('assets.show', $movement->asset_id) }}">
                                {{ optional($movement->asset)->inventory_code ?? 'Deleted Asset' }}
                            </a>
                        </td>

                        <!-- From / To (Location) -->
                        <td class="p-5 text-sm text-gray-600">
                            <span class="whitespace-normal inline-block w-24 leading-tight">{{ optional($movement->fromLocation)->name ?? '-' }}</span>
                        </td>
                        <td class="p-5 text-sm text-gray-600">
                            <span class="whitespace-normal inline-block w-24 leading-tight">{{ optional($movement->toLocation)->name ?? '-' }}</span>
                        </td>

                        <!-- From / To (Employee) -->
                        <td class="p-5 text-sm text-gray-600">
                            <span class="whitespace-normal inline-block w-20 leading-tight">{{ optional($movement->fromEmployee)->full_name ?? '-' }}</span>
                        </td>
                        <td class="p-5 text-sm text-gray-600">
                            <span class="whitespace-normal inline-block w-20 leading-tight">{{ optional($movement->toEmployee)->full_name ?? '-' }}</span>
                        </td>

                        <!-- Moved By -->
                        <td class="p-5 text-sm text-gray-900 font-medium">
                            <span class="whitespace-normal inline-block w-20 leading-tight">{{ optional($movement->movedBy->employee)->full_name ?? optional($movement->movedBy)->username }}</span>
                        </td>

                        <!-- Note -->
                        <td class="p-5 text-xs text-gray-500">
                            <span class="whitespace-normal inline-block w-32 leading-tight">{{ $movement->note ?? '-' }}</span>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-500">
                            <span class="text-lg font-medium text-gray-900">Aucun mouvement enregistré.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
        <div class="p-4 border-t border-gray-100 bg-white">
            {{ $movements->links() }}
        </div>
        @endif
    </div>

</x-app-layout>
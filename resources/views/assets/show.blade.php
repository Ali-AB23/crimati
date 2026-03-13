@section('title', 'Détails Matériel ' . $asset->inventory_code)

<x-app-layout>

    @php
        // CORRECTION ICI : On utilise le chemin absolu de la classe (\App\Enums\...)
        $role = Auth::user()->role->value;
        $isAdminOrInv = in_array($role,[
            \App\Enums\UserRole::ADMIN_IT->value, 
            \App\Enums\UserRole::INVENTORISTE->value
        ]);

        // Définition des couleurs de statuts
        $statusColors = [
            'en_panne' => 'bg-red-100 text-red-700',
            'en_reparation' => 'bg-orange-100 text-orange-700',
            'en_stock' => 'bg-blue-100 text-blue-700',
            'en_service' => 'bg-green-100 text-green-700',
            'reforme' => 'bg-gray-200 text-gray-700',
        ];
        $colorClass = $statusColors[$asset->status->value] ?? 'bg-gray-100 text-gray-800';
    @endphp

    <!-- BREADCRUMB & HEADER -->
    <div x-data="{ showMoveModal: false, showDeleteModal: false }">
        <div class="mb-6">
            <div class="text-sm text-gray-500 mb-2">
                <a href="{{ route('assets.index') }}" class="hover:underline">Materiels</a> 
                <span class="mx-1">&gt;</span> 
                <span class="text-gray-400">{{ $asset->inventory_code }}</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <h1 class="text-2xl font-bold text-gray-900">Materiel {{ $asset->inventory_code }}</h1>
                
                <div class="flex flex-wrap gap-2 sm:space-x-3">
                    <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Back to list</a>
                    
                    @if($isAdminOrInv)
                        <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Edit</a>
                        
                        <!-- 1. BOUTON DEPLACER CONNECTÉ À ALPINE -->
                        <button type="button" @click="showMoveModal = true" class="px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 transition">
                            Deplacer
                        </button>
                        
                        <!-- 2. BOUTON DELETE CONNECTÉ À ALPINE (Plus de <form> direct ici) -->
                        <button type="button" @click="showDeleteModal = true" class="px-4 py-2 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition">
                            Delete
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- CARTE RÉSUMÉ (TOP) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Code Inventaire</p>
                    <p class="text-sm font-bold text-gray-900">{{ $asset->inventory_code }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Type</p>
                    <p class="text-sm font-bold text-gray-900">{{ optional($asset->type)->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Categorie</p>
                    <p class="text-sm font-bold text-gray-900">{{ optional(optional($asset->type)->category)->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Statut</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold capitalize {{ $colorClass }}">
                        {{ str_replace('_', ' ', $asset->status->value) }}
                    </span>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Localisation</p>
                    <p class="text-sm font-bold text-gray-900">{{ optional($asset->currentLocation)->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Affecte A</p>
                    <p class="text-sm font-bold text-gray-900">{{ optional($asset->currentEmployee)->full_name ?? 'Non affecté' }}</p>
                </div>
            </div>
        </div>

        <!-- GRILLE PRINCIPALE (2/3 Gauche, 1/3 Droite) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- COLONNE DE GAUCHE (Contenu principal) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- INFORMATIONS GÉNÉRALES -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">General information</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <p class="text-[11px] font-bold text-gray-500 mb-1">Marque</p>
                            <p class="text-sm text-gray-900">{{ $asset->brand ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-500 mb-1">Modele</p>
                            <p class="text-sm text-gray-900">{{ $asset->model ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-500 mb-1">Numero de serie</p>
                            <p class="text-sm text-gray-900">{{ $asset->serial_number ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-gray-500 mb-1">Notes</p>
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $asset->notes ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- SPECS (JSON Dynamique) -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Specs</h2>
                        <p class="text-xs text-gray-400 mt-1">Technical attributes (from asset specs).</p>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-1/3">Spec</th>
                                <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Value</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if($asset->specs && is_array($asset->specs) && count($asset->specs) > 0)
                                @foreach($asset->specs as $key => $value)
                                <tr class="hover:bg-gray-50">
                                    <!-- Formatage de la clé: "ram_gb" devient "Ram Gb" -->
                                    <td class="p-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $key) }}</td>
                                    <td class="p-4 text-sm text-gray-900 font-medium">{{ is_array($value) ? json_encode($value) : $value }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2" class="p-4 text-center text-sm text-gray-500">Aucune spécification technique renseignée.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- HISTORIQUE DES MOUVEMENTS -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Recent movements</h2>
                    </div>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Moved At</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Type</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">From Loc</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">To Loc</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">From Emp</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">To Emp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($asset->movements->sortByDesc('moved_at') as $movement)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 text-sm text-gray-600">{{ $movement->moved_at->format('Y-m-d') }}</td>
                                    <td class="p-4 text-sm text-gray-900 font-medium capitalize">{{ strtolower($movement->type->value) }}</td>
                                    <td class="p-4 text-sm text-gray-600">{{ optional($movement->fromLocation)->name ?? 'Stock' }}</td>
                                    <td class="p-4 text-sm text-gray-900">{{ optional($movement->toLocation)->name ?? 'Stock' }}</td>
                                    <td class="p-4 text-sm text-gray-600">{{ optional($movement->fromEmployee)->full_name ?? 'N/A' }}</td>
                                    <td class="p-4 text-sm text-gray-900">{{ optional($movement->toEmployee)->full_name ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-center text-sm text-gray-500">Aucun historique disponible.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TICKETS LIÉS -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900">Linked tickets</h2>
                    </div>
                    <div class="overflow-x-auto w-full">
                        <table class="w-full text-left border-collapse whitespace-nowrap">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100">
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Reference</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Priorite</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Statut</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date Limite</th>
                                    <th class="p-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($asset->tickets->sortByDesc('created_at') as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-4 text-sm font-medium text-gray-900">{{ $ticket->reference }}</td>
                                    <td class="p-4">
                                        @php
                                            $prioColors =[
                                                'urgent' => 'bg-red-100 text-red-700',
                                                'high'   => 'bg-orange-100 text-orange-700',
                                                'medium' => 'bg-blue-100 text-blue-700',
                                                'low'    => 'bg-gray-100 text-gray-700',
                                            ];
                                            $prioClass = $prioColors[$ticket->priority->value] ?? 'bg-gray-100 text-gray-800';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $prioClass }}">
                                            {{ $ticket->priority->value }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $ticket->status->value) }}</td>
                                    <td class="p-4 text-sm text-gray-600">
                                        @if($ticket->due_at)
                                            <div class="flex items-center space-x-2">
                                                <span>{{ $ticket->due_at->format('Y-m-d') }}</span>
                                                @if($ticket->due_at->isPast() && !in_array($ticket->status->value,['resolu', 'ferme', 'annule']))
                                                    <span class="bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">LATE</span>
                                                @endif
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="p-4 text-sm font-medium">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-green-600 hover:text-green-800 font-bold">View</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="p-4 text-center text-sm text-gray-500">Aucun ticket lié à ce matériel.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- COLONNE DE DROITE (Sidebar de la page) -->
            @if($isAdminOrInv)
            <div class="space-y-6">
                
                <!-- ASSIGNMENT -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center space-x-2 mb-6">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <h2 class="text-lg font-bold text-gray-900">Assignment</h2>
                    </div>

                    <div class="space-y-5">
                        <!-- Localisation -->
                        <div class="flex items-start space-x-4">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Localisation</p>
                                <p class="text-sm font-bold text-gray-900">{{ optional($asset->currentLocation)->name ?? 'Non définie' }}</p>
                            </div>
                        </div>

                        <!-- Employé -->
                        <div class="flex items-start space-x-4">
                            @if($asset->currentEmployee)
                                <img class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0" 
                                    src="https://ui-avatars.com/api/?name={{ urlencode($asset->currentEmployee->full_name) }}&background=f3f4f6&color=111827&bold=true" 
                                    alt="Avatar">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center shrink-0 border border-gray-100">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </div>
                            @endif
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5">Employe</p>
                                <p class="text-sm font-bold text-gray-900">{{ optional($asset->currentEmployee)->full_name ?? 'Non affecté' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                

            </div>
            @endif

        </div>
        @if($isAdminOrInv)
            @include('assets.modals.move')
            @include('assets.modals.delete')
        @endif
    </div>
</x-app-layout>
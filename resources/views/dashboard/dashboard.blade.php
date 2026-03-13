<x-app-layout>
    <!-- EN-TÊTE -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <div class="flex flex-wrap gap-2 sm:gap-3">
            <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">View materiels</a>
            <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">View reclamations</a>
        </div>
    </div>

    <!-- LES 6 CARTES (KPIs) -->
    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-green-600 p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Assets</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_assets'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-green-600 p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Assets En_Panne</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['broken_assets'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-green-600 p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">En_Reparation</p>
            <p class="text-3xl font-bold text-gray-900">0</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-green-600 p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tickets Ouvert</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['active_tickets'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-green-600 p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tickets En_Cours</p>
            <p class="text-3xl font-bold text-gray-900">0</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-green-600 p-5">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tickets Late</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['late_tickets'] ?? 0 }}</p>
        </div>
    </div>

    <!-- ZONE DES TABLEAUX -->
    <div class="flex flex-col space-y-6 w-full">
        
        <!-- TABLEAU 1 : RECENT TICKETS -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Recent tickets</h2>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Reference</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Materiel</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Priority</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Due At</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Assigne A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-sm font-medium text-gray-900">{{ $ticket->reference }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ optional($ticket->asset)->inventory_code ?? 'N/A' }}</td>
                            <td class="p-4">
                                <span class="text-xs font-bold px-2 py-1 rounded bg-orange-100 text-orange-700 uppercase">
                                    {{ $ticket->priority->value }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-gray-600">{{ $ticket->status->value }}</td>
                            
                            <!-- NOUVEAU : DUE AT avec Badge LATE -->
                            <td class="p-4 text-sm text-gray-600">
                                @if($ticket->due_at)
                                    <div class="flex items-center space-x-2">
                                        <span class="leading-tight">
                                            {{ $ticket->due_at->format('Y-m-d') }}<br>
                                            <span class="text-xs text-gray-400">{{ $ticket->due_at->format('H:i') }}</span>
                                        </span>
                                        @if($ticket->due_at->isPast() && !in_array($ticket->status->value, ['resolu', 'ferme', 'annule']))
                                            <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">Late</span>
                                        @endif
                                    </div>
                                @else
                                    -
                                @endif
                            </td>
                            
                            <!-- NOUVEAU : ASSIGNE A -->
                            <td class="p-4 text-sm text-gray-600">
                                @if($ticket->assignedTo && $ticket->assignedTo->employee)
                                    <span class="whitespace-normal inline-block w-24 leading-tight">
                                        {{ $ticket->assignedTo->employee->full_name }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-4 text-center text-gray-500 text-sm">Aucun ticket récent.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TABLEAU 2 : ASSETS NEEDING ATTENTION -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Assets needing attention</h2>
            </div>
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Code Inventaire</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Localisation</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Affecte A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($attentionAssets as $asset)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-sm font-bold text-gray-900">{{ $asset->inventory_code }}</td>
                            
                            <!-- NOUVEAU : Type avec Catégorie en dessous -->
                            <td class="p-4 text-sm text-gray-600 leading-tight">
                                {{ optional($asset->type)->name ?? 'N/A' }} <br>
                                @if($asset->type && $asset->type->category)
                                    <span class="text-xs text-gray-400">({{ $asset->type->category->name }})</span>
                                @endif
                            </td>
                            
                            <!-- NOUVEAU : Statut en texte rouge -->
                            <td class="p-4 text-sm font-bold text-red-600 capitalize">
                                {{ str_replace('_', ' ', $asset->status->value) }}
                            </td>
                            
                            <td class="p-4 text-sm text-gray-600">{{ optional($asset->currentLocation)->name ?? 'N/A' }}</td>
                            
                            <!-- NOUVEAU : Affecté à -->
                            <td class="p-4 text-sm text-gray-600">
                                @if($asset->currentEmployee)
                                    <span class="whitespace-normal inline-block w-24 leading-tight text-gray-900">
                                        {{ $asset->currentEmployee->full_name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">Non affecte</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-500 text-sm">Tout le matériel est opérationnel !</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
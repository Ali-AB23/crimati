<x-app-layout>

    @php
        // On détermine si l'utilisateur est un simple employé
        $isEmployee = Auth::user()->role->value === \App\Enums\UserRole::EMPLOYE->value;
    @endphp

    <!-- ======================================================= -->
    <!-- 1. VUE SPÉCIFIQUE POUR L'EMPLOYÉ                        -->
    <!-- ======================================================= -->
    @if($isEmployee)
        
        <!-- EN-TÊTE EMPLOYÉ -->
        <div class="mb-8 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bonjour, {{ Auth::user()->employee->full_name ?? Auth::user()->username }} </h1>
                <p class="text-sm text-gray-500 mt-1">Bienvenue sur votre espace. Voici un résumé de vos équipements et demandes.</p>
            </div>
            <a href="{{ route('tickets.create') }}" class="px-4 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 transition shadow-sm whitespace-nowrap">
                + Nouvelle demande
            </a>
        </div>

        <!-- CARTES (KPIs) RESPONSIVES  de employee -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            
            <!-- Carte 1 : Mon Matériel Personnel -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex items-center justify-between border-l-4 border-l-green-600 hover:shadow-md transition">
                <div>
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Mon Matériel Personnel</h2>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['my_assets'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
            </div>

            <!-- Carte 2 : Matériels de mon Service -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex items-center justify-between border-l-4 border-l-emerald-400 hover:shadow-md transition">
                <div>
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Matériels de mon Service</h2>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['public_assets'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-full text-emerald-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
            </div>

            <!-- Carte 3 : Mes Réclamations en cours -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex items-center justify-between border-l-4 border-l-blue-500 hover:shadow-md transition">
                <div>
                    <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1">Réclamations en cours</h2>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['active_tickets'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-blue-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
            </div>
        </div>

        <!-- TABLEAU UNIQUE : MES DERNIERS TICKETS -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Mes dernières réclamations</h2>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Référence</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Matériel</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-sm font-medium text-gray-900">{{ $ticket->reference }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ optional($ticket->asset)->inventory_code ?? 'N/A' }}</td>
                            <td class="p-4">
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $ticket->status->value) }}
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-green-600 text-sm font-bold hover:text-green-800 transition">Voir détails</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500 text-sm">Vous n'avez aucune réclamation récente.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    <!-- ======================================================= -->
    <!-- 2. VUE GLOBALE POUR L'ADMIN & INVENTORISTE              -->
    <!-- ======================================================= -->
    @else

        <!-- EN-TÊTE (Ton code original) -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">View materiels</a>
                <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">View reclamations</a>
            </div>
        </div>

        <!-- LES 6 CARTES (KPIs) (Ton code original) -->
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

        <!-- ZONE DES TABLEAUX (Ton code original) -->
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
                                <td class="p-4 text-sm text-gray-600">{{ str_replace('_', ' ', $ticket->status->value) }}</td>
                                
                                <td class="p-4 text-sm text-gray-600">
                                    @if($ticket->due_at)
                                        <div class="flex items-center space-x-2">
                                            <span class="leading-tight">
                                                {{ $ticket->due_at->format('Y-m-d') }}<br>
                                                <span class="text-xs text-gray-400">{{ $ticket->due_at->format('H:i') }}</span>
                                            </span>
                                            @if($ticket->due_at->isPast() && !in_array($ticket->status->value,['resolu', 'ferme', 'annule']))
                                                <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase">Late</span>
                                            @endif
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                
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
                                
                                <td class="p-4 text-sm text-gray-600 leading-tight">
                                    {{ optional($asset->type)->name ?? 'N/A' }} <br>
                                    @if($asset->type && $asset->type->category)
                                        <span class="text-xs text-gray-400">({{ $asset->type->category->name }})</span>
                                    @endif
                                </td>
                                
                                <td class="p-4 text-sm font-bold text-red-600 capitalize">
                                    {{ str_replace('_', ' ', $asset->status->value) }}
                                </td>
                                
                                <td class="p-4 text-sm text-gray-600">{{ optional($asset->currentLocation)->name ?? 'N/A' }}</td>
                                
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

    @endif

</x-app-layout>
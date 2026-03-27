<x-app-layout>

    @php
        $role = Auth::user()->role->value;
        $isEmployee = $role === \App\Enums\UserRole::EMPLOYE->value;
        $isAdmin = $role === \App\Enums\UserRole::ADMIN_IT->value;


        $prioTranslations =[
        'urgent' => 'Urgente',
        'high'   => 'Haute',
        'medium' => 'Normale',
        'low'    => 'Basse',
    ];
        $prioColors = [
        'urgent' => ['bg' => 'bg-red-100', 'text' => 'text-red-700'],
        'high'   => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700'],
        'medium' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
        'low'    => ['bg' => 'bg-green-100', 'text' => 'text-green-700'],
    ];
    
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
                + Nouvelle réclamation
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

        <!-- EN-TÊTE 100% FRANÇAIS -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tableau de bord</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Vue globale du parc matériel 
                    @if($isAdmin) et des réclamations @endif.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 sm:gap-3">
                <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">Voir matériels</a>
                @if($isAdmin)
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">Voir réclamations</a>
                @endif
            </div>
        </div>

        <!-- LES 6 CARTES (KPIs) : 100% FRANÇAIS -->
        <div class="grid grid-cols-2 md:grid-cols-3 {{ $isAdmin ? 'xl:grid-cols-6' : '' }} gap-4 mb-8">

            <!-- Total Matériels -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-blue-500 p-5 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Total Matériels</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['total_assets'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 md:p-3 bg-blue-50 rounded-full text-blue-500">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- En Panne -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-red-500 p-5 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">En Panne</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['broken_assets'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 md:p-3 bg-red-50 rounded-full text-red-500">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86l-7 12A1 1 0 004.14 18h15.72a1 1 0 00.85-1.54l-7-12a1 1 0 00-1.7 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- En Réparation -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-orange-400 p-5 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">En Réparation</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ $chartData['status']['en_reparation'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 md:p-3 bg-orange-50 rounded-full text-orange-500">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.7 6.3a4 4 0 01-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4l-3 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            @if($isAdmin)

            <!-- Tickets Ouverts -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-green-500 p-5 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tickets Ouverts</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['active_tickets'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 md:p-3 bg-green-50 rounded-full text-green-500">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tickets En Cours -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-indigo-500 p-5 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tickets En Cours</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900">
                            {{ max(0, ($stats['active_tickets'] ?? 0) - ($chartData['status']['en_panne'] ?? 0)) }}
                        </p>
                    </div>
                    <div class="p-2 md:p-3 bg-indigo-50 rounded-full text-indigo-500">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tickets En Retard -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm border-l-4 border-l-rose-600 p-5 hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Tickets En Retard</p>
                        <p class="text-2xl md:text-3xl font-bold text-gray-900">{{ $stats['late_tickets'] ?? 0 }}</p>
                    </div>
                    <div class="p-2 md:p-3 bg-rose-50 rounded-full text-rose-600">
                        <svg class="w-6 h-6 md:w-7 md:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M4.93 19h14.14c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.2 16c-.77 1.33.19 3 1.73 3z"/>
                        </svg>
                    </div>
                </div>
            </div>

            @endif

        </div>

        <!-- 📊 LES GRAPHIQUES (Pour Admin & Inventoriste) -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" integrity="sha512-CQBWl4fJHWbryGE+Pc7UAxWMUMNMWzWxF4SQo9CgkJIN1kx6djDQZjh3Y8SZ1d+6I+1zze6Z7kHXO7q3UyZAWw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 w-full mb-8">
            <!-- GRAPHIQUE 1 -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Répartition du parc matériel</h2>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- GRAPHIQUE 2 -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Évolution des ajouts (6 derniers mois)</h2>
                <div class="relative h-64 w-full">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- TABLEAU DES TICKETS RÉCENTS (Réservé à l'Admin IT) -->
        @if($isAdmin)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col mb-8">
            <div class="p-5 border-b border-gray-100 shrink-0">
                <h2 class="text-lg font-bold text-gray-900">Dernières réclamations</h2>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Référence</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Matériel</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Priorité</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Statut</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Date Limite</th>
                            <th class="p-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Assigné À</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTickets as $ticket)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4 text-sm font-medium text-gray-900">{{ $ticket->reference }}</td>
                            <td class="p-4 text-sm text-gray-600">{{ optional($ticket->asset)->inventory_code ?? 'N/A' }}</td>
                            <td class="p-4">
                                <span class="text-[10px] font-bold px-2 py-1 rounded {{ $prioColors[$ticket->priority->value]['bg'] }} {{ $prioColors[$ticket->priority->value]['text'] }} uppercase">
                                    {{ $ticket->priority->value }}
                                </span>
                            </td>
                            <td class="p-4 text-sm text-gray-600">{{ str_replace('_', ' ', ucfirst($ticket->status->value)) }}</td>
                            
                            <td class="p-4 text-sm text-gray-600">
                                @if($ticket->due_at)
                                    <div class="flex items-center space-x-2">
                                        <span class="leading-tight">
                                            {{ $ticket->due_at->format('Y-m-d') }}<br>
                                            <span class="text-[10px] text-gray-400">{{ $ticket->due_at->format('H:i') }}</span>
                                        </span>
                                        @if($ticket->due_at->isPast() && !in_array($ticket->status->value,['resolu', 'ferme', 'annule']))
                                            <span class="bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wider">RETARD</span>
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
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-gray-500 text-sm">Aucune réclamation récente.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- INITIALISATION DES GRAPHIQUES -->
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // 1. Graphique Doughnut (Statuts)
                const ctxStatus = document.getElementById('statusChart').getContext('2d');
                new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: ['En Stock', 'En Service', 'En Panne', 'En Réparation', 'Réformé'],
                        datasets: [{
                            data: [
                                {{ $chartData['status']['en_stock'] }},
                                {{ $chartData['status']['en_service'] }},
                                {{ $chartData['status']['en_panne'] }},
                                {{ $chartData['status']['en_reparation'] }},
                                {{ $chartData['status']['reforme'] }}
                            ],
                            backgroundColor:[
                                '#3b82f6', // Bleu
                                '#22c55e', // Vert
                                '#ef4444', // Rouge
                                '#f97316', // Orange
                                '#9ca3af'  // Gris
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        cutout: '70%'
                    }
                });

                // 2. Graphique Line (Évolution dans le temps)
                const ctxTrend = document.getElementById('trendChart').getContext('2d');
                new Chart(ctxTrend, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartData['trendLabels']) !!},
                        datasets:[{
                            label: 'Nouveaux matériels',
                            data: {!! json_encode($chartData['trendData']) !!},
                            borderColor: '#16a34a',
                            backgroundColor: 'rgba(22, 163, 74, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { precision: 0 }
                            }
                        }
                    }
                });
            });
        </script>

    @endif

</x-app-layout>

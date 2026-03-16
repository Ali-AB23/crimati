@section('title', 'Réclamations')

<x-app-layout>

    @php
        // CORRECTION : Plus de mot-clé "use", on utilise les chemins absolus (\App\Enums\...)
        $role = Auth::user()->role->value;
        $canTreat = $role === \App\Enums\UserRole::ADMIN_IT->value;
    @endphp

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Reclamations</h1>
        <div class="flex space-x-3">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">View dashboard</a>
            
            <a href="{{ route('tickets.create') }}" class="px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition">Nouveau Ticket</a>
        </div>
    </div>

    <!-- ZONE DE RECHERCHE ET FILTRES -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="p-6">
            <h2 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4">Search and filters</h2>
            
            <form action="{{ route('tickets.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Reference</label>
                        <input type="text" name="reference" value="{{ request('reference') }}" placeholder="TCK-2026-0001" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 focus:ring-green-500 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Statut</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">All</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ str_replace('_', ' ', ucfirst($status->value)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Priorite</label>
                        <select name="priority" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">All</option>
                            @foreach($priorities as $priority)
                                <option value="{{ $priority->value }}" {{ request('priority') == $priority->value ? 'selected' : '' }}>
                                    {{ ucfirst($priority->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Late</label>
                        <select name="late" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">All</option>
                            <option value="yes" {{ request('late') == 'yes' ? 'selected' : '' }}>Yes (En retard)</option>
                            <option value="no" {{ request('late') == 'no' ? 'selected' : '' }}>No (À temps)</option>
                        </select>
                    </div>

                    @if($canTreat)
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Assigne A</label>
                        <select name="assigned_to_user_id" class="w-full border-gray-300 rounded-lg text-sm text-gray-600 focus:border-green-500 focus:ring-green-500 shadow-sm">
                            <option value="">All</option>
                            @foreach($technicians as $tech)
                                <option value="{{ $tech->id }}" {{ request('assigned_to_user_id') == $tech->id ? 'selected' : '' }}>
                                    {{ $tech->employee->full_name ?? $tech->username }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                <div class="mt-5 flex space-x-3">
                    <button type="submit" class="px-6 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition">
                        Search
                    </button>
                    <a href="{{ route('tickets.index') }}" class="px-6 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- LISTE DES TICKETS -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-gray-900 uppercase tracking-wider">Tickets List</h2>
                <p class="text-sm text-gray-500">Showing {{ $tickets->firstItem() ?? 0 }}-{{ $tickets->lastItem() ?? 0 }} of {{ $tickets->total() }}</p>
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Materiel</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Priorite</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Date Limite</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Demandeur</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Assigne A</th>
                        <th class="p-5 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 transition">
                        
                        <td class="p-5 text-sm font-bold text-green-700">{{ $ticket->reference }}</td>
                        
                        <td class="p-5 text-sm text-gray-600">{{ optional($ticket->asset)->inventory_code ?? 'N/A' }}</td>
                        
                        <td class="p-5">
                            @php
                                $prioColors =[
                                    'urgent' => 'bg-red-100 text-red-700',
                                    'high'   => 'bg-orange-100 text-orange-700',
                                    'medium' => 'bg-blue-100 text-blue-700',
                                    'low'    => 'bg-gray-100 text-gray-700',
                                ];
                                $prioClass = $prioColors[$ticket->priority->value] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $prioClass }}">
                                {{ $ticket->priority->value }}
                            </span>
                        </td>

                        <td class="p-5">
                            @php
                                $statusColors =[
                                    'ouvert'   => 'bg-green-100 text-green-700',
                                    'assigne'  => 'bg-gray-100 text-gray-700',
                                    'en_cours' => 'bg-blue-100 text-blue-700',
                                    'resolu'   => 'bg-teal-100 text-teal-700',
                                    'ferme'    => 'bg-gray-200 text-gray-800',
                                    'annule'   => 'text-gray-400 font-semibold',
                                ];
                                $statusClass = $statusColors[$ticket->status->value] ?? 'text-gray-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[10px] font-bold uppercase {{ $statusClass }}">
                                {{ str_replace('_', ' ', $ticket->status->value) }}
                            </span>
                        </td>

                        <td class="p-5 text-sm text-gray-600">
                            @if($ticket->due_at)
                                <div class="flex items-center space-x-2">
                                    <span class="leading-tight">
                                        {{ $ticket->due_at->format('Y-m-d') }}<br>
                                        <span class="text-xs text-gray-400">{{ $ticket->due_at->format('H:i') }}</span>
                                    </span>
                                    
                                    @if($ticket->due_at->isPast() && !in_array($ticket->status->value, ['resolu', 'ferme', 'annule']))
                                        <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded">LATE</span>
                                    @endif
                                </div>
                            @else
                                -
                            @endif
                        </td>

                        <td class="p-5 text-sm text-gray-600">
                            <span class="whitespace-normal inline-block w-20 leading-tight">
                                {{ optional($ticket->requester)->full_name ?? 'N/A' }}
                            </span>
                        </td>

                        <td class="p-5 text-sm text-gray-600">
                            @if($ticket->assignedTo && $ticket->assignedTo->employee)
                                <span class="whitespace-normal inline-block w-20 leading-tight">
                                    {{ $ticket->assignedTo->employee->full_name }}
                                </span>
                            @else
                                <span class="text-gray-400 font-bold">-</span>
                            @endif
                        </td>

                        <td class="p-5 text-sm font-medium text-right space-y-1">
                            
                            <!-- CORRECTION : Utilisation de \App\Enums\TicketStatus:: au lieu de TicketStatus:: -->
                            @if($canTreat && !in_array($ticket->status->value,[\App\Enums\TicketStatus::FERME->value, \App\Enums\TicketStatus::ANNULE->value]))
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="block text-green-700 font-bold hover:text-green-900">Traiter</a>
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="block text-gray-400 hover:text-gray-600 text-xs">Voir</a>
                            @else
                                <a href="{{ route('tickets.show', $ticket->id) }}" class="block text-gray-400 hover:text-gray-600">Voir</a>
                            @endif
                            
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-8 text-center text-gray-500">
                            <span class="text-lg font-medium text-gray-900">Aucune réclamation trouvée.</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
        <div class="p-4 border-t border-gray-100 bg-white">
            {{ $tickets->links() }} 
        </div>
        @endif
    </div>

</x-app-layout>
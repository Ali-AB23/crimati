@section('title', 'Ticket ' . $ticket->reference)

<x-app-layout>

    @php
        $role = Auth::user()->role->value;
        $canTreat = $role === \App\Enums\UserRole::ADMIN_IT->value;

        // Couleurs de priorité
        $prioColors =[
            'urgent' => 'text-red-600',
            'high'   => 'text-orange-600',
            'medium' => 'text-yellow-600',
            'low'    => 'text-gray-600',
        ];
        $prioClass = $prioColors[$ticket->priority->value] ?? 'text-gray-600';
        $prioDot = str_replace('text-', 'bg-', $prioClass); // Pour le petit point de couleur

        // Couleurs de statut
        $statusColors =[
            'ouvert'   => 'bg-green-100 text-green-700',
            'assigne'  => 'bg-blue-100 text-blue-700',
            'en_cours' => 'bg-indigo-100 text-indigo-700',
            'resolu'   => 'bg-teal-100 text-teal-700',
            'ferme'    => 'bg-gray-200 text-gray-800',
            'annule'   => 'bg-gray-100 text-gray-500',
        ];
        $statusClass = $statusColors[$ticket->status->value] ?? 'bg-gray-100 text-gray-800';
    @endphp

    <!-- On enveloppe TOUTE la page dans x-data pour gérer les 3 modales -->
    <div x-data="{ showAssignModal: false, showStatusModal: false, showDateModal: false, showCancelModal: false }">

        <!-- BREADCRUMB & HEADER -->
        <div class="mb-6">
            <div class="text-sm text-gray-500 mb-2">
                <a href="{{ route('tickets.index') }}" class="hover:underline">Reclamations</a> 
                <span class="mx-1">&gt;</span> 
                <span class="text-gray-400">{{ $ticket->reference }}</span>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">Ticket {{ $ticket->reference }}</h1>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Badge Priorité -->
                        <div class="flex items-center bg-white border border-gray-200 rounded-full px-3 py-1 shadow-sm">
                            <span class="h-2 w-2 rounded-full {{ $prioDot }} mr-2"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-700">Priorité : <span class="{{ $prioClass }}">{{ $ticket->priority->value }}</span></span>
                        </div>

                        <!-- Badge Statut -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusClass }}">
                            Statut: {{ str_replace('_', ' ', $ticket->status->value) }}
                        </span>

                    </div>
                </div>
                
                <div class="flex flex-wrap gap-2 sm:space-x-3 mt-2 sm:mt-0">
                    <a href="{{ route('tickets.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">Retour à la liste</a>
                    
                    @if($canTreat && !in_array($ticket->status->value,[\App\Enums\TicketStatus::FERME->value, \App\Enums\TicketStatus::ANNULE->value]))
                        <!-- Bouton qui ouvre la modale de statut -->
                        <button type="button" @click="showStatusModal = true" class="px-6 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-bold text-white hover:bg-green-800 transition shadow-sm">
                            Traiter
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- GRILLE PRINCIPALE (2/3 Gauche, 1/3 Droite) -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <!-- COLONNE DE GAUCHE -->
            <div class="xl:col-span-2 space-y-6">
                
                <!-- DESCRIPTION -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <h2 class="text-lg font-bold text-gray-900">Description du problème</h2>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>
                    </div>
                </div>

                <!-- COMMENTS SECTION -->
                <!-- COMMENTS SECTION (Style Conversation) -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
                    
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-white shrink-0">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            <h2 class="text-lg font-bold text-gray-900">Fil de discussion</h2>
                        </div>
                        <span class="bg-gray-100 text-gray-600 text-xs font-medium px-2.5 py-0.5 rounded">{{ $ticket->comments->count() }} message(s)</span>
                    </div>
                    
                    <!-- Zone des messages (Avec hauteur max et scroll) -->
                    <div class="p-6 space-y-6 max-h-[500px] overflow-y-auto bg-gray-50 flex flex-col">
                        
                        @forelse($ticket->comments as $comment)
                            @php
                                // On vérifie si c'est MON message
                                $isMe = Auth::id() === $comment->user_id;
                                $authorName = optional($comment->author->employee)->full_name ?? $comment->author->username;
                                $authorInitials = strtoupper(substr($authorName, 0, 2)); // ex: AS pour Administrateur Système
                            @endphp

                            <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }} w-full">
                                
                                <div class="flex {{ $isMe ? 'flex-row-reverse' : 'flex-row' }} items-end max-w-[85%] sm:max-w-[75%] gap-3">
                                    
                                    <!-- Avatar (Initials façon maquette) -->
                                    {{-- <div class="shrink-0 h-10 w-10 rounded-full flex items-center justify-center font-bold text-sm {{ $isMe ? 'bg-green-100 text-green-700' : 'bg-white border border-gray-200 text-gray-600 shadow-sm' }}">
                                        {{ $authorInitials }}
                                    </div> --}}

                                    <!-- Bulle de message -->
                                    <div class="flex flex-col {{ $isMe ? 'items-end' : 'items-start' }}">
                                        
                                        <!-- Nom et Date -->
                                        <div class="flex items-baseline gap-2 mb-1 px-1">
                                            <span class="text-xs font-bold text-gray-900">{{ $isMe ? 'Vous' : $authorName }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $comment->created_at->format('Y-m-d H:i') }}</span>
                                        </div>

                                        <!-- Le texte -->
                                        <div class="px-4 py-2.5 rounded-2xl shadow-sm text-sm leading-relaxed {{ $isMe ? 'bg-green-700 text-white rounded-br-none' : 'bg-white border border-gray-200 text-gray-800 rounded-bl-none' }}">
                                            {{ $comment->body }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center h-32 opacity-50">
                                <svg class="w-8 h-8 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                <p class="text-sm text-gray-500 italic text-center">Aucun message pour le moment.<br>Soyez le premier à écrire !</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- FORMULAIRE DE RÉPONSE -->
                    <div class="p-5 border-t border-gray-100 bg-white shrink-0">
                        @if(in_array($ticket->status->value,[\App\Enums\TicketStatus::FERME->value, \App\Enums\TicketStatus::ANNULE->value]))
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 text-center">
                                <svg class="w-6 h-6 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <p class="text-sm font-medium text-gray-600">Le fil de discussion est verrouillé.</p>
                                <p class="text-xs text-gray-500 mt-1">Ce ticket a été {{ strtolower($ticket->status->value) }}. Vous ne pouvez plus y ajouter de messages.</p>
                            </div>
                        @else
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Ajouter une réponse</h3>
                            
                            @error('comment_error')
                                <div class="mb-3 text-xs font-bold text-red-600 bg-red-50 p-2 rounded">{{ $message }}</div>
                            @enderror

                            <form action="{{ route('ticket-comments.store', $ticket) }}" method="POST">
                                @csrf
                                <div class="relative">
                                    <textarea name="body" rows="3" required class="w-full border-gray-200 rounded-xl shadow-sm focus:ring-green-500 focus:border-green-500 sm:text-sm mb-3 resize-none bg-gray-50 p-4" placeholder="Saisissez votre message ici..."></textarea>
                                    <div class="absolute bottom-6 right-3 flex space-x-2">
                                        <button type="submit" class="p-2 bg-green-700 rounded-lg text-white hover:bg-green-800 transition shadow-sm" title="Envoyer">
                                            <!-- rotate-[270deg] oriente l'avion vers le haut à gauche ! -->
                                            <svg class="w-5 h-5 rotate-[90deg] -ml-0.5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

            </div>

            <!-- COLONNE DE DROITE (Sidebar de la page) -->
            <div class="space-y-6">
                
                <!-- SUMMARY -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center space-x-2 mb-6">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h2 class="text-lg font-bold text-gray-900">Détails de la demande</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Reference:</span>
                            <span class="text-sm font-bold text-gray-900">{{ $ticket->reference }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Matériel:</span>
                            <span class="text-sm font-bold text-gray-900">{{ optional($ticket->asset)->inventory_code ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Priorite:</span>
                            <span class="text-sm font-bold {{ $prioClass }} uppercase">{{ $ticket->priority->value }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Statut:</span>
                            <span class="text-sm font-bold uppercase text-blue-700">{{ str_replace('_', ' ', $ticket->status->value) }}</span>
                        </div>
                        <div class="flex justify-between items-start pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-500 mt-1">Date limite:</span>
                            <div class="text-right">
                                @if($ticket->due_at)
                                    <span class="text-sm font-bold text-gray-900">{{ $ticket->due_at->format('Y-m-d H:i') }}</span>
                                    @if($ticket->due_at->isPast() && !in_array($ticket->status->value, ['resolu', 'ferme', 'annule']))
                                        <div class="mt-1"><span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded">LATE</span></div>
                                    @endif
                                @else
                                    <span class="text-sm font-bold text-gray-900">-</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm text-gray-500">Demandeur:</span>
                            <span class="text-sm font-bold text-gray-900">{{ optional($ticket->requester)->full_name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Assigne a:</span>
                            <span class="text-sm font-bold text-gray-900">{{ optional(optional($ticket->assignedTo)->employee)->full_name ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- QUICK ACTIONS (Réservées à l'Admin IT) -->
                <!-- QUICK ACTIONS (Disparaît totalement si Fermé ou Annulé) -->
                @if($canTreat && !in_array($ticket->status->value,[\App\Enums\TicketStatus::FERME->value, \App\Enums\TicketStatus::ANNULE->value]))
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                    <div class="flex items-center space-x-2 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        <h2 class="text-lg font-bold text-gray-900">Actions Rapides</h2>
                    </div>
                    
                    <div class="flex flex-col space-y-3">
                        
                        <!-- L'Assignation n'est possible QUE si le ticket est Ouvert ou Assigné -->
                        @if(in_array($ticket->status->value,[\App\Enums\TicketStatus::OUVERT->value, \App\Enums\TicketStatus::ASSIGNE->value]))
                            <button @click="showAssignModal = true" class="flex justify-center items-center w-full px-4 py-2.5 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                Assigner un technicien
                            </button>
                        @endif
                        
                        <!-- Bouton Status -->
                        <!-- Astuce UI : S'il n'y a plus le bouton Assign vert au-dessus, on passe ce bouton en vert pour qu'il devienne l'action principale ! -->
                        @php
                            $isStatusBtnPrimary = !in_array($ticket->status->value,[\App\Enums\TicketStatus::OUVERT->value, \App\Enums\TicketStatus::ASSIGNE->value]);
                        @endphp
                        
                        <button @click="showStatusModal = true" class="flex justify-center items-center w-full px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-sm {{ $isStatusBtnPrimary ? 'bg-green-700 text-white hover:bg-green-800' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                            <svg class="w-4 h-4 mr-2 {{ $isStatusBtnPrimary ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            Modifier le statut
                        </button>
                        
                        <!-- Bouton Date -->
                        <button @click="showDateModal = true" class="flex justify-center items-center w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 transition shadow-sm">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Ajuster l'échéance
                        </button>

                        <div class="pt-2">
                            <button @click="showCancelModal = true" class="flex justify-center items-center w-full px-4 py-2.5 bg-white border border-red-200 text-red-600 rounded-lg text-sm font-bold hover:bg-red-50 transition shadow-sm">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Annuler la réclamation
                            </button>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

        <!-- ============================================== -->
        <!-- INCLUSION DES MODALES D'ACTION (Admin IT)      -->
        <!-- ============================================== -->
        @if($canTreat)
            @include('tickets.modals.assign')
            @include('tickets.modals.status')
            @include('tickets.modals.due-date')
            @include('tickets.modals.cancel')
        @endif

    </div> 

</x-app-layout>
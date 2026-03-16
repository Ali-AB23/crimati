<template x-teleport="body">
    <div x-show="showCancelModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div x-show="showCancelModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCancelModal = false"></div>
            <div x-show="showCancelModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md w-full p-6">
                
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 text-center">Annuler le ticket</h3>
                <p class="text-sm text-gray-500 mt-2 mb-6 text-center">Êtes-vous sûr de vouloir annuler ce ticket ? Il ne sera plus traité.</p>
                
                <!-- Changement d'approche : C'est une mise à jour de statut, pas un Delete en base -->
                <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="flex justify-center gap-3">
                    @csrf @method('PUT')
                    <!-- On force le statut à ANNULE -->
                    <input type="hidden" name="status" value="{{ \App\Enums\TicketStatus::ANNULE->value }}">
                    
                    <button type="button" @click="showCancelModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 w-full hover:bg-gray-50">Retour</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold w-full hover:bg-red-700">Confirmer l'annulation</button>
                </form>
            </div>
        </div>
    </div>
</template>
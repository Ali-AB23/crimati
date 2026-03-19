<template x-teleport="body">
    <div x-show="showDateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div x-show="showDateModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDateModal = false"></div>
            <div x-show="showDateModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md w-full p-6">
                
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Ajuster l'échéance</h3>
                    <button @click="showDateModal = false" class="text-gray-400 hover:text-gray-500"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                
                <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                    @csrf @method('PUT')
                    <!-- Le status est requis par le contrôleur -->
                    <input type="hidden" name="status" value="{{ $ticket->status->value }}">
                    
                    <div class="mb-6">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Date et heure limites</label>
                        <!-- Champ de type datetime-local pour choisir date ET heure -->
                        <input type="datetime-local" name="due_at" 
                               value="{{ $ticket->due_at ? $ticket->due_at->format('Y-m-d\TH:i') : '' }}" 
                               class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showDateModal = false" class="px-4 py-2 bg-white text-gray-700 text-sm font-bold">Annuler</button>
                        <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800">Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
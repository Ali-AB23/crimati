<template x-teleport="body">
    <div x-show="showStatusModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div x-show="showStatusModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showStatusModal = false"></div>
            <div x-show="showStatusModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md w-full p-6">
                
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Change status</h3>
                    <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-500"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                
                <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                    @csrf @method('PUT')
                    
                    <div class="mb-6">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                        <select name="status" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                            @forelse($allowedStatuses as $statusValue)
                                <option value="{{ $statusValue }}">
                                    Proumouvoir vers : {{ str_replace('_', ' ', strtoupper($statusValue)) }}
                                </option>
                            @empty
                                <option value="" disabled>Aucune transition possible</option>
                            @endforelse
                        </select>
                    </div>
                    
                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showStatusModal = false" class="px-4 py-2 bg-white text-gray-700 text-sm font-bold">Annuler</button>
                        <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800">Confirmer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
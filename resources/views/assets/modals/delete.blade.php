<template x-teleport="body">
    <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <div x-show="showDeleteModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showDeleteModal = false" aria-hidden="true"></div>

            <div x-show="showDeleteModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md">
                
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900" id="modal-title">Delete Asset</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-2">Are you sure you want to delete asset <strong>{{ $asset->inventory_code }}</strong>? This action cannot be undone.</p>
                </div>
                
                <!-- Boutons responsives : s'empilent sur mobile (flex-col), côte à côte sur sm (sm:flex-row) -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-3">
                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-red-600 text-base font-bold text-white shadow-sm hover:bg-red-700 focus:outline-none sm:text-sm transition">
                            Yes, delete it
                        </button>
                    </form>
                    <button type="button" @click="showDeleteModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:text-sm transition">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>
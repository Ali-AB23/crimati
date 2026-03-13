<template x-teleport="body">
    <!-- Conteneur principal (prend tout l'écran, gère le fond sombre et le z-index) -->
    <div x-show="showMoveModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <!-- Conteneur d'alignement (permet le centrage et les marges sur mobile) -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            
            <!-- Le fond sombre (Backdrop) -->
            <div x-show="showMoveModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                 @click="showMoveModal = false" aria-hidden="true"></div>

            <!-- Panel de la modale -->
            <!-- sm:my-8 sm:w-full sm:max-w-lg gère la largeur sur tablette/desktop. w-full s'applique sur mobile. -->
            <div x-show="showMoveModal" 
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative inline-block w-full align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg flex flex-col max-h-[90vh]">
                
                <!-- HEADER MODALE -->
                <div class="px-4 py-5 sm:p-6 border-b border-gray-100 shrink-0">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">Deplacer materiel</h3>
                            <p class="text-sm text-gray-500 mt-1">Asset: {{ $asset->inventory_code }}</p>
                        </div>
                        <!-- Bouton croix (X) pour mobile -->
                        <button @click="showMoveModal = false" class="text-gray-400 hover:text-gray-500">
                            <span class="sr-only">Fermer</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>

                <!-- CORPS MODALE (Scrollable si l'écran est trop petit) -->
                <form action="{{ route('assets.move', $asset) }}" method="POST" class="flex flex-col flex-1 overflow-hidden">
                    @csrf
                    
                    <div class="px-4 py-5 sm:p-6 overflow-y-auto">
                        <!-- Rappel des infos actuelles (grid-cols-1 sur mobile, 2 sur tablette) -->
                        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Current Location</p>
                                <p class="text-sm font-bold text-gray-900">{{ optional($asset->currentLocation)->name ?? 'Stock' }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Current Employee</p>
                                <p class="text-sm font-bold text-gray-900">{{ optional($asset->currentEmployee)->full_name ?? 'N/A' }}</p>
                            </div>
                            <div class="sm:col-span-2 border-t border-gray-200 pt-3">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Moved By</p>
                                <p class="text-sm font-bold text-gray-900">{{ Auth::user()->employee->full_name ?? Auth::user()->username }}</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-1">Type de mouvement</label>
                                <select name="type" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                    <option value="TRANSFERT">TRANSFERT</option>
                                    <option value="AFFECTATION">AFFECTATION</option>
                                    <option value="RETOUR">RETOUR</option>
                                    <option value="DEPLACEMENT">DEPLACEMENT</option>
                                </select>
                            </div>

                            <div class="bg-gray-50 border border-gray-100 rounded-lg p-3 text-[11px] text-gray-600 leading-relaxed">
                                <span class="font-bold text-gray-800">AFFECTATION:</span> assign to an office and/or employee<br>
                                <span class="font-bold text-gray-800">TRANSFERT:</span> change office and/or employee<br>
                                <span class="font-bold text-gray-800">RETOUR:</span> return to stock (employee cleared)<br>
                                <span class="font-bold text-gray-800">DEPLACEMENT:</span> location change only
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-1">To localisation</label>
                                <select name="to_location_id" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ $asset->current_location_id == $location->id ? 'selected' : '' }}>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-1">To employe (optional)</label>
                                <select name="to_employee_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                    <option value="">-</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $asset->current_employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-1">Note (optional)</label>
                                <textarea name="note" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm" placeholder="Reason for movement..."></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER MODALE (Boutons, s'empilent sur mobile) -->
                    <div class="px-4 py-4 sm:px-6 sm:flex sm:flex-row-reverse bg-gray-50 border-t border-gray-100 shrink-0">
                        <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent px-4 py-2 bg-green-700 text-base font-bold text-white shadow-sm hover:bg-green-800 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm transition">
                            Confirm
                        </button>
                        <button type="button" @click="showMoveModal = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 px-4 py-2 bg-white text-base font-bold text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
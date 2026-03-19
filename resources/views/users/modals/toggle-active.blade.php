<template x-teleport="body">
    <div x-show="showToggleModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto text-left">
        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
            <div x-show="showToggleModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showToggleModal = false"></div>
            
            <div x-show="showToggleModal" class="relative bg-white rounded-2xl overflow-hidden shadow-xl sm:my-8 sm:max-w-md w-full p-6 text-center">
                
                <!-- Icône dynamique (Orange = Désactiver, Vert = Activer) -->
                @if($targetUser->active)
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 mb-4">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Désactiver le compte</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-6">Êtes-vous sûr de vouloir bloquer l'accès à <strong>{{ $targetUser->username }}</strong> ?</p>
                @else
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Activer le compte</h3>
                    <p class="text-sm text-gray-500 mt-2 mb-6">Autoriser <strong>{{ $targetUser->username }}</strong> à se connecter au système ?</p>
                @endif
                
                <form action="{{ route('users.toggle', $targetUser) }}" method="POST" class="flex justify-center gap-3">
                    @csrf
                    <button type="button" @click="showToggleModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 w-full hover:bg-gray-50">Annuler</button>
                    
                    @if($targetUser->active)
                        <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg text-sm font-bold w-full hover:bg-orange-600">Désactiver</button>
                    @else
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-bold w-full hover:bg-green-700">Activer</button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</template>
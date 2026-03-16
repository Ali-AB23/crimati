@section('title', 'Catégories Matériel')

<x-app-layout>

    <!-- HEADER & BREADCRUMB -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <span class="text-gray-400">Referentiels</span> 
            <span class="mx-1">/</span> 
            <span class="text-gray-500 font-medium">Categories materiel</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Categories materiel</h1>
            
            <!-- BOUTON ADD CATEGORY (Déclenche une modale) -->
            <div x-data="{ showCreateModal: false }">
                <button @click="showCreateModal = true" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition w-full sm:w-auto justify-center">
                    <span class="mr-2 text-lg leading-none">+</span> Add category
                </button>

                <!-- MODALE : ADD CATEGORY -->
                <template x-teleport="body">
                    <div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
                            <div x-show="showCreateModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showCreateModal = false"></div>
                            
                            <div x-show="showCreateModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-lg w-full p-6">
                                
                                <div class="mb-5">
                                    <h3 class="text-xl font-bold text-gray-900">Add category</h3>
                                    <p class="text-sm text-gray-500 mt-1">Create a new main category for assets.</p>
                                </div>
                                
                                <form action="{{ route('asset-categories.store') }}" method="POST">
                                    @csrf
                                    <div class="space-y-4 mb-6">
                                        <div>
                                            <label class="block text-sm font-bold text-gray-900 mb-1">Name</label>
                                            <input type="text" name="name" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Cancel</button>
                                        <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 transition shadow-sm">Create</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- AFFICHAGE DES ERREURS DE SUPPRESSION (try/catch du controller) -->
    @error('delete_error')
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.78 7.25a.75.75 0 00-1.06 1.06L9.44 10l-1.72 1.69a.75.75 0 101.06 1.06L10.56 11l1.72 1.69a.75.75 0 101.06-1.06L11.56 10l1.72-1.69a.75.75 0 00-1.06-1.06L10.56 9 8.78 7.25z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ $message }}</p>
                </div>
            </div>
        </div>
    @enderror

    <!-- ZONE DE RECHERCHE -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="p-5">
            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Search</h2>
            
            <form action="{{ route('asset-categories.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
                
                <div class="w-full sm:w-1/2 md:w-1/3">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Name</label>
                    <input type="text" name="name" value="{{ request('name') }}" placeholder="Ex: IT, Mobilier..." class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm">
                </div>

                <div class="flex space-x-3 w-full sm:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 shadow-sm transition">Search</button>
                    <a href="{{ route('asset-categories.index') }}" class="w-full sm:w-auto px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 text-center shadow-sm transition">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLEAU DES CATÉGORIES -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Asset categories</h2>
            <div class="text-sm text-gray-500">
                Showing {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }}
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($categories as $category)
                    
                    <!-- X-DATA pour isoler les Modales Edit / Delete de cette catégorie spécifique -->
                    <tr x-data="{ showEditModal: false, showDeleteModal: false }" class="hover:bg-gray-50 transition">
                        <td class="p-4 text-sm text-gray-900">{{ $category->name }}</td>
                        
                        <td class="p-4 text-sm font-medium text-right space-x-3">
                            <button @click="showEditModal = true" class="text-green-600 hover:text-green-800">Edit</button>
                            <button @click="showDeleteModal = true" class="text-red-600 hover:text-red-800">Delete</button>

                            <!-- ========================================== -->
                            <!-- MODALE EDIT -->
                            <!-- ========================================== -->
                            <template x-teleport="body">
                                <div x-show="showEditModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto text-left">
                                    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
                                        <div x-show="showEditModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showEditModal = false"></div>
                                        <div x-show="showEditModal" class="relative bg-white rounded-2xl overflow-hidden shadow-xl sm:my-8 sm:max-w-lg w-full p-6">
                                            
                                            <div class="mb-5">
                                                <h3 class="text-xl font-bold text-gray-900">Edit category</h3>
                                            </div>
                                            
                                            <form action="{{ route('asset-categories.update', $category) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="space-y-4 mb-6">
                                                    <div>
                                                        <label class="block text-sm font-bold text-gray-900 mb-1">Name</label>
                                                        <input type="text" name="name" value="{{ $category->name }}" required class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-green-500 sm:text-sm">
                                                    </div>
                                                </div>
                                                <div class="flex justify-end space-x-3">
                                                    <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">Cancel</button>
                                                    <button type="submit" class="px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 transition shadow-sm">Save</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- ========================================== -->
                            <!-- MODALE DELETE -->
                            <!-- ========================================== -->
                            <template x-teleport="body">
                                <div x-show="showDeleteModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
                                    <div class="flex items-center justify-center min-h-screen px-4 text-center sm:p-0">
                                        <div x-show="showDeleteModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                                        <div x-show="showDeleteModal" class="relative bg-white rounded-2xl text-left overflow-hidden shadow-xl sm:my-8 sm:max-w-md w-full p-6 text-center">
                                            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-900">Delete category</h3>
                                            <p class="text-sm text-gray-500 mt-2 mb-6">Are you sure you want to delete <strong>{{ $category->name }}</strong>? This action cannot be undone.</p>
                                            <form action="{{ route('asset-categories.destroy', $category) }}" method="POST" class="flex justify-center gap-3">
                                                @csrf @method('DELETE')
                                                <button type="button" @click="showDeleteModal = false" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-bold text-gray-700 w-full hover:bg-gray-50">Cancel</button>
                                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold w-full hover:bg-red-700">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </template>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="p-6 text-center text-gray-500 text-sm">No asset categories found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $categories->links() }}
        </div>
        @endif
    </div>

</x-app-layout>
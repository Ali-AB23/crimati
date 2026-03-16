@section('title', 'Utilisateurs')

<x-app-layout>

    <!-- HEADER & BREADCRUMB -->
    <div class="mb-6">
        
        
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Utilisateurs</h1>
            
            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition">
                Add user
            </a>
        </div>
    </div>

    <!-- MESSAGES DE SUCCÈS OU ERREUR (Surtout pour le Toggle Active) -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
        </div>
    @endif
    @error('toggle_error')
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <p class="text-sm font-medium text-red-800">{{ $message }}</p>
        </div>
    @enderror

    <!-- ZONE DE RECHERCHE -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm mb-6">
        <div class="p-5">
            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Search and filters</h2>
            
            <form action="{{ route('users.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
                
                <!-- Query (Matricule, Nom, Username) -->
                <div class="w-full lg:w-1/2">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Query</label>
                    <input type="text" name="query" value="{{ request('query') }}" placeholder="Amina ou 12345" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm">
                </div>

                <!-- Filtre par Rôle -->
                <div class="w-full lg:w-1/4">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Role</label>
                    <select name="role" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm text-gray-600">
                        <option value="">All</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}" {{ request('role') == $role->value ? 'selected' : '' }}>{{ str_replace('_', ' ', $role->value) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtre par Statut (Active/Inactive) -->
                <div class="w-full lg:w-1/4">
                    <label class="block text-[11px] font-bold text-gray-500 mb-2">Active</label>
                    <select name="active" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm text-gray-600">
                        <option value="">All</option>
                        <option value="1" {{ request('active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="flex space-x-3 w-full lg:w-auto">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2 bg-green-700 text-white rounded-lg text-sm font-bold hover:bg-green-800 shadow-sm transition">Search</button>
                    <a href="{{ route('users.index') }}" class="w-full sm:w-auto px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 text-center shadow-sm transition">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- TABLEAU DES UTILISATEURS -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900">Users list</h2>
            <div class="text-sm text-gray-500">
                Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
            </div>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Matricule</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Full name</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Org unit</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Office location</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-center">Active</th>
                        <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $u)
                    
                    <!-- X-DATA pour la modale Toggle Active de cette ligne -->
                    <tr x-data="{ showToggleModal: false }" class="hover:bg-gray-50 transition">
                        
                        <td class="p-4 text-sm text-gray-500">{{ optional($u->employee)->matricule ?? '-' }}</td>
                        
                        <td class="p-4 text-sm font-bold text-gray-900">
                            <!-- Permet au nom de se casser sur deux lignes proprement -->
                            <span class="whitespace-normal inline-block w-24 leading-tight">{{ optional($u->employee)->full_name ?? '-' }}</span>
                        </td>
                        
                        <td class="p-4 text-sm text-gray-500">{{ $u->username }}</td>
                        
                        <td class="p-4">
                            @php
                                $roleColors =[
                                    'ADMIN_IT' => 'bg-blue-50 text-blue-700',
                                    'INVENTORISTE' => 'bg-yellow-50 text-yellow-700',
                                    'EMPLOYE' => 'bg-gray-100 text-gray-600',
                                ];
                                $rColor = $roleColors[$u->role->value] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider {{ $rColor }}">
                                {{ $u->role->value }}
                            </span>
                        </td>
                        
                        <td class="p-4 text-sm text-gray-600">
                            <span class="whitespace-normal inline-block w-24 leading-tight">
                                {{ optional(optional($u->employee)->orgUnit)->name ?? '-' }}
                            </span>
                        </td>
                        
                        <td class="p-4 text-sm text-gray-600">
                            <span class="whitespace-normal inline-block w-20 leading-tight">
                                {{ optional(optional($u->employee)->officeLocation)->name ?? '-' }}
                            </span>
                        </td>
                        
                        <!-- BADGE ACTIVE / INACTIVE -->
                        <td class="p-4 text-center">
                            @if($u->active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-100">
                                    <span class="w-1.5 h-1.5 mr-1.5 bg-red-500 rounded-full"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        
                       <!-- ACTIONS -->
                        <td class="p-4 text-sm font-medium text-right flex items-center justify-end space-x-4">
                            <a href="{{ route('users.edit', $u) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                            
                            <!-- TOGGLE SWITCH VISUEL POUR LA MODALE -->
                            <div class="flex items-center" title="Activer/Désactiver l'accès">
                                <button type="button" @click="showToggleModal = true" 
                                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none" 
                                        :class="{{ $u->active ? 'true' : 'false' }} ? 'bg-green-500' : 'bg-gray-300'">
                                    <span class="sr-only">Toggle active</span>
                                    <span aria-hidden="true" 
                                          class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $u->active ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                </button>
                            </div>

                            <!-- INCLUSION DE LA MODALE PROPRE À CETTE LIGNE -->
                            @include('users.modals.toggle-active',['targetUser' => $u])
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="p-6 text-center text-gray-500 text-sm">Aucun utilisateur trouvé.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</x-app-layout>
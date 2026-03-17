@section('title', 'Résultats de l\'Import')

<x-app-layout>

    @php
        // On récupère le tableau de résultats stocké dans la session par l'ImportController
        $results = session('import_results', [
            'success_count' => 0,
            'errors' => []
        ]);
    @endphp

    <!-- HEADER & BREADCRUMB -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <span class="text-gray-400">Import Excel</span> 
            <span class="mx-1">/</span> 
            <span class="text-gray-400">Materiels</span>
            <span class="mx-1">/</span> 
            <span class="text-gray-900 font-medium">Resultats</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Rapport d'importation</h1>
            
            <div class="flex gap-3">
                <a href="{{ route('import.upload') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition">Nouvel import</a>
                <a href="{{ route('assets.index') }}" class="px-4 py-2 bg-green-700 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-green-800 shadow-sm transition">Voir les materiels</a>
            </div>
        </div>
    </div>

    <!-- BILAN GÉNÉRAL -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Succès -->
        <div class="bg-white rounded-xl border border-green-200 shadow-sm p-6 border-l-4 border-l-green-500 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Matériels importés</h3>
                <p class="text-sm text-gray-500 mt-1">Lignes traitées et enregistrées avec succès.</p>
            </div>
            <span class="text-4xl font-extrabold text-green-600">{{ $results['success_count'] }}</span>
        </div>

        <!-- Échecs -->
        @php $errorCount = count($results['errors']); @endphp
        <div class="bg-white rounded-xl border {{ $errorCount > 0 ? 'border-red-200 border-l-red-500' : 'border-gray-200 border-l-gray-300' }} shadow-sm p-6 border-l-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Lignes rejetées</h3>
                <p class="text-sm text-gray-500 mt-1">Lignes ignorées suite à des erreurs.</p>
            </div>
            <span class="text-4xl font-extrabold {{ $errorCount > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ $errorCount }}</span>
        </div>
    </div>

    <!-- TABLEAU DES ERREURS (S'affiche uniquement s'il y a des erreurs) -->
    @if($errorCount > 0)
        <div class="bg-white rounded-xl border border-red-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-gray-100 bg-red-50 flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                <h2 class="text-lg font-bold text-red-800">Détail des erreurs</h2>
            </div>
            
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider w-24">Ligne Excel</th>
                            <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider w-48">Code Inventaire</th>
                            <th class="p-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Raison du rejet</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($results['errors'] as $error)
                        <tr class="hover:bg-red-50 transition">
                            <td class="p-4 text-sm font-bold text-gray-900"># {{ $error['row'] }}</td>
                            <td class="p-4 text-sm font-medium text-gray-700">{{ $error['inventory_code'] ?? 'Non défini' }}</td>
                            <td class="p-4 text-sm text-red-600">
                                <ul class="list-disc list-inside">
                                    @foreach($error['messages'] as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-green-50 rounded-xl border border-green-200 shadow-sm p-8 text-center mt-6">
            <svg class="mx-auto h-12 w-12 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <h3 class="text-lg font-bold text-green-800">Import Parfait !</h3>
            <p class="text-sm text-green-600 mt-2">Toutes les lignes du fichier Excel ont été traitées sans aucune erreur.</p>
        </div>
    @endif

</x-app-layout>
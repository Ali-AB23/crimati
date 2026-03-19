@section('title', 'Import Excel (Matériels)')

<x-app-layout>

    <!-- HEADER & BREADCRUMB -->
    <div class="mb-6">
        <div class="text-sm text-gray-500 mb-2">
            <span class="text-gray-400">Import Excel</span> 
            <span class="mx-1">/</span> 
            <span class="text-gray-900 font-medium">Materiels</span>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <h1 class="text-2xl font-bold text-gray-900">Import materiels (Excel)</h1>
        </div>
    </div>

    <!-- MESSAGES D'ERREUR -->
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.78 7.25a.75.75 0 00-1.06 1.06L9.44 10l-1.72 1.69a.75.75 0 101.06 1.06L10.56 11l1.72 1.69a.75.75 0 101.06-1.06L11.56 10l1.72-1.69a.75.75 0 00-1.06-1.06L10.56 9 8.78 7.25z" clip-rule="evenodd" /></svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Erreur lors de l'upload :</h3>
                    <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- COLONNE GAUCHE (Formulaire & Tableaux) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- BLOC 1 : UPLOAD FILE -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Téléverser un fichier</h2>
                
                <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data" id="upload-form">
                    @csrf
                    
                    <!-- Zone de Drag & Drop -->
                    <div class="mt-1 flex justify-center px-6 pt-10 pb-12 border-2 border-gray-300 border-dashed rounded-xl bg-gray-50 relative hover:bg-gray-100 transition cursor-pointer" id="drop-zone" onclick="document.getElementById('file-upload').click()">
                        <div class="space-y-2 text-center pointer-events-none">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <span class="relative font-bold text-green-700 hover:text-green-800">
                                    Choisir un fichier
                                </span>
                                <p class="pl-1">ou glisser-déposer un fichier .xlsx ici</p>
                            </div>
                        </div>
                        <!-- Le vrai input file, invisible -->
                        <input id="file-upload" name="excel_file" type="file" accept=".xlsx" class="sr-only" onchange="updateFileName(this)">
                    </div>

                    <!-- Ligne d'actions du bas (Nom du fichier + Bouton Validate) -->
                    <div class="flex items-center justify-between mt-6">
                        <div class="flex items-center text-sm text-gray-600">
                            <!-- Icône de succès (verte) qui apparaît quand un fichier est choisi -->
                            <svg id="success-icon" class="w-5 h-5 text-green-500 mr-2 hidden" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span id="file-name">Sélectionné : aucun</span>
                        </div>
                        
                        <button type="submit" id="submit-btn" disabled class="px-6 py-2.5 bg-green-700 text-white font-bold rounded-lg text-sm shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed hover:bg-green-800">
                            Valider et importer
                        </button>
                    </div>
                </form>
            </div>

            <!-- BLOC 2 : REQUIRED COLUMNS -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Colonnes requises</h2>
                
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Colonne</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Requis</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">inventory_code</td>
                                <td class="px-6 py-4 font-bold text-green-600">Oui</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">asset_type</td>
                                <td class="px-6 py-4 font-bold text-green-600">Oui</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">status</td>
                                <td class="px-6 py-4 text-gray-500">Optionnel</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">location</td>
                                <td class="px-6 py-4 text-gray-500">Optionnel</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">brand</td>
                                <td class="px-6 py-4 text-gray-500">Optionnel</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">model</td>
                                <td class="px-6 py-4 text-gray-500">Optionnel</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">serial_number</td>
                                <td class="px-6 py-4 text-gray-500">Optionnel</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 font-mono text-gray-700">notes</td>
                                <td class="px-6 py-4 text-gray-500">Optionnel</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BLOC 3 : NOTES -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 md:p-8 mb-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Remarques</h2>
                <ul class="space-y-3 text-sm text-gray-600 list-inside">
                    <li class="flex items-start">
                        <span class="mr-2 mt-1.5 h-1.5 w-1.5 bg-gray-400 rounded-full shrink-0"></span>
                        Les doublons basés sur le <code class="bg-gray-100 px-1 rounded mx-1">inventory_code</code> sont rejetés.
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 mt-1.5 h-1.5 w-1.5 bg-gray-400 rounded-full shrink-0"></span>
                        L'importation crée uniquement des matériels.
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 mt-1.5 h-1.5 w-1.5 bg-gray-400 rounded-full shrink-0"></span>
                        Pas d'importation de tickets.
                    </li>
                    <li class="flex items-start">
                        <span class="mr-2 mt-1.5 h-1.5 w-1.5 bg-gray-400 rounded-full shrink-0"></span>
                        Statuts autorisés :
                        <span class="flex flex-wrap gap-1 ml-2 mt-0.5">
                            <code class="bg-gray-100 px-1.5 py-0.5 text-xs rounded text-gray-600">en_stock</code>, 
                            <code class="bg-gray-100 px-1.5 py-0.5 text-xs rounded text-gray-600">en_service</code>, 
                            <code class="bg-gray-100 px-1.5 py-0.5 text-xs rounded text-gray-600">en_panne</code>, 
                            <code class="bg-gray-100 px-1.5 py-0.5 text-xs rounded text-gray-600">en_reparation</code>, 
                            <code class="bg-gray-100 px-1.5 py-0.5 text-xs rounded text-gray-600">reforme</code>.
                        </span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- COLONNE DROITE (Rules Summary) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 sticky top-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Résumé des règles</h2>
                
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="ml-3 text-sm text-gray-700">Format de fichier : .xlsx</p>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="ml-3 text-sm text-gray-700">Code d'inventaire (inventory_code) unique requis</p>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0 mt-0.5">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="ml-3 text-sm text-gray-700">Rapport après import (importés / rejetés + raison)</p>
                    </div>
                </div>

                <div class="mt-8 bg-gray-50 border border-gray-100 rounded-lg p-4">
                    <h4 class="text-sm font-bold text-gray-900 mb-1">Astuce :</h4>
                    <p class="text-sm text-gray-500 leading-relaxed">Commencez avec les colonnes minimales, puis enrichissez les données plus tard.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT POUR LE DRAG & DROP ET LE NOM DU FICHIER -->
    <script>
        function updateFileName(input) {
            const fileNameDisplay = document.getElementById('file-name');
            const submitBtn = document.getElementById('submit-btn');
            const successIcon = document.getElementById('success-icon');
            
            if (input.files && input.files[0]) {
                fileNameDisplay.textContent = 'Sélectionné : ' + input.files[0].name;
                fileNameDisplay.classList.add('text-gray-900', 'font-medium');
                submitBtn.disabled = false;
                successIcon.classList.remove('hidden');
            } else {
                fileNameDisplay.textContent = 'Sélectionné : aucun';
                fileNameDisplay.classList.remove('text-gray-900', 'font-medium');
                submitBtn.disabled = true;
                successIcon.classList.add('hidden');
            }
        }
        
        // Logique de Drag & Drop visuelle
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('file-upload');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        function preventDefaults(e) { e.preventDefault(); e.stopPropagation(); }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('bg-green-50', 'border-green-400'), false);
        });['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('bg-green-50', 'border-green-400'), false);
        });
        
        dropZone.addEventListener('drop', handleDrop, false);
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            if(files.length) {
                fileInput.files = files;
                updateFileName(fileInput);
            }
        }
    </script>
</x-app-layout>
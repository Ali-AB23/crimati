<!-- Component (partial): spec-schema -->
<div class="border border-gray-200 rounded-xl overflow-hidden">
    <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center space-x-2">
        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
        <div>
            <h3 class="text-sm font-bold text-gray-900">Component (partial): spec-schema</h3>
            <p class="text-xs text-gray-500 mt-1">Définissez ici les caractéristiques techniques requises pour ce type de matériel.</p>
        </div>
    </div>
    
    <div class="p-5 space-y-4">
        
        <!-- Les lignes dynamiques gérées par le x-data parent -->
        <template x-for="(row, index) in schemaRows" :key="index">
            <div class="flex flex-col md:flex-row gap-3 items-start md:items-end bg-white p-3 rounded-lg border border-gray-100 relative group">
                
                <div class="w-full md:w-1/3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Nom du champ (ex: RAM)</label>
                    <input type="text" x-model="row.key" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm bg-gray-50">
                </div>
                
                <div class="w-full md:w-1/3">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Type de donnée</label>
                    <select x-model="row.type" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm bg-gray-50 text-gray-700">
                        <option value="text">Texte libre</option>
                        <option value="number">Nombre entier/décimal</option>
                        <option value="select">Liste déroulante (Select)</option>
                    </select>
                </div>

                <div class="w-full md:w-1/3" x-show="row.type === 'select'">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Options (séparées par une virgule)</label>
                    <input type="text" x-model="row.options" placeholder="Ex: SSD, HDD" class="w-full border-gray-300 rounded-lg text-sm focus:border-green-500 shadow-sm bg-gray-50">
                </div>

                <button type="button" @click="schemaRows.splice(index, 1)" class="absolute top-2 right-2 md:static md:mb-1.5 p-2 text-gray-300 hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Supprimer ce champ">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
        </template>

        <button type="button" @click="schemaRows.push({ key: '', type: 'text', options: '' })" class="w-full py-3 border-2 border-dashed border-gray-200 rounded-lg text-sm font-bold text-gray-500 hover:border-green-400 hover:text-green-600 hover:bg-green-50 transition flex items-center justify-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
            Ajouter une ligne
        </button>

    </div>
</div>
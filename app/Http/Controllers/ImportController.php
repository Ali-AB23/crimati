<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AssetsImport; // La classe qu'on va créer juste après !

class ImportController extends Controller
{
    /**
     * Affiche la page d'upload Excel (La maquette).
     */
    public function showUploadForm(): View
    {
        return view('import.assets-upload');
    }

    /**
     * Traite le fichier Excel uploadé.
     */
    public function processUpload(Request $request): RedirectResponse
    {
        // 1. Validation stricte du fichier (uniquement .xlsx, taille max 5MB)
        $request->validate([
            'excel_file' =>[
                'required',
                'file',
                'mimes:xlsx',
                'max:5120' // 5MB max
            ],
        ]);

        try {
            // 2. On instancie notre classe d'import "Maison"
            $import = new AssetsImport();
            
            // 3. On demande au package Maatwebsite de lire le fichier en utilisant notre classe
            Excel::import($import, $request->file('excel_file'));

            // 4. On récupère le bilan de l'import généré par notre classe
            $results = $import->getResults();

            // 5. On stocke le résultat dans la session pour l'afficher sur la page de résultat
            return redirect()->route('import.result')->with('import_results', $results);

        } catch (\Exception $e) {
            // S'il y a un crash total du fichier Excel (fichier corrompu, etc.)
            return back()->withErrors(['import_error' => 'Erreur critique lors de la lecture du fichier Excel : ' . $e->getMessage()]);
        }
    }

    /**
     * Affiche la page de rapport (Résumé des succès/échecs).
     */
    public function showResultForm(): View | RedirectResponse
    {
        // Si l'utilisateur tape l'URL directement sans avoir fait d'import, on le renvoie
        if (!session('import_results')) {
            return redirect()->route('import.upload');
        }

        return view('import.assets-result');
    }
}
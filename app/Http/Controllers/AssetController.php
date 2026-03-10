<?php
namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Location;
use App\Models\Employee;
use App\Http\Requests\StoreAssetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AssetController extends Controller
{
    /**
     * Affiche la liste du matériel (Tableau de bord de l'inventoriste).
     */
    public function index(): View
    {
        // ⚠️ LEÇON DE PERFORMANCE : Le Eager Loading (avec "with")
        // Au lieu de faire 1 requête pour lister 50 assets, puis 50 requêtes pour trouver 
        // le nom du lieu de chaque asset, on demande tout en 1 seule requête optimisée !
        $assets = Asset::with(['type', 'currentLocation', 'currentEmployee'])
                       ->orderBy('created_at', 'desc')
                       ->paginate(15); // Pagination native Laravel (15 par page)

        return view('assets.index', compact('assets'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau matériel.
     */
    public function create(): View
    {
        // On envoie à la vue toutes les données nécessaires pour remplir les listes déroulantes (Selects)
        $assetTypes = AssetType::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $employees = Employee::orderBy('full_name')->get();

        return view('assets.create', compact('assetTypes', 'locations', 'employees'));
    }

    /**
     * Sauvegarde le nouveau matériel en base (On a vu cette méthode juste avant).
     */
    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        $asset = Asset::create($validatedData);

        return redirect()->route('assets.index')
                         ->with('success', 'Matériel ajouté avec succès (Code: ' . $asset->inventory_code . ')');
    }

    /**
     * Affiche les détails d'un matériel spécifique + son historique.
     */
    public function show(Asset $asset): View
    {
        // On charge l'historique des mouvements et les tickets associés pour la page détails
        $asset->load(['movements.fromLocation', 'movements.toLocation', 'tickets']);

        return view('assets.show', compact('asset'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit(Asset $asset): View
    {
        $assetTypes = AssetType::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        $employees = Employee::orderBy('full_name')->get();

        return view('assets.edit', compact('asset', 'assetTypes', 'locations', 'employees'));
    }

    /**
     * Met à jour un matériel existant.
     * Note: Pour simplifier, on réutilise StoreAssetRequest. Dans un projet plus vaste, 
     * on créerait un UpdateAssetRequest spécifique (pour gérer l'unicité du code d'inventaire).
     */
    public function update(StoreAssetRequest $request, Asset $asset): RedirectResponse
    {
        $validatedData = $request->validated();
        
        $asset->update($validatedData);

        return redirect()->route('assets.show', $asset)
                         ->with('success', 'Les informations du matériel ont été mises à jour.');
    }

    /**
     * Supprime un matériel.
     */
    public function destroy(Asset $asset): RedirectResponse
    {
        // Grâce au cascadeOnDelete() dans nos migrations, la suppression de cet asset
        // supprimera automatiquement ses mouvements et ses tickets associés.
        $asset->delete();

        return redirect()->route('assets.index')
                         ->with('success', 'Le matériel a été supprimé définitivement.');
    }
}
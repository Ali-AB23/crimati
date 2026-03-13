<?php
namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Location;
use App\Models\AssetCategory;
use App\Models\Employee;
use App\Enums\AssetStatus;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use App\Http\Requests\StoreAssetRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Affiche la liste du matériel (Tableau de bord de l'inventoriste).
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        
        // 1. Initialisation de la requête de base (Eager Loading pour éviter N+1)
        $query = Asset::with(['type.category', 'currentLocation', 'currentEmployee']);

        // 2. APPLICATION DES RÈGLES DE VISIBILITÉ (Adaptation Métier : Espaces Publics)
        if ($user->role->value === UserRole::EMPLOYE->value && $user->employee) {
            $employee = $user->employee;
            
            $query->where(function ($q) use ($employee) {
                // A. Matériel affecté personnellement à l'employé
                $q->where('current_employee_id', $employee->id)
                  
                  // B. OU Matériel situé dans le bureau physique de l'employé
                  ->orWhere('current_location_id', $employee->office_location_id)
                  
                  // C. OU Matériel situé dans un lieu spécifique...
                  ->orWhereHas('currentLocation', function ($locQuery) use ($employee) {
                      
                      // C1. ... qui appartient au même Service/Pôle que l'employé
                      $locQuery->where('org_unit_id', $employee->org_unit_id)
                      
                               // C2. ... OU BIEN c'est un "Espace Public" (NULL) 
                               // à l'exclusion stricte des Magasins/Stocks !
                               ->orWhere(function ($publicQuery) {
                                   $publicQuery->whereNull('org_unit_id')
                                               ->where('type', '!=', \App\Enums\LocationType::STORAGE->value);
                               });
                  });
            });
        }

        // 3. GESTION DES FILTRES (Moteur de recherche de la maquette)
        if ($request->filled('code')) {
            $query->where('inventory_code', 'like', '%' . $request->code . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location_id')) {
            $query->where('current_location_id', $request->location_id);
        }

        if ($request->filled('type_id')) {
            $query->where('asset_type_id', $request->type_id);
        }

        if ($request->filled('category_id')) {
            // On filtre à travers la relation 'type' pour atteindre la 'category'
            $query->whereHas('type', function ($q) use ($request) {
                $q->where('asset_category_id', $request->category_id);
            });
        }

        // 4. Exécution de la requête avec Pagination
        $assets = $query->orderBy('created_at', 'desc')->paginate(15);

        // 5. Récupération des données pour remplir les listes déroulantes des filtres
        $statuses = AssetStatus::cases();
        $locations = Location::orderBy('name')->get();
        $categories = AssetCategory::orderBy('name')->get();
        $types = AssetType::orderBy('name')->get();

        // Envoi de toutes les données à la vue
        return view('assets.index', compact('assets', 'statuses', 'locations', 'categories', 'types'));
    }

    /**
     * Affiche le formulaire de création d'un nouveau matériel.
     */
    public function create(): View
    {
        $assetTypes = AssetType::orderBy('name')->get();
        
        // CORRECTION : Uniquement les "Feuilles" de l'arbre (lieux qui n'ont PAS d'enfants)
        $locations = Location::doesntHave('children')->orderBy('name')->get();
        
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
       // $asset->load(['movements.fromLocation', 'movements.toLocation', 'tickets']);
        $asset->load([
            'type.category', 
            'currentLocation', 
            'currentEmployee', 
            'movements.fromLocation', 
            'movements.toLocation', 
            'movements.fromEmployee', 
            'movements.toEmployee',
            'tickets'
        ]);
        
        $locations = \App\Models\Location::doesntHave('children')->orderBy('name')->get();
        $employees = \App\Models\Employee::orderBy('full_name')->get();

        return view('assets.show', compact('asset', 'locations', 'employees'));
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
    /**
     * Traite la soumission de la modale "Deplacer materiel"
     */
    public function move(Request $request, Asset $asset, \App\Services\MoveAssetService $moveService): RedirectResponse
    {
        $validated = $request->validate([
            'type'           =>['required', new \Illuminate\Validation\Rules\Enum(\App\Enums\MovementType::class)],
            'to_location_id' => ['required', 'exists:locations,id'],
            'to_employee_id' =>['nullable', 'exists:employees,id'],
            'note'           =>['nullable', 'string', 'max:255'],
        ]);

        try {
            // Le Service gère la transaction SQL et la création de l'historique !
            $moveService->move(
                $asset,
                $validated['to_location_id'],
                $validated['to_employee_id'],
                \App\Enums\MovementType::from($validated['type']),
                Auth::user(),
                $validated['note']
            );
            return back()->with('success', 'Mouvement enregistré avec succès.');
        } catch (\Exception $e) {
            return back()->withErrors(['move_error' => $e->getMessage()]);
        }
    }
}
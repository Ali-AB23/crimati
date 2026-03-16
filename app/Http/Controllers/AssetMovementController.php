<?php

namespace App\Http\Controllers;

use App\Models\AssetMovement;
use App\Models\Location;
use App\Enums\MovementType;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetMovementController extends Controller
{
    public function index(Request $request): View
    {
        // 1. Initialisation de la requête (Eager Loading massif car on a beaucoup de relations)
        $query = AssetMovement::with([
            'asset', 
            'fromLocation', 
            'toLocation', 
            'fromEmployee', 
            'toEmployee', 
            'movedBy.employee' // On charge l'employé lié au user pour avoir son vrai nom
        ]);

        // 2. GESTION DES FILTRES (selon la maquette)
        
        // Filtre : Asset Code
        if ($request->filled('asset_code')) {
            $query->whereHas('asset', function ($q) use ($request) {
                $q->where('inventory_code', 'like', '%' . $request->asset_code . '%');
            });
        }

        // Filtre : Type de Mouvement
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre : From Location
        if ($request->filled('from_location_id')) {
            $query->where('from_location_id', $request->from_location_id);
        }

        // Filtre : To Location
        if ($request->filled('to_location_id')) {
            $query->where('to_location_id', $request->to_location_id);
        }

        // Filtre : Moved By (Recherche textuelle sur le nom de l'employé via la relation)
        if ($request->filled('moved_by_name')) {
            $query->whereHas('movedBy.employee', function ($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->moved_by_name . '%');
            });
        }

        // 3. Exécution (les plus récents en premier)
        $movements = $query->orderBy('moved_at', 'desc')->paginate(40); // 40 par page selon ta maquette

        // 4. Données pour les listes déroulantes des filtres
        $types = MovementType::cases();
        $locations = Location::orderBy('name')->get();

        return view('movements.index', compact('movements', 'types', 'locations'));
        }
}

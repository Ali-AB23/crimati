<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Location;
use App\Enums\AssetStatus;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Pour dire que la ligne 1 contient les titres des colonnes
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class AssetsImport implements ToModel, WithHeadingRow
{
    // Variables pour stocker le bilan de l'import
    private $successCount = 0;
    private $errors =[];
    private $currentRow = 1; // Commence à 1 à cause de l'en-tête (HeadingRow)

    /**
     * Cette méthode est appelée pour CHAQUE ligne du fichier Excel (à partir de la ligne 2).
     * Le tableau $row contient les données de la ligne, avec les titres de la ligne 1 comme clés.
     * Ex: $row['inventory_code'], $row['asset_type']
     */
    public function model(array $row)
    {
        $this->currentRow++;

        // 1. On saute les lignes complètement vides
        if (empty(array_filter($row))) {
            return null;
        }

        // 2. VALIDATION MANUELLE DE LA LIGNE EXCEL
        // On utilise le Validator de Laravel pour s'assurer que les données respectent nos règles métiers
        $validator = Validator::make($row,[
            'inventory_code' => ['required', 'string', 'max:50', 'unique:assets,inventory_code'],
            'asset_type'     => ['required', 'string'],
            // Statut optionnel, par défaut ça sera 'en_stock'
            'status'         => ['nullable', 'string', new Enum(AssetStatus::class)],
            // Localisation optionnelle (Nom textuel dans le fichier Excel)
            'location'       => ['nullable', 'string'],
            'brand'          =>['nullable', 'string'],
            'model'          => ['nullable', 'string'],
            'serial_number'  =>['nullable', 'string'],
            'notes'          => ['nullable', 'string'],
        ]);

        // Si la validation échoue, on enregistre l'erreur et on ignore cette ligne (return null)
        if ($validator->fails()) {
            $this->errors[] =[
                'row'            => $this->currentRow,
                'inventory_code' => $row['inventory_code'] ?? 'Inconnu',
                'messages'       => $validator->errors()->all()
            ];
            return null;
        }

        // 3. RÉSOLUTION DES CLÉS ÉTRANGÈRES (Text -> ID)
        
        // A. Résoudre le Type (Obligatoire)
        // L'utilisateur tape "PC Portable" dans Excel, on doit trouver l'ID correspondant en base de données.
        $type = AssetType::where('name', $row['asset_type'])->first();
        if (!$type) {
            $this->errors[] =[
                'row'            => $this->currentRow,
                'inventory_code' => $row['inventory_code'],
                'messages'       => ["Le type de matériel '{$row['asset_type']}' n'existe pas dans le système."]
            ];
            return null;
        }

        // B. Résoudre la Localisation (Optionnelle, sinon on le met dans le Stock par défaut)
        $locationId = null;
        if (!empty($row['location'])) {
            $location = Location::where('name', $row['location'])->first();
            if (!$location) {
                $this->errors[] =[
                    'row'            => $this->currentRow,
                    'inventory_code' => $row['inventory_code'],
                    'messages'       => ["La localisation '{$row['location']}' n'existe pas dans le système."]
                ];
                return null;
            }
            $locationId = $location->id;
        } else {
            // RÈGLE MÉTIER (CDC) : S'il n'y a pas de lieu précisé, on l'affecte au Magasin/Stock par défaut.
            // On cherche le premier lieu de type STORAGE.
            $storage = Location::where('type', \App\Enums\LocationType::STORAGE->value)->first();
            $locationId = $storage ? $storage->id : null;
            
            // Si on ne trouve vraiment aucun lieu de stockage dans la base, on rejette la ligne.
            if (!$locationId) {
                $this->errors[] =[
                    'row'            => $this->currentRow,
                    'inventory_code' => $row['inventory_code'],
                    'messages'       =>["Aucun lieu de type 'Stock/Magasin' (STORAGE) n'est configuré par défaut dans le système pour y affecter le matériel."]
                ];
                return null;
            }
        }

        // 4. TOUT EST BON ! ON CRÉE LE MATÉRIEL
        $this->successCount++;

        return new Asset([
            'inventory_code'      => $row['inventory_code'],
            'asset_type_id'       => $type->id,
            'status'              => $row['status'] ?? AssetStatus::EN_STOCK->value, // Default: en_stock
            'current_location_id' => $locationId,
            'current_employee_id' => null, // Un import d'inventaire physique initial n'est généralement pas affecté directement aux employés. (Conforme CDC Rule 9: "Import creates assets only").
            'brand'               => $row['brand'] ?? null,
            'model'               => $row['model'] ?? null,
            'serial_number'       => $row['serial_number'] ?? null,
            'notes'               => $row['notes'] ?? null,
            // Les 'specs' JSON ne sont pas gérés par l'import Excel basique, ils seront ajoutés à la main plus tard.
            'specs'               => null, 
        ]);
    }

    /**
     * Méthode pour récupérer le bilan final à la fin de l'import.
     * Cette méthode est appelée par notre ImportController.
     */
    public function getResults(): array
    {
        return[
            'success_count' => $this->successCount,
            'errors'        => $this->errors,
        ];
    }
}
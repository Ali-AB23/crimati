<?php
namespace App\Http\Requests;

use App\Models\AssetType;
use App\Enums\AssetStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreAssetRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur a le droit de faire cette requête.
     */
    public function authorize(): bool
    {
        // Pour l'instant on retourne true. Plus tard, on vérifiera si 
        // l'utilisateur a le rôle ADMIN_IT ou INVENTORISTE.
        return true; 
    }

    /**
     * Les règles de validation de base.
     */
    public function rules(): array
    {

        $asset = $this->route('asset');
        $assetId = $asset ? $asset->id : null;
        // 1. On valide d'abord les champs standards de la table `assets`
        $rules = [
            'inventory_code'      =>['required', 'string', 'unique:assets,inventory_code,'. $assetId],
            'asset_type_id'       =>['required', 'exists:asset_types,id'],
            'brand'               =>['nullable', 'string', 'max:100'],
            'model'               =>['nullable', 'string', 'max:100'],
            'serial_number'       => ['nullable', 'string', 'max:100'],
            'status'              => ['required', new Enum(AssetStatus::class)],
            'current_location_id' => ['required', 'exists:locations,id'],
            'current_employee_id' =>['nullable', 'exists:employees,id'],
            'notes'               => ['nullable', 'string'],
            
            // On s'attend à recevoir un tableau (ou un objet JSON) pour les specs
            'specs'               =>['nullable', 'array'], 
        ];

        // 2. VALIDATION DYNAMIQUE (La vraie puissance de notre architecture)
        // Si on a bien reçu un type d'asset valide, on va chercher son schéma.
        $typeId = $this->input('asset_type_id');
        
        if ($typeId) {
            $type = AssetType::find($typeId);
            
            if ($type && $type->spec_schema) {
                // On boucle sur le schéma défini en base de données (ex: 'ram_gb', 'cpu')
                foreach ($type->spec_schema as $key => $validationRules) {
                    
                    // On construit la règle Laravel pour CHAQUE clé du JSON !
                    // Ex: 'specs.ram_gb' => 'required|numeric|min:4'
                    $ruleDefinition =[];
                    
                    if (isset($validationRules['type']) && $validationRules['type'] === 'number') {
                        $ruleDefinition[] = 'numeric';
                        if (isset($validationRules['min'])) $ruleDefinition[] = 'min:' . $validationRules['min'];
                    } else {
                        $ruleDefinition[] = 'string';
                    }

                    // On assigne la règle dynamique
                    $rules["specs.{$key}"] = $ruleDefinition;
                }
            }
        }

        return $rules;
    }
}
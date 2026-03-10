<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetMovement;
use App\Models\User;
use App\Enums\MovementType;
use Illuminate\Support\Facades\DB;
use Exception;

class MoveAssetService
{
    /**
     * Déplace un matériel en garantissant la création de l'historique.
     * 
     * @param Asset $asset Le matériel à déplacer
     * @param int $toLocationId L'ID du nouveau lieu
     * @param int|null $toEmployeeId L'ID du nouvel employé (null si retour en stock)
     * @param MovementType $type Le type de mouvement (Enum)
     * @param User $movedBy L'utilisateur qui fait l'action
     * @param string|null $note Une note optionnelle
     * @return AssetMovement L'historique créé
     * @throws Exception Si la transaction échoue
     */
    public function move(
        Asset $asset, 
        int $toLocationId, 
        ?int $toEmployeeId, 
        MovementType $type, 
        User $movedBy, 
        ?string $note = null
    ): AssetMovement {
        
        // 1. VÉRIFICATION (Guard Clause)
        // On vérifie si l'état de destination est exactement le même que l'état actuel.
        // Si oui, on refuse de polluer l'historique avec un mouvement fantôme.
        if ($asset->current_location_id === $toLocationId && $asset->current_employee_id === $toEmployeeId) {
            throw new Exception("Le matériel est déjà à cet emplacement et affecté à cet employé.");
        }

        // 2. LA TRANSACTION BASE DE DONNÉES (Le bouclier absolu)
        // DB::transaction() garantit que TOUT le code à l'intérieur réussit, ou TOUT est annulé.
        return DB::transaction(function () use ($asset, $toLocationId, $toEmployeeId, $type, $movedBy, $note) {
            
            // A. On sauvegarde l'état ACTUEL (Avant modification)
            $fromLocationId = $asset->current_location_id;
            $fromEmployeeId = $asset->current_employee_id;

            // B. On crée la ligne d'historique dans `asset_movements`
            $movement = AssetMovement::create([
                'asset_id'           => $asset->id,
                'type'               => $type->value, // On extrait la string de l'Enum
                'from_location_id'   => $fromLocationId,
                'from_employee_id'   => $fromEmployeeId,
                'to_location_id'     => $toLocationId,
                'to_employee_id'     => $toEmployeeId,
                'moved_by_user_id'   => $movedBy->id,
                'moved_at'           => now(), // Date et heure exactes
                'note'               => $note,
            ]);

            // C. On met à jour l'état du matériel dans `assets`
            $asset->update([
                'current_location_id' => $toLocationId,
                'current_employee_id' => $toEmployeeId,
                // Bonus métier : Si on l'affecte à un employé, on le passe en "en_service"
                // S'il retourne en stock (employé null), tu pourrais le repasser en "en_stock"
            ]);

            // Si on arrive ici sans erreur, Laravel valide (COMMIT) la transaction dans MySQL.
            return $movement;
        });
    }
}
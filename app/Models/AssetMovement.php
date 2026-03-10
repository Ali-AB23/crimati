<?php

namespace App\Models;

use App\Enums\MovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetMovement extends Model
{
    use HasFactory;

    protected $fillable =[
        'asset_id',
        'type',
        'from_location_id',
        'to_location_id',
        'from_employee_id',
        'to_employee_id',
        'moved_by_user_id',
        'moved_at',
        'note',
    ];

    /**
     * LE CASTING : Enum + Dates
     */
    protected $casts =[
        'type'     => MovementType::class,
        // On dit à Laravel que moved_at est une date. 
        // Il va automatiquement la transformer en objet "Carbon", 
        // ce qui permettra de faire $movement->moved_at->diffForHumans() (ex: "Il y a 2 jours")
        'moved_at' => 'datetime', 
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    // ⚠️ PIÈGE CLASSIQUE : Multiples relations vers la MÊME table
    // On doit absolument préciser la clé étrangère, sinon Laravel va chercher `location_id`
    
    public function fromLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }

    public function fromEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'from_employee_id');
    }

    public function toEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'to_employee_id');
    }

    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by_user_id');
    }
}
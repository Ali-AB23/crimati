<?php

namespace App\Models;

use App\Enums\AssetStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;

    protected $fillable =[
        'inventory_code',
        'asset_type_id',
        'brand',
        'model',
        'serial_number',
        'specs',
        'status',
        'current_location_id',
        'current_employee_id',
        'notes',
    ];

    /**
     * LE CASTING : JSON + ENUM
     */
    protected $casts =[
        'specs'  => 'array',             // Transforme le JSON en tableau PHP
        'status' => AssetStatus::class,  // Transforme la string en Enum AssetStatus
    ];

    /**
     * RELATION : Le type du matériel (ex: Laptop).
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    /**
     * RELATION : Le lieu actuel.
     */
    public function currentLocation(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    /**
     * RELATION : L'employé actuel (peut être null si en stock).
     */
    public function currentEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'current_employee_id');
    }

    /**
     * RELATION : L'historique des mouvements.
     */
    public function movements(): HasMany
    {
        return $this->hasMany(AssetMovement::class);
    }

    /**
     * RELATION : L'historique des tickets/réclamations sur ce matériel.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}

<?php

namespace App\Models;

use App\Enums\LocationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'type',
        'parent_id',
        'org_unit_id',
    ];

    protected $casts = [
        'type' => LocationType::class,
    ];

    /**
     * RELATION AUTO-RÉFÉRENCÉE (Parent : Étage -> Bâtiment)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    /**
     * RELATION AUTO-RÉFÉRENCÉE (Enfants : Bâtiment -> Étages)
     */
    public function children(): HasMany
    {
        return $this->hasMany(Location::class, 'parent_id');
    }

    /**
     * RELATION : Le lieu peut être rattaché à une Unité (pour les règles de visibilité des tickets).
     */
    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    /**
     * RELATION : Les employés qui ont ce lieu comme bureau principal.
     */
    public function employees(): HasMany
    {
        // Laravel chercherait `location_id` par défaut. 
        // Notre MLD dit `office_location_id`. Il faut le préciser.
        return $this->hasMany(Employee::class, 'office_location_id');
    }

    /**
     * RELATION : Les matériels (Assets) physiquement présents ici en ce moment.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'current_location_id');
    }
}

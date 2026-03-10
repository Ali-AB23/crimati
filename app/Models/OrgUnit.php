<?php
namespace App\Models;

use App\Enums\OrgUnitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Location;

class OrgUnit extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'type',
        'parent_id',
    ];

    /**
     * LE CASTING ENUM : On transforme la string de la DB en Enum PHP.
     */
    protected $casts =[
        'type' => OrgUnitType::class,
    ];

    /**
     * RELATION AUTO-RÉFÉRENCÉE (Parent)
     * Une unité (ex: Service IT) appartient à une unité supérieure (ex: Pôle Technique).
     */
    public function parent(): BelongsTo
    {
        // On pointe sur la même classe !
        return $this->belongsTo(OrgUnit::class, 'parent_id');
    }

    /**
     * RELATION AUTO-RÉFÉRENCÉE (Enfants)
     * Une unité (ex: Pôle) possède plusieurs sous-unités (ex: Services).
     */
    public function children(): HasMany
    {
        return $this->hasMany(OrgUnit::class, 'parent_id');
    }

    /**
     * RELATION : Une unité contient plusieurs employés.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * RELATION : Les lieux "communs" rattachés à cette unité (ex: Salle de réunion du service).
     */
    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }
}
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\orgUnit;

class Employee extends Model
{
    use HasFactory;

    protected $fillable =[
        'user_id',
        'matricule',
        'full_name',
        'email',
        'phone',
        'org_unit_id',
        'office_location_id',
    ];

    /**
     * RELATION : L'employé "appartient" à un User. (Relation inverse de HasOne)
     */
    public function user(): BelongsTo
    {
        // Pourquoi BelongsTo ? Parce que c'est la table `employees` qui 
        // contient la clé étrangère `user_id`. C'est "l'enfant".
        return $this->belongsTo(User::class);
    }

    /**
     * RELATION : L'employé "appartient" à une Unité Organisationnelle. (0..* --> 1)
     */
    public function orgUnit(): BelongsTo
    {
        return $this->belongsTo(OrgUnit::class);
    }

    /**
     * RELATION : Le bureau physique de l'employé. (0..* --> 0..1)
     */
    public function officeLocation(): BelongsTo
    {
        // ⚠️ PIÈGE CLASSIQUE ÉVITÉ !
        // Si on ne précise pas le 2ème paramètre, Laravel va chercher une colonne `location_id`.
        // Mais dans notre migration, on l'a appelée `office_location_id`. 
        // Il faut donc le préciser explicitement à Laravel.
        return $this->belongsTo(Location::class, 'office_location_id');
    }
}
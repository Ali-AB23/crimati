<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetType extends Model
{
    use HasFactory;

    protected $fillable =[
        'asset_category_id',
        'name',
        'spec_schema',
    ];

    /**
     * LE CASTING JSON
     * Laravel va transformer automatiquement la colonne JSON de MySQL en Array PHP.
     */
    protected $casts =[
        'spec_schema' => 'array',
    ];

    /**
     * RELATION : Le type appartient à une grande catégorie.
     */
    public function category(): BelongsTo
    {
        // ⚠️ PIÈGE CLASSIQUE : Si ta méthode s'appelle category(), 
        // Laravel va chercher une clé 'category_id'. Mais notre colonne est 'asset_category_id'.
        // Il faut donc surcharger le nom de la clé étrangère !
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    /**
     * RELATION : Un type possède plusieurs matériels physiques.
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
}
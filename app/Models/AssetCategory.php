<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * RELATION : Une catégorie (ex: Informatique) possède plusieurs types (ex: PC, Imprimante).
     */
    public function assetTypes(): HasMany
    {
        return $this->hasMany(AssetType::class);
    }
}
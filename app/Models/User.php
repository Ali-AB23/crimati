<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Employee; 

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable =[
        'username',
        'password',
        'role',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts =[
        'password' => 'hashed', 
        'active'   => 'boolean', 
        'role'     => UserRole::class, 
    ];

    public function employee(): HasOne
    {
        // Pourquoi HasOne ? Parce que la clé étrangère `user_id` est dans la table `employees`.
        // Le User est le "Parent" dans cette relation.
        return $this->hasOne(Employee::class);
    }
}

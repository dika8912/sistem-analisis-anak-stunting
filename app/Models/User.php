<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** @var array<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /** @var array<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Automatically hash passwords when set.
     */
    public function setPasswordAttribute(?string $value): void
    {
        if ($value === null) {
            return;
        }

        $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
    }

    /**
     * Provide the factory for the model.
     *
     * @return UserFactory
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}

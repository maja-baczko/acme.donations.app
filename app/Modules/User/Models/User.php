<?php

namespace App\Modules\User\Models;

use App\Modules\Media\Models\Image;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    // Create a new factory instance for the model
    protected static function newFactory(): UserFactory {
        return UserFactory::new();
    }

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'department',
        'function',
        'still_working',
        'profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'still_working' => 'boolean',
    ];

    public function profile(): BelongsTo {
        return $this->belongsTo(Image::class, 'profile');
    }
}

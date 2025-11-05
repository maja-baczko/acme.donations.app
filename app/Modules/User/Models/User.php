<?php

namespace App\Modules\User\Models;

use App\Modules\Media\Models\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasRoles;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
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

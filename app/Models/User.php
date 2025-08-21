<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password_hash',
        'district',
        'city',
        'ward',
        'area_name',
        'citizenship_id_number',
        'gender',
        'is_verified',
        'agreed_to_terms',
        'citizenship_front_image',
        'citizenship_back_image',
        'profile_image',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'agreed_to_terms' => 'boolean',
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->user_id;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getCitizenshipFrontImageAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function getCitizenshipBackImageAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function getProfileImageAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }
    public function posts()
{
    return $this->hasMany(Post::class, 'user_id', 'user_id');
}
}

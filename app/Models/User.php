<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'username', 'first_name', 'last_name', 'district', 'city',
        'ward', 'area_name', 'phone_number', 'gender', 'email', 'bio', 'profile_image', 'password_hash',
        'citizenship_front_image', 'citizenship_back_image', 'citizenship_id_number',
        'is_verified', 'agreed_to_terms', 'likes_count', 'posts_count',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'agreed_to_terms' => 'boolean',
        'likes_count' => 'integer',
        'posts_count' => 'integer',
        'ward' => 'integer',
        'bio' => 'string',
        'profile_image' => 'string',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getProfileImageAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }
}

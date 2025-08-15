<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $primaryKey = 'user_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'username', 'first_name', 'last_name', 'district', 'city',
        'ward', 'area_name', 'phone_number', 'gender', 'email', 'bio','password_hash',
        'citizenship_front_image', 'citizenship_back_image', 'citizenship_id_number',
        'is_verified', 'agreed_to_terms',
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
        'bio'=> 'string',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Model
{
    use HasFactory, HasApiTokens;

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
}

<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'superadmins';
    protected $primaryKey = 'superadmin_id';
    protected $fillable = [
        'username',
        'password_hash',
        'email',
        'first_name',
        'last_name',
    ];

    public function getAuthIdentifierName()
    {
        return 'superadmin_id';
    }

    public function getAuthIdentifier()
    {
        return $this->superadmin_id;
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}

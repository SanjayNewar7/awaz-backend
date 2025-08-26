<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Issue extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'heading',
        'description',
        'report_type',
        'district',
        'ward',
        'area_name',
        'location',
        'photo1',
        'photo2',
        'support_count',
        'affected_count',
        'not_sure_count',
        'invalid_count',
        'fixed_count',
    ];

    public function getPhoto1Attribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function getPhoto2Attribute($value)
    {
        return $value ? Storage::url($value) : null;
    }
}

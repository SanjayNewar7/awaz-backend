<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'heading',
        'description',
        'report_type',
        'district',
        'ward',
        'area_name',
        'location',
        'photo1', // Stores file path (e.g., images/issues/photo1_abc123.jpg)
        'photo2', // Stores file path (e.g., images/issues/photo2_abc123.jpg)
    ];

    // Optional: Add accessor to get full storage URL for images
    public function getPhoto1Attribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function getPhoto2Attribute($value)
    {
        return $value ? Storage::url($value) : null;
    }
}

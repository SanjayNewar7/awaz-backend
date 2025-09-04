<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'district',
        'ward',
        'area_name',
        'issue_id',
        'image',
        'is_read',
    ];

     protected $casts = [
        'is_read' => 'boolean', // Cast to boolean
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    // Accessor to get full URL for image
    public function getImageAttribute($value)
    {
        return $value ? asset('storage/' . $value) : null;
    }
}

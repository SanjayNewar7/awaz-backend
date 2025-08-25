<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'author_id',
        'author_name',
        'action',
        'issue_id',
        'post_id',
        'issue_description',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id', 'id');
    }
}

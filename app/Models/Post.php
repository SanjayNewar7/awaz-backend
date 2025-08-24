<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $primaryKey = 'post_id';
    protected $fillable = [
        'issue_id', 'user_id', 'username', 'title', 'description', 'category',
        'image1', 'image2', 'support_count', 'affected_count', 'not_sure_count',
        'invalid_count', 'fixed_count', 'comment_count'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getFormattedCreatedAt()
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    public function getImage1Attribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function getImage2Attribute($value)
    {
        return $value ? Storage::url($value) : null;
    }
}


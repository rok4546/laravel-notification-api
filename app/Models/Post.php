<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
    ];

    /**
     * Get all activity logs for this post
     */
    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'notifiable');
    }
}

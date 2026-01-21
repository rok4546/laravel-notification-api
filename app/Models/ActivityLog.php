<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'channel',
        'message',
        'notifiable_type',
        'notifiable_id',
    ];

    /**
     * Get the parent notifiable model (User or Post)
     */
    public function notifiable()
    {
        return $this->morphTo();
    }
}

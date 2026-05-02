<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NudgeNotification extends Model
{
    protected $table = 'nudge_notifications';

    protected $fillable = [
        'admin_id',
        'barangay',
        'category_ids',
        'message',
        'is_cleared',
    ];

    protected $casts = [
        'category_ids' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'barangay',
        'category',
        'description',
        'files',
        'status',
        'admin_comment'
    ];

    protected $casts = [
        'files' => 'array',
        'is_edited' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

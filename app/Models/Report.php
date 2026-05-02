<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'barangay',
        'category_id',
        'description',
        'files',
        'status',
        'admin_comment',
        'is_late',
        'is_edited'
    ];

    protected $casts = [
        'files' => 'array',
        'is_edited' => 'boolean',
        'is_late' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    }

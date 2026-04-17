<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id',
        'barangay',
        'category_id', // ✅ MUST BE HERE
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function deadline()
    {
        return $this->belongsTo(CategoryDeadline::class, 'category_deadline_id');
    }
}

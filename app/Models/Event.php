<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EventImage;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'status',
        'start_date',
        'end_date',
        'is_public'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    public function youths()
    {
        return $this->belongsToMany(Youth::class)
            ->withPivot('attended_at')
            ->withTimestamps();
    }
}

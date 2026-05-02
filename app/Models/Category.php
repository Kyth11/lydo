<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'deadline',
        'is_archived',
        'is_active',
    ];

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

}

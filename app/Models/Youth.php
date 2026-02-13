<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Youth extends Model
{
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'sex',
        'age',
        'birthday',
        'home_address',
        'religion',
        'education',
        'is_osy',
        'is_isy',
        'is_working_youth',
        'skills',
        'source_of_income',
        'contact_number',
        'region',
        'province',
        'municipality',
        'barangay',
        'purok_zone',
        'family_members',
        'is_archived'
    ];

    protected $casts = [
        'birthday' => 'date',
        'age' => 'integer',
        'is_osy' => 'boolean',
        'is_isy' => 'boolean',
        'is_working_youth' => 'boolean',
        'family_members' => 'array',
        'is_archived' => 'boolean'
    ];
}

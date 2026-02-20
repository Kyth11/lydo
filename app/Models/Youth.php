<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

  protected static function booted()
{
    // 🔹 When creating new profile
    static::creating(function ($youth) {

        if ($youth->birthday) {
            $age = Carbon::parse($youth->birthday)->age;
            $youth->age = $age;

            // ✅ Auto archive ONLY on create if 31 and above
            if ($age >= 31) {
                $youth->is_archived = 1;
            }
        }
    });

    // 🔹 When updating profile (only recalculate age)
    static::updating(function ($youth) {

        if ($youth->birthday) {
            $youth->age = Carbon::parse($youth->birthday)->age;
        }

        // ❌ No auto-archive here
    });
}
}

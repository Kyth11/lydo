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
        'profile_photo',
        'sex',
        'gender',
        'age',
        'civil_status',
        'birthday',
        'home_address',
        'religion',
        'education',
        'is_sk_voter',
        'is_osy',
        'is_isy',
        'is_unemployed',
        'is_employed',
        'is_self_employed',
        'is_4ps',
        'is_ip',
        'is_pwd',
        'skills',
        'preferred_skills',
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
        'is_unemployed' => 'boolean',
        'is_employed' => 'boolean',
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
    public function events()
    {
        return $this->belongsToMany(Event::class)
            ->withPivot('attended_at')
            ->withTimestamps();
    }
}

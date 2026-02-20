<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'for_all_barangays',
        'barangay'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'for_all_barangays' => 'boolean',
        'barangay' => 'array'
    ];

    public function barangays()
    {
        return $this->hasMany(AnnouncementBarangay::class);
    }

    public function scopeActive($query)
    {
        return $query->whereDate('start_date', '<=', Carbon::today())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhereDate('end_date', '>=', Carbon::today());
            });
    }
}

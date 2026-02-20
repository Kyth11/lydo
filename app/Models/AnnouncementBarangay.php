<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementBarangay extends Model
{
    protected $table = 'announcement_barangay';

    protected $fillable = [
        'announcement_id',
        'barangay'
    ];

    public $timestamps = false;

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}

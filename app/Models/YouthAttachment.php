<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YouthAttachment extends Model
{
    protected $fillable = ['youth_id', 'file_path'];

    public function youth()
    {
        return $this->belongsTo(\App\Models\Youth::class);
    }
}

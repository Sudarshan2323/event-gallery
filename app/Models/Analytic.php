<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Analytic extends Model
{
    protected $fillable = [
        'photo_id',
        'downloads',
        'qr_scans',
        'visitor_ip',
    ];

    public function photo()
    {
        return $this->belongsTo(Photo::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = [
        'event_id',
        'image_path',
        'qr_code_path',
        'downloads',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function analytics()
    {
        return $this->hasMany(Analytic::class);
    }
}

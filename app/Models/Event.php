<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'event_name',
        'couple_name',
        'event_date',
        'location',
        'organizer_logo',
        'description',
        'qr_code_path',
        'slug',
    ];

    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
}

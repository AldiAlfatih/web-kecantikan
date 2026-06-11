<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPcaProfile extends Model
{
    protected $fillable = [
        'user_id',
        'skin_tone_level',
        'undertone',
        'vein_color',

        'eye_contrast',
        'hair_color',

        'hue',
        'value',
        'chroma',
        'season',
        'confidence',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
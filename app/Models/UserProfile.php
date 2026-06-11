<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $fillable = [
        'user_id',
        'skin_type',
        'tone',        // Fair/Light/Medium/Tan/Deep/Dark
        'undertone',   // Warm/Neutral/Cool
        'skin_problem',
        'vein_color',  // Biru/Hijau/Ungu/Campuran
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

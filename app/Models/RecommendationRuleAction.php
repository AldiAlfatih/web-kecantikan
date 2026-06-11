<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationRuleAction extends Model
{
    protected $fillable = [
        'rule_id',
        'target',
        'operator',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];

    public function rule()
    {
        return $this->belongsTo(RecommendationRule::class, 'rule_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationRuleCondition extends Model
{
    protected $fillable = [
        'rule_id',
        'field',
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

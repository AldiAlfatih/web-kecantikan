<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecommendationRule extends Model
{
    protected $fillable = [
        'name',
        'priority',
        'is_active',
    ];

    public function conditions()
    {
        return $this->hasMany(RecommendationRuleCondition::class, 'rule_id');
    }

    public function actions()
    {
        return $this->hasMany(RecommendationRuleAction::class, 'rule_id');
    }
}

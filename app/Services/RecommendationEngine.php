<?php

namespace App\Services;

use App\Models\RecommendationRule;
use App\Models\ProductShade;
use Illuminate\Support\Facades\DB;

class RecommendationEngine
{
    public function recommend(array $profile, array $options = [])
    {
        $category = $options['category'] ?? null;
        $brand    = $options['brand'] ?? null;

        $rules = RecommendationRule::query()
            ->where('is_active', true)
            ->with(['conditions', 'actions'])
            ->orderByDesc('priority')
            ->get();

        $filters = [
            'shade_undertone' => [],
            'shade_tone'      => [],
        ];

        // ===== Default filter dari PCA user =====
        if (!empty($profile['undertone'])) {
            $filters['shade_undertone'] = [strtolower($profile['undertone'])];
        }

        if (!empty($profile['skin_tone_level'])) {
            $filters['shade_tone'] = $this->mapToneLevelToShadeTones((int) $profile['skin_tone_level']);
        }

        // ===== Apply rule (optional) =====
        foreach ($rules as $rule) {
            if ($this->ruleMatchesProfile($rule->conditions, $profile)) {
                $this->applyActionsToFilters($rule->actions, $filters);
            }
        }

        // ===== Query ke ProductShade =====
        $query = ProductShade::query()->with('product');

        if ($category) {
            $query->whereHas('product', fn ($q) => $q->where('category', $category));
        }

        if ($brand) {
            $query->whereHas('product', fn ($q) => $q->where('brand', $brand));
        }

        // stok shade harus ada
        $query->where('stock', '>', 0);

        // CASE-INSENSITIVE + support value "all"
        // undertone: (LOWER(undertone) in filters) OR undertone == 'all'
        if (!empty($filters['shade_undertone'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereIn(DB::raw('LOWER(undertone)'), $filters['shade_undertone'])
                  ->orWhere(DB::raw('LOWER(undertone)'), 'all');
            });
        } else {
            // kalau user belum punya undertone, tampilkan yang "all"
            $query->where(DB::raw('LOWER(undertone)'), 'all');
        }

        // tone: (LOWER(tone) in filters) OR tone == 'all'
        if (!empty($filters['shade_tone'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereIn(DB::raw('LOWER(tone)'), $filters['shade_tone'])
                  ->orWhere(DB::raw('LOWER(tone)'), 'all');
            });
        } else {
            $query->where(DB::raw('LOWER(tone)'), 'all');
        }

        return $query->get();
    }

    /**
     * Range mapping agar tidak terlalu “kaku”
     */
    private function mapToneLevelToShadeTones(int $level): array
    {
        return match ($level) {
            1 => ['fair'],
            2 => ['fair', 'light'],
            3 => ['light', 'medium'],
            4 => ['medium', 'tan'],
            5 => ['tan', 'deep', 'dark'],
            6 => ['deep', 'dark'],
            default => [],
        };
    }

    private function ruleMatchesProfile($conditions, array $profile): bool
    {
        foreach ($conditions as $cond) {
            $field = $cond->field;
            $op    = strtolower($cond->operator);
            $vals  = is_array($cond->value) ? $cond->value : [$cond->value];

            $profileVal = $profile[$field] ?? null;

            if (!$this->match($profileVal, $op, $vals)) {
                return false;
            }
        }
        return true;
    }

    private function match($profileVal, string $op, array $vals): bool
    {
        $p = is_string($profileVal) ? strtolower(trim($profileVal)) : $profileVal;
        $v = array_map(fn ($x) => is_string($x) ? strtolower(trim($x)) : $x, $vals);

        return match ($op) {
            '=', '==' => $p !== null && in_array($p, $v, true),
            '!='      => $p === null ? true : !in_array($p, $v, true),
            'in'      => $p !== null && in_array($p, $v, true),
            'not_in'  => $p === null ? true : !in_array($p, $v, true),
            default   => false,
        };
    }

    private function applyActionsToFilters($actions, array &$filters): void
    {
        foreach ($actions as $act) {
            $target = strtolower(trim($act->target));
            $op     = strtolower(trim($act->operator));
            $vals   = is_array($act->value) ? $act->value : [$act->value];

            if ($op !== 'in') continue;
            if (!isset($filters[$target])) continue;

            foreach ($vals as $val) {
                $filters[$target][] = is_string($val) ? strtolower(trim($val)) : $val;
            }
        }

        foreach ($filters as $k => $arr) {
            $filters[$k] = array_values(array_unique($arr));
        }
    }
}
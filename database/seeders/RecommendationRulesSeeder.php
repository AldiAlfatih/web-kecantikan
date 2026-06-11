<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use App\Models\RecommendationRule;
use App\Models\RecommendationRuleCondition;
use App\Models\RecommendationRuleAction;

class RecommendationRulesSeeder extends Seeder
{
    public function run(): void
    {
        // Biar tidak dobel setiap kali seed (opsional tapi sangat disarankan)
        DB::table('recommendation_rule_actions')->truncate();
        DB::table('recommendation_rule_conditions')->truncate();
        DB::table('recommendation_rules')->truncate();

        /**
         * =========================================================
         * RULE UNDERTONE (paling penting)
         * =========================================================
         * user undertone -> shade undertone
         */
        $this->makeRule(
            name: 'User warm -> warm shades',
            priority: 100,
            conditions: [
                ['field' => 'undertone', 'operator' => '=', 'value' => ['warm']],
            ],
            actions: [
                ['target' => 'shade_undertone', 'operator' => 'in', 'value' => ['warm']],
            ],
            description: 'Jika undertone user warm, rekomendasi shade undertone warm.'
        );

        $this->makeRule(
            name: 'User neutral -> neutral shades',
            priority: 100,
            conditions: [
                ['field' => 'undertone', 'operator' => '=', 'value' => ['neutral']],
            ],
            actions: [
                ['target' => 'shade_undertone', 'operator' => 'in', 'value' => ['neutral']],
            ],
            description: 'Jika undertone user neutral, rekomendasi shade undertone neutral.'
        );

        $this->makeRule(
            name: 'User cool -> cool shades',
            priority: 100,
            conditions: [
                ['field' => 'undertone', 'operator' => '=', 'value' => ['cool']],
            ],
            actions: [
                ['target' => 'shade_undertone', 'operator' => 'in', 'value' => ['cool']],
            ],
            description: 'Jika undertone user cool, rekomendasi shade undertone cool.'
        );

        /**
         * =========================================================
         * RULE TONE (kedua paling penting)
         * =========================================================
         * user tone -> shade tone
         */
        $toneList = ['fair','light','medium','tan','deep','dark'];

        foreach ($toneList as $tone) {
            $this->makeRule(
                name: "User tone {$tone} -> {$tone} shades",
                priority: 90,
                conditions: [
                    ['field' => 'tone', 'operator' => '=', 'value' => [$tone]],
                ],
                actions: [
                    ['target' => 'shade_tone', 'operator' => 'in', 'value' => [$tone]],
                ],
                description: "Jika tone user {$tone}, rekomendasi shade tone {$tone}."
            );
        }
    }

    private function makeRule(
        string $name,
        int $priority,
        array $conditions,
        array $actions,
        ?string $description = null
    ): void {
        $rule = RecommendationRule::create([
            'name' => $name,
            'priority' => $priority,
            'is_active' => true,
            'description' => $description,
        ]);

        foreach ($conditions as $c) {
            RecommendationRuleCondition::create([
                'rule_id' => $rule->id,
                'field' => $c['field'],
                'operator' => $c['operator'],
                // simpan sebagai JSON array (casting kamu: array)
                'value' => $c['value'],
                'logic' => 'AND',
            ]);
        }

        foreach ($actions as $a) {
            RecommendationRuleAction::create([
                'rule_id' => $rule->id,
                'target' => $a['target'],
                'operator' => $a['operator'],
                // simpan sebagai JSON array (casting kamu: array)
                'value' => $a['value'],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\ImcType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImcTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ImcType::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $imcTypes = [
            [
                'id' => 1,
                'name' => 'Bajo peso severo',
                'description' => 'Desnutrición grave con alto riesgo de complicaciones orgánicas y deficiencias nutricionales.',
                'recommendation' => 'Atención médica urgente, evaluación nutricional especializada, plan de recuperación supervisado.',
                'min_value' => 0,
                'max_value' => 15.9,
                'color' => '#8E44AD',
            ],
            [
                'id' => 2,
                'name' => 'Bajo peso',
                'description' => 'Peso por debajo del rango saludable. Puede indicar ingesta insuficiente o patología subyacente.',
                'recommendation' => 'Aumentar ingesta calórica con alimentos nutritivos, consultar con nutricionista, descartar causas médicas.',
                'min_value' => 16.0,
                'max_value' => 18.4,
                'color' => '#2980B9',
            ],
            [
                'id' => 3,
                'name' => 'Peso normal',
                'description' => 'Rango considerado saludable. Menor riesgo de enfermedades crónicas asociadas al peso.',
                'recommendation' => 'Mantener hábitos actuales: dieta equilibrada, actividad física regular (150 min/sem), controles médicos periódicos.',
                'min_value' => 18.5,
                'max_value' => 24.9,
                'color' => '#27AE60',
            ],
            [
                'id' => 4,
                'name' => 'Sobrepeso',
                'description' => 'Exceso de peso que puede aumentar el riesgo de hipertensión, diabetes tipo 2 y enfermedades cardiovasculares.',
                'recommendation' => 'Reducir consumo de ultraprocesados y azúcares, incrementar actividad física, objetivo de pérdida gradual (0.5–1 kg/sem).',
                'min_value' => 25.0,
                'max_value' => 29.9,
                'color' => '#F39C12',
            ],
            [
                'id' => 5,
                'name' => 'Obesidad grado I',
                'description' => 'Obesidad moderada. Riesgo elevado de síndrome metabólico, apnea del sueño y problemas articulares.',
                'recommendation' => 'Intervención médica y nutricional estructurada, ejercicio aeróbico y de fuerza, evaluar factores de riesgo cardiovascular.',
                'min_value' => 30.0,
                'max_value' => 34.9,
                'color' => '#E67E22',
            ],
            [
                'id' => 6,
                'name' => 'Obesidad grado II',
                'description' => 'Obesidad severa con riesgo muy alto de comorbilidades graves como diabetes, cardiopatía e insuficiencia renal.',
                'recommendation' => 'Seguimiento médico multidisciplinario, posible indicación de medicamentos, considerar programa de pérdida de peso intensivo.',
                'min_value' => 35.0,
                'max_value' => 39.9,
                'color' => '#E74C3C',
            ],
            [
                'id' => 7,
                'name' => 'Obesidad grado III',
                'description' => 'Obesidad mórbida. Riesgo extremo de complicaciones letales. Impacto severo en movilidad y calidad de vida.',
                'recommendation' => 'Evaluación para cirugía bariátrica, seguimiento médico continuo, apoyo psicológico, rehabilitación física adaptada.',
                'min_value' => 40.0,
                'max_value' => null,
                'color' => '#C0392B',
            ],
        ];

        foreach ($imcTypes as $imcType) {
            ImcType::create($imcType);
        }
    }
}

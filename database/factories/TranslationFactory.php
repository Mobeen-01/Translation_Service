<?php

namespace Database\Factories;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    public function definition(): array
    {
        return [
            'translation_key_id' => TranslationKey::factory(),
            'locale_id' => Locale::query()->inRandomOrder()->value('id') ?? Locale::factory(),
            'value' => fake()->sentence(),
            'is_approved' => fake()->boolean(),
        ];
    }
}
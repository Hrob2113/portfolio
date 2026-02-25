<?php

namespace Database\Factories;

use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Translation>
 */
class TranslationFactory extends Factory
{
    protected $model = Translation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group' => fake()->randomElement(['nav', 'hero', 'contact']),
            'key' => fake()->unique()->slug(2),
            'locale' => fake()->randomElement(['en', 'cs']),
            'value' => fake()->sentence(),
        ];
    }
}

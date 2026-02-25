<?php

namespace Database\Seeders;

use App\Models\Translation;
use Illuminate\Database\Seeder;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['en', 'cs'] as $locale) {
            $path = lang_path("{$locale}.json");

            if (! file_exists($path)) {
                continue;
            }

            $translations = json_decode(file_get_contents($path), true);

            foreach ($translations as $key => $value) {
                $group = str_contains($key, '.') ? explode('.', $key)[0] : 'general';

                Translation::query()->updateOrCreate(
                    ['key' => $key, 'locale' => $locale],
                    ['group' => $group, 'value' => $value],
                );
            }
        }
    }
}

<?php

namespace App\Services;

use App\Models\Translation;

class TranslationCompiler
{
    public function compile(?string $locale = null): void
    {
        $locales = $locale ? [$locale] : ['en', 'cs'];

        foreach ($locales as $loc) {
            $translations = Translation::query()
                ->forLocale($loc)
                ->orderBy('key')
                ->pluck('value', 'key')
                ->all();

            $path = lang_path("{$loc}.json");

            file_put_contents(
                $path,
                json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)."\n",
            );
        }
    }
}

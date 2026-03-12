<?php

namespace App\Observers;

use App\Models\Translation;
use App\Services\TranslationCompiler;

class TranslationObserver
{
    public function __construct(private readonly TranslationCompiler $compiler) {}

    public function saved(Translation $translation): void
    {
        $this->compiler->compile($translation->locale);
    }

    public function deleted(Translation $translation): void
    {
        $this->compiler->compile($translation->locale);
    }
}

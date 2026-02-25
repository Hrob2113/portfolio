<?php

namespace App\Console\Commands;

use App\Services\TranslationCompiler;
use Illuminate\Console\Command;

class CompileTranslations extends Command
{
    protected $signature = 'translations:compile {locale? : Compile a specific locale (en, cs)}';

    protected $description = 'Compile translations from database to JSON files';

    public function handle(TranslationCompiler $compiler): int
    {
        $compiler->compile($this->argument('locale'));

        $this->info('Translations compiled successfully.');

        return self::SUCCESS;
    }
}

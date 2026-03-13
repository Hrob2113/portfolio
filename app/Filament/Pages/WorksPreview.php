<?php

namespace App\Filament\Pages;

use App\Models\Work;
use Filament\Pages\Page;

class WorksPreview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $navigationLabel = 'Works Preview';

    protected static ?string $navigationGroup = null;

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.works-preview';

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        return [
            'works' => Work::query()->ordered()->get(),
        ];
    }
}

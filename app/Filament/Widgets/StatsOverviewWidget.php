<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\Translation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Unread Messages', ContactMessage::query()->unread()->count())
                ->description('Contact form submissions')
                ->icon('heroicon-o-envelope')
                ->color('danger'),

            Stat::make('Translations', Translation::query()->count())
                ->description(Translation::query()->select('locale')->distinct()->count().' languages')
                ->icon('heroicon-o-language')
                ->color('success'),

            Stat::make('Settings', Setting::query()->count())
                ->description('Site configuration values')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TranslationResource\Pages;
use App\Models\Translation;
use App\Services\TranslationCompiler;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationLabel = 'Translations';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('group')
                ->options(function (): array {
                    $groups = Translation::groups() ?: ['about', 'card', 'contact', 'footer', 'hero', 'nav', 'process', 'services', 'stack', 'works'];

                    return array_combine($groups, $groups);
                })
                ->searchable()
                ->required()
                ->columnSpan(1),

            TextInput::make('key')
                ->required()
                ->maxLength(255)
                ->columnSpan(1),

            Select::make('locale')
                ->options(Translation::LOCALES)
                ->required()
                ->columnSpan(1),

            Textarea::make('value')
                ->rows(4)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->badge()
                    ->sortable()
                    ->searchable(),

                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),

                TextColumn::make('locale')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'en' => 'info',
                        'cs' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('value')
                    ->limit(60)
                    ->searchable()
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(function (): array {
                        $groups = Translation::groups();

                        return array_combine($groups, $groups);
                    }),

                SelectFilter::make('locale')
                    ->options(Translation::LOCALES),
            ])
            ->defaultSort('group')
            ->headerActions([
                Action::make('compile')
                    ->label('Compile to JSON')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function () {
                        app(TranslationCompiler::class)->compile();

                        Notification::make()
                            ->title('Translations compiled')
                            ->body('JSON files for en and cs have been updated.')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}

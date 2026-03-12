<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages;
use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SettingResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('group')
                ->options(Setting::GROUPS)
                ->required()
                ->columnSpan(1),

            TextInput::make('key')
                ->required()
                ->maxLength(255)
                ->columnSpan(1),

            TextInput::make('value')
                ->maxLength(1000)
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('group')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'social' => 'info',
                        'contact' => 'success',
                        'seo' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('key')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono'),

                TextColumn::make('value')
                    ->limit(60)
                    ->searchable(),

                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options(Setting::GROUPS),
            ])
            ->defaultSort('group')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSetting::route('/create'),
            'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}

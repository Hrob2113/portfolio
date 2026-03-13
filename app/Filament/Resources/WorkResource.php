<?php

namespace App\Filament\Resources;

use App\Filament\Pages\WorksPreview;
use App\Filament\Resources\WorkResource\Pages;
use App\Models\Work;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class WorkResource extends Resource
{
    protected static ?string $model = Work::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationLabel = 'Works';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Content')->schema([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),

                Grid::make(2)->schema([
                    Select::make('category')
                        ->options(Work::CATEGORIES)
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set): void {
                            if ($state && isset(Work::CATEGORIES[$state])) {
                                $set('category_label', Work::CATEGORIES[$state]);
                            }
                        }),

                    TextInput::make('category_label')
                        ->label('Category label (shown on card)')
                        ->required()
                        ->maxLength(100)
                        ->helperText('Auto-filled from category, edit for a more specific label.'),
                ]),

                TagsInput::make('tags')
                    ->placeholder('Add tag…')
                    ->columnSpanFull(),

                Grid::make(2)->schema([
                    TextInput::make('link')
                        ->label('External URL')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://…'),

                    TextInput::make('year')
                        ->numeric()
                        ->required()
                        ->minValue(2000)
                        ->maxValue(2099)
                        ->default(date('Y')),
                ]),
            ]),

            Section::make('Image')->schema([
                FileUpload::make('image')
                    ->label('Card image')
                    ->image()
                    ->disk('public')
                    ->directory('works')
                    ->imagePreviewHeight('200')
                    ->columnSpanFull(),
            ]),

            Section::make('Layout')->schema([
                Radio::make('layout')
                    ->label('Card layout')
                    ->required()
                    ->options(Work::LAYOUTS)
                    ->descriptions([
                        'pc--featured' => 'Largest card — dominant hero piece, spans most of the row.',
                        'pc--tall' => 'Portrait card — tall format, great for app/mobile screenshots.',
                        'pc--wide' => 'Very wide landscape — ideal for website screenshots.',
                        'pc--wide2' => 'Wide landscape — slightly narrower than Wide.',
                        'pc--sq' => 'Compact square — works well for illustrations or logos.',
                        'pc--sq2' => 'Medium square — slightly larger than Square.',
                        'pc--half' => 'Half width — balanced, neutral proportions.',
                        'pc--third' => 'Narrow — fits as a small accent piece.',
                    ])
                    ->columns(2),
            ]),

            Section::make('Visibility & Order')->schema([
                Grid::make(2)->schema([
                    TextInput::make('sort_order')
                        ->label('Sort order')
                        ->numeric()
                        ->required()
                        ->minValue(0)
                        ->default(0)
                        ->helperText('Lower numbers appear first.'),

                    Toggle::make('published')
                        ->label('Published')
                        ->default(true)
                        ->inline(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->width('48px'),

                ImageColumn::make('image')
                    ->label('')
                    ->height(48)
                    ->width(72)
                    ->defaultImageUrl(asset('favicon.svg'))
                    ->getStateUsing(fn (Work $record): ?string => $record->imageUrl()),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'web' => 'info',
                        'ui' => 'success',
                        'graphic' => 'warning',
                        'brand' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('layout')
                    ->badge()
                    ->color('gray')
                    ->sortable(),

                TextColumn::make('year')
                    ->sortable(),

                ToggleColumn::make('published'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('preview')
                    ->label('Grid preview')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (): string => WorksPreview::getUrl()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorks::route('/'),
            'create' => Pages\CreateWork::route('/create'),
            'edit' => Pages\EditWork::route('/{record}/edit'),
        ];
    }
}

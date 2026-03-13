<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    /** @use HasFactory<\Database\Factories\WorkFactory> */
    use HasFactory;

    /** @var array<string, string> */
    public const CATEGORIES = [
        'web' => 'Web & Apps',
        'graphic' => 'Graphic Design',
        'brand' => 'Brand Identity',
        'ui' => 'UI / UX',
    ];

    /**
     * CSS class => human label (cols × rows in the 12-col grid)
     *
     * @var array<string, string>
     */
    public const LAYOUTS = [
        'pc--featured' => 'Featured — 7 × 3',
        'pc--tall' => 'Tall — 5 × 3',
        'pc--wide' => 'Wide — 8 × 2',
        'pc--wide2' => 'Wide 2 — 7 × 2',
        'pc--sq' => 'Square — 4 × 2',
        'pc--sq2' => 'Square 2 — 5 × 2',
        'pc--half' => 'Half — 6 × 2',
        'pc--third' => 'Third — 4 × 2',
    ];

    /** @var list<string> */
    protected $fillable = [
        'title',
        'description',
        'category',
        'category_label',
        'layout',
        'tags',
        'image',
        'link',
        'year',
        'sort_order',
        'published',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published' => 'boolean',
            'year' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        // Uploaded via Filament (storage path) vs legacy public asset
        if (str_starts_with($this->image, 'works/')) {
            return asset('storage/'.$this->image);
        }

        return asset($this->image);
    }
}

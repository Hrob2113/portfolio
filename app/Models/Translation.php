<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'group',
        'key',
        'locale',
        'value',
    ];

    public function scopeForGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }

    public function scopeForLocale(Builder $query, string $locale): Builder
    {
        return $query->where('locale', $locale);
    }

    /**
     * @return array<int, string>
     */
    public static function groups(): array
    {
        return static::query()
            ->select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->all();
    }
}

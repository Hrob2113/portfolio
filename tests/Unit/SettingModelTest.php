<?php

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('returns a setting value by key', function () {
    Setting::factory()->create(['key' => 'site_name', 'value' => 'Robin Portfolio']);

    expect(Setting::getValue('site_name'))->toBe('Robin Portfolio');
});

it('returns the default value when the key does not exist', function () {
    expect(Setting::getValue('nonexistent', 'fallback'))->toBe('fallback');
});

it('returns null when the key does not exist and no default is given', function () {
    expect(Setting::getValue('nonexistent'))->toBeNull();
});

it('creates a new setting via setValue', function () {
    Setting::setValue('new_key', 'new_value');

    expect(Setting::query()->where('key', 'new_key')->value('value'))->toBe('new_value');
});

it('updates an existing setting via setValue', function () {
    Setting::factory()->create(['key' => 'github_url', 'value' => 'https://github.com/old']);

    Setting::setValue('github_url', 'https://github.com/new');

    expect(Setting::getValue('github_url'))->toBe('https://github.com/new');
});

it('allows setting a value to null', function () {
    Setting::factory()->create(['key' => 'optional_key', 'value' => 'something']);

    Setting::setValue('optional_key', null);

    expect(Setting::query()->where('key', 'optional_key')->value('value'))->toBeNull();
});

<?php

use App\Models\Translation;
use App\Services\TranslationCompiler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->originalEn = File::exists(lang_path('en.json')) ? File::get(lang_path('en.json')) : '{}';
    $this->originalCs = File::exists(lang_path('cs.json')) ? File::get(lang_path('cs.json')) : '{}';
    File::put(lang_path('en.json'), '{}');
    File::put(lang_path('cs.json'), '{}');
});

afterEach(function () {
    File::put(lang_path('en.json'), $this->originalEn);
    File::put(lang_path('cs.json'), $this->originalCs);
});

it('compiles all locales to JSON files', function () {
    Translation::factory()->create(['key' => 'hero.tagline', 'locale' => 'en', 'group' => 'hero', 'value' => 'Works and feels right.']);
    Translation::factory()->create(['key' => 'hero.tagline', 'locale' => 'cs', 'group' => 'hero', 'value' => 'Funguje a dává smysl.']);

    app(TranslationCompiler::class)->compile();

    $en = json_decode(File::get(lang_path('en.json')), true);
    $cs = json_decode(File::get(lang_path('cs.json')), true);

    expect($en['hero.tagline'])->toBe('Works and feels right.')
        ->and($cs['hero.tagline'])->toBe('Funguje a dává smysl.');
});

it('compiles only the specified locale', function () {
    Translation::factory()->create(['key' => 'nav.about', 'locale' => 'en', 'group' => 'nav', 'value' => 'About']);
    Translation::factory()->create(['key' => 'nav.about', 'locale' => 'cs', 'group' => 'nav', 'value' => 'O mně']);

    File::put(lang_path('cs.json'), '{"nav.about":"old"}');

    app(TranslationCompiler::class)->compile('en');

    $en = json_decode(File::get(lang_path('en.json')), true);
    $cs = json_decode(File::get(lang_path('cs.json')), true);

    expect($en['nav.about'])->toBe('About')
        ->and($cs['nav.about'])->toBe('old'); // cs should not be touched
});

it('produces valid JSON output', function () {
    Translation::factory()->create(['key' => 'contact.send', 'locale' => 'en', 'group' => 'contact', 'value' => 'Send →']);

    app(TranslationCompiler::class)->compile('en');

    $raw = File::get(lang_path('en.json'));

    expect(json_decode($raw))->not->toBeNull();
});

it('outputs an empty object when no translations exist for a locale', function () {
    app(TranslationCompiler::class)->compile('en');

    $en = json_decode(File::get(lang_path('en.json')), true);

    expect($en)->toBeArray()->toBeEmpty();
});

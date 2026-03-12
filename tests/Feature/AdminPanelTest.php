<?php

use App\Filament\Resources\ContactMessageResource;
use App\Filament\Resources\ContactMessageResource\Pages\ListContactMessages;
use App\Filament\Resources\SettingResource\Pages\CreateSetting;
use App\Filament\Resources\SettingResource\Pages\ListSettings;
use App\Filament\Resources\TranslationResource\Pages\ListTranslations;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\Translation;
use App\Models\User;
use Livewire\Livewire;

// ── Authentication ────────────────────────────────────────────────────────────

it('shows the admin login page', function () {
    $this->get('/admin/login')->assertSuccessful();
});

it('redirects guests to the admin login page', function (string $path) {
    $this->get($path)->assertRedirect('/admin/login');
})->with([
    '/admin',
    '/admin/settings',
    '/admin/translations',
    '/admin/contact-messages',
]);

it('lets an authenticated user access the admin dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertSuccessful();
});

it('logs a user out of the admin panel', function () {
    $this->actingAs(User::factory()->create())
        ->post('/admin/logout')
        ->assertRedirect();

    $this->assertGuest();
});

// ── Settings resource ─────────────────────────────────────────────────────────

it('lists settings in the admin panel', function () {
    Setting::factory()->create(['key' => 'github_url', 'value' => 'https://github.com', 'group' => 'social']);

    Livewire::actingAs(User::factory()->create())
        ->test(ListSettings::class)
        ->assertSuccessful()
        ->assertSee('github_url');
});

it('creates a new setting through the admin form', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(CreateSetting::class)
        ->fillForm([
            'group' => 'general',
            'key' => 'site_title',
            'value' => 'My Portfolio',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Setting::query()->where('key', 'site_title')->value('value'))->toBe('My Portfolio');
});

it('validates required fields when creating a setting', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(CreateSetting::class)
        ->fillForm(['key' => '', 'group' => ''])
        ->call('create')
        ->assertHasFormErrors(['key', 'group']);
});

// ── Translations resource ─────────────────────────────────────────────────────

it('lists translations in the admin panel', function () {
    Translation::factory()->create([
        'key' => 'hero.tagline',
        'locale' => 'en',
        'group' => 'hero',
        'value' => 'Software that works.',
    ]);

    Livewire::actingAs(User::factory()->create())
        ->test(ListTranslations::class)
        ->assertSuccessful()
        ->assertSee('hero.tagline');
});

// ── Contact messages resource ─────────────────────────────────────────────────

it('lists contact messages in the admin panel', function () {
    ContactMessage::factory()->create(['name' => 'Alice', 'email' => 'alice@example.com']);

    Livewire::actingAs(User::factory()->create())
        ->test(ListContactMessages::class)
        ->assertSuccessful()
        ->assertSee('Alice');
});

it('shows only the unread count as a navigation badge', function () {
    ContactMessage::factory()->count(3)->create(['is_read' => false]);
    ContactMessage::factory()->read()->count(2)->create();

    expect(ContactMessageResource::getNavigationBadge())->toBe('3');
});

it('returns null badge when there are no unread messages', function () {
    ContactMessage::factory()->read()->count(2)->create();

    expect(ContactMessageResource::getNavigationBadge())->toBeNull();
});

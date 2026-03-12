<?php

// ── Locale switching ──────────────────────────────────────────────────────────

it('switches the locale to Czech', function () {
    $this->post('/locale/cs')
        ->assertRedirect();

    expect(session('locale'))->toBe('cs');
});

it('switches the locale to English', function () {
    $this->withSession(['locale' => 'cs'])
        ->post('/locale/en')
        ->assertRedirect();

    expect(session('locale'))->toBe('en');
});

it('rejects an unsupported locale', function () {
    $this->post('/locale/fr')->assertStatus(400);
});

it('sets locale_switched flag in session after switching', function () {
    $this->post('/locale/cs')
        ->assertSessionHas('locale_switched', true);
});

// ── Homepage ──────────────────────────────────────────────────────────────────

it('renders the homepage', function () {
    $this->get('/')->assertSuccessful();
});

<?php

use App\Mail\ContactNotification;
use App\Models\ContactMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
});

// ── Successful submission ─────────────────────────────────────────────────────

it('stores a contact message and queues a notification email', function () {
    Setting::factory()->create(['key' => 'contact_email', 'value' => 'hello@example.com', 'group' => 'contact']);

    $this->postJson('/contact', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'subject' => 'Project inquiry',
        'message' => 'I would love to work with you.',
    ])->assertSuccessful();

    expect(ContactMessage::query()->where('email', 'jane@example.com')->exists())->toBeTrue();

    Mail::assertQueued(ContactNotification::class, fn ($mail) => $mail->hasTo('hello@example.com'));
});

it('stores a contact message without a subject', function () {
    $this->postJson('/contact', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'message' => 'No subject here.',
    ])->assertSuccessful();

    expect(ContactMessage::query()->where('name', 'Jane Doe')->first())
        ->subject->toBeNull();
});

it('marks new messages as unread by default', function () {
    $this->postJson('/contact', [
        'name' => 'Bob',
        'email' => 'bob@example.com',
        'message' => 'Hello!',
    ])->assertSuccessful();

    expect(ContactMessage::query()->first()->is_read)->toBeFalse();
});

it('falls back to the default email when contact_email setting is missing', function () {
    $this->postJson('/contact', [
        'name' => 'Alice',
        'email' => 'alice@example.com',
        'message' => 'Hi there.',
    ])->assertSuccessful();

    Mail::assertQueued(ContactNotification::class, fn ($mail) => $mail->hasTo('hello@robinhrdlicka.dev'));
});

// ── Validation ────────────────────────────────────────────────────────────────

it('rejects a submission with missing required fields', function (array $payload, string $field) {
    $this->postJson('/contact', $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors($field);
})->with([
    'missing name' => [['email' => 'a@b.com', 'message' => 'Hi'], 'name'],
    'missing email' => [['name' => 'Alice', 'message' => 'Hi'], 'email'],
    'missing message' => [['name' => 'Alice', 'email' => 'a@b.com'], 'message'],
    'invalid email' => [['name' => 'Alice', 'email' => 'not-an-email', 'message' => 'Hi'], 'email'],
]);

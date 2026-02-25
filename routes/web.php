<?php

use App\Http\Controllers\ContactController;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $settings = Setting::pluck('value', 'key')->toArray();

    return view('welcome', compact('settings'));
})->name('home');

Route::get('/bg', function () {
    return view('bg');
});

Route::post('/locale/{locale}', function (Request $request, string $locale) {
    if (! in_array($locale, ['en', 'cs'])) {
        abort(400);
    }

    $request->session()->put('locale', $locale);

    return redirect()->back()->with('locale_switched', true);
})->name('locale.switch');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

require __DIR__.'/auth.php';

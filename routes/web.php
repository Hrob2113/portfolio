<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

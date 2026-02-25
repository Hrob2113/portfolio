<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function edit()
    {
        $settings = Setting::query()->orderBy('group')->orderBy('key')->get()->groupBy('group');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request)
    {
        foreach ($request->validated('settings') as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Settings updated.');
    }
}

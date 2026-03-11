<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingsRequest;
use App\Models\Setting;

class SettingsController extends Controller
{
    private const DEFAULTS = [
        ['key' => 'github_url',    'value' => '',  'group' => 'social'],
        ['key' => 'linkedin_url',  'value' => '',  'group' => 'social'],
        ['key' => 'dribbble_url',  'value' => '',  'group' => 'social'],
        ['key' => 'twitter_url',   'value' => '',  'group' => 'social'],
        ['key' => 'contact_email', 'value' => '',  'group' => 'contact'],
    ];

    public function edit()
    {
        foreach (self::DEFAULTS as $default) {
            Setting::query()->firstOrCreate(
                ['key' => $default['key']],
                ['value' => $default['value'], 'group' => $default['group']],
            );
        }

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

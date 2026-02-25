<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'github_url', 'value' => 'https://github.com', 'group' => 'social'],
            ['key' => 'linkedin_url', 'value' => 'https://linkedin.com', 'group' => 'social'],
            ['key' => 'dribbble_url', 'value' => 'https://dribbble.com', 'group' => 'social'],
            ['key' => 'twitter_url', 'value' => 'https://x.com', 'group' => 'social'],
            ['key' => 'contact_email', 'value' => 'hello@robinhrdlicka.dev', 'group' => 'contact'],
        ];

        foreach ($settings as $setting) {
            Setting::query()->updateOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }
    }
}

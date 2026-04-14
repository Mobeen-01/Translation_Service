<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        $locales = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'fr', 'name' => 'French'],
            ['code' => 'es', 'name' => 'Spanish'],
        ];

        foreach ($locales as $locale) {
            Locale::firstOrCreate(
                ['code' => $locale['code']],
                ['name' => $locale['name'], 'is_active' => true]
            );
        }

        foreach (['mobile', 'desktop', 'web'] as $tag) {
            Tag::firstOrCreate(
                ['slug' => $tag],
                ['name' => ucfirst($tag)]
            );
        }

        // 👇 Add this line
        $this->call(TranslationSeeder::class);
    }
}
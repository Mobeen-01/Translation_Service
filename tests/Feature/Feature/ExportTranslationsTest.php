<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportTranslationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_returns_json(): void
    {
        $user = User::factory()->create();
        $locale = Locale::create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
        ]);

        $key = TranslationKey::create([
            'key' => 'auth.login.title',
        ]);

        Translation::create([
            'translation_key_id' => $key->id,
            'locale_id' => $locale->id,
            'value' => 'Login',
            'is_approved' => true,
        ]);

        $this->actingAs($user)
            ->getJson('/api/export/en')
            ->assertOk()
            ->assertJsonStructure(['locale', 'translations', 'timestamp']);
    }
}
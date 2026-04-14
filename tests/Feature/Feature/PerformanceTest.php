<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_endpoint_is_fast(): void
    {
        $user = User::factory()->create();

        $locale = Locale::create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
        ]);

        for ($i = 0; $i < 500; $i++) {
            $key = TranslationKey::create([
                'key' => 'group.key.' . $i,
            ]);

            Translation::create([
                'translation_key_id' => $key->id,
                'locale_id' => $locale->id,
                'value' => 'Value ' . $i,
                'is_approved' => true,
            ]);
        }

        $start = microtime(true);

        $response = $this->actingAs($user)
            ->getJson('/api/export/en')
            ->assertOk();

        $duration = (microtime(true) - $start) * 1000;

        $response->assertJsonStructure([
            'locale',
            'translations',
            'timestamp',
        ]);

        $this->assertLessThan(
            500,
            $duration,
            "Export endpoint took {$duration}ms, expected less than 500ms."
        );
    }

    public function test_search_endpoint_is_fast(): void
    {
        $user = User::factory()->create();

        $locale = Locale::create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
        ]);

        for ($i = 0; $i < 300; $i++) {
            $key = TranslationKey::create([
                'key' => 'search.key.' . $i,
            ]);

            Translation::create([
                'translation_key_id' => $key->id,
                'locale_id' => $locale->id,
                'value' => 'Search Value ' . $i,
                'is_approved' => true,
            ]);
        }

        $start = microtime(true);

        $response = $this->actingAs($user)
            ->postJson('/api/translations/search', [
                'search_type' => 'content',
                'query' => 'Search Value',
                'locale' => 'en',
                'page' => 1,
                'per_page' => 50,
            ])
            ->assertOk();

        $duration = (microtime(true) - $start) * 1000;

        $response->assertJsonStructure([
            'data',
            'meta' => [
                'total',
                'per_page',
                'current_page',
                'last_page',
            ],
        ]);

        $this->assertLessThan(
            200,
            $duration,
            "Search endpoint took {$duration}ms, expected less than 200ms."
        );
    }
}
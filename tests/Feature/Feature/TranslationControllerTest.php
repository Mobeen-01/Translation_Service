<?php

namespace Tests\Feature;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Locale $locale;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->locale = Locale::create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
        ]);

        Tag::create([
            'name' => 'Mobile',
            'slug' => 'mobile',
        ]);
    }

    public function test_create_translation(): void
    {
        $this->actingAs($this->user)->postJson('/api/translations', [
            'key' => 'welcome.title',
            'locale' => 'en',
            'value' => 'Welcome',
            'tags' => ['mobile'],
        ])->assertCreated()->assertJsonPath('key', 'welcome.title');
    }

    public function test_search_translation_by_key(): void
    {
        $translation = $this->makeTranslation();
        $translation->tags()->attach(Tag::first());

        $this->actingAs($this->user)->postJson('/api/translations/search', [
            'search_type' => 'key',
            'query' => 'sample',
        ])->assertOk()->assertJsonStructure(['data', 'meta']);
    }

    public function test_update_translation(): void
    {
        $translation = $this->makeTranslation();

        $this->actingAs($this->user)->patchJson('/api/translations/' . $translation->id, [
            'value' => 'Updated',
            'is_approved' => true,
        ])->assertOk()->assertJsonPath('value', 'Updated');
    }

    public function test_delete_translation(): void
    {
        $translation = $this->makeTranslation();

        $this->actingAs($this->user)
            ->deleteJson('/api/translations/' . $translation->id)
            ->assertNoContent();
    }

    public function test_approve_translation(): void
    {
        $translation = $this->makeTranslation();

        $this->actingAs($this->user)
            ->patchJson('/api/translations/' . $translation->id . '/approve')
            ->assertOk()
            ->assertJsonPath('is_approved', true);
    }

    private function makeTranslation(): Translation
    {
        $key = TranslationKey::create([
            'key' => 'sample.key.' . uniqid(),
        ]);

        return Translation::create([
            'translation_key_id' => $key->id,
            'locale_id' => $this->locale->id,
            'value' => 'Sample value',
            'is_approved' => false,
        ]);
    }
}
<?php

namespace Tests\Unit;

use App\Models\Locale;
use App\Services\TranslationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_translation(): void
    {
        Locale::create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
        ]);

        $service = new TranslationService();

        $translation = $service->create([
            'key' => 'unit.test',
            'locale' => 'en',
            'value' => 'Unit Test Value',
        ]);

        $this->assertEquals('Unit Test Value', $translation->value);
        $this->assertEquals('unit.test', $translation->translationKey->key);
    }

    public function test_export_returns_nested_json(): void
    {
        Locale::create([
            'code' => 'en',
            'name' => 'English',
            'is_active' => true,
        ]);

        $service = new TranslationService();

        $service->create([
            'key' => 'auth.login.title',
            'locale' => 'en',
            'value' => 'Login',
            'is_approved' => true,
        ]);

        $result = $service->export('en');

        $this->assertEquals('Login', data_get($result, 'auth.login.title'));
    }
}
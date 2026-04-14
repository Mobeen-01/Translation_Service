<?php

namespace Database\Seeders;

use App\Models\Locale;
use App\Models\TranslationKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TranslationSeeder extends Seeder
{
    public function run(): void
    {
        $targetTranslations = 100000;
        $locales = Locale::pluck('id')->toArray();
        $localeCount = count($locales);

        if ($localeCount === 0) {
            $this->command?->error('No locales found. Seed locales first.');
            return;
        }

        $keyCount = (int) ceil($targetTranslations / $localeCount);
        $now = now();

        $insertedKeys = 0;
        while ($insertedKeys < $keyCount) {
            $rows = [];
            $batchSize = min(1000, $keyCount - $insertedKeys);

            for ($i = 0; $i < $batchSize; $i++) {
                $number = $insertedKeys + $i + 1;

                $rows[] = [
                    'key' => "seed.key.$number",
                    'description' => "Generated key $number",
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('translation_keys')->insert($rows);
            $insertedKeys += $batchSize;
        }

        $keyIds = TranslationKey::where('key', 'like', 'seed.key.%')
            ->orderBy('id')
            ->pluck('id')
            ->toArray();

        $rows = [];
        $insertedTranslations = 0;

        foreach ($keyIds as $keyId) {
            foreach ($locales as $localeId) {
                if ($insertedTranslations >= $targetTranslations) {
                    break 2;
                }

                $rows[] = [
                    'translation_key_id' => $keyId,
                    'locale_id' => $localeId,
                    'value' => "Generated translation for key {$keyId} locale {$localeId}",
                    'is_approved' => (bool) random_int(0, 1),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $insertedTranslations++;

                if (count($rows) === 1000) {
                    DB::table('translations')->insert($rows);
                    $rows = [];
                }
            }
        }

        if (! empty($rows)) {
            DB::table('translations')->insert($rows);
        }

        $this->command?->info("Inserted {$insertedTranslations} translations.");
    }
}
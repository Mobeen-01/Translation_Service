<?php

namespace App\Console\Commands;

use App\Models\Locale;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PopulateTranslationsCommand extends Command
{
    protected $signature = 'translations:populate {count=100000}';
    protected $description = 'Populate database with 100k+ translations';

    public function handle(): int
    {
        $count = (int) $this->argument('count');
        $locales = Locale::all();

        if ($locales->isEmpty()) {
            $this->error('Please seed locales first.');
            return self::FAILURE;
        }

        $batchSize = 1000;
        $progressBar = $this->output->createProgressBar($count);

        for ($i = 0; $i < $count; $i += $batchSize) {
            $currentBatchSize = min($batchSize, $count - $i);

            $keys = [];
            for ($j = 0; $j < $currentBatchSize; $j++) {
                $keys[] = [
                    'key' => 'group.' . Str::random(10) . '.' . ($i + $j),
                    'description' => 'Auto generated key',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            TranslationKey::insert($keys);

            $insertedKeys = TranslationKey::latest('id')
                ->take($currentBatchSize)
                ->get();

            $translations = [];

            foreach ($insertedKeys as $translationKey) {
                $locale = $locales->random();

                $translations[] = [
                    'translation_key_id' => $translationKey->id,
                    'locale_id' => $locale->id,
                    'value' => 'Value for ' . $translationKey->key,
                    'is_approved' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Translation::insert($translations);
            $progressBar->advance($currentBatchSize);
        }

        $progressBar->finish();
        $this->newLine();
        $this->info('Translations populated successfully.');

        return self::SUCCESS;
    }
}
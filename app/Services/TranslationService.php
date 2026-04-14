<?php

namespace App\Services;

use App\Models\Locale;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class TranslationService
{
    public function create(array $data): Translation
    {
        return DB::transaction(function () use ($data) {
            $locale = Locale::where('code', $data['locale'])->firstOrFail();

            $translationKey = TranslationKey::firstOrCreate(
                ['key' => $data['key']],
                ['description' => $data['key_description'] ?? null]
            );

            $translation = Translation::updateOrCreate(
                [
                    'translation_key_id' => $translationKey->id,
                    'locale_id' => $locale->id,
                ],
                [
                    'value' => $data['value'],
                    'is_approved' => $data['is_approved'] ?? false,
                ]
            );

            if (! empty($data['tags'])) {
                $tagIds = Tag::whereIn('slug', $data['tags'])->pluck('id')->toArray();
                $translation->tags()->sync($tagIds);
            }

            return $translation->load(['translationKey', 'locale', 'tags']);
        });
    }

    public function update(Translation $translation, array $data): Translation
    {
        if (array_key_exists('value', $data)) {
            $translation->value = $data['value'];
        }

        if (array_key_exists('is_approved', $data)) {
            $translation->is_approved = $data['is_approved'];
        }

        $translation->save();

        if (array_key_exists('tags', $data)) {
            $tagIds = Tag::whereIn('slug', $data['tags'] ?? [])->pluck('id')->toArray();
            $translation->tags()->sync($tagIds);
        }

        return $translation->load(['translationKey', 'locale', 'tags']);
    }

    public function delete(Translation $translation): void
    {
        $translation->delete();
    }

    public function approve(Translation $translation): Translation
    {
        $translation->update([
            'is_approved' => true,
        ]);

        return $translation->load(['translationKey', 'locale', 'tags']);
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = Translation::query()
            ->with(['translationKey', 'locale', 'tags'])
            ->join('translation_keys', 'translations.translation_key_id', '=', 'translation_keys.id')
            ->join('locales', 'translations.locale_id', '=', 'locales.id')
            ->select('translations.*');

        if (! empty($filters['locale'])) {
            $query->where('locales.code', $filters['locale']);
        }

        if (! empty($filters['search_type']) && ! empty($filters['query'])) {
            if ($filters['search_type'] === 'key') {
                $query->where('translation_keys.key', 'like', '%' . $filters['query'] . '%');
            } elseif ($filters['search_type'] === 'content') {
                $query->where('translations.value', 'like', '%' . $filters['query'] . '%');
            } elseif ($filters['search_type'] === 'tag') {
                $query->whereHas('tags', function ($tagQuery) use ($filters) {
                    $tagQuery->where('slug', 'like', '%' . $filters['query'] . '%')
                        ->orWhere('name', 'like', '%' . $filters['query'] . '%');
                });
            }
        }

        $perPage = $filters['per_page'] ?? 20;

        return $query->latest('translations.id')->paginate($perPage);
    }

    public function bulkCreate(array $translations): array
    {
        $created = [];

        foreach ($translations as $translationData) {
            $created[] = $this->create($translationData);
        }

        return $created;
    }

    public function export(string $localeCode): array
    {
        $locale = Locale::where('code', $localeCode)->first();

        if (! $locale) {
            throw new ModelNotFoundException("Locale {$localeCode} not found.");
        }

        $translations = Translation::query()
            ->with('translationKey')
            ->where('locale_id', $locale->id)
            ->where('is_approved', true)
            ->get();

        $result = [];

        foreach ($translations as $translation) {
            if (! $translation->translationKey) {
                continue;
            }

            Arr::set($result, $translation->translationKey->key, $translation->value);
        }

        return $result;
    }
}
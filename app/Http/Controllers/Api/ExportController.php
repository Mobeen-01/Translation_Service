<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ExportController extends Controller
{
    public function __construct(
        private readonly TranslationService $translationService
    ) {
    }

    #[OA\Get(
        path: '/export/{locale}',
        operationId: 'exportTranslations',
        summary: 'Export translations by locale',
        security: [['bearerAuth' => []]],
        tags: ['Export'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                description: 'Locale code',
                schema: new OA\Schema(type: 'string', example: 'en')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Exported translations',
                content: new OA\JsonContent(ref: '#/components/schemas/ExportResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function export(string $locale): JsonResponse
    {
        return response()->json([
            'locale' => $locale,
            'translations' => $this->translationService->export($locale),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
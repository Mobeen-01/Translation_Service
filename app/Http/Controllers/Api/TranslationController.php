<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkCreateTranslationRequest;
use App\Http\Requests\CreateTranslationRequest;
use App\Http\Requests\SearchTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;
use App\Http\Resources\TranslationResource;
use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class TranslationController extends Controller
{
    public function __construct(
        private readonly TranslationService $translationService
    ) {
    }

    #[OA\Get(
        path: '/translations',
        operationId: 'listTranslations',
        summary: 'Get paginated translations',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Paginated translation list',
                content: new OA\JsonContent(ref: '#/components/schemas/TranslationListResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function index(): JsonResponse
    {
        $translations = Translation::with(['translationKey', 'locale', 'tags'])
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => TranslationResource::collection($translations),
            'meta' => [
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: '/translations',
        operationId: 'storeTranslation',
        summary: 'Create translation',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateTranslationRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created',
                content: new OA\JsonContent(ref: '#/components/schemas/Translation')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(CreateTranslationRequest $request): JsonResponse
    {
        $translation = $this->translationService->create($request->validated());

        return response()->json(
            new TranslationResource($translation),
            Response::HTTP_CREATED
        );
    }

    #[OA\Get(
        path: '/translations/{translation}',
        operationId: 'showTranslation',
        summary: 'Get one translation',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],
        parameters: [
            new OA\Parameter(
                name: 'translation',
                in: 'path',
                required: true,
                description: 'Translation ID',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Translation details',
                content: new OA\JsonContent(ref: '#/components/schemas/Translation')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function show(Translation $translation): JsonResponse
    {
        $translation->load(['translationKey', 'locale', 'tags']);

        return response()->json(new TranslationResource($translation));
    }

    #[OA\Patch(
        path: '/translations/{translation}',
        operationId: 'updateTranslation',
        summary: 'Update translation',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],
        parameters: [
            new OA\Parameter(
                name: 'translation',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateTranslationRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Translation')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function update(UpdateTranslationRequest $request, Translation $translation): JsonResponse
    {
        $translation = $this->translationService->update($translation, $request->validated());

        return response()->json(new TranslationResource($translation));
    }

    #[OA\Delete(
        path: '/translations/{translation}',
        operationId: 'deleteTranslation',
        summary: 'Delete translation',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],
        parameters: [
            new OA\Parameter(
                name: 'translation',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function destroy(Translation $translation): JsonResponse
    {
        $this->translationService->delete($translation);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    #[OA\Post(
        path: '/translations/search',
        operationId: 'searchTranslations',
        summary: 'Search translations',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/SearchTranslationRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Search results',
                content: new OA\JsonContent(ref: '#/components/schemas/TranslationListResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function search(SearchTranslationRequest $request): JsonResponse
    {
        $translations = $this->translationService->search($request->validated());

        return response()->json([
            'data' => TranslationResource::collection($translations->items()),
            'meta' => [
                'current_page' => $translations->currentPage(),
                'last_page' => $translations->lastPage(),
                'per_page' => $translations->perPage(),
                'total' => $translations->total(),
            ],
        ]);
    }

    #[OA\Post(
        path: '/translations/bulk',
        operationId: 'bulkCreateTranslations',
        summary: 'Bulk create translations',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],

        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/BulkTranslationsRequest',
                examples: [
                    new OA\Examples(
                        example: 'multiple_translations',
                        summary: 'Bulk create multiple translations',
                        value: [
                            'translations' => [
                                [
                                    'key' => 'home.title',
                                    'locale' => 'en',
                                    'value' => 'Home',
                                    'tags' => ['web']
                                ],
                                [
                                    'key' => 'home.title',
                                    'locale' => 'fr',
                                    'value' => 'Accueil',
                                    'tags' => ['web']
                                ],
                                [
                                    'key' => 'auth.login.button',
                                    'locale' => 'en',
                                    'value' => 'Login',
                                    'tags' => ['web', 'mobile']
                                ]
                            ]
                        ]
                    )
                ]
            )
        ),

        responses: [
            new OA\Response(
                response: 201,
                description: 'Bulk created',
                content: new OA\JsonContent(ref: '#/components/schemas/BulkTranslationResponse')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function bulk(BulkCreateTranslationRequest $request): JsonResponse
    {
        $translations = $this->translationService->bulkCreate($request->validated()['translations']);

        return response()->json([
            'data' => TranslationResource::collection(collect($translations)),
        ], Response::HTTP_CREATED);
    }

    #[OA\Patch(
        path: '/translations/{translation}/approve',
        operationId: 'approveTranslation',
        summary: 'Approve translation',
        security: [['bearerAuth' => []]],
        tags: ['Translations'],
        parameters: [
            new OA\Parameter(
                name: 'translation',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Approved',
                content: new OA\JsonContent(ref: '#/components/schemas/Translation')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function approve(Translation $translation): JsonResponse
    {
        $translation = $this->translationService->approve($translation);

        return response()->json(new TranslationResource($translation));
    }
}
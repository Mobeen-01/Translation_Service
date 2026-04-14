<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLocaleRequest;
use App\Http\Requests\UpdateLocaleRequest;
use App\Models\Locale;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class LocaleController extends Controller
{
    #[OA\Get(
        path: '/locales',
        operationId: 'listLocales',
        summary: 'List all locales',
        security: [['bearerAuth' => []]],
        tags: ['Locales'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of locales',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/Locale')
                )
            ),
            new OA\Response(response: 401, description: 'Unauthenticated')
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(Locale::latest()->get());
    }

    #[OA\Post(
        path: '/locales',
        operationId: 'storeLocale',
        summary: 'Create locale',
        security: [['bearerAuth' => []]],
        tags: ['Locales'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/StoreLocaleRequest')
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Locale created',
                content: new OA\JsonContent(ref: '#/components/schemas/Locale')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function store(StoreLocaleRequest $request): JsonResponse
    {
        $locale = Locale::create($request->validated());

        return response()->json($locale, Response::HTTP_CREATED);
    }

    #[OA\Get(
        path: '/locales/{locale}',
        operationId: 'showLocale',
        summary: 'Get one locale',
        security: [['bearerAuth' => []]],
        tags: ['Locales'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                description: 'Locale ID',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Locale details',
                content: new OA\JsonContent(ref: '#/components/schemas/Locale')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function show(Locale $locale): JsonResponse
    {
        return response()->json($locale);
    }

    #[OA\Patch(
        path: '/locales/{locale}',
        operationId: 'updateLocale',
        summary: 'Update locale',
        security: [['bearerAuth' => []]],
        tags: ['Locales'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                description: 'Locale ID',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateLocaleRequest')
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Locale updated',
                content: new OA\JsonContent(ref: '#/components/schemas/Locale')
            ),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error')
        ]
    )]
    public function update(UpdateLocaleRequest $request, Locale $locale): JsonResponse
    {
        $locale->update($request->validated());

        return response()->json($locale);
    }

    #[OA\Delete(
        path: '/locales/{locale}',
        operationId: 'deleteLocale',
        summary: 'Delete locale',
        security: [['bearerAuth' => []]],
        tags: ['Locales'],
        parameters: [
            new OA\Parameter(
                name: 'locale',
                in: 'path',
                required: true,
                description: 'Locale ID',
                schema: new OA\Schema(type: 'integer', example: 1)
            )
        ],
        responses: [
            new OA\Response(response: 204, description: 'Deleted'),
            new OA\Response(response: 401, description: 'Unauthenticated'),
            new OA\Response(response: 404, description: 'Not found')
        ]
    )]
    public function destroy(Locale $locale): JsonResponse
    {
        $locale->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
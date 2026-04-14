<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Translation Management API',
    description: 'API documentation for authentication, translations, locales, approval, bulk operations, and export.'
)]
#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'Local API Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Use the Sanctum API token returned by /login. Example: Bearer 1|xxxx'
)]
#[OA\Tag(name: 'Auth', description: 'Authentication endpoints')]
#[OA\Tag(name: 'Translations', description: 'Translation CRUD and workflows')]
#[OA\Tag(name: 'Locales', description: 'Locale management endpoints')]
#[OA\Tag(name: 'Export', description: 'Export translations by locale')]
class OpenApiSpec
{
}
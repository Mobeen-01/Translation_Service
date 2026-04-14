<?php

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'LoginRequest',
    required: ['email', 'password'],
    properties: [
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'admin@example.com'),
        new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'LoginSuccessResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Login successful.'),
        new OA\Property(property: 'token', type: 'string', example: '1|long-sanctum-token'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'MessageResponse',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Logout successful.'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'TagResource',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Web'),
        new OA\Property(property: 'slug', type: 'string', example: 'web'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Web context'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'Translation',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'key', type: 'string', example: 'auth.login.title'),
        new OA\Property(property: 'locale', type: 'string', example: 'en'),
        new OA\Property(property: 'value', type: 'string', example: 'Login'),
        new OA\Property(property: 'is_approved', type: 'boolean', example: true),
        new OA\Property(
            property: 'tags',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/TagResource')
        ),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'TranslationListResponse',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Translation')
        ),
        new OA\Property(
            property: 'meta',
            type: 'object',
            properties: [
                new OA\Property(property: 'current_page', type: 'integer', example: 1),
                new OA\Property(property: 'last_page', type: 'integer', example: 10),
                new OA\Property(property: 'per_page', type: 'integer', example: 50),
                new OA\Property(property: 'total', type: 'integer', example: 500),
            ]
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'CreateTranslationRequest',
    required: ['key', 'locale', 'value'],
    properties: [
        new OA\Property(property: 'key', type: 'string', example: 'auth.login.title'),
        new OA\Property(property: 'locale', type: 'string', example: 'en'),
        new OA\Property(property: 'value', type: 'string', example: 'Login'),
        new OA\Property(
            property: 'tags',
            type: 'array',
            items: new OA\Items(type: 'string'),
            example: ['web', 'mobile']
        ),
        new OA\Property(property: 'key_description', type: 'string', example: 'Login page title'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UpdateTranslationRequest',
    required: ['value'],
    properties: [
        new OA\Property(property: 'value', type: 'string', example: 'Sign In'),
        new OA\Property(
            property: 'tags',
            type: 'array',
            items: new OA\Items(type: 'string'),
            example: ['web']
        ),
        new OA\Property(property: 'is_approved', type: 'boolean', example: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'SearchTranslationRequest',
    required: ['search_type', 'query'],
    properties: [
        new OA\Property(property: 'search_type', type: 'string', enum: ['key', 'content', 'tag'], example: 'key'),
        new OA\Property(property: 'query', type: 'string', example: 'auth.login'),
        new OA\Property(property: 'locale', type: 'string', example: 'en'),
        new OA\Property(property: 'page', type: 'integer', example: 1),
        new OA\Property(property: 'per_page', type: 'integer', example: 20),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'BulkTranslationsRequest',
    required: ['translations'],
    properties: [
        new OA\Property(
            property: 'translations',
            type: 'array',
            items: new OA\Items(
                type: 'object',
                required: ['key', 'locale', 'value'],
                properties: [
                    new OA\Property(property: 'key', type: 'string', example: 'home.title'),
                    new OA\Property(property: 'locale', type: 'string', example: 'en'),
                    new OA\Property(property: 'value', type: 'string', example: 'Home'),
                    new OA\Property(
                        property: 'tags',
                        type: 'array',
                        items: new OA\Items(type: 'string'),
                        example: ['web']
                    ),
                    new OA\Property(property: 'key_description', type: 'string', example: 'Homepage title'),
                ]
            )
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'BulkTranslationResponse',
    properties: [
        new OA\Property(
            property: 'data',
            type: 'array',
            items: new OA\Items(ref: '#/components/schemas/Translation')
        ),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'ExportResponse',
    properties: [
        new OA\Property(property: 'locale', type: 'string', example: 'en'),
        new OA\Property(property: 'translations', type: 'object', additionalProperties: true),
        new OA\Property(property: 'timestamp', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]

#[OA\Schema(
    schema: 'Locale',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'code', type: 'string', example: 'en'),
        new OA\Property(property: 'name', type: 'string', example: 'English'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time'),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'StoreLocaleRequest',
    required: ['code', 'name'],
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'fr'),
        new OA\Property(property: 'name', type: 'string', example: 'French'),
        new OA\Property(property: 'is_active', type: 'boolean', example: true),
    ],
    type: 'object'
)]
#[OA\Schema(
    schema: 'UpdateLocaleRequest',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'French'),
        new OA\Property(property: 'is_active', type: 'boolean', example: false),
    ],
    type: 'object'
)]


class Schemas
{
}
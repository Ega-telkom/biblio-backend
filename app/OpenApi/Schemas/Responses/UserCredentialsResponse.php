<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UserCredentialsResponse",
    properties: [
        new OA\Property(
            property: "user",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "name", type: "string", example: "user"),
                new OA\Property(property: "email", type: "string", example: "user@somewhere.com"),
                new OA\Property(property: "email_verified_at", type: "string", nullable: true),
                new OA\Property(property: "created_at", type: "string", example: "2026-05-22T02:03:07.000000Z"),
                new OA\Property(property: "updated_at", type: "string", example: "2026-05-22T02:03:07.000000Z"),
                new OA\Property(property: "role", type: "string", example: "user"),
                new OA\Property(property: "avatar_url", type: "string", nullable: true),
                new OA\Property(property: "avatar", type: "string", nullable: true),
            ],
            type: "object"
        ),
        new OA\Property(property: "token", type: "string", example: "XX|XXXXX"),
    ]
)]
class UserCredentialsResponse {}
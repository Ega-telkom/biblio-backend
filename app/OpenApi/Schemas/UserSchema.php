<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "User",
    properties: [
        new OA\Property(property: "id", type: "integer"),
        new OA\Property(property: "name", type: "string"),
        new OA\Property(property: "email", type: "string", format: "email"),
        new OA\Property(property: "email_verified_at", type: "string", format: "date-time", nullable: true),
        new OA\Property(property: "created_at", type: "string", format: "date-time"),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
        new OA\Property(property: "role", type: "string", enum: ["user", "admin"]),
        new OA\Property(property: "avatar_url", type: "string", nullable: true),
        new OA\Property(property: "subscribed_until", type: "string", format: "date-time", nullable: true),
        new OA\Property(property: "avatar", type: "string", nullable: true),
        new OA\Property(property: "is_subscribed", type: "boolean"),
    ]
)]
class UserSchema {}
<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpsertProgressRequest",
    required: ["book_id", "last_page"],
    properties: [
        new OA\Property(property: "book_id", type: "string", format: "uuid", example: "550e8400-e29b-41d4-a716-446655440000"),
        new OA\Property(property: "last_page", type: "integer", example: 42),
    ]
)]
class UpsertProgressRequest {}
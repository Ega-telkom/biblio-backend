<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Progress",
    properties: [
        new OA\Property(property: "last_page", type: "integer", example: 42),
        new OA\Property(property: "updated_at", type: "string", format: "date-time"),
        new OA\Property(property: "book", ref: "#/components/schemas/Book"),
    ]
)]
class ProgressSchema {}
<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BookPaginatedResponse",
    properties: [
        new OA\Property(property: "current_page", type: "integer", example: 1),
        new OA\Property(property: "data", type: "array", items: new OA\Items(ref: "#/components/schemas/Book")),
        new OA\Property(property: "first_page_url", type: "string"),
        new OA\Property(property: "from", type: "integer"),
        new OA\Property(property: "last_page", type: "integer"),
        new OA\Property(property: "last_page_url", type: "string"),
        new OA\Property(property: "next_page_url", type: "string", nullable: true),
        new OA\Property(property: "path", type: "string"),
        new OA\Property(property: "per_page", type: "integer"),
        new OA\Property(property: "prev_page_url", type: "string", nullable: true),
        new OA\Property(property: "to", type: "integer"),
        new OA\Property(property: "total", type: "integer"),
    ]
)]
class BookPaginatedResponse {}
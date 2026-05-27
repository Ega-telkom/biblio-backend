<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "GenreWithBooksResponse",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Biologi"),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),
        new OA\Property(property: "books_count", type: "integer", example: 1),
        new OA\Property(
            property: "books",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Book")
        ),
    ]
)]
class GenreWithBooksResponse {}
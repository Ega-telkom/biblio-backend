<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Genre",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Genre Keren"),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),
        new OA\Property(property: "books_count", type: "integer", example: 1),
    ]
)]
class GenreSchema {}
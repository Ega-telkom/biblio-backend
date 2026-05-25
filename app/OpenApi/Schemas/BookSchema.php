<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Book",
    properties: [
        new OA\Property(property: "id", type: "string", example: "8fd93ee3-3815-4334-9218-7ebc50e1343f"),
        new OA\Property(property: "genre_id", type: "integer", example: 1),
        new OA\Property(property: "title", type: "string", example: "Judul Buku"),
        new OA\Property(property: "isbn", type: "string", example: "123ABC"),
        new OA\Property(property: "description", type: "string", nullable: true),
        new OA\Property(property: "author", type: "string", example: "Seseorang"),
        new OA\Property(property: "publisher", type: "string", nullable: true),
        new OA\Property(property: "lang", type: "string", example: "id"),
        new OA\Property(property: "published_date", type: "string", nullable: true),
        new OA\Property(property: "format", type: "string", example: "pdf"),
        new OA\Property(property: "page_count", type: "integer", nullable: true),
        new OA\Property(property: "price", type: "number", example: 1234),
        new OA\Property(property: "cover_url", type: "string", nullable: true),
        new OA\Property(property: "cover_sm", type: "string", nullable: true),
        new OA\Property(property: "cover_md", type: "string", nullable: true),
        new OA\Property(property: "cover_lg", type: "string", nullable: true),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),
        new OA\Property(property: "genre", ref: "#/components/schemas/Genre"),
    ]
)]
class BookSchema {}
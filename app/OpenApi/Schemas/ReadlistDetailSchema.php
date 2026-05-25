<?php

namespace App\OpenApi\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReadlistDetail",
    properties: [
        new OA\Property(property: "id", type: "string", example: "bbaab57d-69c9-49c8-8652-a930b5ee8fa0"),
        new OA\Property(property: "name", type: "string", example: "Buku Favorit"),
        new OA\Property(property: "description", type: "string", nullable: true),
        new OA\Property(property: "user_id", type: "integer", example: 4),
        new OA\Property(property: "created_at", type: "string"),
        new OA\Property(property: "updated_at", type: "string"),
        new OA\Property(
            property: "books",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Book")
        ),
    ]
)]
class ReadlistDetailSchema {}
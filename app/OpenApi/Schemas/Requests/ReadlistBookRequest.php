<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReadlistBookRequest",
    required: ["book_id"],
    properties: [
        new OA\Property(property: "book_id", type: "string", format: "uuid"),
    ]
)]
class ReadlistBookRequest {}
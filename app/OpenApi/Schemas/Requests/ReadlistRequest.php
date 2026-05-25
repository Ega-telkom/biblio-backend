<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ReadlistRequest",
    required: ["name"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "Buku Favorit"),
        new OA\Property(property: "description", type: "string", nullable: true),
    ]
)]
class ReadlistRequest {}
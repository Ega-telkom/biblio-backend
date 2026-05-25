<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UpdateReadlistRequest",
    properties: [
        new OA\Property(property: "name", type: "string", example: "Buku Favorit"),
        new OA\Property(property: "description", type: "string", nullable: true),
    ]
)]
class UpdateReadlistRequest {}
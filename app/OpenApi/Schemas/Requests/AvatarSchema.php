<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AvatarRequest",
    required: ["avatar"],
    properties: [
        new OA\Property(property: "avatar", type: "string", format: "binary"),
    ]
)]
class AvatarSchema {}
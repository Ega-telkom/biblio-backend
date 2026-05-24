<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "AvatarResponse",
    properties: [
        new OA\Property(property: "avatar", type: "string"),
    ]
)]
class AvatarResponse {}
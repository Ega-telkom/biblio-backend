<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "UserCredentialsResponse",
    properties: [
        new OA\Property(property: "user", ref: "#/components/schemas/User"),
        new OA\Property(property: "token", type: "string", example: "XX|XXXXX"),
    ]
)]
class UserCredentialsResponse {}
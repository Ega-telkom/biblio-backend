<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ApiMessageResponse",
    properties: [
        new OA\Property(property: "message", type: "string"),
    ]
)]
class ApiMessageResponse {}
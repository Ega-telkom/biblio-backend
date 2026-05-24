<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "FirebaseLoginRequest",
    required: ["token"],
    properties: [
        new OA\Property(property: "token", type: "string", example: "firebase-id-token"),
    ]
)]
class FirebaseLoginSchema {}
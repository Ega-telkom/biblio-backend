<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "FirebaseLogin",
    required: ["token"],
    properties: [
        new OA\Property(property: "token", type: "string", example: "firebase-id-token"),
        new OA\Property(property: "display_name", type: "string", nullable: true),
    ]
)]
class FirebaseLoginSchema {}
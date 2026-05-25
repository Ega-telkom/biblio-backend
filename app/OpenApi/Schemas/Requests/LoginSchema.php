<?php

namespace App\OpenApi\Schemas\Requests;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Login",
    required: ["email", "password"],
    properties: [
        new OA\Property(property: "email", type: "string", example: "admin@biblio.com"),
        new OA\Property(property: "password", type: "string", example: "password"),
    ]
)]
class LoginSchema {}
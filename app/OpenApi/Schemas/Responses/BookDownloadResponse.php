<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BookDownloadResponse",
    properties: [
        new OA\Property(property: "url", type: "string"),
    ]
)]
class BookDownloadResponse {}
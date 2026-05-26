<?php

namespace App\OpenApi\Schemas\Responses;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "PaymentSubscribeResponse",
    properties: [
        new OA\Property(property: "snap_token", type: "string"),
        new OA\Property(property: "order_id", type: "string"),
    ]
)]
class PaymentSubscribeResponse {}
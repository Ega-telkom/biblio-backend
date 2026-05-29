<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\OpenApi(
    info: new OA\Info(
        title: "Biblio API",
        version: "1.0.0",
        description: "API untuk Biblio. \n- Repositori: [https://github.com/Ega-telkom/biblio-backend](https://github.com/Ega-telkom/biblio-backend) 
        
        \n ~ Dari kelompok 3
        "
    ),
    tags: [
        new OA\Tag(name: "Auth", description: "Autentikasi via Firebase"),
        new OA\Tag(name: "Profile", description: "Profil user yang sedang login"),
        new OA\Tag(name: "Books", description: "CRUD dan download buku"),
        new OA\Tag(name: "Genres", description: "List dan detail genre"),
        new OA\Tag(name: "Progress", description: "Progress baca user"),
        new OA\Tag(name: "Readlists", description: "Koleksi buku (mirip kaya )"),
        new OA\Tag(name: "Payment", description: "Langganan via Midtrans"),
    ]
)]
#[OA\SecurityScheme(securityScheme: "sanctum", type: "http", scheme: "bearer")]
class OpenApiSpec {}
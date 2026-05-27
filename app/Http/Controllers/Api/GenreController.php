<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class GenreController extends Controller
{
    # ---
    #[OA\Get(
        path: "/genres",
        operationId: "getGenres",
        summary: "List semua genre",
        security: [["sanctum" => []]],
        tags: ["Genres"],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/Genre"))
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function index()
    {
        $genres = Genre::withCount('books')->orderBy('name')->get();
        return response()->json($genres);
    }

    # ---
    #[OA\Get(
        path: "/genres/{id}",
        operationId: "getGenre",
        summary: "Detail genre",
        security: [["sanctum" => []]],
        tags: ["Genres"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/Genre")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 404, 
                description: "Not Found",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function show(Genre $genre)
    {
        return response()->json($genre->loadCount('books'));
    }

    # ---
    #[OA\Get(
        path: "/genres/with-books",
        operationId: "getGenreWithBooks",
        summary: "List genre beserta preview buku (untuk beranda)",
        security: [["sanctum" => []]],
        tags: ["Genres"],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(type: "array", items: new OA\Items(ref: "#/components/schemas/GenreWithBooksResponse"))
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function withBooks()
    {
        $genres = cache()->remember('genres_with_books', 300, function () {
            $genres = Genre::withCount('books')
                ->with(['books' => fn($q) => $q->limit(10)])
                ->orderByDesc('books_count')
                ->limit(10)
                ->get()
                ->filter(fn ($genre) => $genre->books_count > 0)
                ->values();
            
            $genres->each(function ($genre) {
                $genre->books->each(function ($book) {
                    $book->makeVisible(['cover_sm', 'cover_md', 'cover_lg']);
                });
            });
            
            return $genres->toArray();
        });
        
        return response()->json($genres);
    }
}


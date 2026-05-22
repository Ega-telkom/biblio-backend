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
    summary: "List semua genre",
    security: [["sanctum" => []]],
    tags: ["Genres"],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function index()
    {
        $genre = Genre::all();
        return response()->json([
        'status' => true,
        'message' => 'genre_index',
        'data' => $genre
        ], 200);
    }

    # ---
    #[OA\Post(
    path: "/genres",
    summary: "Tambah genre (admin)",
    security: [["sanctum" => []]],
    tags: ["Genres"],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
    required: ["name"],
    properties: [new OA\Property(property: "name", type: "string", example: "Sains")]
    )),
    responses: [
    new OA\Response(response: 201, description: "Genre dibuat"),
    new OA\Response(response: 422, description: "Validasi gagal"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name' => 'required|unique:genres,name'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $genre = Genre::create([
        'name' => $request->name
        ]);

        return response()->json([
        'status' => true,
        'message' => 'genre_created',
        'data' => $genre
        ], 201);
    }

    # ---
    #[OA\Get(
    path: "/genres/{id}",
    summary: "Detail genre",
    security: [["sanctum" => []]],
    tags: ["Genres"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 404, description: "Not found"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function show($id)
    {
        $genre = Genre::find($id);
        if (!$genre) return response()->json(['message' => 'genre_notfound'], 404);

        return response()->json(['data' => $genre], 200);
    }

    # ---
    #[OA\Put(
    path: "/genres/{id}",
    summary: "Update genre (admin)",
    security: [["sanctum" => []]],
    tags: ["Genres"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    requestBody: new OA\RequestBody(content: new OA\JsonContent(
    properties: [new OA\Property(property: "name", type: "string")]
    )),
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 404, description: "Not found"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function update(Request $request, $id)
    {
        $genre = Genre::find($id);
        if (!$genre) return response()->json(['message' => 'genre_notfound'], 404);

        $genre->update(['name' => $request->name]);

        return response()->json(['message' => 'genre_updated', 'data' => $genre], 200);
    }

    # ---
    #[OA\Delete(
    path: "/genres/{id}",
    summary: "Hapus genre (admin)",
    security: [["sanctum" => []]],
    tags: ["Genres"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))],
    responses: [
    new OA\Response(response: 200, description: "Deleted"),
    new OA\Response(response: 404, description: "Not found"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function destroy($id)
    {
        $genre = Genre::find($id);
        if (!$genre) return response()->json(['message' => 'genre_notfound'], 404);

        $genre->delete();
        return response()->json(['message' => 'genre_deleted'], 200);
    }
    
    # ---
    #[OA\Get(
    path: "/genres/with-books",
    summary: "List genre beserta preview buku (untuk beranda)",
    security: [["sanctum" => []]],
    tags: ["Genres"],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function withBooks()
    {
        $genres = cache()->remember('genres_with_books', 300, function () {
            return Genre::with(['books' => fn($q) => $q->limit(10)])->get();
        });
    
        $genres->each(function ($genre) {
            $genre->books->each(function ($book) {
                $book->cover_url = $book->cover_url
                    ? Storage::disk('covers')->url($book->cover_url)
                    : null;
            });
        });
    
        return response()->json($genres);
    }
}


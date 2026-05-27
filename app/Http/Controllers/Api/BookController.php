<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use OpenApi\Attributes as OA;

class BookController extends Controller
{
    # ---
    #[OA\Get(
        path: "/books",
        operationId: "getBooks",
        summary: "List semua buku (paginated)",
        security: [["sanctum" => []]],
        tags: ["Books"],
        parameters: [
            new OA\Parameter(name: "page", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "search", in: "query", schema: new OA\Schema(type: "string")),
            new OA\Parameter(name: "genre_id", in: "query", schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "format", in: "query", schema: new OA\Schema(type: "string", enum: ["pdf", "epub", "mobi", "djvu"])),
            new OA\Parameter(name: "sort", in: "query", schema: new OA\Schema(type: "string", enum: ["latest", "oldest", "price_asc", "price_desc"])),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/BookPaginatedResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function index(Request $request)
    {
        $books = Book::with('genre')
            ->when($request->search, fn ($q) =>
                $q->where(fn ($q) =>
                    $q->whereRaw("title ILIKE ?", ["%{$request->search}%"])
                    ->orWhereRaw("author ILIKE ?", ["%{$request->search}%"])
                    ->orWhereRaw("similarity(title, ?) > 0.15", [$request->search])
                    ->orWhereRaw("similarity(author, ?) > 0.15", [$request->search])
                )
            )
            ->when($request->genre_id, fn ($q) => $q->where('genre_id', $request->genre_id))
            ->when($request->format, fn ($q) => $q->where('format', $request->format))
            ->when($request->sort, function ($q) use ($request) {
                match ($request->sort) {
                    'latest'     => $q->latest(),
                    'oldest'     => $q->oldest(),
                    'price_asc'  => $q->orderBy('price'),
                    'price_desc' => $q->orderByDesc('price'),
                    default      => $q->latest(),
                };
            }, fn ($q) => $q->latest())
            ->paginate(15)
            ->withQueryString();
    
        return response()->json($books);
    }

    # ---
    #[OA\Get(
        path: "/books/{id}",
        operationId: "getBook",
        summary: "Detail buku",
        security: [["sanctum" => []]],
        tags: ["Books"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/Book")
            ),
            new OA\Response(response: 404, 
                description: "Not Found",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function show(string $id)
    {
        $book = Book::with('genre')->find($id);
    
        if (!$book) {
            return response()->json(['message' => 'not_found'], 404);
        }
    
        return response()->json($book);
    }

    # ---
    #[OA\Get(
        path: "/books/{id}/download",
        operationId: "downloadBook",
        summary: "Dapatkan temporary URL download file buku",
        security: [["sanctum" => []]],
        tags: ["Books"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/BookDownloadResponse")
            ),
            new OA\Response(response: 404, 
                description: "Not Found",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function download(Request $request, Book $book)
    {
        // Baca Saja (preview): hanya buku gratis (price = 0)
        if ($request->boolean('preview') && $book->price > 0) {
            return response()->json([
                'message' => 'Buku berbayar. Gunakan Beli & Baca untuk akses penuh.',
            ], 403);
        }

        if (!Storage::disk('s3')->exists($book->file_path)) {
            return response()->json(['message' => 'File not found'], 404);
        }
        
        $url = Storage::disk('s3_public')->temporaryUrl(
        $book->file_path,
        now()->addMinutes(5)
        );
        
        return response()->json(['url' => $url]);
    }
    
    # ---
    #[OA\Get(
        path: "/genres/{id}/books",
        operationId: "getBooksByGenre",
        summary: "List buku berdasarkan genre (paginated)",
        security: [["sanctum" => []]],
        tags: ["Books"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
            new OA\Parameter(name: "page", in: "query", schema: new OA\Schema(type: "integer")),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/BookPaginatedResponse")
            ),
            new OA\Response(response: 404, 
                description: "Not Found",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function byGenre(string $id)
    {
        $genre = Genre::find($id);
    
        if (!$genre) {
            return response()->json(['message' => 'not_found'], 404);
        }
    
        $books = Book::with('genre')->where('genre_id', $genre->id)->paginate(15);
        return response()->json($books);
    }
}

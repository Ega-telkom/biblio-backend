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
    summary: "List semua buku (paginated)",
    security: [["sanctum" => []]],
    tags: ["Books"],
    parameters: [new OA\Parameter(name: "page", in: "query", schema: new OA\Schema(type: "integer"))],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function index()
    {
        $books = Book::with('genre')->paginate(15);
        $books->through(function ($book) {
            $book->cover_url = $book->cover_url
            ? Storage::disk('covers')->url($book->cover_url)
            : null;
            return $book;
        });
        return response()->json($books);
    }

    # ---
    #[OA\Post(
    path: "/books",
    summary: "Upload buku baru (admin)",
    security: [["sanctum" => []]],
    tags: ["Books"],
    requestBody: new OA\RequestBody(required: true, content: new OA\MediaType(
    mediaType: "multipart/form-data",
    schema: new OA\Schema(
    required: ["genre_id", "title", "author", "lang", "format", "price", "file"],
    properties: [
    new OA\Property(property: "genre_id", type: "integer"),
    new OA\Property(property: "title", type: "string"),
    new OA\Property(property: "isbn", type: "string"),
    new OA\Property(property: "description", type: "string"),
    new OA\Property(property: "author", type: "string"),
    new OA\Property(property: "publisher", type: "string"),
    new OA\Property(property: "lang", type: "string", example: "id"),
    new OA\Property(property: "published_date", type: "string", format: "date"),
    new OA\Property(property: "format", type: "string", enum: ["pdf", "epub", "mobi", "djvu"]),
    new OA\Property(property: "page_count", type: "integer"),
    new OA\Property(property: "price", type: "integer"),
    new OA\Property(property: "file", type: "string", format: "binary"),
    new OA\Property(property: "cover", type: "string", format: "binary"),
    ]
    )
    )),
    responses: [
    new OA\Response(response: 201, description: "Buku dibuat"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function store(StoreBookRequest $request)
    {
        // Buat book dulu tanpa file
        $book = Book::create($request->except(['file', 'cover']));
        
        // Upload pakai UUID dari DB
        $filePath = Storage::disk('s3')->putFileAs(
        "books/{$book->id}",
        $request->file('file'),
        'file.' . $request->file('file')->getClientOriginalExtension()
        );
        
        $coverUrl = null;
        if ($request->hasFile('cover')) {
            $coverUrl = $this->uploadCover($request->file('cover'), $book->id);
        }
        
        $book->update([
        'file_path' => $filePath,
        'cover_url' => $coverUrl,
        ]);
        
        return response()->json($book, 201);
    }

    # ---
    #[OA\Get(
    path: "/books/{id}",
    summary: "Detail buku",
    security: [["sanctum" => []]],
    tags: ["Books"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 404, description: "Not found"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function show(Book $book)
    {
        $book->cover_url = $book->cover_url
        ? Storage::disk('covers')->url($book->cover_url)
        : null;
        return response()->json($book->load('genre'));
    }

    # ---
    #[OA\Put(
    path: "/books/{id}",
    summary: "Update buku (admin)",
    security: [["sanctum" => []]],
    tags: ["Books"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    requestBody: new OA\RequestBody(content: new OA\MediaType(
    mediaType: "multipart/form-data",
    schema: new OA\Schema(properties: [
    new OA\Property(property: "title", type: "string"),
    new OA\Property(property: "file", type: "string", format: "binary"),
    new OA\Property(property: "cover", type: "string", format: "binary"),
    ])
    )),
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function update(UpdateBookRequest $request, Book $book)
    {
        $filePath  = $book->file_path;
        $coverUrl  = $book->cover_url;
        
        if ($request->hasFile('file')) {
            Storage::disk('s3')->delete($filePath);
            $filePath = Storage::disk('s3')->putFileAs(
            "books/{$book->id}",
            $request->file('file'),
            'file.' . $request->file('file')->getClientOriginalExtension()
            );
        }
        
        if ($request->hasFile('cover')) {
            if ($book->cover_url) {
                Storage::disk('s3')->delete($book->cover_url);
            }
            $coverUrl = $this->uploadCover($request->file('cover'), $book->id);
        }
        
        $book->update([
        ...$request->except(['file', 'cover']),
        'file_path' => $filePath,
        'cover_url' => $coverUrl,
        ]);
        
        return response()->json($book);
    }

    # ---
    #[OA\Delete(
    path: "/books/{id}",
    summary: "Hapus buku (admin)",
    security: [["sanctum" => []]],
    tags: ["Books"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    responses: [
    new OA\Response(response: 204, description: "Deleted"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function destroy(Book $book)
    {
        Storage::disk('s3')->deleteDirectory("books/{$book->id}");
        $book->delete();
        
        return response()->json(null, 204);
    }

    # ---
    #[OA\Get(
    path: "/books/{id}/download",
    summary: "Dapatkan temporary URL download file buku",
    security: [["sanctum" => []]],
    tags: ["Books"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    responses: [
    new OA\Response(response: 200, description: "OK", content: new OA\JsonContent(
    properties: [new OA\Property(property: "url", type: "string")]
    )),
    new OA\Response(response: 404, description: "File not found"),
    new OA\Response(response: 401, description: "Unauthenticated"),
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
        
        $url = Storage::disk('s3')->temporaryUrl(
        $book->file_path,
        now()->addMinutes(5)
        );
        
        return response()->json(['url' => $url]);
    }
    
    private function uploadCover($file, string $bookId): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->decode($file->getPathname())
        ->scale(width: 400)
        ->encodeUsingFileExtension('jpg', quality: 75);
        
        $path = "{$bookId}/cover.jpg";
        Storage::disk('covers')->put($path, (string) $image);
        
        return $path;
    }
    
    public function cover(Book $book)
    {
        if (!$book->cover_url) {
            return response()->json(['message' => 'No cover'], 404);
        }
        
        $url = Storage::disk('s3')->temporaryUrl(
        $book->cover_url,
        now()->addMinutes(30)
        );
        
        return redirect($url);
    }
    
    # ---
    #[OA\Get(
    path: "/genres/{id}/books",
    summary: "List buku berdasarkan genre (paginated)",
    security: [["sanctum" => []]],
    tags: ["Books"],
    parameters: [
    new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer")),
    new OA\Parameter(name: "page", in: "query", schema: new OA\Schema(type: "integer")),
    ],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function byGenre(Genre $genre)
    {
        $books = $genre->books()->paginate(15);
        $books->through(function ($book) {
            $book->cover_url = $book->cover_url
            ? Storage::disk('covers')->url($book->cover_url)
            : null;
            return $book;
        });
        return response()->json($books);
    }
}

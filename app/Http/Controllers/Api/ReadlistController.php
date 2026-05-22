<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Readlist;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReadlistController extends Controller
{
    # ---
    #[OA\Get(
    path: "/readlists",
    summary: "List readlist milik user",
    security: [["sanctum" => []]],
    tags: ["Readlists"],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function index(Request $request)
    {
        $readlists = $request->user()->readlists()->with('books')->get();
        
        $readlists->each(function ($readlist) {
            $readlist->books->each(function ($book) {
                $book->cover_url = $book->cover_url
                    ? Storage::disk('covers')->url($book->cover_url)
                    : null;
            });
        });
        
        return response()->json($readlists);
    }

    # ---
    #[OA\Post(
    path: "/readlists",
    summary: "Buat readlist baru",
    security: [["sanctum" => []]],
    tags: ["Readlists"],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
    required: ["name"],
    properties: [
    new OA\Property(property: "name", type: "string", example: "Buku Favorit"),
    new OA\Property(property: "description", type: "string"),
    ]
    )),
    responses: [
    new OA\Response(response: 201, description: "Readlist dibuat"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function store(Request $request)
    {
        $request->validate([
        'name'        => 'required|string|max:255',
        'description' => 'nullable|string',
        ]);

        $readlist = $request->user()->readlists()->create($request->only('name', 'description'));

        return response()->json($readlist, 201);
    }
    
    private function authorize(Request $request, Readlist $readlist)
    {
        if ($readlist->user_id !== $request->user()->id) {
            abort(403, 'Forbidden');
        }
    }

    # ---
    #[OA\Get(
    path: "/readlists/{id}",
    summary: "Detail readlist",
    security: [["sanctum" => []]],
    tags: ["Readlists"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function show(Request $request, Readlist $readlist)
    {
        $this->authorize($request, $readlist);
        $readlist->load('books');
        
        $readlist->books->each(function ($book) {
            $book->cover_url = $book->cover_url
                ? Storage::disk('covers')->url($book->cover_url)
                : null;
        });
        return response()->json($readlist);
    }

    # ---
    #[OA\Put(
    path: "/readlists/{id}",
    summary: "Update readlist",
    security: [["sanctum" => []]],
    tags: ["Readlists"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
    new OA\Property(property: "name", type: "string"),
    new OA\Property(property: "description", type: "string"),
    ])),
    responses: [
    new OA\Response(response: 200, description: "OK"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function update(Request $request, Readlist $readlist)
    {
        $this->authorize($request, $readlist);

        $request->validate([
        'name'        => 'sometimes|string|max:255',
        'description' => 'nullable|string',
        ]);

        $readlist->update($request->only('name', 'description'));
        return response()->json($readlist);
    }

    # ---
    #[OA\Delete(
    path: "/readlists/{id}",
    summary: "Hapus readlist",
    security: [["sanctum" => []]],
    tags: ["Readlists"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    responses: [
    new OA\Response(response: 204, description: "Deleted"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function destroy(Request $request, Readlist $readlist)
    {
        $this->authorize($request, $readlist);
        $readlist->delete();
        return response()->json(null, 204);
    }

    # ---
    #[OA\Post(
    path: "/readlists/{id}/books",
    summary: "Tambah buku ke readlist",
    security: [["sanctum" => []]],
    tags: ["Readlists"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
    required: ["book_id"],
    properties: [new OA\Property(property: "book_id", type: "string", format: "uuid")]
    )),
    responses: [
    new OA\Response(response: 200, description: "Buku ditambahkan"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function addBook(Request $request, Readlist $readlist)
    {
        $this->authorize($request, $readlist);

        $request->validate(['book_id' => 'required|uuid|exists:books,id']);
        $readlist->books()->syncWithoutDetaching($request->book_id);

        return response()->json(['message' => 'Buku ditambahkan']);
    }

    # ---
    #[OA\Delete(
    path: "/readlists/{id}/books",
    summary: "Hapus buku dari readlist",
    security: [["sanctum" => []]],
    tags: ["Readlists"],
    parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
    requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
    required: ["book_id"],
    properties: [new OA\Property(property: "book_id", type: "string", format: "uuid")]
    )),
    responses: [
    new OA\Response(response: 200, description: "Buku dihapus"),
    new OA\Response(response: 403, description: "Forbidden"),
    new OA\Response(response: 401, description: "Unauthenticated"),
    ]
    )]
    # ---
    public function removeBook(Request $request, Readlist $readlist)
    {
        $this->authorize($request, $readlist);

        $request->validate(['book_id' => 'required|uuid|exists:books,id']);
        $readlist->books()->detach($request->book_id);

        return response()->json(['message' => 'Buku dihapus']);
    }
}

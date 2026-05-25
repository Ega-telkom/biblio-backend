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
        operationId: "getReadlists",
        summary: "List readlist milik user",
        security: [["sanctum" => []]],
        tags: ["Readlists"],
        responses: [
            new OA\Response( response: 200,
                description: "OK",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/ReadlistDetail")
                )
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
        $readlists = $request->user()->readlists()->with('books.genre')->get();
        
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
        operationId: "postReadlist",
        summary: "Buat readlist baru",
        security: [["sanctum" => []]],
        tags: ["Readlists"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ReadlistRequest")
        ),
        responses: [
            new OA\Response(response: 201, 
                description: "Readlist dibuat",
                content: new OA\JsonContent(ref: "#/components/schemas/Readlist")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
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
        operationId: "getReadlist",
        summary: "Detail readlist",
        security: [["sanctum" => []]],
        tags: ["Readlists"],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
        responses: [
            new OA\Response( response: 200,
                description: "OK",
                content: new OA\JsonContent(ref: "#/components/schemas/ReadlistDetail")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 403, 
                description: "Forbidden",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
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
        operationId: "updateReadlist",
        summary: "Update readlist",
        security: [["sanctum" => []]],
        tags: ["Readlists"],
        parameters: [new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpdateReadlistRequest")
        ),
        responses: [
            new OA\Response(response: 201, 
                description: "Readlist dibuat",
                content: new OA\JsonContent(ref: "#/components/schemas/Readlist")
            ),
            new OA\Response(response: 403, 
                description: "Forbidden",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
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
        operationId: "deleteReadlist",
        summary: "Hapus readlist",
        security: [["sanctum" => []]],
        tags: ["Readlists"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        responses: [
            new OA\Response(response: 204, 
                description: "Readlist dihapus",
            ),
            new OA\Response(response: 403, 
                description: "Forbidden",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function destroy(Request $request, Readlist $readlist)
    {
        if (!$readlist) {
            return response()->json(['message' => 'not_found'], 404);
        }
    
        $this->authorize($request, $readlist);
        $readlist->delete();
        return response()->json(null, 204);
    }

    # ---
    #[OA\Post(
        path: "/readlists/{id}/books",
        operationId: "addBookToReadlist",
        summary: "Tambah buku ke readlist",
        security: [["sanctum" => []]],
        tags: ["Readlists"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ReadlistBookRequest")
        ),
        responses: [
            new OA\Response(response: 201, 
                description: "Readlist dibuat",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 403, 
                description: "Forbidden",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 422, 
                description: "Buku tidak valid",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function addBook(Request $request, Readlist $readlist)
    {
        $this->authorize($request, $readlist);

        $request->validate(['book_id' => 'required|uuid|exists:books,id']);
        $readlist->books()->syncWithoutDetaching($request->book_id);

        return response()->json(['message' => 'book_added_to_readlist'], 201);
    }

    # ---
    #[OA\Delete(
        path: "/readlists/{id}/books",
        operationId: "deleteBookFromReadlist",
        summary: "Hapus buku dari readlist",
        security: [["sanctum" => []]],
        tags: ["Readlists"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "string", format: "uuid"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ReadlistBookRequest")
        ),
        responses: [
            new OA\Response(response: 201, 
                description: "Buku dihapus",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 403, 
                description: "Forbidden",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 422, 
                description: "Buku tidak valid",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function removeBook(Request $request, Readlist $readlist)
    {
        $this->authorize($request, $readlist);

        $request->validate(['book_id' => 'required|uuid|exists:books,id']);
        $readlist->books()->detach($request->book_id);

        return response()->json(['message' => 'book_deleted_from_readlist'], 201);
    }
}

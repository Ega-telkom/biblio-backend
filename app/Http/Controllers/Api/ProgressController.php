<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ProgressController extends Controller
{
    #---
    #[OA\Post(
        path: "/progress",
        operationId: "upsertProgress",
        summary: "Simpan atau update progress baca",
        security: [["sanctum" => []]],
        tags: ["Progress"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UpsertProgressRequest")
        ),
        responses: [
            new OA\Response(response: 200, 
                description: "OK", 
                content: new OA\JsonContent(ref: "#/components/schemas/User")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated", 
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 422, 
                description: "Validation error", 
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
        ]
    )]
    #---
    public function upsert(Request $request)
    {
        $request->validate([
            'book_id'   => ['required', 'uuid', 'exists:books,id'],
            'last_page' => ['required', 'integer', 'min:1'],
        ]);
    
        $request->user()->update([
            'last_book_id' => $request->book_id,
            'last_page'    => $request->last_page,
        ]);
    
        return response()->json($request->user()->load('lastBook'));
    }

    #---
    #[OA\Delete(
        path: "/progress",
        operationId: "deleteProgress",
        summary: "Hapus progress baca",
        security: [["sanctum" => []]],
        tags: ["Progress"],
        responses: [
            new OA\Response(response: 204, description: "Deleted"),
            new OA\Response(response: 401, 
                description: "Unauthenticated", 
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    #---
    public function destroy(Request $request)
    {
        $request->user()->update([
            'last_book_id' => null,
            'last_page'    => null,
        ]);
    
        return response()->json(null, 204);
    }
}

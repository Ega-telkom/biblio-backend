<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use OpenApi\Attributes as OA;

class AvatarController extends Controller
{
    #---
    #[OA\Post(
        path: "/profile/avatar",
        operationId: "uploadAvatar",
        summary: "Upload avatar user",
        security: [["sanctum" => []]],
        tags: ["Avatar"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(ref: "#/components/schemas/Avatar")
            )
        ),
        responses: [
            new OA\Response(response: 200, 
                description: "Berhasil",
                content: new OA\JsonContent(ref: "#/components/schemas/AvatarResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 422, 
                description: "File tidak valid",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
        ]
    )]
    #---
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'],
        ]);

        $user = $request->user();

        if ($user->avatar_url) {
            Storage::disk('avatars')->delete($user->avatar_url);
        }

        $manager = new ImageManager(new Driver());
        $image = $manager->decode($request->file('avatar')->getPathname())
            ->coverDown(200, 200)
            ->encodeUsingFileExtension('jpg', quality: 80);

        $path = "{$user->id}/avatar.jpg";
        Storage::disk('avatars')->put($path, (string) $image);

        $user->update(['avatar_url' => $path]);

        return response()->json(['avatar' => $user->avatar]);
    }

    #---
    #[OA\Delete(
        path: "/profile/avatar",
        operationId: "deleteAvatar",
        summary: "Hapus avatar user",
        security: [["sanctum" => []]],
        tags: ["Avatar"],
        responses: [
            new OA\Response(response: 200, 
                description: "Berhasil",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    #---
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        if ($user->avatar_url) {
            Storage::disk('avatars')->delete($user->avatar_url);
            $user->update(['avatar_url' => null]);
        }

        return response()->json(['message' => 'avatar_deleted']);
    }
}

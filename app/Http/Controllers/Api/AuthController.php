<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Kreait\Laravel\Firebase\Facades\Firebase;
use OpenApi\Attributes as OA;

#[OA\Info(title: "Biblio API", version: "1.0.0", 
description: "API untuk Biblio.
- Repositori: [https://github.com/Ega-telkom/biblio-backend](https://github.com/Ega-telkom/biblio-backend) 

~ Dari kelompok 3
"
)]
#[OA\SecurityScheme(securityScheme: "sanctum", type: "http", scheme: "bearer")]
class AuthController extends Controller
{
    # ---
    #[OA\Post(
        path: "/auth/firebase",
        operationId: "authFirebase",
        summary: "Login user via Firebase token",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/FirebaseLogin")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login berhasil",
                content: new OA\JsonContent(ref: "#/components/schemas/UserCredentialsResponse")
            ),
            new OA\Response(response: 422, 
                description: "Token tidak valid",
                content: new OA\JsonContent(ref: "#/components/schemas/ValidationErrorResponse")
            ),
        ]
    )]
    # ---
    public function firebaseLogin(Request $request)
    {
        $request->validate([
            'token'        => 'required|string',
            'display_name' => 'nullable|string|max:255',
        ]);
        
        try {
            $verifiedToken = Firebase::auth()->verifyIdToken($request->token);
            $uid           = $verifiedToken->claims()->get('sub');
            $email         = $verifiedToken->claims()->get('email');
            $name          = $request->display_name ?? 'User';

            
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'     => $name,
                    'password' => bcrypt(str()->random(32)), // random, tidak dipakai
                    'role'     => 'user',
                ]
            );
            
            // Sync name dari Firebase jika berubah
            if ($request->display_name && $user->name !== $request->display_name) {
                $user->update(['name' => $request->display_name]);
            }
            
            $token = $user->createToken('firebase')->plainTextToken;
            
            return response()->json([
                'user'  => $user,
                'token' => $token,
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'token_invalid'], 401);
        }
    }

    # ---
    #[OA\Post(
        path: "/auth/logout",
        operationId: "authLogout",
        summary: "Logout",
        security: [["sanctum" => []]],
        tags: ["Auth"],
        responses: [
            new OA\Response(response: 200, 
                description: "Logged out",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(response: 401, 
                description: "Unauthenticated",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
        ]
    )]
    # ---
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'logged_out'], 200);
    }
}

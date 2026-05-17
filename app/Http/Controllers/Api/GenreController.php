<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GenreController extends Controller
{
    // GET: Ambil Semua Kategori
    public function index()
    {
        $genre = Genre::all();
        return response()->json([
            'status' => true,
            'message' => 'genre_index',
            'data' => $genre
        ], 200);
    }

    // POST: Tambah Kategori
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

    // GET: Detail 1 Kategori
    public function show($id)
    {
        $genre = Genre::find($id);
        if (!$genre) return response()->json(['message' => 'genre_notfound'], 404);

        return response()->json(['data' => $genre], 200);
    }

    // PUT: Update Kategori
    public function update(Request $request, $id)
    {
        $genre = Genre::find($id);
        if (!$genre) return response()->json(['message' => 'genre_notfound'], 404);

        $genre->update(['name' => $request->name]);

        return response()->json(['message' => 'genre_updated', 'data' => $genre], 200);
    }

    // DELETE: Hapus Kategori
    public function destroy($id)
    {
        $genre = Genre::find($id);
        if (!$genre) return response()->json(['message' => 'genre_notfound'], 404);

        $genre->delete();
        return response()->json(['message' => 'genre_deleted'], 200);
    }
}


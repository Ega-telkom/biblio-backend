<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     
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


    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
     public function show(Book $book)
     {
         $book->cover_url = $book->cover_url
             ? Storage::disk('covers')->url($book->cover_url)
             : null;
         return response()->json($book->load('genre'));
     }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
     public function destroy(Book $book)
     {
         Storage::disk('s3')->deleteDirectory("books/{$book->id}");
         $book->delete();
     
         return response()->json(null, 204);
     }

     /**
      * Custom function(s)
      */
      public function download(Book $book)
      {
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
}

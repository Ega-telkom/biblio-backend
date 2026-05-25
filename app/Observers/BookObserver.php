<?php

namespace App\Observers;

use App\Models\Book;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class BookObserver
{
    public function saved(Book $book): void
    {
        $coverChanged = $book->wasRecentlyCreated || $book->wasChanged('cover_url');
        if (!$coverChanged || !$book->cover_url) return;
    
        // Pindahkan dari temp jika perlu
        if (!str_starts_with($book->cover_url, $book->id)) {
            $ext = pathinfo($book->cover_url, PATHINFO_EXTENSION);
            $newPath = "{$book->id}/cover_original.{$ext}";
            Storage::disk('covers')->move($book->cover_url, $newPath);
            $book->updateQuietly(['cover_url' => $newPath]);
        }
    
        // Generate thumbnails
        $original = Storage::disk('covers')->get($book->cover_url);
        if (!$original) return;
    
        $manager = new ImageManager(new Driver());
        foreach (['sm' => 100, 'md' => 300, 'lg' => 600] as $label => $width) {
            $image = $manager->decode($original)->scale(width: $width)->encodeUsingFileExtension('jpg', quality: 75);
            Storage::disk('covers')->put("{$book->id}/cover_{$label}.jpg", (string) $image);
        }
    }
    
    public function deleted(Book $book): void
    {
        // Hapus semua file buku
        Storage::disk('s3')->deleteDirectory("books/{$book->id}");
        
        // Hapus semua cover
        Storage::disk('covers')->deleteDirectory("{$book->id}");
    }
}

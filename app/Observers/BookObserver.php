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

        $original = Storage::disk('covers')->get($book->cover_url);

        if (!$original) return;

        $manager = new ImageManager(new Driver());

        $sizes = [
            'sm' => 100,
            'md' => 300,
            'lg' => 600,
        ];

        foreach ($sizes as $label => $width) {
            $image = $manager->decode($original)
                ->scale(width: $width)
                ->encodeUsingFileExtension('jpg', quality: 75);

            Storage::disk('covers')->put(
                "{$book->id}/cover_{$label}.jpg",
                (string) $image
            );
        }
    }
}

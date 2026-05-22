<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        if ($record->cover_url && !str_starts_with($record->cover_url, $record->id)) {
            Storage::disk('covers')->move(
                $record->cover_url,
                "{$record->id}/cover.jpg"
            );
            $record->updateQuietly(['cover_url' => "{$record->id}/cover.jpg"]);
        }

        if ($record->file_path && !str_starts_with($record->file_path, "books/{$record->id}")) {
            $ext = pathinfo($record->file_path, PATHINFO_EXTENSION);
            Storage::disk('s3')->move(
                $record->file_path,
                "books/{$record->id}/file.{$ext}"
            );
            $record->updateQuietly(['file_path' => "books/{$record->id}/file.{$ext}"]);
        }
    }
}

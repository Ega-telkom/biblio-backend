<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        if ($record->cover_url && !str_starts_with($record->cover_url, $record->id)) {
            // Hapus thumbnail lama
            foreach (['sm', 'md', 'lg'] as $size) {
                Storage::disk('covers')->delete("{$record->id}/cover_{$size}.jpg");
            }

            Storage::disk('covers')->move(
                $record->cover_url,
                "{$record->id}/cover.jpg"
            );
            $record->updateQuietly(['cover_url' => "{$record->id}/cover.jpg"]);
        }

        if ($record->file_path && !str_starts_with($record->file_path, "books/{$record->id}")) {
            $ext = pathinfo($record->file_path, PATHINFO_EXTENSION);
            Storage::disk('s3')->delete("books/{$record->id}/file.*");
            Storage::disk('s3')->move(
                $record->file_path,
                "books/{$record->id}/file.{$ext}"
            );
            $record->updateQuietly(['file_path' => "books/{$record->id}/file.{$ext}"]);
        }
    }
}

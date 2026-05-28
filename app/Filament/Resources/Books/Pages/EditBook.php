<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBook extends EditRecord
{
    protected static string $resource = BookResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['cover_url'])) {
            $data['cover_url'] = $this->record->cover_url;
        }
        if (empty($data['file_path'])) {
            $data['file_path'] = $this->record->file_path;
        }
        return $data;
    }
}

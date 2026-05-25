<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Pastikan UUID yang sama dipakai di DB
        $data['id'] = $data['id'] ?? Str::uuid()->toString();
        return $data;
    }

    protected function getFormDefaults(): array
    {
        return [
            'id' => Str::uuid()->toString(),
        ];
    }
}
<?php

namespace App\Filament\Resources\Books\Pages;

use App\Filament\Resources\Books\BookResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateBook extends CreateRecord
{
    protected static string $resource = BookResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // 1. Proses Pemindahan COVER BUKU
        if (!empty($data['cover_url'])) {
            $localCoverPath = $data['cover_url']; // contoh: temp-covers/xyz.jpg
            
            if (Storage::disk('public')->exists($localCoverPath)) {
                // Ambil file dari lokal
                $fileContent = Storage::disk('public')->get($localCoverPath);
                
                // Lempar ke MinIO disk 'covers'
                Storage::disk('covers')->put($localCoverPath, $fileContent);
                
                // Hapus file temporary di lokal server
                Storage::disk('public')->delete($localCoverPath);
            }
        }

        // 2. Proses Pemindahan FILE BUKU (PDF/EPUB)
        if (!empty($data['file_path'])) {
            $localBookPath = $data['file_path']; // contoh: temp-buku/xyz.pdf
            
            if (Storage::disk('public')->exists($localBookPath)) {
                // Ambil file dari lokal
                $fileContent = Storage::disk('public')->get($localBookPath);
                
                // Lempar ke MinIO disk 's3' (bucket privat)
                Storage::disk('s3')->put($localBookPath, $fileContent);
                
                // Hapus file temporary di lokal server
                Storage::disk('public')->delete($localBookPath);
            }
        }

        // Kembalikan data yang sudah bersih untuk disimpan di DB
        return $data;
    }
}
<?php

namespace App\Filament\Resources\Users\Pages;
use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->before(function () {
                    $user = $this->record;
                    // Hapus avatar dari MinIO
                    if ($user->avatar_url) {
                        Storage::disk('avatars')->deleteDirectory($user->id);
                    }
                }),
        ];
    }
}

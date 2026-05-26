<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('extend')
                ->label('Extend 30 Hari')
                ->color('success')
                ->icon('heroicon-o-plus-circle')
                ->action(function () {
                    $user = $this->record;
                    $from = ($user->subscribed_until && $user->subscribed_until->isFuture())
                        ? $user->subscribed_until
                        : now();
                    $user->update(['subscribed_until' => $from->addDays(30)]);
                    $this->refreshFormData(['subscribed_until']);
                }),

            Action::make('revoke')
                ->label('Cabut Langganan')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['subscribed_until' => null]);
                    $this->refreshFormData(['subscribed_until']);
                }),

            DeleteAction::make()
                ->before(function () {
                    $user = $this->record;
                    if ($user->avatar_url) {
                        Storage::disk('avatars')->deleteDirectory((string) $user->id);
                    }
                }),
        ];
    }
}

<?php
namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Storage;

class UserObserver
{
    public function deleted(User $user): void
    {
        Storage::disk('avatars')->deleteDirectory((string) $user->id);
    }
}
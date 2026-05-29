<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Readlist> $readlists
 * @property-read int|null $readlists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRole($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[Fillable(['name', 'email', 'password', 'role', 'avatar_url', 'subscribed_until'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $hidden = ['password', 'remember_token', 'last_book_id', 'last_page', 'avatar_url', 'role'];
    
    public function toArray()
    {
        $array = parent::toArray();
        unset($array['last_book']);
        return $array;
    }
    
    protected $appends = ['avatar', 'is_subscribed', 'progress'];
    /**
    * Get the attributes that should be cast.
    *
    * @return array<string, string>
    */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'subscribed_until'  => 'datetime'
        ];
    }
    
    public function readlists()
    {
        return $this->hasMany(Readlist::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Sesuaikan dengan cara kamu menentukan role admin. 
        // Contoh jika kamu pakai kolom 'role':
        return $this->role === 'admin'; 
        
        // Atau jika kamu pakai package Spatie Permission:
        // return $this->hasRole('admin');
    }
    
    public function getAvatarAttribute(): ?string
    {
        if (!$this->avatar_url) return null;
        return Storage::disk('avatars')->url($this->avatar_url);
    }
    
    public function getIsSubscribedAttribute(): bool
    {
        return $this->subscribed_until !== null 
            && $this->subscribed_until->isFuture();
    }
    
    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class)
            ->orderByDesc('updated_at')
            ->limit(10)
            ->with('book');
    }
    
    public function getProgressAttribute(): array
    {
        return $this->readingProgress->map(fn ($p) => [
            'last_page'  => $p->last_page,
            'updated_at' => $p->updated_at,
            'book'       => $p->book,
        ])->toArray();
    }
    
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

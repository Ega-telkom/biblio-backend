<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Book> $books
 * @property-read int|null $books_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Readlist whereUserId($value)
 * @mixin \Eloquent
 */
class Readlist extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['user_id', 'name', 'description'];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = $model->id ?: Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class, 'readlist_book')
                    ->withTimestamps();
    }
}

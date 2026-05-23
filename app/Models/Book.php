<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property string $id
 * @property int $genre_id
 * @property string $title
 * @property string|null $isbn
 * @property string|null $description
 * @property string $author
 * @property string|null $publisher
 * @property string $lang
 * @property string|null $published_date
 * @property string $format
 * @property int|null $page_count
 * @property int $price
 * @property string|null $cover_url
 * @property string|null $file_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Genre $genre
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCoverUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereGenreId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereIsbn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereLang($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePageCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePublishedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book wherePublisher($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Book whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Book extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    protected $hidden = ['file_path'];
    protected $appends = ['cover_sm', 'cover_md', 'cover_lg'];
    public $incrementing = false;
    
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            $model->id = $model->id ?: Str::uuid();
        });
    }

    protected $fillable = [
        'genre_id', 
        'title', 
        'isbn', 
        'description', 
        'author', 
        'publisher', 
        'lang', 
        'published_date', 
        'format',
        'page_count',
        'price',
        'cover_url',
        'file_path',
    ];

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }
    
    public function getCoverSmAttribute(): ?string
    {
        if (!$this->id) return null;
        return Storage::disk('covers')->url("{$this->id}/cover_sm.jpg");
    }
    
    public function getCoverMdAttribute(): ?string
    {
        if (!$this->id) return null;
        return Storage::disk('covers')->url("{$this->id}/cover_md.jpg");
    }
    
    public function getCoverLgAttribute(): ?string
    {
        if (!$this->id) return null;
        return Storage::disk('covers')->url("{$this->id}/cover_lg.jpg");
    }
}

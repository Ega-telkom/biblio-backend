<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory;

    protected $keyType = 'string';
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
}

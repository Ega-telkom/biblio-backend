<?php
namespace App\Filament\Resources\Books\Schemas;

use App\Models\Genre;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('genre_id')
                ->relationship('genre', 'name')
                ->required(),
            TextInput::make('title')->required(),
            TextInput::make('isbn'),
            Textarea::make('description')->columnSpanFull(),
            TextInput::make('author')->required(),
            TextInput::make('publisher'),
            TextInput::make('lang')->required()->default('id'),
            DatePicker::make('published_date'),
            Select::make('format')
                ->options(['pdf' => 'PDF', 'epub' => 'EPUB', 'mobi' => 'MOBI', 'djvu' => 'DJVU'])
                ->required(),
            TextInput::make('page_count')->numeric(),
            TextInput::make('price')->required()->numeric()->prefix('Rp'),
            FileUpload::make('cover_url')
                ->disk('covers')
                ->image()
                ->label('Cover Buku'),
            FileUpload::make('file_path')
                ->disk('s3')
                ->acceptedFileTypes(['application/pdf', 'application/epub+zip'])
                ->label('File Buku'),
        ]);
    }
}

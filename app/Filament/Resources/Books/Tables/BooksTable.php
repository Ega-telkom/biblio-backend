<?php

namespace App\Filament\Resources\Books\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BooksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover_md')
                    ->label('Cover')
                    ->height(60)
                    ->width(40),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->weight('bold'),
                TextColumn::make('author')
                    ->label('Author')
                    ->searchable(),
                TextColumn::make('genre.name')
                    ->label('Genre')
                    ->badge(),
                TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
            ])
                ->contentGrid(null) // pastikan grid dimatikan
                    ->defaultPaginationPageOption(20)
                    ->paginationPageOptions([10, 20, 50])
                    ->filters([])
                ->recordActions([
                    EditAction::make(),
                ])
                ->toolbarActions([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ]);
    }
}

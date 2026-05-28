<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DateTimePicker;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required(),
            TextInput::make('email')->email()->required(),
            TextInput::make('password')->password()->revealable()->nullable(),
            DateTimePicker::make('subscribed_until')
                ->label('Aktif hingga')
                ->nullable()
                ->seconds(false),
            Select::make('role')
                ->options(['admin' => 'Admin', 'user' => 'User'])
                ->required(),
            FileUpload::make('avatar_url')
                ->disk('avatars_public')
                ->directory(fn ($record) => $record?->id ?? 'temp')
                ->image()
                ->deletable()
                ->imageResizeTargetWidth(200)
                ->imageResizeTargetHeight(200)
                ->label('Avatar'),
        ]);
    }
}

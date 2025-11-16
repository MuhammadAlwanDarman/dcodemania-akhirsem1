<?php

namespace App\Filament\Resources\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class OrderRelationManagers extends RelationManager
{
    protected static string $relationship = 'orders';

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
            Tables\Columns\TextColumn::make('status'),
        ]);
    }
}

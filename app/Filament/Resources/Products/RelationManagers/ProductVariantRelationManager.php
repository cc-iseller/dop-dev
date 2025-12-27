<?php

namespace App\Filament\Resources\Products\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;

class ProductVariantRelationManager extends RelationManager
{
    protected static string $relationship = 'variants'; // HARUS cocok dengan Product::variants()

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->unique(ignoreRecord: true),

                TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->minValue(0)
                    ->required(),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')->label('SKU')->searchable(),
                TextColumn::make('price')->money('IDR'),
                TextColumn::make('stock'),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif')
                    ->badge(),
            ])
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}

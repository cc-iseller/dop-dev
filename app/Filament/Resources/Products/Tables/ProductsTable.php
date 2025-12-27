<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('has_variants')
                    ->boolean(),
                TextColumn::make('sku_display')
                    ->label('SKU / Variant')
                    ->getStateUsing(function ($record) {
                        if (! $record->has_variants) {
                            return $record->base_sku;
                        }

                        return $record->variants()->count() . ' variants';
                    }),
                TextColumn::make('price_display')
                    ->label('Harga')
                    ->state(function ($record) {
                        // CASE 1: Produk tanpa variant
                        if (! $record->has_variants) {
                            if ($record->base_price === null) {
                                return '-';
                            }

                            return 'Rp ' . number_format($record->base_price, 0, ',', '.');
                        }

                        // CASE 2: Produk dengan variant
                        $variants = $record->variants();

                        if (! $variants->exists()) {
                            return '-';
                        }

                        $min = $variants->min('price');
                        $max = $variants->max('price');

                        if ($min === $max) {
                            return 'Rp ' . number_format($min, 0, ',', '.');
                        }

                        return 'Rp ' . number_format($min, 0, ',', '.') .
                            ' - Rp ' . number_format($max, 0, ',', '.');
                    })
                    ->sortable(false),

                TextColumn::make('stock_display')
                    ->label('Stok')
                    ->getStateUsing(function ($record) {
                        if (! $record->has_variants) {
                            return $record->base_stock;
                        }

                        return $record->variants()->sum('stock');
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

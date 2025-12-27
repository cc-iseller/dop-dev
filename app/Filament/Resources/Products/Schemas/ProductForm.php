<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                            ->label('Nama Produk')
                            ->required()
                            ->maxLength(255),

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->unique(Category::class, 'name'),
                            ]),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                        Toggle::make('has_variants')
                            ->label('Produk memiliki variant?')
                            ->live()
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Produk Aktif')
                            ->default(true),

                Section::make('Detail Produk (Tanpa Variant)')
                    ->columns(3)
                    ->visible(fn ($get) => $get('has_variants') === false)
                    ->components([

                        TextInput::make('base_sku')
                            ->label('SKU Produk')
                            ->required(fn ($get) => $get('has_variants') === false)
                            ->unique(ignoreRecord: true),

                        TextInput::make('base_price')
                            ->label('Harga')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(fn ($get) => $get('has_variants') === false),

                        TextInput::make('base_stock')
                            ->label('Stok')
                            ->numeric()
                            ->minValue(0)
                            ->required(fn ($get) => $get('has_variants') === false),
                    ]),
            ]);
    }
}

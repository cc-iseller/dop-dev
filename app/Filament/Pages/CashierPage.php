<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;

class CashierPage extends Page
{
    protected string $view = 'filament.pages.cashier-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalculator;

    protected static ?string $navigationLabel = 'Kasir Point of Sale';

    public static function getLabel(): string
    {
        return 'Kasir Point of Sale';
    }

    public static function getPluralLabel(): string
    {
        return 'Kasir Point of Sale';
    }
}

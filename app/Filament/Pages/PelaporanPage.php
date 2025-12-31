<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class PelaporanPage extends Page
{
    protected string $view = 'filament.pages.pelaporan-page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Reporting';

    public static function getLabel(): string
    {
        return 'Reporting';
    }

    public static function getPluralLabel(): string
    {
        return 'Reporting';
    }
}

<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class BillingPage extends Page
{
    protected string $view = 'filament.pages.billing-page';

    protected static bool $shouldRegisterNavigation = false;
}

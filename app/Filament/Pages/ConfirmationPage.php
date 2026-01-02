<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ConfirmationPage extends Page
{
    protected string $view = 'filament.pages.confirmation-page';

    protected static bool $shouldRegisterNavigation = false;
}

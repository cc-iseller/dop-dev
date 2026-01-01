<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;

class TransactionPage extends Component
{
    public $transactions = [];

    public function mount()
    {
        $this->transactions = Transaction::latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.transaction-page');
    }
}

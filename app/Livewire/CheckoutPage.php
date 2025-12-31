<?php

namespace App\Livewire;

use Livewire\Component;

class CheckoutPage extends Component
{
    public array $cart = [];

    public function addToCart($productId, $variantId = null)
    {
        $key = $variantId
            ? "p{$productId}_v{$variantId}"
            : "p{$productId}";

        if (! isset($this->cart[$key])) {
            $this->cart[$key] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'qty' => 1,
            ];
        } else {
            $this->cart[$key]['qty']++;
        }
    }

    public function removeItem($key)
    {
        unset($this->cart[$key]);
    }
    
    public function render()
    {
        return view('livewire.checkout-page');
    }
}

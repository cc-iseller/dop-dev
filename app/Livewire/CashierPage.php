<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductVariant;

class CashierPage extends Component
{   
    public function addToCart(int $productId, ?int $variantId = null)
    {
        $this->dispatch('cart:add', 
            productId: $productId,
            variantId: $variantId
        );
    }

    public function render()
    {
        $products = Product::with([
                'variants.options',
                'category'
            ])
            ->where('is_active', true)
            ->where(function($query) {
                // Produk tanpa variant: cek base_stock > 0
                $query->where(function($q) {
                    $q->where('has_variants', false)
                      ->where('base_stock', '>', 0);
                })
                // ATAU Produk dengan variant: minimal ada 1 variant dengan stock > 0
                ->orWhere(function($q) {
                    $q->where('has_variants', true)
                      ->whereHas('variants', function($variantQuery) {
                          $variantQuery->where('stock', '>', 0)
                                       ->where('is_active', true);
                      });
                });
            })
            ->get()
            // Filter variants yang stoknya habis
            ->map(function($product) {
                if ($product->has_variants) {
                    $product->setRelation('variants', 
                        $product->variants->filter(function($variant) {
                            return $variant->stock > 0 && $variant->is_active;
                        })
                    );
                }
                return $product;
            });

        return view('livewire.cashier-page', compact('products'));
    }
}
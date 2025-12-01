<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helper\CartManagement;
use App\Models\Product;

class ProductDetailPage extends Component
{
    public $product;
    public $quantity = 1;
    public $total_price = 0;

    public function mount($productId)
    {
        $this->product = Product::with('images')->findOrFail($productId);
        $this->calculateTotal();
    }

    public function increaseQty()
    {
        $this->quantity++;
        $this->calculateTotal();
    }

    public function decreaseQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
            $this->calculateTotal();
        }
    }

    public function calculateTotal()
    {
        $this->total_price = $this->product->price * $this->quantity;
    }

    public function addToCart($product_id)
    {
        // Tambahkan produk ke cart dengan quantity yang dipilih
        $total_count = CartManagement::addItemToCartWithQty($product_id, $this->quantity);
        
        // Dispatch event untuk update cart count di navbar
        $this->dispatch('update-cart-count', total_count: $total_count);
        
        // Reset quantity ke 1 setelah berhasil add to cart
        $this->quantity = 1;
        $this->calculateTotal();
        
        // Show success message
        session()->flash('success', 'Product added to cart successfully!');
    }

    public function render()
    {
        return view('livewire.product-detail-page');
    }
}
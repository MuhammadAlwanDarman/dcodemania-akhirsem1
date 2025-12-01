<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use App\Helper\CartManagement;
use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

#[Title('Cart - DCodeMania')]
class CartPage extends Component
{
    public $cart_items = [];
    public $grand_total = 0;
    public $cart_items_with_details = [];

    public function mount()
    {
        $this->updateCartData();
    }

    public function updateCartData()
    {
        // Get cart items from cookie
        $this->cart_items = CartManagement::getCartItemFromCookie();
        
        // Get products data
        $products = Product::whereIn('id', array_column($this->cart_items, 'product_id'))->get();
        
        // Calculate grand total
        $this->grand_total = CartManagement::calculateCartTotal($products);
        
        // Map product details to cart items
        $this->cart_items_with_details = [];
        foreach ($this->cart_items as $cart_item) {
            $product = $products->firstWhere('id', $cart_item['product_id']);
            if ($product) {
                $this->cart_items_with_details[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->images[0] ?? null,
                    'quantity' => $cart_item['quantity'],
                    'total' => $product->price * $cart_item['quantity']
                ];
            }
        }
    }

    public function removeItem($product_id)
    {
        // Remove item from cart menggunakan CartManagement
        $this->cart_items = CartManagement::removeItemFromCart($product_id);
        
        // Update cart data
        $this->updateCartData();
        
        // Dispatch event to update cart count in navbar
        $this->dispatch('update-cart-count', total_count: count($this->cart_items));
        
        // Show success message
        session()->flash('success', 'Item removed from cart successfully!');
        
        // Force refresh component untuk memastikan UI terupdate
        $this->js('window.location.reload()');
    }

    public function increaseQty($product_id)
    {
        $current_qty = $this->getItemQty($product_id);
        $this->cart_items = CartManagement::updateCartItemQuantity($product_id, $current_qty + 1);
        
        // Update cart data
        $this->updateCartData();
        
        $this->dispatch('update-cart-count', total_count: count($this->cart_items));
        
        session()->flash('success', 'Quantity increased!');
    }

    public function decreaseQty($product_id)
    {
        $current_qty = $this->getItemQty($product_id);
        if ($current_qty > 1) {
            $this->cart_items = CartManagement::updateCartItemQuantity($product_id, $current_qty - 1);
            
            // Update cart data
            $this->updateCartData();
            
            $this->dispatch('update-cart-count', total_count: count($this->cart_items));
            
            session()->flash('success', 'Quantity decreased!');
        } else {
            // Jika quantity = 1, remove item
            $this->removeItem($product_id);
        }
    }

    public function getItemQty($product_id)
    {
        foreach ($this->cart_items as $item) {
            if ($item['product_id'] == $product_id) {
                return $item['quantity'];
            }
        }
        return 0;
    }

    public function checkout()
    {
        if (count($this->cart_items) > 0) {
            session()->flash('success', 'Proceeding to checkout!');
            // Redirect ke checkout page
            // return redirect()->route('checkout');
        } else {
            session()->flash('error', 'Your cart is empty!');
        }
    }

    public function clearCart()
    {
        CartManagement::clearCartItemFromCookie();
        $this->cart_items = [];
        $this->cart_items_with_details = [];
        $this->grand_total = 0;
        
        $this->dispatch('update-cart-count', total_count: 0);
        
        session()->flash('success', 'Cart cleared successfully!');
        
        // Force refresh
        $this->js('window.location.reload()');
    }

    public function render()
    {
        return view('livewire.cart-page', [
            'cart_items_with_details' => $this->cart_items_with_details,
            'grand_total' => $this->grand_total
        ]);
    }
}
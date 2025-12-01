<?php

namespace App\Helper;

use Illuminate\Support\Facades\Cookie;

class CartManagement
{
    // Get cart items from cookie
    static public function getCartItemFromCookie() {
        $cart_items = json_decode(request()->cookie('cart_items'), true) ?? [];
        return $cart_items;
    }

    // Add cart items to cookie
    static public function addCartItemToCookie($cart_items) {
        $cookie = Cookie::make('cart_items', json_encode($cart_items), 60 * 24 * 30); // 30 days
        return $cookie;
    }

    // Clear cart items from cookie
    static public function clearCartItemFromCookie() {
        $cookie = Cookie::forget('cart_items');
        return $cookie;
    }

    // Remove item from cart - METHOD YANG DIPERBAIKI
    static public function removeItemFromCart($product_id) {
        $cart_items = self::getCartItemFromCookie();

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                unset($cart_items[$key]);
                break;
            }
        }

        // Reset array keys untuk menghindari masalah index
        $cart_items = array_values($cart_items);

        // Simpan kembali ke cookie
        $cookie = self::addCartItemToCookie($cart_items);
        Cookie::queue($cookie);

        return $cart_items;
    }

    // Update cart item quantity
    static public function updateCartItemQuantity($product_id, $qty) {
        $cart_items = self::getCartItemFromCookie();

        foreach ($cart_items as $key => &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] = $qty;
                break;
            }
        }

        $cookie = self::addCartItemToCookie($cart_items);
        Cookie::queue($cookie);

        return $cart_items;
    }

    // Add item to cart with quantity
    static public function addItemToCartWithQty($product_id, $qty = 1) {
        $cart_items = self::getCartItemFromCookie();

        $existing_item = null;

        foreach ($cart_items as $key => $item) {
            if ($item['product_id'] == $product_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            $cart_items[$existing_item]['quantity'] += $qty;
        } else {
            $cart_items[] = [
                'product_id' => $product_id,
                'quantity' => $qty
            ];
        }

        $cookie = self::addCartItemToCookie($cart_items);
        Cookie::queue($cookie);

        return count($cart_items);
    }

    // Calculate cart total
    static public function calculateCartTotal($products) {
        $cart_items = self::getCartItemFromCookie();
        $total = 0;

        foreach ($cart_items as $item) {
            $product = $products->firstWhere('id', $item['product_id']);
            if ($product) {
                $total += $product->price * $item['quantity'];
            }
        }

        return $total;
    }

    // Get cart total count
    static public function getCartTotalCount() {
        $cart_items = self::getCartItemFromCookie();
        return count($cart_items);
    }
}
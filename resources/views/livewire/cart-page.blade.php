<div>
    <div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
        <div class="container mx-auto px-4">
            
            <!-- Flash Messages -->
            @if (session()->has('success'))
                <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Debug Info (bisa dihapus setelah testing) -->
            <div class="mb-4 p-3 bg-gray-100 rounded-lg text-sm">
                <strong>Debug Info:</strong> 
                Total Items in Cart: {{ count($cart_items_with_details) }} |
                Cart Items Count: {{ count($cart_items) }}
            </div>

            <h1 class="text-3xl font-bold mb-6 text-gray-900">Shopping Cart</h1>
            
            @if(count($cart_items_with_details) > 0)
            <div class="flex flex-col md:flex-row gap-6">
                <div class="md:w-3/4">
                    <div class="bg-white overflow-x-auto rounded-lg shadow-lg p-6 mb-4">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-gray-200">
                                    <th class="text-left font-bold text-lg py-4 text-gray-800">Product</th>
                                    <th class="text-left font-bold text-lg py-4 text-gray-800">Price</th>
                                    <th class="text-left font-bold text-lg py-4 text-gray-800">Quantity</th>
                                    <th class="text-left font-bold text-lg py-4 text-gray-800">Total</th>
                                    <th class="text-left font-bold text-lg py-4 text-gray-800">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cart_items_with_details as $item)
                                    <tr wire:key="cart-item-{{ $item['product_id'] }}-{{ $item['quantity'] }}" class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-6">
                                            <div class="flex items-center">
                                                <img class="h-24 w-24 mr-6 object-cover rounded-lg shadow-md" 
                                                     src="{{ url('storage', $item['image']) }}" 
                                                     alt="{{ $item['name'] }}"
                                                     onerror="this.src='https://via.placeholder.com/96?text=No+Image'">
                                                <div>
                                                    <span class="font-semibold text-xl text-gray-900 block">{{ $item['name'] }}</span>
                                                    <span class="text-sm text-gray-500">ID: {{ $item['product_id'] }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6">
                                            <span class="text-lg font-medium text-gray-700">
                                                {{ Number::currency($item['price'], 'IDR') }}
                                            </span>
                                        </td>
                                        <td class="py-6">
                                            <div class="flex items-center">
                                                <button wire:click="decreaseQty({{ $item['product_id'] }})" 
                                                        class="w-12 h-12 border-2 border-gray-300 rounded-lg hover:bg-gray-100 text-xl font-bold transition-colors flex items-center justify-center
                                                               {{ $item['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                        {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                                    -
                                                </button>
                                                <span class="text-center w-16 text-xl font-semibold text-gray-900 mx-3">
                                                    {{ $item['quantity'] }}
                                                </span>
                                                <button wire:click="increaseQty({{ $item['product_id'] }})" 
                                                        class="w-12 h-12 border-2 border-gray-300 rounded-lg hover:bg-gray-100 text-xl font-bold transition-colors flex items-center justify-center">
                                                    +
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-6">
                                            <span class="text-xl font-bold text-blue-600">
                                                {{ Number::currency($item['total'], 'IDR') }}
                                            </span>
                                        </td>
                                        <td class="py-6">
                                            <button wire:click="removeItem({{ $item['product_id'] }})" 
                                                    wire:confirm="Are you sure you want to remove '{{ $item['name'] }}' from your cart?"
                                                    class="text-red-500 hover:text-red-700 p-3 rounded-full hover:bg-red-50 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Cart Summary -->
                <div class="md:w-1/4">
                    <div class="bg-white rounded-lg shadow-lg p-6 sticky top-4">
                        <h2 class="text-xl font-bold mb-4 text-gray-800">Cart Summary</h2>
                        <div class="flex justify-between mb-3">
                            <span class="text-lg text-gray-600">Items in Cart:</span>
                            <span class="text-lg font-semibold">{{ count($cart_items_with_details) }}</span>
                        </div>
                        <div class="flex justify-between mb-3">
                            <span class="text-lg text-gray-600">Subtotal:</span>
                            <span class="text-lg font-semibold">{{ Number::currency($grand_total, 'IDR') }}</span>
                        </div>
                        <div class="flex justify-between mb-3">
                            <span class="text-lg text-gray-600">Shipping:</span>
                            <span class="text-lg font-semibold text-green-600">FREE</span>
                        </div>
                        <div class="flex justify-between mb-6 pt-4 border-t border-gray-300">
                            <span class="text-xl font-bold text-gray-800">Total:</span>
                            <span class="text-xl font-bold text-blue-600">{{ Number::currency($grand_total, 'IDR') }}</span>
                        </div>
                        
                        <button wire:click="checkout" 
                                class="w-full bg-blue-600 text-white py-4 px-4 rounded-lg hover:bg-blue-700 transition-colors text-lg font-semibold mb-3">
                            Proceed to Checkout
                        </button>
                        <button wire:click="clearCart" 
                                wire:confirm="Are you sure you want to clear your entire cart?"
                                class="w-full bg-red-500 text-white py-3 px-4 rounded-lg hover:bg-red-600 transition-colors text-lg font-semibold">
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
            @else
            <!-- Empty Cart State -->
            <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                <div class="flex flex-col items-center justify-center py-12">
                    <svg class="w-24 h-24 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-500 mb-4">Your cart is empty</h3>
                    <p class="text-gray-400 mb-6">Add some amazing products to get started!</p>
                    <a href="{{ route('products') }}" 
                       class="bg-blue-500 text-white px-8 py-4 rounded-lg hover:bg-blue-600 transition-colors text-lg font-semibold">
                        Start Shopping
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
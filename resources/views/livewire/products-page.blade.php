<div>
    <div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 mx-auto">
        <section class="py-10 bg-gray-50 font-poppins dark:bg-gray-800 rounded-lg">
            <div class="px-4 py-4 mx-auto max-w-7xl lg:py-6 md:px-6">
                
                <!-- Toast Notification Container -->
                <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

                <div class="flex flex-wrap mb-24 -mx-3">
                    <!-- Sidebar Filters -->
                    <div class="w-full pr-2 lg:w-1/4 lg:block">
                        <!-- Categories Filter -->
                        <div class="p-4 mb-5 bg-white border border-gray-200 dark:border-gray-900 dark:bg-gray-900">
                            <h2 class="text-2xl font-bold dark:text-gray-400">Categories</h2>
                            <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
                            <ul>
                                @foreach ($categories as $category)
                                <li class="mb-4" wire:key="category-{{ $category->id }}">
                                    <label for="category-{{ $category->slug }}" class="flex items-center dark:text-gray-400">
                                        <input type="checkbox" wire:model.live="selected_categories" 
                                               id="category-{{ $category->slug }}" 
                                               value="{{ $category->id }}" 
                                               class="w-4 h-4 mr-2">
                                        <span class="text-lg">{{ $category->name }}</span>
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Brands Filter -->
                        <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
                            <h2 class="text-2xl font-bold dark:text-gray-400">Brand</h2>
                            <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
                            <ul>
                                @foreach ($brands as $brand)
                                <li class="mb-4" wire:key="brand-{{ $brand->id }}">
                                    <label for="brand-{{ $brand->slug }}" class="flex items-center dark:text-gray-300">
                                        <input type="checkbox" wire:model.live="selected_brands" 
                                               id="brand-{{ $brand->slug }}" 
                                               value="{{ $brand->id }}" 
                                               class="w-4 h-4 mr-2">
                                        <span class="text-lg dark:text-gray-400">{{ $brand->name }}</span>
                                    </label>
                                </li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Product Status Filter -->
                        <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
                            <h2 class="text-2xl font-bold dark:text-gray-400">Product Status</h2>
                            <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
                            <ul>
                                <li class="mb-4">
                                    <label for="featured" class="flex items-center dark:text-gray-300">
                                        <input type="checkbox" id="featured" wire:model.live="featured" 
                                               value="1" class="w-4 h-4 mr-2">
                                        <span class="text-lg dark:text-gray-400">Featured Product</span>
                                    </label>
                                </li>
                                <li class="mb-4">
                                    <label for="on_sale" class="flex items-center dark:text-gray-300">
                                        <input type="checkbox" id="on_sale" wire:model.live="on_sale" 
                                               value="1" class="w-4 h-4 mr-2">
                                        <span class="text-lg dark:text-gray-400">On Sale</span>
                                    </label>
                                </li>
                            </ul>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="p-4 mb-5 bg-white border border-gray-200 dark:bg-gray-900 dark:border-gray-900">
                            <h2 class="text-2xl font-bold dark:text-gray-400">Price</h2>
                            <div class="w-16 pb-2 mb-6 border-b border-rose-600 dark:border-gray-400"></div>
                            <div class="mb-2 font-semibold text-blue-600">
                                {{ Number::currency($price_range, 'IDR') }}
                            </div>
                            <input type="range" wire:model.live="price_range" 
                                   class="w-full h-1 mb-4 bg-blue-100 rounded appearance-none cursor-pointer"
                                   min="0" max="5000000" step="1000">
                            <div class="flex justify-between">
                                <span class="inline-block text-sm font-bold text-blue-400">{{ Number::currency(0, 'IDR') }}</span>
                                <span class="inline-block text-sm font-bold text-blue-400">{{ Number::currency(5000000, 'IDR') }}</span>
                            </div>
                        </div>

                        <!-- Clear Filters Button -->
                        <button wire:click="clearFilters" 
                                class="w-full px-4 py-2 font-bold text-white bg-red-600 rounded hover:bg-red-700">
                            Clear All Filters
                        </button>
                    </div>

                    <!-- Products Grid -->
                    <div class="w-full px-3 lg:w-3/4">
                        <!-- Sort Options -->
                        <div class="px-3 mb-4">
                            <div class="items-center justify-between hidden px-3 py-2 bg-gray-100 md:flex dark:bg-gray-900">
                                <div class="flex items-center justify-between">
                                    <select wire:model.live="sort"
                                            class="block w-40 text-base bg-gray-100 cursor-pointer dark:text-gray-400 dark:bg-gray-900">
                                        <option value="latest">Sort by Latest</option>
                                        <option value="price">Price: Low to High</option>
                                        <option value="price_desc">Price: High to Low</option>
                                        <option value="name">Name: A to Z</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="flex flex-wrap items-center">
                            @foreach ($products as $product)
                            <div class="w-full px-3 mb-6 sm:w-1/2 md:w-1/3" wire:key="product-{{ $product->id }}">
                                <div class="border border-gray-300 dark:border-gray-700">
                                    <!-- Product Image -->
                                    <div class="relative bg-gray-200">
                                        <a href="/products/{{ $product->slug }}">
                                            <img src="{{ url('storage/' . ($product->images[0] ?? 'default.jpg')) }}"
                                                 alt="{{ $product->name }}"
                                                 class="object-cover w-full h-56 mx-auto">
                                        </a>
                                    </div>

                                    <!-- Product Info -->
                                    <div class="p-3">
                                        <div class="flex items-center justify-between gap-2 mb-2">
                                            <h3 class="text-xl font-medium dark:text-gray-400">
                                                {{ $product->name }}
                                            </h3>
                                        </div>
                                        <p class="text-lg">
                                            <span class="text-green-600 dark:text-green-600">
                                                {{ Number::currency($product->price, 'IDR') }}
                                            </span>
                                        </p>
                                    </div>

                                    <!-- Add to Cart Button -->
                                    <div class="flex justify-center p-4 border-t border-gray-300 dark:border-gray-700">
                                        <a href="#" 
                                           wire:click.prevent="addToCart({{ $product->id }})"
                                           class="text-gray-500 flex items-center space-x-2 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                 fill="currentColor" class="w-4 h-4 bi bi-cart3" viewBox="0 0 16 16">
                                                <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                            </svg>
                                            <span wire:loading.remove wire:target="addToCart({{ $product->id }})">Add to Cart</span>
                                            <span wire:loading wire:target="addToCart({{ $product->id }})">Adding...</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="flex justify-end mt-6">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('show-toast', (event) => {
            showToast(event.type, event.message);
        });
    });

    function showToast(type, message) {
        const toastContainer = document.getElementById('toast-container');
        
        const toast = document.createElement('div');
        toast.className = `p-4 rounded-lg shadow-lg transform transition-all duration-300 ease-in-out ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        
        toast.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    ${type === 'success' ? 
                        '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>' :
                        '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>'
                    }
                </svg>
                <span>${message}</span>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
</script>
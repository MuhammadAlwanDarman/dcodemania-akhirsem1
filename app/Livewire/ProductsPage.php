<?php

namespace App\Livewire;

use App\Helper\CartManagement;
use App\Livewire\Partials\Navbar;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Product Page - DCodeMania')]
class ProductsPage extends Component
{
    use WithPagination;

    #[Url]
    public $selected_categories = [];

    #[Url]
    public $selected_brands = [];

    #[Url]
    public $price_range = 300000;

    #[Url]
    public $sort = 'latest';

    #[Url]
    public $featured = null;

    #[Url]
    public $on_sale = null;

    // Add to cart function
    public function addToCart($product_id) {
        $total_count = CartManagement::addItemToCart($product_id);

        $this->dispatch('update-cart-count', total_count: $total_count)->to(Navbar::class);

        // Show success message - cara yang benar di Livewire
        $this->dispatch('show-toast', 
            type: 'success', 
            message: 'Product added to the cart successfully!'
        );
    }

    // Reset pagination when filters change
    public function updatedSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatedSelectedBrands()
    {
        $this->resetPage();
    }

    public function updatedFeatured()
    {
        $this->resetPage();
    }

    public function updatedOnSale()
    {
        $this->resetPage();
    }

    public function updatedPriceRange()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    // Clear all filters
    public function clearFilters()
    {
        $this->selected_categories = [];
        $this->selected_brands = [];
        $this->price_range = 300000;
        $this->sort = 'latest';
        $this->featured = null;
        $this->on_sale = null;
        $this->resetPage();
        
        $this->dispatch('show-toast', 
            type: 'info', 
            message: 'All filters have been cleared.'
        );
    }

    public function render()
    {
        $productQuery = Product::query()->where('is_active', 1);

        // Category filter
        if (!empty($this->selected_categories)) {
            $productQuery->whereIn('category_id', $this->selected_categories);
        }

        // Brand filter
        if (!empty($this->selected_brands)) {
            $productQuery->whereIn('brand_id', $this->selected_brands);
        }

        // Featured filter
        if ($this->featured) {
            $productQuery->where('is_featured', 1);
        }

        // On sale filter
        if ($this->on_sale) {
            $productQuery->where('on_sale', 1);
        }

        // Price range filter
        if ($this->price_range) {
            $productQuery->whereBetween('price', [0, $this->price_range]);
        }

        // Sorting
        if ($this->sort == 'latest') {
            $productQuery->latest();
        } elseif ($this->sort == 'price') {
            $productQuery->orderBy('price');
        } elseif ($this->sort == 'price_desc') {
            $productQuery->orderBy('price', 'desc');
        } elseif ($this->sort == 'name') {
            $productQuery->orderBy('name');
        }

        return view('livewire.products-page', [
            'products'   => $productQuery->paginate(6),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
            'brands'     => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}
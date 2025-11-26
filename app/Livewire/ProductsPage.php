<?php

namespace App\Livewire;

use App\Helper\CartManagement;
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
    public $featured = false; // ✔ boolean

    #[Url]
    public $on_sale = false; // ✔ boolean

    #[Url]
    public $price_range = 300000;

    #[Url]
    public $sort = 'latest';

    /**
     * Reset pagination when filter changes
     */
    public function updated($name)
    {
        if (in_array($name, [
            'selected_categories',
            'selected_brands',
            'featured',
            'on_sale',
            'price_range',
            'sort'
        ])) {
            $this->resetPage();
        }
    }

    public function mount()
{
    $this->featured = filter_var($this->featured, FILTER_VALIDATE_BOOLEAN);
    $this->on_sale = filter_var($this->on_sale, FILTER_VALIDATE_BOOLEAN);

    // pastikan array benar
    $this->selected_categories = is_array($this->selected_categories) ? $this->selected_categories : [];
    $this->selected_brands = is_array($this->selected_brands) ? $this->selected_brands : [];
}


    /**
     * Add product to cart
     */
    public function addToCart($product_id)
    {
        CartManagement::addItemToCart($product_id);

        $this->dispatchBrowserEvent('swal', [
            'title' => 'Item Added',
            'text'  => 'Product successfully added to cart!',
            'icon'  => 'success',
        ]);
    }

    /**
     * Render component
     */
    public function render()
    {

        $this->selected_categories = [];
$this->selected_brands = [];
$this->featured = false;
$this->on_sale = false;
$this->price_range = null;


        $query = Product::query()->where('is_active', 1);

        if (!empty($this->selected_categories)) {
            $query->whereIn('category_id', $this->selected_categories);
        }

        if (!empty($this->selected_brands)) {
            $query->whereIn('brand_id', $this->selected_brands);
        }

        if ($this->featured === true) {
            $query->where('is_featured', 1);
        }

        if ($this->on_sale === true) {
            $query->where('on_sale', 1);
        }

        if (!empty($this->price_range)) {
            $query->whereBetween('price', [0, $this->price_range]);
        }

        if ($this->sort === 'latest') {
            $query->latest();
        } elseif ($this->sort === 'price') {
            $query->orderBy('price');
        }


        return view('livewire.products-page', [
            'products'   => $query->paginate(6),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
            'brands'     => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}

<?php

namespace App\Livewire;

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
    public $selected_Categories = []; // FIX: huruf besar-kecil disamakan

    #[Url]
    public $price_range = null;

    // Reset pagination setiap kali filter kategori berubah
    public function updatedSelectedCategories()
    {
        $this->resetPage();
    }

    public function render()
    {
        $productQuery = Product::query()->where('is_active', 1);

        // Filter kategori
        if (!empty($this->selected_Categories)) {
            $productQuery->whereIn('category_id', $this->selected_Categories);
        }

        return view('livewire.products-page', [
            'products'   => $productQuery->paginate(6),
            'categories' => Category::where('is_active', 1)->get(['id', 'name', 'slug']),
            'brands'     => Brand::where('is_active', 1)->get(['id', 'name', 'slug']),
        ]);
    }
}

<?php

use App\Livewire\Auth\ForgotPasswordPage;
use App\Livewire\CancelsPage;
use Illuminate\Support\Facades\Route;

use App\Livewire\HomePage;
use App\Livewire\CategoriesPage;

use App\Livewire\ProductDetailPage;
use App\Livewire\CartPage;
use App\Livewire\CheckOutPage;
use App\Livewire\SuccessPage;
use App\Livewire\MyOrderPage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\CancelPage;

use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ResetPasswordPage;
use App\Livewire\ProductsPage;

Route::get('/', HomePage::class)->name('home');
Route::get('/categories', CategoriesPage::class);
Route::get('/products', ProductsPage::class);
Route::get('/cart', CartPage::class);
Route::get('/products/{slug}',ProductDetailPage::class);

Route::get('/checkout', CheckOutPage::class);
Route::get('/my-order', MyOrderPage::class);
Route::get('/my-order/{order}', MyOrderDetailPage::class);

Route::get('/login', LoginPage::class);
Route::get('/register', RegisterPage::class);
Route::get('/forgot', ForgotPasswordPage::class);
Route::get('/forgot', ResetPasswordPage::class);
Route::get('/reset-password', ResetPasswordPage::class);

Route::get('/success', SuccessPage::class);
Route::get('/cancel', CancelsPage::class);

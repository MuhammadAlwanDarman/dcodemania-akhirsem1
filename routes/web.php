<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\HomePage;
use App\Livewire\CategoriesPage;
use App\Livewire\ProductPage;
use App\Livewire\ProductDetailPage;
use App\Livewire\CartPage;
use App\Livewire\CheckOutPage;
use App\Livewire\SuccessPage;
use App\Livewire\MyOrderPage;
use App\Livewire\MyOrderDetailPage;
use App\Livewire\CancelPage;

use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\ForgotPage;
use App\Livewire\Auth\ResetPasswordPage;

Route::get('/', HomePage::class)->name('home');
Route::get('/categories', CategoriesPage::class)->name('categories');

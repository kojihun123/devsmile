<?php

namespace App\Providers;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->first();
            } else {
                $cart = Cart::where('session_id', session()->getId())->first();
            }

            $cartCount = $cart ? $cart->items()->sum('quantity') : 0;
            $view->with('cartCount', $cartCount);
        });
    }
}

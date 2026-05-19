<?php

namespace App\Http\Services;

use App\Models\Cart;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class CartService
{
  public function getCart(): ?Cart
  {
    if (Auth::check()) {
        return Cart::where('user_id', Auth::id())->first();
    }

    return Cart::where('session_id', session()->getId())->first();
  }

  public function getOrCreateCart(): Cart
  {
    if (Auth::check()) {
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }

    return Cart::firstOrCreate(['session_id' => session()->getId()]);
  } 

  public function validateForCheckout(Collection $items): ?string
  {
    if ($items->isEmpty()) {
        return '장바구니가 비어있습니다.';
    }

    $inactiveItems = $items->filter(fn($item) => $item->product->status === 'inactive');
    if ($inactiveItems->isNotEmpty()) {
        $names = $inactiveItems->map(fn($item) => $item->product->name)->join(', ');
        return "판매 중단된 상품이 있습니다: {$names}";
    }

    $outOfStockItems = $items->filter(fn($item) => $item->product->stock < $item->quantity);
    if ($outOfStockItems->isNotEmpty()) {
        $names = $outOfStockItems->map(fn($item) => $item->product->name)->join(', ');
        return "재고가 부족한 상품이 있습니다: {$names}";
    }

    return null;    
  }
}
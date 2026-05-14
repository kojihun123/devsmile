<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getCart();
        $items = $cart ? $cart->items()->with('product')->get() : collect();
        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        return view('carts.index', compact('items', 'total'));
    }

    public function add(Request $request)
    {        
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $product = Product::find($productId);
        $cart = $this->getOrCreateCart();
        $cartItem = $cart->items()->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            $currentQuantity = $cartItem->quantity + $quantity;
            $message = "{$product->name} 이(가) 총 {$currentQuantity}개 담겼습니다!";
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
            $message = "{$product->name} 이(가) {$quantity}개 담겼습니다!";
        }

        return response()->json(compact('message'));
    }

    private function getCart(): ?Cart
    {
        if (Auth::check()) {
            return Cart::where('user_id', Auth::id())->first();
        }

        return Cart::where('session_id', session()->getId())->first();
    }

    private function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(['user_id' => Auth::id()]);
        }

        return Cart::firstOrCreate(['session_id' => session()->getId()]);
    }
}

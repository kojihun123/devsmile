<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
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

        $currentQuantity = $cartItem ? $cartItem->quantity : 0;

        if ($currentQuantity + $quantity > $product->stock) {
            return response()->json(['message' => "'{$product->name}' 의 재고가 부족합니다. (재고: {$product->stock}개, 현재 담긴 수량: {$currentQuantity}개)"], 422);
        }

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            $message = "{$product->name} 이(가) 총 {$cartItem->fresh()->quantity}개 담겼습니다!";
        } else {
            $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
            $message = "{$product->name} 이(가) {$quantity}개 담겼습니다!";
        }

        return response()->json(compact('message'));        
    }

    public function update(Request $request, CartItem $cartItem)        
    {     
        $cart = $cartItem->cart;          

        if (Auth::check()) {
            abort_if($cart->user_id !== Auth::id(), 403);
        } else {
            abort_if($cart->session_id !== session()->getId(), 403);
        }

        $request->validate([
            'action' => ['required', 'in:increase,decrease'],
        ]);

        $action = $request->input('action');

        match($action) {
            'increase' => $cartItem->product->stock > $cartItem->quantity ? $cartItem->increment('quantity') : null,
            'decrease' => $cartItem->quantity > 1 ? $cartItem->decrement('quantity') : null,
        };        

        $items = $cart->items()->with('product')->get();
        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);

        return response()->json(compact('total'));
    }

    public function remove(CartItem $cartItem)
    {
        $cart = $cartItem->cart;

        if (Auth::check()) {
            abort_if($cart->user_id !== Auth::id(), 403);
        } else {
            abort_if($cart->session_id !== session()->getId(), 403);
        }

        $cartItem->delete();

        return redirect()->route('cart.index');
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

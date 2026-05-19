<?php

namespace App\Http\Controllers;

use App\Http\Services\CartService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function detail(Order $order)
    {
        abort_if($order->user_id !== Auth::id(), 403);
        $order->load('items', 'payment');
        return view('orders.detail', compact('order'));
    }

    public function lookupForm()
    {
        return view('orders.lookup');
    }

    public function lookup(Request $request)
    {
        $request->validate([
            'order_id' => ['required', 'integer'],
            'email'    => ['required', 'email'],
        ]);

        $order = Order::where('id', $request->order_id)
            ->where('guest_email', $request->email)
            ->whereNull('user_id')
            ->with('items', 'payment')
            ->first();

        if (!$order) {
            return back()->withErrors(['order_id' => '주문 정보를 찾을 수 없습니다.']);
        }

        return view('orders.detail', compact('order'));
    }

    public function create()
    {
        $cart = $this->cartService->getCart();
        $items = $cart ? $cart->items()->with('product')->get() : collect();        

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', '장바구니가 비어있습니다.');
        }

        $total = $items->sum(fn($item) => $item->product->price * $item->quantity);
        return view('orders.create', compact('items', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255'],
            'phone' => ['required', 'regex:/^[0-9\-+\s]+$/', 'max:20'],
        ]);

        $cart = $this->cartService->getCart();
        $items = $cart ? $cart->items()->with('product')->get() : collect();

        if ($error = $this->cartService->validateForCheckout($items)) {
            return redirect()->route('orders.create')->with('error', $error);
        }           
        
        $order = DB::transaction(function () use ($request, $cart, $items) {

            $order = Order::create([
                'user_id' => Auth::id(),
                'guest_name'  => $request->name,
                'guest_email' => $request->email,
                'guest_phone' => $request->phone,
                'total_amount' => $items->sum(fn($item) => $item->product->price * $item->quantity),
                'status'       => 'pending',                 
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->price,
                    'quantity'     => $item->quantity,
                ]);
            }

            $cart->items()->delete();

            return $order;
        });

        if (!Auth::check()) {
            session()->put('pending_order_id', $order->id);
        }

        return redirect()->route('orders.show', $order);
    }

    public function show(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->route('home')->with('error', '결제할 수 없는 주문입니다.');
        }

        if (Auth::check()) {
            abort_if($order->user_id !== Auth::id(), 403);
        } else {
            abort_if(session('pending_order_id') !== $order->id, 403);
        }

        $order->load('items');
        return view('orders.show', compact('order'));
    }
}

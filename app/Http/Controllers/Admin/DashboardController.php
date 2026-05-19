<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();
        $orderCount   = Order::count();
        $paidCount    = Order::where('status', 'paid')->count();

        return view('admin.dashboard', compact('productCount', 'orderCount', 'paidCount'));
    }
}

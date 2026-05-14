<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();

        $products = Product::with('category')
            ->where('status', 'active')
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_if($product->status === 'inactive', 404);

        return view('products.show', compact('product'));
    }
}

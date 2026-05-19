<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->category, fn($q) => $q->where('category_id', $request->category))
            ->latest()
            ->paginate(20);

        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id'    => ['required', 'exists:categories,id'],
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'price'          => ['required', 'integer', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'status'         => ['required', 'in:active,inactive'],
            'thumbnail'      => ['nullable', 'image', 'max:2048'],
            'delivery_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['category_id', 'name', 'description', 'price', 'stock', 'status']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        }

        if ($request->hasFile('delivery_image')) {
            $data['delivery_image'] = $request->file('delivery_image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', '상품이 등록되었습니다.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id'    => ['required', 'exists:categories,id'],
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string'],
            'price'          => ['required', 'integer', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'status'         => ['required', 'in:active,inactive'],
            'thumbnail'      => ['nullable', 'image', 'max:2048'],
            'delivery_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->only(['category_id', 'name', 'description', 'price', 'stock', 'status']);

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) Storage::disk('public')->delete($product->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail')->store('products', 'public');
        } elseif ($request->boolean('delete_thumbnail') && $product->thumbnail) {
            Storage::disk('public')->delete($product->thumbnail);
            $data['thumbnail'] = null;
        }

        if ($request->hasFile('delivery_image')) {
            if ($product->delivery_image) Storage::disk('public')->delete($product->delivery_image);
            $data['delivery_image'] = $request->file('delivery_image')->store('products', 'public');
        } elseif ($request->boolean('delete_delivery_image') && $product->delivery_image) {
            Storage::disk('public')->delete($product->delivery_image);
            $data['delivery_image'] = null;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', '상품이 수정되었습니다.');
    }

    public function destroy(Product $product)
    {
        if ($product->thumbnail) Storage::disk('public')->delete($product->thumbnail);
        if ($product->delivery_image) Storage::disk('public')->delete($product->delivery_image);

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', '상품이 삭제되었습니다.');
    }
}

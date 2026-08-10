<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category')->get();
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_category_id' => ['required', 'exists:product_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'unique:products,sku'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'active' => ['boolean'],
        ]);

        $product = Product::create($validated);

        return response()->json($product->load('category'), 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'product_category_id' => ['sometimes', 'exists:product_categories,id'],
        'name' => ['sometimes', 'string', 'max:255'],
        'sku' => ['sometimes', 'string', 'unique:products,sku,' . $product->id],
        'description' => ['nullable', 'string'],
        'price' => ['sometimes', 'numeric', 'min:0'],
        'stock' => ['sometimes', 'integer', 'min:0'],
        'active' => ['boolean'],
    ]);

    $product->update($validated);

    return response()->json($product->load('category'));
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
{
    $product->delete();

    return response()->json(null, 204);
}
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bar_code', 'like', "%{$search}%")
                  ->orWhere('alias', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only('search'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'unit' => 'required|in:Pcs,Box',
            'bar_code' => 'required|string|unique:products,bar_code',
            'alias' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
        ]);

        Product::create($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product added successfully.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'mrp' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'unit' => 'required|in:Pcs,Box',
            'bar_code' => 'required|string|unique:products,bar_code,' . $product->id,
            'alias' => 'nullable|string|max:100',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    // API endpoint for barcode/name/alias lookup (used by POS)
    public function search(Request $request)
    {
        $product = null;

        if ($barCode = $request->input('bar_code')) {
            $product = Product::where('bar_code', $barCode)->first();
        } elseif ($name = $request->input('name')) {
            $product = Product::where('name', $name)->first();
        } elseif ($alias = $request->input('alias')) {
            $product = Product::where('alias', $alias)->first();
        }

        if ($product) {
            return response()->json([
                'type' => 'Success',
                'id' => $product->id,
                'name' => $product->name,
                'bar_code' => $product->bar_code,
                'alias' => $product->alias,
                'mrp' => $product->mrp,
                'sale_price' => $product->sale_price,
                'unit' => $product->unit,
            ]);
        }

        return response()->json(['type' => 'Error'], 404);
    }

    // Return all products as JSON (for POS dropdown)
    public function list()
    {
        return response()->json(
            Product::orderBy('name')->get(['id', 'name', 'bar_code', 'alias', 'mrp', 'sale_price', 'unit'])
        );
    }
}

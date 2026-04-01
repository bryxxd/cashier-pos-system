<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get(['id', 'name', 'bar_code', 'alias', 'mrp', 'sale_price', 'unit']);

        return Inertia::render('Pos/Index', [
            'products' => $products,
        ]);
    }

    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'tendered_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'total_change' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.sale_price' => 'required|numeric|min:0',
        ]);

        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'total_amount' => $validated['total_amount'],
            'tendered_amount' => $validated['tendered_amount'],
            'discount' => $validated['discount'] ?? 0,
            'total_change' => $validated['total_change'],
            'month' => now()->month,
            'year' => now()->year,
        ]);

        foreach ($validated['items'] as $item) {
            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'sale_price' => $item['sale_price'],
            ]);
        }

        return redirect()->route('pos.index')
            ->with('success', 'Payment processed successfully. Transaction #' . $transaction->id);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'products' => Product::count(),
                'users' => User::count(),
                'transactions' => Transaction::count(),
            ],
        ]);
    }
}

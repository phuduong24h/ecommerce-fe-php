<?php
// app/Http/Controllers/CartController.php

namespace App\Http\Controllers\Cart;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);

        // DEMO DATA – chỉ chạy lần đầu
        if (empty($cart)) {
            Session::put('cart', [
                [
                    'name' => 'Mechanical Keyboard',
                    'price' => 89.99,
                    'qty' => 1,
                    'image' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=100&h=100&fit=crop'
                ],
                [
                    'name' => 'Wireless Mouse',
                    'price' => 29.99,
                    'qty' => 1,
                    'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=100&h=100&fit=crop'
                ]
            ]);
            $cart = Session::get('cart');
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);

        return view('cart.index', compact('cart', 'subtotal'));
    }

    public function update(Request $request)
    {
        $index = $request->index;
        $qty = max(1, $request->qty);

        $cart = Session::get('cart', []);
        if (isset($cart[$index])) {
            $cart[$index]['qty'] = $qty;
            Session::put('cart', $cart);
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);

        return response()->json([
            'success' => true,
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($subtotal + 9.60, 2),
            'item_total' => number_format($cart[$index]['price'] * $qty, 2)
        ]);
    }

    public function remove(Request $request)
    {
        $index = $request->index;
        $cart = Session::get('cart', []);

        if (isset($cart[$index])) {
            unset($cart[$index]);
            $cart = array_values($cart);
            Session::put('cart', $cart);
        }

        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['qty']);

        return response()->json([
            'success' => true,
            'subtotal' => number_format($subtotal, 2),
            'total' => number_format($subtotal + 9.60, 2),
            'item_count' => count($cart)
        ]);
    }
}
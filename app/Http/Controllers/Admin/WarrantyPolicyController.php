<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class WarrantyPolicyController extends Controller
{
    public function index()
    {
        // DỮ LIỆU GIẢ - KHÔNG DÙNG DB
        $policies = [
            ['id' => 1, 'name' => 'Standard Warranty', 'duration_days' => 365, 'description' => '1 year warranty for all standard products'],
            ['id' => 2, 'name' => 'Extended Warranty', 'duration_days' => 730, 'description' => '2 year warranty with enhanced coverage'],
            ['id' => 3, 'name' => 'Premium Warranty', 'duration_days' => 1095, 'description' => '3 year comprehensive warranty'],
            ['id' => 4, 'name' => 'Lifetime Warranty', 'duration_days' => 3650, 'description' => '10 year extended lifetime warranty'],
        ];

        return view('admin.warranty_policies.index', compact('policies'));
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WarrantyClaim;

class WarrantyClaimController extends Controller
{
    // Hiển thị danh sách warranty claims
    public function index(Request $request)
    {
        $query = WarrantyClaim::query();

        // Lọc theo status nếu có
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tìm kiếm theo product_name hoặc customer_name hoặc id
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $claims = $query->orderBy('date_submitted', 'desc')->paginate(10);

        return view('admin.warranty.index', compact('claims'));
    }

    // Xem chi tiết claim
    public function show(WarrantyClaim $claim)
    {
        return view('admin.warranty_claims.show', compact('claim'));
    }

    // Cập nhật status claim
    public function updateStatus(Request $request, WarrantyClaim $claim)
    {
        $request->validate([
            'status' => 'required|in:pending,in-progress,approved,rejected'
        ]);

        $claim->status = $request->status;
        $claim->save();

        return redirect()->route('admin.warranty_claims.index')->with('success', 'Status updated successfully');
    }
}

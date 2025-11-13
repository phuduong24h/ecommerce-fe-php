<?php

namespace App\Http\Controllers\Warranty;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\WarrantyClaim;

class WarrantyController extends Controller
{
    public function index()
    {
        // Lấy danh sách claim để hiển thị trong view
        $claims = WarrantyClaim::latest()->get();

        return view('admin.warranty.warranty', compact('claims'));
    }

    // =========================================
    // FIX TÊN METHOD CHO KHỚP ROUTE
    // =========================================
    public function checkSerial(Request $request)
    {
        $request->validate([
            'serial_number' => 'required'
        ]);

        $response = Http::get("http://localhost:3000/api/v1/warranty/check", [
            'serial' => $request->serial_number
        ]);

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'Không tìm thấy mã serial!']);
        }

        return back()->with('checkResult', $response->json());
    }

    public function submitClaim(Request $request)
    {
        $request->validate([
            'serial_number' => 'required',
            'description' => 'required'
        ]);

        // Gửi lên NodeJS API
        $response = Http::post("http://localhost:3000/api/v1/warranty/claim", [
            'serial' => $request->serial_number,
            'description' => $request->description
        ]);

        if ($response->failed()) {
            return back()->withErrors(['msg' => 'Gửi yêu cầu bảo hành thất bại!']);
        }

        // Lưu vào DB Laravel
        WarrantyClaim::create([
            'serial_number' => $request->serial_number,
            'description' => $request->description
        ]);

        return back()->with('success', 'Gửi yêu cầu bảo hành thành công!');
    }
}

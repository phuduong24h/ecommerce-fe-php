<?php
// app/Http/Controllers/Admin/SettingController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function privacy()
    {
        return view('admin.settings.privacy');
    }

    public function update(Request $request)
    {
        // Lưu cài đặt (cache, file, DB)
        cache()->forever('site_name', $request->site_name);
        cache()->forever('contact_email', $request->contact_email);

        return back()->with('success', 'Cài đặt đã được lưu!');
    }
}
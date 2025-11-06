@extends('layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold">Người Dùng</h1>
        <!-- Nút Thêm Người Dùng (tạm thời chưa có route) -->
        <a href="#" class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-4 py-2 rounded hover:from-cyan-600 hover:to-blue-600 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm Người Dùng
        </a>
    </div>

    <!-- Container bảng + search -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <!-- Search bar -->
        <div class="p-4 border-b border-gray-200 relative">
            <input type="text" placeholder="Tìm kiếm người dùng..." class="w-full pl-10 pr-4 py-2 rounded border border-gray-300 focus:ring-2 focus:ring-cyan-400 focus:border-cyan-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z"/>
            </svg>
        </div>

        <!-- Bảng -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tên</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vai trò</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $users = [
                            ['id'=>1,'name'=>'John Doe','email'=>'john@example.com','role'=>'customer','status'=>'active'],
                            ['id'=>2,'name'=>'Jane Smith','email'=>'jane@example.com','role'=>'customer','status'=>'active'],
                            ['id'=>3,'name'=>'Admin User','email'=>'admin@example.com','role'=>'admin','status'=>'active'],
                            ['id'=>4,'name'=>'Support Agent','email'=>'support@example.com','role'=>'support','status'=>'active'],
                            ['id'=>5,'name'=>'Bob Johnson','email'=>'bob@example.com','role'=>'customer','status'=>'inactive'],
                        ];
                        $roleClasses = ['admin'=>'bg-red-100 text-red-700','customer'=>'bg-blue-100 text-blue-700','support'=>'bg-purple-100 text-purple-700'];
                        $statusClasses = ['active'=>'bg-emerald-100 text-emerald-700','inactive'=>'bg-gray-100 text-gray-700','suspended'=>'bg-red-100 text-red-700'];
                    @endphp

                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-3">{{ $user['id'] }}</td>
                            <td class="px-6 py-3">{{ $user['name'] }}</td>
                            <td class="px-6 py-3">{{ $user['email'] }}</td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded text-xs {{ $roleClasses[$user['role']] }}">
                                    {{ $user['role'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <span class="px-2 py-1 rounded text-xs {{ $statusClasses[$user['status']] }}">
                                    {{ $user['status'] }}
                                </span>
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex gap-2">
                                    <button class="text-blue-600 hover:text-blue-700 hover:bg-blue-50 px-2 py-1 rounded">Sửa</button>
                                    <button class="text-red-600 hover:text-red-700 hover:bg-red-50 px-2 py-1 rounded">Xóa</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

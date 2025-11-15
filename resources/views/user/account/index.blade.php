{{-- resources/views/user/account/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Tài Khoản Của Tôi</h1>
        <p class="text-gray-500 mt-1">Quản lý đơn hàng và sản phẩm bảo hành</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex gap-4 mb-6">
        <a href="{{ route('account.orders') }}" 
           class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-lg text-sm font-medium transition-all
                  {{ $activeTab === 'orders' ? 'bg-white shadow-md text-gray-900' : 'bg-gray-100 text-gray-600 hover:bg-white hover:shadow-sm' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            Đơn Hàng Của Tôi
        </a>
        
        <a href="{{ route('account.warranty') }}" 
           class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-lg text-sm font-medium transition-all
                  {{ $activeTab === 'warranty' ? 'bg-white shadow-md text-gray-900' : 'bg-gray-100 text-gray-600 hover:bg-white hover:shadow-sm' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Bảo Hành Của Tôi
        </a>
        
        <a href="{{ route('account.profile') }}" 
           class="flex-1 flex items-center justify-center gap-2 px-6 py-4 rounded-lg text-sm font-medium transition-all
                  {{ $activeTab === 'profile' ? 'bg-white shadow-md text-gray-900' : 'bg-gray-100 text-gray-600 hover:bg-white hover:shadow-sm' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Hồ Sơ
        </a>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        @if($activeTab === 'orders')
            @include('user.account._orders')
        @elseif($activeTab === 'warranty')
            @include('user.account._warranty')
        @elseif($activeTab === 'profile')
            @include('user.account._profile')
        @endif
    </div>
</div>
@endsection
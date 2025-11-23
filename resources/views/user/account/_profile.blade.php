{{-- resources/views/user/account/_profile.blade.php --}}

<div class="max-w-2xl">
    <h2 class="text-xl font-semibold text-gray-900 mb-6">Thông Tin Cá Nhân</h2>

    <!-- User Avatar & Name -->
    <div class="flex items-center gap-6 mb-8 pb-8 border-b">
        <div class="w-20 h-20 bg-cyan-100 rounded-full flex items-center justify-center">
            <svg class="w-10 h-10 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <h3 class="text-2xl font-bold text-gray-900">{{ $user['name'] ?? 'Chưa cập nhật' }}</h3>
            <p class="text-gray-500">{{ $user['email'] ?? 'Chưa cập nhật' }}</p>
        </div>
    </div>

    <!-- User Details -->
    <div class="space-y-6">
        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Điện Thoại</label>
            <p class="text-lg text-gray-900">{{ $user['phone'] ?? 'Chưa cập nhật' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Địa Chỉ</label>
            <p class="text-lg text-gray-900">{{ $user['address'] ?? 'Chưa cập nhật' }}</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Thành Viên Từ</label>
            <p class="text-lg text-gray-900">{{ $user['member_since'] ?? 'Chưa cập nhật' }}</p>
        </div>
    </div>

    <!-- Edit Button -->
    <!-- <div class="mt-8 pt-6 border-t">
        <button class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-medium hover:shadow-lg transition-all">
            Chỉnh Sửa Thông Tin
        </button>
    </div> -->
</div>
@extends('layouts.warranty')

@section('title', 'Bảo Hành Sản Phẩm')

@section('content')
<div class="bg-white shadow rounded-lg p-6">
    <h2 class="text-2xl font-bold text-blue-700 mb-2">Bảo Hành Sản Phẩm</h2>
    <p class="text-gray-600 mb-6">Kiểm tra tình trạng bảo hành và yêu cầu bảo hành</p>

    <!-- Tabs -->
    <div class="flex bg-gray-100 rounded-lg p-1 mb-6">
        <button id="checkTab" class="flex-1 py-2 font-medium text-sm rounded-md bg-gradient-to-r from-cyan-500 to-blue-500 text-white transition-all">Kiểm Tra Bảo Hành</button>
        <button id="requestTab" class="flex-1 py-2 font-medium text-sm rounded-md text-gray-600 hover:text-pink-600 transition-all">Yêu Cầu Bảo Hành</button>
    </div>

    <!-- Kiểm tra bảo hành -->
    <div id="checkSection" class="space-y-6">
        <div>
            <label class="block text-gray-700 font-medium mb-2">Nhập Số Serial Sản Phẩm</label>
            <input type="text" class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500" placeholder="Ví dụ: SN-12345-ABCD">
            <p class="text-gray-400 text-sm mt-1">Tìm số serial ở mặt sau hoặc mặt dưới sản phẩm</p>
        </div>

        <button class="bg-gradient-to-r from-cyan-500 to-blue-500 text-white px-5 py-2 rounded-md font-medium shadow hover:opacity-90">
            <i class="fas fa-search mr-1"></i> Kiểm Tra
        </button>

        <!-- Kết quả -->
        <div class="border-t pt-6 mt-6">
            <h3 class="font-semibold text-lg mb-4">My Warranty Claims</h3>
            <div class="space-y-4">
                <div class="border rounded-lg p-4 hover:shadow transition">
                    <div class="flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">Wireless Mouse</h4>
                        <span class="text-green-600 text-sm font-medium bg-green-100 px-2 py-1 rounded">approved</span>
                    </div>
                    <p class="text-sm text-gray-600">Claim ID: WC-001</p>
                    <p class="text-gray-500 text-sm">Left click button not responding properly</p>
                    <p class="text-sm text-blue-600 mt-1">Estimated resolution: 2025-10-30</p>
                </div>

                <div class="border rounded-lg p-4 hover:shadow transition">
                    <div class="flex justify-between items-center">
                        <h4 class="font-semibold text-gray-800">Mechanical Keyboard</h4>
                        <span class="text-blue-600 text-sm font-medium bg-blue-100 px-2 py-1 rounded">in-progress</span>
                    </div>
                    <p class="text-sm text-gray-600">Claim ID: WC-002</p>
                    <p class="text-gray-500 text-sm">Some keys are not registering keystrokes</p>
                    <p class="text-sm text-blue-600 mt-1">Estimated resolution: 2025-11-02</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Yêu cầu bảo hành -->
    <div id="requestSection" class="hidden">
        <h3 class="font-semibold text-lg mb-4">Submit a Warranty Claim</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Product Serial Number</label>
                <input type="text" class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500" placeholder="SN-12345-ABCD">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Describe the Issue</label>
                <textarea class="w-full border rounded-md px-4 py-2 focus:ring-2 focus:ring-pink-500 focus:border-pink-500" rows="3" placeholder="Please describe the problem you're experiencing with your product..."></textarea>
            </div>

            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-3 rounded-md text-sm">
                ⚠️ Please ensure your product serial number is correct. Claims are typically processed within 3–5 business days.
            </div>

            <button class="w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white py-2 rounded-md font-medium hover:opacity-90 transition">
                Submit Claim
            </button>
        </div>
    </div>
</div>

<script>
    const checkTab = document.getElementById('checkTab');
    const requestTab = document.getElementById('requestTab');
    const checkSection = document.getElementById('checkSection');
    const requestSection = document.getElementById('requestSection');

    checkTab.addEventListener('click', () => {
        checkTab.classList.add('bg-gradient-to-r', 'from-cyan-500', 'to-blue-500', 'text-white');
        requestTab.classList.remove('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');
        checkSection.classList.remove('hidden');
        requestSection.classList.add('hidden');
    });

    requestTab.addEventListener('click', () => {
        requestTab.classList.add('bg-gradient-to-r', 'from-purple-500', 'to-pink-500', 'text-white');
        checkTab.classList.remove('bg-gradient-to-r', 'from-cyan-500', 'to-blue-500', 'text-white');
        requestSection.classList.remove('hidden');
        checkSection.classList.add('hidden');
    });
</script>
@endsection

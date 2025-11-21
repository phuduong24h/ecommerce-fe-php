@extends('layouts.app')

@section('title', 'Bảo Hành Sản Phẩm')

@section('content')

    <div class="max-w-5xl mx-auto px-4 py-8">

        <!-- HEADER -->
        <div class="flex items-center gap-4 mb-8">
            <div class="bg-blue-100 text-blue-600 p-3 rounded-2xl">
                <i class="fas fa-shield-alt text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bảo Hành Sản Phẩm</h1>
                <p class="text-gray-500 text-sm">Kiểm tra tình trạng bảo hành và yêu cầu bảo hành</p>
            </div>
        </div>

        <!-- TABS -->
        <div class="flex bg-gray-100 rounded-full p-1 mb-8">

            <button id="tabCheck"
                class="flex-1 py-3 rounded-full text-center font-semibold text-white
                   bg-gradient-to-r from-blue-600 to-blue-400 shadow">
                🔍 Kiểm Tra Bảo Hành
            </button>

            <button id="tabRequest"
                class="flex-1 py-3 rounded-full text-center font-semibold text-gray-600 hover:bg-gray-200 transition">
                📄 Yêu Cầu Bảo Hành
            </button>
        </div>

        <!-- =================== -->
        <!-- CHECK WARRANTY TAB -->
        <!-- =================== -->
        <div id="checkSection">

            <!-- Search Serial -->
            <div class="border border-blue-100 bg-blue-50 rounded-2xl p-6 mb-8">
                <form method="POST" action="{{ route('warranty.check') }}">
                    @csrf
                    <div class="flex bg-white border rounded-full px-5 py-3 shadow-sm items-center">
                        <input name="serial_number" placeholder="Ví dụ: SN-12345-ABCD"
                            class="flex-1 outline-none bg-transparent">
                        <button class="ml-3 px-5 py-2 rounded-full bg-blue-600 text-white font-semibold">
                            Kiểm Tra
                        </button>
                    </div>
                </form>
            </div>

            <!-- RESULT -->
            @if (session('productInfo'))
                <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-8">

                    <p class="font-semibold text-green-700 mb-2 text-lg">🔎 Kết quả kiểm tra</p>

                    <p><strong>Tên sản phẩm:</strong> {{ session('productInfo')['name'] }}</p>
                    <p><strong>Serial:</strong> {{ session('productInfo')['serial'] }}</p>
                    <p><strong>Order ID:</strong> {{ session('productInfo')['orderId'] }}</p>
                    <p><strong>Ngày mua:</strong> {{ session('productInfo')['purchasedAt'] }}</p>

                    <hr class="my-3">

                    <!-- FULL WARRANTY HISTORY -->
                    <p class="text-gray-800 font-bold mb-2 text-lg">📌 Lịch sử bảo hành:</p>

                    @php
                        $serialClaims = session('serialClaims') ?? [];
                    @endphp

                    @foreach ($serialClaims as $c)
                        <div class="bg-white border rounded-xl p-4 mb-3 shadow-sm">
                            <p><strong>Trạng thái:</strong> {{ $c['status'] }}</p>
                            <p><strong>Lý do:</strong> {{ $c['issueDesc'] }}</p>
                            <p><strong>Ngày gửi:</strong> {{ date('d/m/Y', strtotime($c['createdAt'])) }}</p>
                        </div>
                    @endforeach

                </div>
            @endif


            <!-- PURCHASED PRODUCT LIST -->
            <h2 class="text-xl font-semibold mb-4">Sản phẩm bạn đã mua</h2>

            @foreach ($purchased as $p)
                <div class="bg-white border rounded-xl p-5 mb-5 shadow">

                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-lg font-semibold">{{ $p['productName'] }}</p>
                            <p class="text-gray-500 text-sm">Order ID: {{ $p['orderId'] }}</p>
                            <p class="text-gray-500 text-sm">Ngày mua: {{ $p['purchasedAt'] }}</p>
                        </div>
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm">
                            SL: {{ $p['quantity'] }}
                        </span>
                    </div>

                    <div class="mt-3">
                        <p class="font-medium text-sm">Serial:</p>

                        @if ($p['serial'])
                            <button
                                class="use-serial-btn px-4 py-1.5 rounded-full bg-gray-100 hover:bg-blue-50 border text-gray-700 text-sm"
                                data-serial="{{ $p['serial'] }}">
                                {{ $p['serial'] }}
                            </button>
                        @else
                            <p class="italic text-gray-500">Không có serial</p>
                        @endif

                        @if ($p['latestClaim'])
                            <p class="mt-2 text-green-700 text-sm">
                                <strong>Bảo hành gần nhất:</strong> {{ $p['latestClaim']['description'] }}
                            </p>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>

        <!-- =============== -->
        <!-- REQUEST TAB     -->
        <!-- =============== -->
        <div id="requestSection" class="hidden">

            <div class="border border-gray-200 bg-white rounded-2xl p-6 shadow">

                <h2 class="text-xl font-bold mb-4">Gửi yêu cầu bảo hành</h2>

                <form method="POST" action="{{ route('warranty.claim') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="font-semibold mb-1 block">Serial</label>
                        <input id="claim_serial_input" name="serial_number"
                            class="w-full bg-gray-50 border rounded-xl px-4 py-3">
                    </div>

                    <div>
                        <label class="font-semibold mb-1 block">Mô tả lỗi</label>
                        <textarea name="description" rows="4" class="w-full bg-gray-50 border rounded-xl px-4 py-3"></textarea>
                    </div>

                    <button class="w-full py-3 text-white rounded-xl bg-gradient-to-r from-purple-500 to-pink-500">
                        Gửi yêu cầu
                    </button>
                </form>
            </div>

            <h2 class="text-xl font-bold mt-8 mb-3">Lịch sử yêu cầu</h2>

            @foreach ($claimList as $c)
                <div class="bg-white border rounded-xl p-5 mb-5 shadow">
                    <p class="text-lg font-semibold">{{ $c['productName'] }}</p>
                    <p><strong>Serial:</strong> {{ $c['productSerial'] }}</p>
                    <p><strong>Lý do:</strong> {{ $c['issueDesc'] }}</p>
                    <p><strong>Trạng thái:</strong> {{ $c['status'] }}</p>
                    <p class="text-gray-500 text-sm">{{ date('d/m/Y', strtotime($c['createdAt'])) }}</p>
                </div>
            @endforeach

        </div>

    </div>

    <script>
        const tabCheck = document.getElementById("tabCheck");
        const tabRequest = document.getElementById("tabRequest");
        const checkSection = document.getElementById("checkSection");
        const requestSection = document.getElementById("requestSection");

        function switchTab(tab) {
            if (tab === "check") {
                checkSection.classList.remove("hidden");
                requestSection.classList.add("hidden");
            } else {
                requestSection.classList.remove("hidden");
                checkSection.classList.add("hidden");
            }
        }

        tabCheck.onclick = () => switchTab("check");
        tabRequest.onclick = () => switchTab("request");

        document.querySelectorAll(".use-serial-btn").forEach(btn => {
            btn.addEventListener("click", function() {
                document.getElementById("claim_serial_input").value = this.dataset.serial;
                switchTab("request");
            });
        });
    </script>

@endsection

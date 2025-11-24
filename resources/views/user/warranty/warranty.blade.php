@extends('layouts.app')

@section('title', 'Bảo Hành Sản Phẩm')

@section('content')
  <div class="max-w-6xl mx-auto px-6 py-10">

    <!-- Header -->
    <div class="flex items-center gap-4 mb-8">
      <div class="p-3 rounded-2xl bg-gradient-to-br from-blue-100 to-blue-50">
        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M12 1.5l6 3v4.875A8.25 8.25 0 0112 20.25 8.25 8.25 0 016 9.375V4.5l6-3z"></path>
        </svg>
      </div>
      <div>
        <h1 class="text-2xl font-semibold text-gray-900">Bảo Hành Sản Phẩm</h1>
        <p class="text-gray-500">Kiểm tra tình trạng bảo hành và gửi yêu cầu bảo hành</p>
      </div>
    </div>

    <!-- Tabs -->
    <div class="mb-6 flex items-center gap-4">
      <button id="tabCheck"
        class="flex-1 py-3 rounded-full text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-400 shadow-md">
        🔍 Kiểm Tra Bảo Hành
      </button>
      <button id="tabRequest"
        class="flex-1 py-3 rounded-full text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
        📄 Yêu Cầu Bảo Hành
      </button>
    </div>

    <!-- Check Section -->
    <div id="checkSection">

      <!-- ⭐⭐⭐ Chọn Serial dạng select ⭐⭐⭐ -->
      <div class="rounded-xl border border-blue-100 p-6 mb-6 bg-white shadow-sm">
        <h3 class="text-lg font-semibold mb-3">Chọn Serial Sản Phẩm</h3>

        <form method="POST" action="{{ route('warranty.check') }}" class="flex gap-3 items-center">
          @csrf
          <select name="serial_number"
            class="flex-1 px-4 py-3 rounded-lg bg-gray-100 border border-transparent focus:outline-none focus:ring-2 focus:ring-blue-200">
            @foreach($userSerials as $us)
              @if(!empty($us['serialCode']))
                <option value="{{ $us['serialCode'] }}">
                  {{ $us['productName'] ?? 'Sản phẩm' }} - {{ $us['serialCode'] }}
                </option>
              @endif
            @endforeach
          </select>


          <button class="px-4 py-2 rounded-lg bg-gradient-to-r from-blue-400 to-teal-300 text-white font-semibold">
            <svg class="inline-block w-4 h-4 mr-2 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35M10.5 18A7.5 7.5 0 1010.5 3a7.5 7.5 0 000 15z" />
            </svg>
            Kiểm tra
          </button>
        </form>
        @if(session('productInfo'))
          @php $p = session('productInfo'); @endphp
          <div class="rounded-lg border bg-white p-5 shadow-sm mt-4">
            <h4 class="font-semibold text-lg mb-2">Thông tin bảo hành</h4>
            <p><strong>Serial:</strong> {{ $p['serialCode'] }}</p>
            <p><strong>Sản phẩm:</strong> {{ $p['productName'] }}</p>
            <p><strong>Ngày mua:</strong> {{ $p['soldAt'] }}</p>
            <p><strong>Trạng thái bảo hành:</strong>
              <span class="{{ $p['warrantyStatus'] == 'Còn hạn' ? 'text-green-600' : 'text-red-600' }}">
                {{ $p['warrantyStatus'] }}
              </span>
            </p>
            <p><strong>Số ngày còn lại:</strong> {{ max(0, $p['daysLeft'] ?? 0) }} ngày</p>
          </div>
        @endif
        <p class="text-sm text-gray-500 mt-3">Chọn serial của sản phẩm để kiểm tra bảo hành</p>
      </div>
      <!-- ⭐⭐⭐ End chọn Serial ⭐⭐⭐ -->

      <div class="mb-6">
        <h3 class="text-xl font-semibold mb-4">Yêu cầu bảo hành của bạn</h3>
        @if($claims->count())
          <div class="space-y-4">
            @foreach($claims as $c)
              <div class="rounded-lg border bg-white shadow-sm p-5 flex justify-between items-start">
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <span class="text-lg font-semibold">{{ $c['productName'] ?? 'Sản phẩm' }}</span>
                    @php
                      $status = $c['status'] ?? 'UNKNOWN';
                      $statusClass = match ($status) {
                        'PENDING' => 'bg-yellow-100 text-yellow-800',
                        'APPROVED' => 'bg-green-100 text-green-800',
                        'REJECTED' => 'bg-red-100 text-red-800',
                        default => 'bg-gray-100 text-gray-600',
                      };
                    @endphp
                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $statusClass }}">
                      {{ $status }}
                    </span>
                  </div>

                  <div class="text-sm text-gray-500">Số Serial: {{ $c['productSerial'] ?? 'Không có' }}</div>
                  <div class="text-sm text-gray-500">Claim ID: {{ $c['id'] ?? ($c['claimId'] ?? '-') }}</div>
                  <p class="mt-2 text-gray-600">Vấn đề: {!! nl2br(e($c['issueDesc'] ?? $c['description'] ?? '')) !!}</p>
                </div>

                <div class="text-sm text-gray-400">
                  {{ !empty($c['createdAt']) ? date("Y-m-d", strtotime($c['createdAt'])) : '' }}
                </div>
              </div>
            @endforeach
          </div>

          {{-- Phân trang --}}
          <div class="mt-4">
            {{ $claims->links() }}
          </div>
        @else
          <div class="rounded-lg border bg-white shadow-sm p-5 text-gray-500">Không có sản phẩm đã mua.</div>
        @endif

      </div>
    </div>

    <!-- Request Section -->
    <div id="requestSection" class="hidden">
      <div class="rounded-xl border bg-white p-6 shadow-sm">
        <h3 class="text-lg font-semibold mb-4">Gửi yêu cầu bảo hành</h3>

        <form method="POST" action="{{ route('warranty.claim') }}" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn sản phẩm (Serial)</label>
            <select id="claim_serial_input" name="serial_number">
              @foreach($userSerials as $us)
                @if(!empty($us['serialCode']))
                  <option value="{{ $us['serialCode'] }}" data-product="{{ $us['productId'] }}"
                    data-order="{{ $us['orderId'] }}" data-name="{{ $us['productName'] }}"
                    data-purchased="{{ $us['purchasedAt'] ?? '' }}">
                    {{ $us['productName'] }} - {{ $us['serialCode'] }}
                  </option>
                @endif
              @endforeach
            </select>

            <div class="mt-2 text-sm text-gray-600">
              <p><strong>Số serial:</strong> <span id="selectedSerialLabel">-</span></p>
              <p><strong>Mã sản phẩm:</strong> <span id="selectedProductIdLabel">-</span></p>
              <p><strong>Mã đơn hàng</strong> <span id="selectedOrderIdLabel">-</span></p>
              <p><strong>Tên sản phẩm:</strong> <span id="selectedProductNameLabel">-</span></p>
              <p><strong>Ngày mua:</strong> <span id="selectedPurchasedAtLabel">-</span></p>
            </div>
          </div>

          <input type="hidden" name="productId" id="productIdInput">
          <input type="hidden" name="orderId" id="orderIdInput">
          <input type="hidden" name="productName" id="productNameInput">
          <input type="hidden" name="purchasedAt" id="purchasedAtInput">
          <input type="hidden" name="productSerial" id="productSerialInput">

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả vấn đề</label>
            <textarea name="description" rows="4" placeholder="Mô tả vấn đề sản phẩm..."
              class="w-full px-4 py-3 rounded-lg bg-gray-100 border border-transparent focus:outline-none focus:ring-2 focus:ring-pink-200"></textarea>
          </div>

          <div class="bg-yellow-50 border border-yellow-100 p-3 rounded-md text-sm text-yellow-800">
            Vui lòng đảm bảo serial sản phẩm chính xác. Thời gian xử lý yêu cầu thường 3-5 ngày làm việc.
          </div>

          <button type="submit"
            class="w-full py-3 rounded-lg bg-gradient-to-r from-pink-500 to-purple-500 text-white font-semibold">
            Gửi yêu cầu bảo hành
          </button>
        </form>
      </div>
    </div>

  </div>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const tabCheck = document.getElementById("tabCheck");
    const tabRequest = document.getElementById("tabRequest");
    const checkSection = document.getElementById("checkSection");
    const requestSection = document.getElementById("requestSection");
    const selectSerial = document.getElementById("claim_serial_input");
    const productIdInput = document.getElementById("productIdInput");
    const orderIdInput = document.getElementById("orderIdInput");
    const productNameInput = document.getElementById("productNameInput");
    const purchasedAtInput = document.getElementById("purchasedAtInput");
    const productSerialInput = document.getElementById("productSerialInput");

    const selectedSerialLabel = document.getElementById('selectedSerialLabel');
    const selectedProductIdLabel = document.getElementById('selectedProductIdLabel');
    const selectedOrderIdLabel = document.getElementById('selectedOrderIdLabel');
    const selectedProductNameLabel = document.getElementById('selectedProductNameLabel');
    const selectedPurchasedAtLabel = document.getElementById('selectedPurchasedAtLabel');

    function switchTab(tab) {
      if (tab === "check") {
        checkSection.classList.remove("hidden");
        requestSection.classList.add("hidden");
        tabCheck.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-400', 'text-white');
        tabCheck.classList.remove('bg-gray-100', 'text-gray-700');
        tabRequest.classList.remove('bg-gradient-to-r', 'from-pink-500', 'to-purple-500', 'text-white');
        tabRequest.classList.add('bg-gray-100', 'text-gray-700');
      } else {
        checkSection.classList.add("hidden");
        requestSection.classList.remove("hidden");
        tabRequest.classList.add('bg-gradient-to-r', 'from-pink-500', 'to-purple-500', 'text-white');
        tabRequest.classList.remove('bg-gray-100', 'text-gray-700');
        tabCheck.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-blue-400', 'text-white');
        tabCheck.classList.add('bg-gray-100', 'text-gray-700');
      }
    }

    tabCheck.onclick = () => switchTab("check");
    tabRequest.onclick = () => switchTab("request");

    function updateLabels() {
      const selectedOption = selectSerial.options[selectSerial.selectedIndex];
      selectedSerialLabel.textContent = selectedOption.value;
      selectedProductIdLabel.textContent = selectedOption.dataset.product || '-';
      selectedOrderIdLabel.textContent = selectedOption.dataset.order || '-';
      selectedProductNameLabel.textContent = selectedOption.dataset.name || '-';
      selectedPurchasedAtLabel.textContent = selectedOption.dataset.purchased || '-';
    }

    selectSerial.addEventListener('change', function () {
      const selectedOption = this.options[this.selectedIndex];
      productIdInput.value = selectedOption.dataset.product || '';
      orderIdInput.value = selectedOption.dataset.order || '';
      productNameInput.value = selectedOption.dataset.name || selectedOption.text.split(' - ')[0];
      purchasedAtInput.value = selectedOption.dataset.purchased || '';
      productSerialInput.value = this.value;
      updateLabels();
    });

    // SweetAlert2 messages
    @if(session('success'))
      Swal.fire({
        icon: 'success',
        title: 'Thành công',
        text: '{{ session("success") }}',
        confirmButtonText: 'OK'
      });
    @endif

    @if($errors->any())
      Swal.fire({
        icon: 'error',
        title: 'Lỗi',
        html: '{!! implode("<br>", $errors->all()) !!}',
        confirmButtonText: 'OK'
      });
    @endif
  </script>

@endsection
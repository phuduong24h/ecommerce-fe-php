// public/js/cart.js

document.addEventListener('DOMContentLoaded', function () {

    // =============================
    //  + TĂNG SỐ LƯỢNG
    // =============================
    document.querySelectorAll('.btn-plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('.cart-item');
            updateQty(row, +1);
        });
    });

    // =============================
    //  - GIẢM SỐ LƯỢNG
    // =============================
    document.querySelectorAll('.btn-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('.cart-item');
            updateQty(row, -1);
        });
    });

    // =============================
    //  🗑 XOÁ SẢN PHẨM
    // =============================
    document.querySelectorAll('.trash-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const row = btn.closest('.cart-item');
            removeItem(row);
        });
    });
});


// ============================
// 📌 CẬP NHẬT BADGE ICON CART
// ============================
function updateCartBadge(count) {
    const badge = document.getElementById("cart-count");
    if (!badge) return;

    if (count > 0) {
        badge.classList.remove("hidden");
        badge.textContent = count;
    } else {
        badge.classList.add("hidden");
    }
}


// ============================
// 📌 CẬP NHẬT SỐ LƯỢNG
// ============================
function updateQty(row, change) {

    const index = row.dataset.index;
    let qty = parseInt(row.querySelector('.quantity-input').value);
    qty += change;

    if (qty < 1) return;

    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            index,
            qty   // PHP sẽ gửi sang NodeJS dưới dạng "quantity"
        })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) return;

        // cập nhật số lượng
        row.querySelector('.quantity-input').value = qty;

        // cập nhật total item (API đã trả về dạng '45.000đ')
        row.querySelector('.item-total').textContent = data.item_total;

        // cập nhật subtotal & total
        document.getElementById('subtotal').textContent = data.subtotal;
        document.getElementById('total').textContent = data.total;

        // cập nhật badge
        updateCartBadge(data.cart_count);
    })
    .catch(err => {
        console.error("Lỗi cập nhật:", err);
    });
}


// ============================
// 📌 XOÁ SẢN PHẨM KHỎI GIỎ
// ============================
function removeItem(row) {

    const index = row.dataset.index;

    fetch('/cart/remove', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ index })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            alert(data.message || 'Lỗi khi xóa sản phẩm');
            return;
        }

        // 1. Xóa phần tử khỏi DOM
        row.remove();

        // ---------------------------------------------------------
        // 2. 🔥 BƯỚC QUAN TRỌNG: CẬP NHẬT LẠI INDEX 🔥
        // Vì PHP array_splice đã đánh lại số thứ tự (0, 1, 2...),
        // nên ta phải cập nhật lại data-index của các dòng còn lại
        // ---------------------------------------------------------
        const remainingRows = document.querySelectorAll('.cart-item');
        remainingRows.forEach((item, newIndex) => {
            item.dataset.index = newIndex; // Gán lại index mới: 0, 1, 2...
        });

        // 3. Cập nhật badge
        updateCartBadge(data.cart_count);

        // 4. Nếu hết sản phẩm → giỏ hàng trống
        if (data.item_count === 0 || remainingRows.length === 0) {
            showEmptyCart();
        } else {
            // Cập nhật lại tiền nong
            document.getElementById('subtotal').textContent = data.subtotal;
            document.getElementById('total').textContent = data.total;
        }
    })
    .catch(err => {
        console.error("Lỗi xoá:", err);
    });
}


// ============================
// 📌 HIỂN THỊ GIAO DIỆN GIỎ TRỐNG
// ============================
function showEmptyCart() {
    const parentRow = document.querySelector('.row.g-4');
    const summary = document.getElementById('cart-summary');

    if (summary) summary.remove();

    parentRow.innerHTML = `
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-center bg-white rounded-3 shadow-sm" 
                 style="min-height: 70vh; padding: 40px 20px;">
                <div class="text-center">
                    <div class="d-flex justify-content-center mb-4">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                            <circle cx="60" cy="60" r="60" fill="#eef3fb"/>
                            <path d="M40 45H80L75 85H45L40 45Z" stroke="#90a4c7" stroke-width="3" fill="none"/>
                            <circle cx="50" cy="95" r="5" fill="#90a4c7"/>
                            <circle cx="70" cy="95" r="5" fill="#90a4c7"/>
                        </svg>
                    </div>
                    <h5 class="mb-2">Giỏ hàng trống</h5>
                    <p class="text-muted mb-4">Thêm sản phẩm để bắt đầu mua sắm!</p>
                    <a href="/" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            </div>
        </div>
    `;

    updateCartBadge(0);
}

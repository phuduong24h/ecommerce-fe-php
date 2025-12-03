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
// 📌 CẬP NHẬT SỐ LƯỢNG (CÓ CHECK STOCK)
// ============================
function updateQty(row, change) {

    const index = row.dataset.index;
    
    // 🟢 LẤY TỒN KHO TỪ HTML (Để chặn ngay lập tức)
    // Nếu data-stock rỗng hoặc lỗi thì mặc định 999
    const maxStock = parseInt(row.dataset.stock) || 999;
    
    let currentQty = parseInt(row.querySelector('.quantity-input').value);
    let newQty = currentQty + change;

    // 1. Chặn nếu giảm dưới 1
    if (newQty < 1) return;

    // 2. 🟢 CHẶN NẾU TĂNG QUÁ TỒN KHO
    if (change > 0 && newQty > maxStock) {
        alert(`Sản phẩm này chỉ còn lại ${maxStock} cái trong kho!`);
        return; // Dừng lại ngay, không gọi API
    }

    // Gọi API cập nhật
    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            index,
            qty: newQty   // PHP sẽ gửi sang NodeJS dưới dạng "quantity"
        })
    })
    .then(res => res.json())
    .then(data => {

        if (!data.success) {
            // Nếu Server trả về lỗi (VD: check lại thấy hết hàng)
            alert(data.message);
            // Reset lại số lượng hiển thị về số cũ
            row.querySelector('.quantity-input').value = currentQty;
            return;
        }

        // Cập nhật thành công
        row.querySelector('.quantity-input').value = newQty;

        // Cập nhật tổng tiền dòng
        row.querySelector('.item-total').textContent = data.item_total;

        // Cập nhật tổng tiền giỏ hàng
        document.getElementById('subtotal').textContent = data.subtotal;
        document.getElementById('total').textContent = data.total;

        // Cập nhật badge
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

        row.remove();

        // Cập nhật lại index cho các dòng còn lại (để mảng không bị lệch)
        const remainingRows = document.querySelectorAll('.cart-item');
        remainingRows.forEach((item, newIndex) => {
            item.dataset.index = newIndex;
        });

        updateCartBadge(data.cart_count);

        if (data.item_count === 0 || remainingRows.length === 0) {
            showEmptyCart();
        } else {
            document.getElementById('subtotal').textContent = data.subtotal;
            document.getElementById('total').textContent = data.total;
        }
    })
    .catch(err => {
        console.error("Lỗi xoá:", err);
    });
}


// ============================
// 📌 HIỂN THỊ GIAO DIỆN GIỎ TRỐNG (ĐÃ KHÔI PHỤC)
// ============================
function showEmptyCart() {
    const parentRow = document.querySelector('.row.g-4');
    const summary = document.getElementById('cart-summary');

    if (summary) summary.remove();

    if (parentRow) {
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
    }

    updateCartBadge(0);
}
// public/js/cart.js
document.addEventListener('DOMContentLoaded', function () {
    // Tăng số lượng
    document.querySelectorAll('.btn-plus').forEach(btn => {
        btn.addEventListener('click', function () {
            const item = this.closest('.cart-item');
            const index = item.dataset.index;
            let qty = parseInt(item.querySelector('.quantity-input').value);
            qty++;
            updateCart(index, qty, item);
        });
    });

    // Giảm số lượng
    document.querySelectorAll('.btn-minus').forEach(btn => {
        btn.addEventListener('click', function () {
            const item = this.closest('.cart-item');
            const index = item.dataset.index;
            let qty = parseInt(item.querySelector('.quantity-input').value);
            if (qty > 1) {
                qty--;
                updateCart(index, qty, item);
            }
        });
    });

    // Xóa sản phẩm
    document.querySelectorAll('.trash-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const item = this.closest('.cart-item');
            const index = item.dataset.index;
            removeFromCart(index, item);
        });
    });
});

// Cập nhật số lượng
function updateCart(index, qty, row) {
    fetch('/cart/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ index, qty })
    })
    .then(res => res.json())
    .then(data => {
        row.querySelector('.quantity-input').value = qty;
        row.querySelector('.item-total').textContent = '$' + data.item_total;
        document.getElementById('subtotal').textContent = '$' + data.subtotal;
        document.getElementById('total').textContent = '$' + data.total;
        updateCartCount();
    })
    .catch(err => console.error('Lỗi cập nhật:', err));
}

// Xóa sản phẩm
function removeFromCart(index, row) {
    // Hiệu ứng fade out
    row.style.transition = 'opacity 0.3s';
    row.style.opacity = '0';

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
        // Xóa khỏi DOM sau hiệu ứng
        setTimeout(() => {
            if (row && row.parentNode) {
                row.parentNode.removeChild(row);
            }

            // Cập nhật badge
            const badge = document.querySelector('.badge');
            if (badge) badge.textContent = data.item_count;

            // Nếu giỏ trống → hiển thị giao diện trống
            if (data.item_count === 0) {
                showEmptyCart();
                return;
            }

            // Cập nhật tổng tiền
            const subtotalEl = document.getElementById('subtotal');
            const totalEl = document.getElementById('total');
            if (subtotalEl) subtotalEl.textContent = '$' + data.subtotal;
            if (totalEl) totalEl.textContent = '$' + data.total;

            updateCartCount();
        }, 300);
    })
    .catch(err => {
        console.error('Lỗi xóa:', err);
        alert('Có lỗi khi xóa sản phẩm!');
        // Khôi phục opacity nếu có lỗi
        row.style.opacity = '1';
    });
}

// Hiển thị giỏ hàng trống (không reload)
function showEmptyCart() {
    const container = document.getElementById('cart-items-container');
    const summaryCol = document.querySelector('.col-lg-4');
    const parentRow = document.querySelector('.row.g-4');

    // Cập nhật nội dung container thành giao diện trống
    if (container) {
        container.innerHTML = `
            <div class="d-flex align-items-center justify-content-center" style="min-height: 600px;">
                <div class="text-center">
                    <div class="d-flex justify-content-center" style="margin-bottom: 30px;">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="60" fill="#dce4f0"/>
                            <path d="M50 35H70C72.21 35 74 36.79 74 39V85C74 88.31 71.31 91 68 91H52C48.69 91 46 88.31 46 85V39C46 36.79 47.79 35 50 35Z" stroke="#8fa3c4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                            <path d="M54 35C54 32.24 56.24 30 59 30C61.76 30 64 32.24 64 35" stroke="#8fa3c4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h5 class="mb-2">Giỏ hàng trống</h5>
                    <p class="text-muted mb-4">Thêm sản phẩm để bắt đầu mua sắm!</p>
                    <a href="/" class="btn btn-primary">Tiếp tục mua sắm</a>
                </div>
            </div>
        `;
    }

    // Ẩn cột tổng đơn hàng với hiệu ứng mượt
    if (summaryCol) {
        summaryCol.style.transition = 'opacity 0.3s';
        summaryCol.style.opacity = '0';
        setTimeout(() => {
            summaryCol.remove();
            
            // Thay đổi layout thành full width
            const cartCol = container.closest('.col-lg-8');
            if (cartCol) {
                cartCol.classList.remove('col-lg-8');
                cartCol.classList.add('col-12');
            }
        }, 300);
    } else {
        // Nếu không có summaryCol, vẫn đổi layout
        const cartCol = container.closest('.col-lg-8');
        if (cartCol) {
            cartCol.classList.remove('col-lg-8');
            cartCol.classList.add('col-12');
        }
    }

    // Cập nhật badge về 0
    const badge = document.querySelector('.badge');
    if (badge) badge.textContent = '0';
}

// Cập nhật số lượng trên badge
function updateCartCount() {
    const count = document.querySelectorAll('.cart-item').length;
    const badge = document.querySelector('.badge');
    if (badge) {
        badge.textContent = count;
    }
}
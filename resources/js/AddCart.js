import './bootstrap';
document.addEventListener('DOMContentLoaded', () => {

    // Lấy các biến toàn cục đã "nhúng" từ Blade
    const { cartAddUrl, isLoggedIn, loginUrl } = window.myApp;
    const cartCountBadge = document.getElementById('cart-count-badge');
    const allAddToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    // Hàm cập nhật số đếm trên icon
    function updateCartIconCount(newCount) {
        if (cartCountBadge) {
            cartCountBadge.textContent = newCount;
        }
    }

    // Gán sự kiện click cho tất cả các nút "Thêm vào Giỏ"
    allAddToCartButtons.forEach(button => {
        button.addEventListener('click', async function(event) {
            event.preventDefault();

            // 1. Kiểm tra đăng nhập (phía client)
            if (!isLoggedIn) {
                window.location.href = loginUrl;
                return;
            }

            // 2. Thay đổi trạng thái nút
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang thêm...';

            // 3. Lấy dữ liệu sản phẩm từ nút
            const productData = JSON.parse(this.dataset.productJson);

            try {
                // 4. Gọi đến CartController (POST /cart/add)
                const response = await fetch(cartAddUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        // Gửi token CSRF của Laravel
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_json: productData // Gửi toàn bộ object sản phẩm
                    })
                });

                const result = await response.json();

                if (!response.ok) {
                    // Nếu là lỗi 401 (chưa auth), Controller sẽ trả về 'redirect'
                    if (response.status === 401 && result.redirect) {
                        window.location.href = result.redirect;
                    }
                    // Lỗi khác
                    throw new Error(result.message || 'Lỗi không xác định');
                }

                // 5. Thành công! Cập nhật số đếm
                if (result.success) {
                    updateCartIconCount(result.newCartCount);

                    // Cập nhật UI nút bấm
                    this.innerHTML = '<i class="home-product-item__cart fa-solid fa-check"></i> Đã thêm!';
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = '<i class="home-product-item__cart fa-solid fa-cart-shopping"></i> Thêm vào Giỏ';
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Lỗi khi thêm vào giỏ hàng');
                }

            } catch (error) {
                console.error('Lỗi khi thêm vào giỏ hàng:', error);
                this.disabled = false;
                this.innerHTML = 'Lỗi! Thử lại';
            }
        });
    });
});

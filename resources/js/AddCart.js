// public/js/AddCart.js
import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {

    // Kiểm tra biến global từ Laravel (được define ở layout chính)
    if(!window.myApp) return; 

    const { cartAddUrl, isLoggedIn, loginUrl } = window.myApp;
    const cartCountBadge = document.getElementById('cart-count'); 
    const allAddToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    // Hàm cập nhật Badge Icon trên Header
    function updateCartIconCount(newCount) {
        if (!cartCountBadge) return;

        cartCountBadge.textContent = newCount;

        if (newCount > 0) {
            cartCountBadge.classList.remove('hidden');
        } else {
            cartCountBadge.classList.add('hidden');
        }
    }

    allAddToCartButtons.forEach(button => {
        button.addEventListener('click', async function(event) {
            event.preventDefault();

            // Nếu chưa đăng nhập -> Chuyển trang
            if (!isLoggedIn) {
                window.location.href = loginUrl;
                return;
            }

            // Lưu nội dung cũ để restore sau
            const originalHtml = this.innerHTML;
            
            // Hiệu ứng loading
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            // Parse dữ liệu từ data-attribute
            let productData;
            try {
                 productData = JSON.parse(this.dataset.productJson);
            } catch (e) {
                console.error("Lỗi JSON sản phẩm", e);
                this.disabled = false;
                this.innerHTML = originalHtml;
                return;
            }

            try {
                const response = await fetch(cartAddUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ product_json: productData })
                });

                const result = await response.json();

                // Xử lý redirect (VD: Token hết hạn)
                if (response.status === 401 && result.redirect) {
                    window.location.href = result.redirect;
                    return;
                }

                if (result.success) {
                    // Cập nhật UI thành công
                    updateCartIconCount(result.newCartCount);

                    this.innerHTML = '<i class="fa-solid fa-check"></i> Đã thêm';
                    
                    // Reset nút sau 1.5s
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Lỗi không xác định');
                }

            } catch (error) {
                // Xử lý lỗi (VD: Hết hàng, Lỗi server)
                console.error('Lỗi:', error);
                
                // Hiển thị thông báo lỗi ngắn gọn trên nút bấm
                this.innerHTML = `<span style="font-size: 12px;">${error.message.substring(0, 15)}...</span>`;
                this.classList.add('bg-red-500', 'hover:bg-red-600'); // Đổi màu đỏ báo lỗi
                
                setTimeout(() => {
                     this.disabled = false;
                     this.innerHTML = originalHtml;
                     this.classList.remove('bg-red-500', 'hover:bg-red-600');
                }, 2000);
            }
        });
    });
});
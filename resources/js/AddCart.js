import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {

    const { cartAddUrl, isLoggedIn, loginUrl } = window.myApp;
    const cartCountBadge = document.getElementById('cart-count'); 
    const allAddToCartButtons = document.querySelectorAll('.add-to-cart-btn');

    // --- ĐÂY LÀ HÀM CẦN SỬA ---
    function updateCartIconCount(newCount) {
        if (!cartCountBadge) return;

        // 1. Cập nhật nội dung số
        cartCountBadge.textContent = newCount;

        // 2. LOGIC QUAN TRỌNG: Ẩn/Hiện badge
        if (newCount > 0) {
            // Nếu có hàng -> Xóa class 'hidden' để nó hiện ra ngay
            cartCountBadge.classList.remove('hidden');
        } else {
            // Nếu = 0 -> Thêm class 'hidden' để ẩn đi
            cartCountBadge.classList.add('hidden');
        }
    }
    // ---------------------------

    allAddToCartButtons.forEach(button => {
        button.addEventListener('click', async function(event) {
            event.preventDefault();

            if (!isLoggedIn) {
                window.location.href = loginUrl;
                return;
            }

            // Hiệu ứng loading
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            // Parse dữ liệu
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

                // Xử lý redirect nếu chưa login (backend trả về 401)
                if (response.status === 401 && result.redirect) {
                    window.location.href = result.redirect;
                    return;
                }

                if (result.success) {
                    // --- GỌI HÀM CẬP NHẬT UI NGAY LẬP TỨC ---
                    updateCartIconCount(result.newCartCount);

                    // Hiệu ứng thành công
                    this.innerHTML = '<i class="fa-solid fa-check"></i> Đã thêm';
                    setTimeout(() => {
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }, 1500);
                } else {
                    throw new Error(result.message || 'Lỗi không xác định');
                }

            } catch (error) {
                console.error('Lỗi:', error);
                this.disabled = false;
                this.innerHTML = 'Lỗi!';
                setTimeout(() => {
                     this.innerHTML = originalHtml;
                }, 2000);
            }
        });
    });
});
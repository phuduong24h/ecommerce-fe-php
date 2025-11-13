import './bootstrap';


/**
 * Hàm này chịu trách nhiệm gọi API và hiển thị sản phẩm ra màn hình
 * @param {string} searchTerm - Từ khóa tìm kiếm (mặc định là chuỗi rỗng)
 */


function loadProducts(searchTerm = '') {

    // Lấy container sản phẩm
    const productContainer = document.querySelector('.home-product .grid__row');

    if (!productContainer) {
        console.error('Không tìm thấy container .home-product .grid__row');
        return;
    }

    // Xây dựng URL động. Nếu có searchTerm, thêm nó vào query
    let apiUrl = 'http://localhost:3000/api/user/products';
    if (searchTerm) {
        apiUrl += `?search=${encodeURIComponent(searchTerm)}`;
    }

    // Hiển thị "Đang tải..."
    productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Đang tải sản phẩm...</p>';

    fetch(apiUrl)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(responseData => {
            console.log('📦 Response:', responseData);

            const { data } = responseData;

            if (!data || !data.products) {
                productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Không có dữ liệu sản phẩm</p>';
                return;
            }

            const products = data.products;
            console.log(`✅ Loaded ${products.length} products`);

            // Xóa nội dung "Đang tải..."
            productContainer.innerHTML = '';

            if (products.length === 0) {
                if(searchTerm) {
                    productContainer.innerHTML = `<p style="padding: 20px; text-align: center;">Không tìm thấy sản phẩm nào với từ khóa "${searchTerm}".</p>`;
                } else {
                    productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Chưa có sản phẩm nào.</p>';
                }
                return;
            }

            // Lặp và hiển thị sản phẩm
            products.forEach(product => {
                let imageUrl = 'https://via.placeholder.com/300?text=No+Image';

                if (product.image) {
                    imageUrl = product.image;
                } else if (product.images && product.images.length > 0) {
                    const firstImage = product.images[0];
                    imageUrl = (typeof firstImage === 'object' && firstImage.url) ? firstImage.url : firstImage;
                }

                const productHtml = `
                    <div class="grid__column-4">
                        <div class="home-product-item">
                            <div class="home-product-item__img" style="background-image: url(${imageUrl});"></div>
                            <div class="home-product-item__body">
                                <div class="home-product-name__wrap">
                                    <h4 class="home-product-item__name">${product.name || 'N/A'}</h4>
                                    <span class="home-product-item__tag">${product.categoryName || 'New'}</span>
                                </div>
                                <div class="home-product-item__rating">
                                    <i class="fa-solid fa-star"></i>
                                    <span>(${product.rating || '0'})</span>
                                </div>
                                <p class="home-product-item__category">${product.categoryId || 'N/A'}</p>
                                <div class="home-product-item__footer">
                                    <span class="home-product-item__price">$${product.price || '0'}</span>
                                    <span class="home-product-item__stock">${product.stock > 0 ? 'Còn Hàng' : 'Hết Hàng'}</span>
                                </div>
                                <button class="home-product-item__button btn_css btn--primary_css">
                                    <i class="home-product-item__cart fa-solid fa-cart-shopping"></i>
                                    Thêm vào Giỏ
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                productContainer.insertAdjacentHTML('beforeend', productHtml);
            });
        })
        .catch(error => {
            console.error('❌ Lỗi khi tải sản phẩm:', error);
            productContainer.innerHTML = '<p style="padding: 20px; text-align: center;">Không thể tải sản phẩm. Vui lòng kiểm tra backend.</p>';
        });
}

// ==========================================================
// SỰ KIỆN CHÍNH
// ==========================================================
document.addEventListener('DOMContentLoaded', function() {

    // 1. Tải tất cả sản phẩm ngay khi trang được mở
    loadProducts();

    // 2. Lắng nghe sự kiện gõ phím trên ô tìm kiếm
    const searchInput = document.querySelector('.header__search-input');

    if (searchInput) {
        searchInput.addEventListener('keyup', function(event) {
            // Lấy giá trị người dùng gõ
            const searchTerm = event.target.value;

            // Gọi lại hàm loadProducts với từ khóa mới
            // (Bạn có thể thêm logic "debounce" ở đây nếu muốn tối ưu hơn)
            loadProducts(searchTerm);
        });
    }
});


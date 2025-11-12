document.querySelectorAll('.btn-plus').forEach(btn => {
    btn.addEventListener('click', function() {
        const item = this.closest('.cart-item');
        const index = item.dataset.index;
        let qty = parseInt(item.querySelector('.quantity-input').value);
        qty++;
        updateCart(index, qty, item);
    });
});

document.querySelectorAll('.btn-minus').forEach(btn => {
    btn.addEventListener('click', function() {
        const item = this.closest('.cart-item');
        const index = item.dataset.index;
        let qty = parseInt(item.querySelector('.quantity-input').value);
        if (qty > 1) {
            qty--;
            updateCart(index, qty, item);
        }
    });
});

document.querySelectorAll('.trash-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const item = this.closest('.cart-item');
        const index = item.dataset.index;
        removeFromCart(index, item);
    });
});

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
    });
}

function removeFromCart(index, row) {
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
        row.remove();
        document.getElementById('subtotal').textContent = '$' + data.subtotal;
        document.getElementById('total').textContent = '$' + data.total;
        updateCartCount();
    });
}

function updateCartCount() {
    const count = document.querySelectorAll('.cart-item').length;
    const badge = document.querySelector('.badge');
    if (badge) badge.textContent = count;
}
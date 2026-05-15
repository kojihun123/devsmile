import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('toast', {
    visible: false,
    message: '',
    timer: null,
    type: 'success',
    show(msg, type = 'success') {
        this.message = msg;
        this.type = type;
        this.visible = true;
        clearTimeout(this.timer);
        this.timer = setTimeout(() => this.visible = false, 3000);
    }
});

Alpine.store('cart', {
    count: window.__CART_COUNT__ ?? 0,
});

// 장바구니 담기
window.addToCart = async function(productId, qty) {
    const res = await fetch('/cart', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ product_id: productId, quantity: qty })
    });

    if (res.ok) {
        const data = await res.json();
        Alpine.store('cart').count += qty;
        Alpine.store('toast').show(data.message);
    } else {
        const data = await res.json();
        Alpine.store('toast').show(data.message || '오류가 발생했습니다.', 'error');
    }
}

Alpine.start();

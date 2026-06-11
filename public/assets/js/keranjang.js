function formatRupiah(number) {
    return 'Rp ' + number.toLocaleString('id-ID');
}

function updateSummary() {
    let subtotal = 0;

    document.querySelectorAll('.cart-item').forEach(item => {
        const price = parseInt(item.dataset.price);
        const qty = parseInt(item.querySelector('.qty').innerText);
        subtotal += price * qty;
    });

    const subtotalEl = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');

    if (subtotalEl && totalEl) {
        subtotalEl.innerText = formatRupiah(subtotal);
        totalEl.innerText = formatRupiah(subtotal + 10000);
    }
}

document.querySelectorAll('.cart-item').forEach(item => {
    const price = parseInt(item.dataset.price);
    const qtyEl = item.querySelector('.qty');
    const totalEl = item.querySelector('.price-total');

    item.querySelector('.plus').addEventListener('click', () => {
        let qty = parseInt(qtyEl.innerText);
        qty++;
        qtyEl.innerText = qty;
        totalEl.innerText = formatRupiah(price * qty);
        updateSummary();
    });

    item.querySelector('.minus').addEventListener('click', () => {
        let qty = parseInt(qtyEl.innerText);
        if (qty > 1) {
            qty--;
            qtyEl.innerText = qty;
            totalEl.innerText = formatRupiah(price * qty);
            updateSummary();
        }
    });
});

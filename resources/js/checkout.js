import { getCart } from './cart.js';

document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    const summaryEl = document.getElementById('orderSummary');

    if (!summaryEl) return;

    // Redirect if cart is empty
    if (cart.length === 0) {
        summaryEl.innerHTML = `
            <p class="text-gray-500 text-center py-4">Your cart is empty.</p>
            <a href="/menu" class="block text-center mt-4 bg-pink-600 text-white py-2 rounded-lg">Browse Menu</a>
        `;
        return;
    }

    // Render cart items
    summaryEl.innerHTML = cart.map(item => `
        <div class="pt-3 first:pt-0 flex justify-between">
            <div>
                <span class="font-medium">${item.name}</span>
                <span class="text-gray-500 text-sm ml-2">×${item.quantity}</span>
            </div>
            <span class="font-medium">₱${item.price * item.quantity}</span>
        </div>
    `).join('');

    // Calculate totals
    const subtotal = cart.reduce((sum, i) => sum + i.price * i.quantity, 0);
    let deliveryFee = 0;
    let eta = 'Ready for pickup';

    const subtotalEl = document.getElementById('subtotalValue');
    const feeEl = document.getElementById('deliveryFeeValue');
    const etaEl = document.getElementById('etaValue');
    const totalEl = document.getElementById('totalValue');
    const addressField = document.getElementById('addressField');

    function updateTotals() {
        const selected = document.querySelector('input[name="deliveryType"]:checked');
        if (!selected) return;

        deliveryFee = parseFloat(selected.dataset.fee);
        const type = selected.value;
        const eta_minutes = parseInt(selected.dataset.eta);

        // Update ETA text
        if (type === 'pickup') {
            eta = 'Ready immediately';
        } else {
            eta = `~${eta_minutes} minutes`;
        }

        // Show/hide address field
        if (type === 'pickup') {
            addressField.classList.add('hidden');
        } else {
            addressField.classList.remove('hidden');
        }

        // Update display
        subtotalEl.textContent = `₱${subtotal}`;
        feeEl.textContent = deliveryFee === 0 ? 'Free' : `₱${deliveryFee}`;
        etaEl.textContent = eta;
        totalEl.textContent = `₱${subtotal + deliveryFee}`;
    }

    // Attach listeners to delivery radios
    document.querySelectorAll('input[name="deliveryType"]').forEach(radio => {
        radio.addEventListener('change', updateTotals);
    });

    updateTotals();

    // Payment button
    const payBtn = document.getElementById('payButton');
    const errorEl = document.getElementById('errorMessage');

    payBtn.addEventListener('click', async () => {
        const name = document.getElementById('customerName').value.trim();
        const phone = document.getElementById('customerPhone').value.trim();
        const email = document.getElementById('customerEmail').value.trim();
        const selectedZone = document.querySelector('input[name="deliveryType"]:checked');
        const deliveryType = selectedZone.value;
        const address = document.getElementById('deliveryAddress')?.value.trim() || '';

        // Validation
        errorEl.classList.add('hidden');
        if (!name) return showError('Please enter your name.');
        if (!phone) return showError('Please enter your phone number.');
        if (!email || !email.includes('@')) return showError('Please enter a valid email.');
        if (deliveryType !== 'pickup' && !address) return showError('Please enter your delivery address.');

        payBtn.disabled = true;
        payBtn.innerHTML = '⏳ Processing...';

        try {
            const response = await fetch('/api/create-payment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: JSON.stringify({
                    items: cart,
                    customer_name: name,
                    customer_phone: phone,
                    customer_email: email,
                    delivery_type: deliveryType,
                    delivery_address: deliveryType === 'pickup' ? null : address,
                })
            });

            const data = await response.json();

            if (!response.ok || data.error) {
                throw new Error(data.error || 'Payment failed.');
            }

            // Clear cart and redirect
            localStorage.removeItem('oreo-cart');
            window.location.href = data.redirectUrl;

        } catch (err) {
            showError(err.message || 'Something went wrong. Please try again.');
            payBtn.disabled = false;
            payBtn.innerHTML = '💳 Pay with GCash';
        }
    });

    function showError(msg) {
        errorEl.textContent = msg;
        errorEl.classList.remove('hidden');
        payBtn.disabled = false;
        payBtn.innerHTML = '💳 Pay with GCash';
    }
});
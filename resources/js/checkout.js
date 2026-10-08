import { getCart } from './cart.js';

let activePoll = null;

document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    const summaryEl = document.getElementById('orderSummary');

    if (!summaryEl) return;

    if (cart.length === 0) {
        summaryEl.innerHTML = `
            <p class="text-stone-500 text-center py-4">Your cart is empty.</p>
            <a href="/menu" class="block text-center mt-4 btn-primary">Browse Menu</a>
        `;
        return;
    }

    summaryEl.innerHTML = cart.map(item => `
        <div class="pt-3 first:pt-0 flex justify-between">
            <div>
                <span class="font-medium">${item.name}</span>
                <span class="text-stone-500 text-sm ml-2">×${item.quantity}</span>
            </div>
            <span class="font-medium">₱${item.price * item.quantity}</span>
        </div>
    `).join('');

    const subtotal = cart.reduce((sum, i) => sum + i.price * i.quantity, 0);

    const subtotalEl   = document.getElementById('subtotalValue');
    const feeEl        = document.getElementById('deliveryFeeValue');
    const etaEl        = document.getElementById('etaValue');
    const totalEl      = document.getElementById('totalValue');
    const addressField = document.getElementById('addressField');
    const payBtn       = document.getElementById('payButton');
    const payHint      = document.getElementById('payHint');
    const errorEl      = document.getElementById('errorMessage');
    const inPersonNote = document.getElementById('inPersonNote');

    let deliveryFee = 0;
    let eta = 'Ready for pickup';
    let paymentMethod = 'online';

    function currentDeliveryType() {
        return document.querySelector('input[name="deliveryType"]:checked')?.value || 'pickup';
    }

    function updateTotals() {
        const selected = document.querySelector('input[name="deliveryType"]:checked');
        if (!selected) return;

        deliveryFee = parseFloat(selected.dataset.fee);
        const type  = selected.value;
        const etaMinutes = parseInt(selected.dataset.eta);

        eta = type === 'pickup' ? 'Ready immediately' : `~${etaMinutes} minutes`;

        if (type === 'pickup') addressField.classList.add('hidden');
        else addressField.classList.remove('hidden');

        subtotalEl.textContent = `₱${subtotal}`;
        feeEl.textContent      = deliveryFee === 0 ? 'Free' : `₱${deliveryFee}`;
        etaEl.textContent      = eta;
        totalEl.textContent    = `₱${subtotal + deliveryFee}`;

        updatePaymentButtonLabel();
    }

    function updatePaymentButtonLabel() {
        if (paymentMethod === 'online') {
            payBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg> Pay with GCash';
            payHint.textContent = "You'll see a QR code. Scan it with GCash or your bank app to pay.";
            inPersonNote.classList.add('hidden');
        } else {
            payBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/></svg> Reserve — Pay at Pickup';
            payHint.textContent = "We'll reserve your order. Show your order number at the kiosk and pay in cash.";
            inPersonNote.classList.remove('hidden');
        }
    }

    document.querySelectorAll('input[name="deliveryType"]').forEach(radio => {
        radio.addEventListener('change', updateTotals);
    });

    document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
        radio.addEventListener('change', () => {
            paymentMethod = radio.value;
            updatePaymentButtonLabel();
        });
    });

    updateTotals();

    payBtn.addEventListener('click', async () => {
        const name = document.getElementById('customerName').value.trim();
        const phone = document.getElementById('customerPhone').value.trim();
        const email = document.getElementById('customerEmail').value.trim();
        const deliveryType = currentDeliveryType();
        const address = document.getElementById('deliveryAddress')?.value.trim() || '';

        errorEl.classList.add('hidden');
        if (!name) return showError('Please enter your name.');
        if (!phone) return showError('Please enter your phone number.');
        if (!email || !email.includes('@')) return showError('Please enter a valid email.');
        if (deliveryType !== 'pickup' && !address) return showError('Please enter your delivery address.');

        payBtn.disabled = true;
        payBtn.innerHTML = paymentMethod === 'in_person'
            ? 'Reserving order…'
            : 'Generating QR…';

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
                    payment_method: paymentMethod,
                }),
            });

            const data = await response.json();

            if (response.status === 401) { window.location.href = '/login'; return; }

            if (!response.ok || data.error) throw new Error(data.error || 'Payment failed.');

            localStorage.removeItem('oreo-cart');

            // In-person: instant success page
            if (data.payment_method === 'in_person') {
                window.location.href = `/success?order=${encodeURIComponent(data.orderNumber)}`;
                return;
            }

            // Online: show QR + poll
            if (!data.qr_image || !data.orderNumber) {
                throw new Error('Payment gateway returned an unexpected response.');
            }
            showQR(data.qr_image, data.orderNumber, data.test_url);
            startPolling(data.orderNumber);

        } catch (err) {
            showError(err.message || 'Something went wrong. Please try again.');
            payBtn.disabled = false;
            updatePaymentButtonLabel();
        }
    });

    function showQR(imageUrl, orderNumber, testUrl) {
        const container = document.getElementById('qr-container');
        const leftCol   = document.getElementById('checkoutFormCol');
        const rightCol  = document.getElementById('checkoutSummaryCol');

        if (leftCol)  leftCol.classList.add('hidden');
        if (rightCol) rightCol.classList.add('hidden');

        container.innerHTML = `
            <div class="text-center">
                <p class="font-display font-bold text-xl text-oreo-noir mb-2">Scan to pay</p>
                <p class="text-sm text-stone-500 mb-5">Open GCash, Maya, or your bank app and scan the code below.</p>
                <img src="${imageUrl}" alt="QR Ph payment code" class="mx-auto w-64 h-64 rounded-lg border border-stone-200 bg-white" />
                ${testUrl ? `
                    <div class="mt-4">
                        <a href="${testUrl}" target="_blank"
                           class="inline-flex items-center gap-2 text-xs font-medium text-cookie-brown hover:text-oreo-noir border border-stone-300 rounded-full px-4 py-2">
                            Simulate payment (test mode)
                        </a>
                    </div>
                ` : ''}
                <p class="mt-5 text-sm">Order #: <strong class="font-mono">${orderNumber}</strong></p>
                <p class="mt-3 text-sm text-stone-500 flex items-center justify-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    Waiting for payment…
                </p>
                <div class="mt-6 pt-6 border-t border-stone-100">
                    <button type="button" id="cancelQrBtn"
                            class="text-xs text-stone-500 hover:text-red-600 font-medium inline-flex items-center gap-1.5">
                        Cancel and edit order
                    </button>
                </div>
            </div>
        `;
        container.classList.remove('hidden');
        container.scrollIntoView({ behavior: 'smooth' });

        document.getElementById('cancelQrBtn')?.addEventListener('click', () => {
            if (activePoll) { clearInterval(activePoll); activePoll = null; }
            container.innerHTML = '';
            container.classList.add('hidden');
            if (leftCol)  leftCol.classList.remove('hidden');
            if (rightCol) rightCol.classList.remove('hidden');
            payBtn.disabled = false;
            updatePaymentButtonLabel();
        });
    }

    function startPolling(orderNumber) {
        if (activePoll) clearInterval(activePoll);
        activePoll = setInterval(async () => {
            try {
                const r = await fetch(`/api/order/${encodeURIComponent(orderNumber)}`, {
                    headers: { 'Accept': 'application/json' },
                });
                if (!r.ok) return;
                const order = await r.json();
                const status = Array.isArray(order) ? order[0]?.payment_status : order?.payment_status;
                if (status === 'paid') {
                    clearInterval(activePoll);
                    activePoll = null;
                    window.location.href = `/success?order=${encodeURIComponent(orderNumber)}`;
                }
            } catch (_) { /* transient */ }
        }, 3000);
    }

    function showError(msg) {
        errorEl.textContent = msg;
        errorEl.classList.remove('hidden');
        payBtn.disabled = false;
        updatePaymentButtonLabel();
    }
});
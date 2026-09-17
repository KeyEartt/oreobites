// ============================================
// CART STATE
// ============================================
const CART_KEY = 'oreo-cart';

export function getCart() {
    return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
}

export function saveCart(cart) {
    localStorage.setItem(CART_KEY, JSON.stringify(cart));
    renderCart();
}

export function addToCart(product) {
    const cart = getCart();
    const existing = cart.find(item => item.product_id === product.id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            product_id: product.id,
            name: product.name,
            price: product.price,
            image_url: product.image_url || null,
            quantity: 1,
            stock: product.stock
        });
    }
    saveCart(cart);
    showToast(`${product.name} added to cart`);
    openDrawer();
}

// ============================================
// RENDER
// ============================================
function renderCart() {
    const cart = getCart();
    const itemsContainer = document.getElementById('cartItems');
    const footer = document.getElementById('cartFooter');
    const totalSpan = document.getElementById('cartTotal');
    const badge = document.getElementById('cartBadge');

    if (!itemsContainer) return;

    // Badge
    const totalItems = cart.reduce((sum, i) => sum + i.quantity, 0);
    if (badge) {
        if (totalItems > 0) {
            badge.textContent = totalItems;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // Empty state
    if (cart.length === 0) {
        itemsContainer.innerHTML = `
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto rounded-full bg-white border border-stone-200 flex items-center justify-center text-stone-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"/>
                    </svg>
                </div>
                <p class="text-cookie-brown font-medium mt-4">Your cart is empty</p>
                <p class="text-stone-500 text-sm mt-1">Add a few bites to get started</p>
            </div>
        `;
        if (footer) footer.classList.add('hidden');
        return;
    }

    // Items
    let total = 0;
    itemsContainer.innerHTML = cart.map(item => {
        total += item.price * item.quantity;
        const imageHtml = item.image_url
            ? `<img src="/${item.image_url}" alt="${escapeHtml(item.name)}" class="w-full h-full object-cover">`
            : `<div class="w-full h-full flex items-center justify-center bg-milk-cream text-cookie-brown/50">
                   <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                       <circle cx="12" cy="12" r="9"/><circle cx="9" cy="10" r="0.9" fill="currentColor" stroke="none"/><circle cx="14" cy="9.5" r="0.9" fill="currentColor" stroke="none"/><circle cx="15" cy="14" r="0.9" fill="currentColor" stroke="none"/><circle cx="9.5" cy="15" r="0.9" fill="currentColor" stroke="none"/><circle cx="12.5" cy="12" r="0.9" fill="currentColor" stroke="none"/>
                   </svg>
               </div>`;

        return `
            <div class="flex gap-3 bg-white rounded-xl p-3 border border-stone-200" data-item-id="${item.product_id}">
                <div class="w-16 h-16 rounded-lg overflow-hidden shrink-0">
                    ${imageHtml}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start gap-2">
                        <p class="font-medium text-oreo-noir text-sm leading-tight truncate">${escapeHtml(item.name)}</p>
                        <button type="button" onclick="window.removeItem('${item.product_id}')"
                                class="text-stone-400 hover:text-red-600 transition-colors shrink-0"
                                aria-label="Remove">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-stone-500 mt-0.5">₱${item.price} each</p>

                    <div class="flex items-center justify-between mt-2">
                        <div class="inline-flex items-center rounded-full border border-stone-200 overflow-hidden">
                            <button type="button" onclick="window.updateQty('${item.product_id}', -1)"
                                    class="w-7 h-7 flex items-center justify-center text-cookie-brown hover:bg-milk-cream transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                </svg>
                            </button>
                            <span class="w-8 text-center text-sm font-semibold text-oreo-noir">${item.quantity}</span>
                            <button type="button" onclick="window.updateQty('${item.product_id}', 1)"
                                    class="w-7 h-7 flex items-center justify-center text-cookie-brown hover:bg-milk-cream transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                            </button>
                        </div>
                        <p class="font-display font-bold text-oreo-noir">₱${item.price * item.quantity}</p>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    if (totalSpan) totalSpan.textContent = `₱${total}`;
    if (footer) footer.classList.remove('hidden');
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ============================================
// GLOBAL ACTIONS
// ============================================
window.updateQty = function(productId, delta) {
    const cart = getCart();
    const item = cart.find(i => i.product_id === productId);
    if (item) {
        item.quantity += delta;
        if (item.quantity <= 0) {
            window.removeItem(productId);
            return;
        }
        saveCart(cart);
    }
};

window.removeItem = function(productId) {
    let cart = getCart();
    cart = cart.filter(i => i.product_id !== productId);
    saveCart(cart);
};

window.addToCart = addToCart;

// ============================================
// DRAWER CONTROLS
// ============================================
function openDrawer() {
    const drawer = document.getElementById('cartDrawer');
    const panel = document.getElementById('cartPanel');
    const overlay = document.getElementById('cartOverlay');
    if (!drawer) return;

    drawer.classList.remove('hidden');
    requestAnimationFrame(() => {
        panel.classList.remove('translate-x-full');
        overlay.classList.remove('opacity-0');
        overlay.classList.add('opacity-100');
    });
    renderCart();
}

function closeDrawer() {
    const drawer = document.getElementById('cartDrawer');
    const panel = document.getElementById('cartPanel');
    const overlay = document.getElementById('cartOverlay');
    if (!drawer) return;

    panel.classList.add('translate-x-full');
    overlay.classList.add('opacity-0');
    overlay.classList.remove('opacity-100');
    setTimeout(() => drawer.classList.add('hidden'), 300);
}

// ============================================
// TOAST
// ============================================
function showToast(message) {
    let toast = document.getElementById('cartToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cartToast';
        toast.className = 'fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] pointer-events-none transition-all duration-300 opacity-0 translate-y-2';
        document.body.appendChild(toast);
    }
    toast.innerHTML = `
        <div class="bg-oreo-noir text-milk-cream rounded-full px-5 py-3 shadow-lift flex items-center gap-2 text-sm font-medium">
            <span class="w-5 h-5 rounded-full bg-green-500 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="white" class="w-3 h-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                </svg>
            </span>
            ${message}
        </div>
    `;

    requestAnimationFrame(() => {
        toast.classList.remove('opacity-0', 'translate-y-2');
        toast.classList.add('opacity-100', 'translate-y-0');
    });

    clearTimeout(toast._hideTimer);
    toast._hideTimer = setTimeout(() => {
        toast.classList.add('opacity-0', 'translate-y-2');
    }, 2000);
}

// ============================================
// INIT
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    const toggle = document.getElementById('cartToggle');
    const close = document.getElementById('cartClose');
    const overlay = document.getElementById('cartOverlay');

    if (toggle) toggle.addEventListener('click', openDrawer);
    if (close) close.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);

    renderCart();
});
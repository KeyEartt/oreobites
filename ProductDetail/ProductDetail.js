function getProductIdFromUrl() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id');
}

async function getProduct(id) {
    const { data, error } = await supabaseClient
        .from('products')
        .select('*')
        .eq('id', id)
        .single();

    if (error) {
        console.error('Error fetching product:', error);
        return null;
    }
    return data;
}

function renderProduct(product) {
    const container = document.getElementById('product-detail');

    if (!product) {
        container.innerHTML = '<p>Product not found.</p>';
        return;
    }

    container.innerHTML = `
        <img class="detail-image" src="${product.image_url || 'placeholder.png'}" alt="${product.name}">

        <div class="detail-info">
            <h1>${product.name}</h1>
            <p class="detail-variant">${product.variant || ''}</p>
            <p class="detail-price">₱${product.price}</p>
            <p class="detail-desc">${product.description}</p>

            <div class="quantity-selector">
                <button type="button" id="qty-minus">-</button>
                <input type="number" id="qty-input" value="1" min="1" max="${product.stock}">
                <button type="button" id="qty-plus">+</button>
            </div>

            <button type="button" id="add-to-cart-btn" class="add-to-cart-btn">ADD TO CART</button>
        </div>
    `;

    setupQuantityControls(product);
}

function setupQuantityControls(product) {
    const qtyInput = document.getElementById('qty-input');
    const minusBtn = document.getElementById('qty-minus');
    const plusBtn = document.getElementById('qty-plus');

    minusBtn.addEventListener('click', () => {
        const current = Number(qtyInput.value);
        if (current > 1) qtyInput.value = current - 1;
    });

    plusBtn.addEventListener('click', () => {
        const current = Number(qtyInput.value);
        if (current < product.stock) qtyInput.value = current + 1;
    });
}

async function init() {
    const id = getProductIdFromUrl();

    if (!id) {
        document.getElementById('product-detail').innerHTML = '<p>No product specified.</p>';
        return;
    }

    const product = await getProduct(id);
    renderProduct(product);
}

init();
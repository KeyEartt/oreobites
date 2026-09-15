const tableBody = document.getElementById('products-table-body');
const productsView = document.getElementById('products-view');
const formView = document.getElementById('product-form-view');
const addBtn = document.getElementById('add-product-btn');
const cancelBtn = document.getElementById('cancel-btn');
const form = document.getElementById('product-form');
const photoInput = document.getElementById('product-photo');
const photoPreview = document.getElementById('photo-preview');

let currentPhotoFile = null;
let currentPhotoUrl = '';

async function getProducts() {
    const { data, error } = await supabaseClient
        .from('products')
        .select('*')
        .order('created_at', { ascending: false });

    if (error) {
        console.error('Error fetching products:', error);
        return [];
    }
    return data;
}

async function renderTable() {
    const products = await getProducts();
    tableBody.innerHTML = '';

    products.forEach(product => {
        const row = document.createElement('tr');

        const photoHtml = product.image_url
            ? `<img src="${product.image_url}" alt="${product.name}">`
            : `<span class="no-photo">No photo</span>`;

        row.innerHTML = `
            <td class="product-photo-cell">
                ${photoHtml}
            </td>
            <td>${product.name}</td>
            <td>${product.variant || '-'}</td>
            <td>₱${product.price}</td>
            <td>${product.stock}</td>
            <td>
                <button class="action-btn edit-btn" data-id="${product.id}">✏️</button>
                <button class="action-btn delete-btn" data-id="${product.id}">🗑️</button>
            </td>
        `;
        tableBody.appendChild(row);
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => openForm(btn.dataset.id));
    });

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => deleteProduct(btn.dataset.id));
    });
}

function showView(view) {
    productsView.classList.toggle('active', view === 'list');
    formView.classList.toggle('active', view === 'form');
}

function resetForm() {
    form.reset();
    document.getElementById('product-id').value = '';
    currentPhotoFile = null;
    currentPhotoUrl = '';
    photoPreview.innerHTML = '<span>No image</span>';
}

async function openForm(id = null) {
    resetForm();

    if (id) {
        const { data, error } = await supabaseClient
            .from('products')
            .select('*')
            .eq('id', id)
            .single();

        if (error) {
            console.error('Error loading product:', error);
            return;
        }

        document.getElementById('product-id').value = data.id;
        document.getElementById('product-name').value = data.name;
        document.getElementById('product-variant').value = data.variant || '';
        document.getElementById('product-price').value = data.price;
        document.getElementById('product-stock').value = data.stock;
        document.getElementById('product-description').value = data.description;
        currentPhotoUrl = data.image_url || '';

        if (currentPhotoUrl) {
            photoPreview.innerHTML = `<img src="${currentPhotoUrl}" alt="preview">`;
        }
    }

    showView('form');
}

async function deleteProduct(id) {
    if (!confirm('Delete this product?')) return;

    const { error } = await supabaseClient
        .from('products')
        .delete()
        .eq('id', id);

    if (error) {
        console.error('Error deleting product:', error);
        return;
    }

    renderTable();
}

photoInput.addEventListener('change', () => {
    const file = photoInput.files[0];
    if (!file) return;

    currentPhotoFile = file;

    const reader = new FileReader();
    reader.onload = () => {
        photoPreview.innerHTML = `<img src="${reader.result}" alt="preview">`;
    };
    reader.readAsDataURL(file);
});

async function uploadPhoto(file) {
    const fileExt = file.name.split('.').pop();
    const fileName = `${Date.now()}.${fileExt}`;

    const { error } = await supabaseClient
        .storage
        .from('product-photos')
        .upload(fileName, file);

    if (error) {
        console.error('Error uploading photo:', error);
        return null;
    }

    const { data } = supabaseClient
        .storage
        .from('product-photos')
        .getPublicUrl(fileName);

    return data.publicUrl;
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const id = document.getElementById('product-id').value;
    let imageUrl = currentPhotoUrl;

    if (currentPhotoFile) {
        const uploadedUrl = await uploadPhoto(currentPhotoFile);
        if (uploadedUrl) imageUrl = uploadedUrl;
    }

    const productData = {
        name: document.getElementById('product-name').value,
        variant: document.getElementById('product-variant').value,
        price: Number(document.getElementById('product-price').value),
        stock: Number(document.getElementById('product-stock').value),
        description: document.getElementById('product-description').value,
        image_url: imageUrl
    };

    let error;
    if (id) {
        ({ error } = await supabaseClient
            .from('products')
            .update(productData)
            .eq('id', id));
    } else {
        ({ error } = await supabaseClient
            .from('products')
            .insert(productData));
    }

    if (error) {
        console.error('Error saving product:', error);
        alert('Failed to save product. Check the console for details.');
        return;
    }

    renderTable();
    showView('list');
});

addBtn.addEventListener('click', () => openForm());
cancelBtn.addEventListener('click', () => showView('list'));

renderTable();
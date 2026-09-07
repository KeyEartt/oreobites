// --- ADMIN AUTH GUARD ---
(async function enforceAdminAccess() {
    const { data: { session } } = await supabaseClient.auth.getSession();

    // 1. If not logged in at all, redirect to Admin Login
    if (!session) {
        window.location.href = 'Admin-login.html';
        return;
    }

    // 2. Verify if the logged-in user has admin privileges
    const { data: profile, error } = await supabaseClient
        .from('profiles')
        .select('is_admin')
        .eq('id', session.user.id)
        .single();

    if (error || !profile || !profile.is_admin) {
        alert('Access Denied: You do not have admin permissions.');
        await supabaseClient.auth.signOut();
        window.location.href = 'Admin-login.html';
    }
})();
// ------------------------

const tableBody = document.getElementById('customers-table-body');
const listView = document.getElementById('customers-view');
const detailView = document.getElementById('customer-detail-view');
const backBtn = document.getElementById('back-to-list-btn');
const deactivateBtn = document.getElementById('deactivate-btn');

let currentCustomerId = null;

async function getCustomers() {
    const { data, error } = await supabaseClient
        .from('profiles')
        .select('*')
        .order('created_at', { ascending: false });

    if (error) {
        console.error('Error fetching customers:', error);
        return [];
    }
    return data;
}

function showView(view) {
    listView.classList.toggle('active', view === 'list');
    detailView.classList.toggle('active', view === 'detail');
}

async function renderTable() {
    const customers = await getCustomers();
    tableBody.innerHTML = '';

    if (!customers || customers.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="4">No customer records found.</td></tr>';
        return;
    }

    customers.forEach(customer => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${customer.student_id || '-'}</td>
            <td>${customer.full_name || '-'}</td>
            <td>${customer.year_section || '-'}</td>
            <td>
                <button class="action-btn view-btn" data-id="${customer.id}" aria-label="View customer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                </button>
                <button class="action-btn ban-btn" data-id="${customer.id}" aria-label="Deactivate customer">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="4.9" y1="4.9" x2="19.1" y2="19.1"></line>
                    </svg>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
    });

    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', () => openDetail(btn.dataset.id));
    });

    document.querySelectorAll('.ban-btn').forEach(btn => {
        btn.addEventListener('click', () => toggleActive(btn.dataset.id));
    });
}

async function openDetail(id) {
    currentCustomerId = id;

    const { data: customer, error } = await supabaseClient
        .from('profiles')
        .select('*')
        .eq('id', id)
        .single();

    if (error) {
        console.error('Error loading customer:', error);
        return;
    }

    document.getElementById('detail-name').textContent = customer.full_name || '-';
    document.getElementById('detail-student-id').textContent = customer.student_id || '-';
    document.getElementById('detail-email').textContent = customer.email || '-';
    document.getElementById('detail-phone').textContent = customer.phone || '-';
    document.getElementById('detail-year-section').textContent = customer.year_section || '-';
    document.getElementById('detail-campus').textContent = customer.school || '-';
    document.getElementById('detail-address').textContent = customer.address || '-';

    deactivateBtn.textContent = customer.is_active === false ? 'Reactivate Account' : 'Deactivate Account';

    const orderList = document.getElementById('order-history-list');
    orderList.innerHTML = '<li>Order history coming soon.</li>';

    showView('detail');
}

async function toggleActive(id) {
    const { data: customer } = await supabaseClient
        .from('profiles')
        .select('is_active')
        .eq('id', id)
        .single();

    const newStatus = customer ? customer.is_active === false : true;

    const { error } = await supabaseClient
        .from('profiles')
        .update({ is_active: newStatus })
        .eq('id', id);

    if (error) {
        console.error('Error updating status:', error);
        alert('Failed to update customer status.');
        return;
    }

    renderTable();
}

backBtn.addEventListener('click', () => showView('list'));

deactivateBtn.addEventListener('click', () => {
    if (currentCustomerId) toggleActive(currentCustomerId);
    showView('list');
});

renderTable();
async function updateHeaderForAuth() {
    const { data: { session } } = await supabaseClient.auth.getSession();
    const accountLink = document.querySelector('.header-icons a[aria-label="Account"]');

    if (!accountLink) return;

    // Detect whether we're already inside the "Log-in" folder or not
    const inLoginFolder = window.location.pathname.includes('/Log-in/');
    const basePath = inLoginFolder ? '' : '../Log-in/';

    if (session) {
        accountLink.href = basePath + 'Profile.html';
        accountLink.setAttribute('aria-label', 'Profile');
    } else {
        accountLink.href = basePath + 'Account.html';
        accountLink.setAttribute('aria-label', 'Account');
    }
}

updateHeaderForAuth();
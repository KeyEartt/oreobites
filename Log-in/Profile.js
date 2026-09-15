async function initProfilePage() {
    const { data: { session } } = await supabaseClient.auth.getSession();

    if (!session) {
        window.location.href = 'Account.html';
        return;
    }

    const { data: profile, error } = await supabaseClient
        .from('profiles')
        .select('*')
        .eq('id', session.user.id)
        .single();

    if (error || !profile) {
        console.error('Failed to load profile:', error);
        return;
    }

    document.getElementById('p-name').textContent = profile.full_name;
    document.getElementById('p-student-id').textContent = profile.student_id;
    document.getElementById('p-year-section').textContent = profile.year_section;
    document.getElementById('p-email').textContent = profile.email;
    document.getElementById('p-phone').textContent = profile.phone;
    document.getElementById('p-address').textContent = profile.address;
    document.getElementById('p-school').textContent = profile.school;
}

document.getElementById('sign-out-btn').addEventListener('click', async () => {
    await supabaseClient.auth.signOut();
    window.location.href = 'Account.html';
});

initProfilePage();
const signinForm = document.getElementById('signin-form');

signinForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const { error } = await supabaseClient.auth.signInWithPassword({ email, password });

    if (error) {
        alert('Sign in failed: ' + error.message);
        return;
    }

    window.location.href = '../Landing/Home.html';
});
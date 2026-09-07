const steps = document.querySelectorAll('.form-step');
const dots = document.querySelectorAll('.dot');
const nextBtn = document.getElementById('next-btn');
const prevBtn = document.getElementById('prev-btn');
let currentStep = 1;
const totalSteps = steps.length;

const eyeOpenIcon = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
        <circle cx="12" cy="12" r="3"></circle>
    </svg>
`;

const eyeOffIcon = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.8 21.8 0 0 1-2.16 3.19"></path>
        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
        <line x1="1" y1="1" x2="23" y2="23"></line>
    </svg>
`;

function validateStep(stepEl) {
    const inputs = stepEl.querySelectorAll('input[required]');
    let allValid = true;

    inputs.forEach(input => {
        const group = input.closest('.input-group');
        const isValid = input.checkValidity();

        group.classList.toggle('invalid', !isValid);

        if (!isValid) {
            const errorSpan = group.querySelector('.error-message');
            errorSpan.textContent = input.validationMessage;
            allValid = false;
        }
    });

    return allValid;
}

function updateStep() {
    steps.forEach(step => {
        step.classList.toggle('active', Number(step.dataset.step) === currentStep);
    });

    dots.forEach(dot => {
        const dotNum = Number(dot.dataset.dot);
        dot.classList.remove('completed', 'current');
        if (dotNum < currentStep) {
            dot.classList.add('completed');
        } else if (dotNum === currentStep) {
            dot.classList.add('current');
        }
    });

    nextBtn.style.display = currentStep === totalSteps ? 'none' : 'flex';
    prevBtn.style.display = currentStep === 1 ? 'none' : 'flex';
}

nextBtn.addEventListener('click', () => {
    const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    if (!validateStep(currentStepEl)) return;

    if (currentStep < totalSteps) {
        currentStep++;
        updateStep();
    }
});

prevBtn.addEventListener('click', () => {
    if (currentStep > 1) {
        currentStep--;
        updateStep();
    }
});

document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.innerHTML = eyeOpenIcon;

    btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.target);
        const isHidden = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';
        btn.innerHTML = isHidden ? eyeOffIcon : eyeOpenIcon;
        btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
});

document.querySelectorAll('.input-group input').forEach(input => {
    input.addEventListener('input', () => {
        input.closest('.input-group').classList.remove('invalid');
    });
});

const contactInput = document.getElementById('contact');

contactInput.addEventListener('input', () => {
    let digits = contactInput.value.replace(/\D/g, '');
    digits = digits.slice(0, 11);

    let formatted = digits;
    if (digits.length > 4) {
        formatted = digits.slice(0, 4) + ' ' + digits.slice(4);
    }
    if (digits.length > 7) {
        formatted = digits.slice(0, 4) + ' ' + digits.slice(4, 7) + ' ' + digits.slice(7);
    }

    contactInput.value = formatted;
});

const form = document.getElementById('signup-form');
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm-password');

function checkPasswordsMatch() {
    const group = confirmInput.closest('.input-group');
    const errorSpan = group.querySelector('.error-message');

    if (confirmInput.value === '' || passwordInput.value === confirmInput.value) {
        group.classList.remove('invalid');
        return true;
    }

    group.classList.add('invalid');
    errorSpan.textContent = 'Passwords do not match.';
    return false;
}

confirmInput.addEventListener('input', checkPasswordsMatch);
passwordInput.addEventListener('input', checkPasswordsMatch);

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const step3 = document.querySelector('.form-step[data-step="3"]');
    if (!validateStep(step3)) return;
    if (!checkPasswordsMatch()) return;

    const { data, error } = await supabaseClient.auth.signUp({
        email: document.getElementById('email').value,
        password: passwordInput.value
    });

    if (error) {
        alert('Sign up failed: ' + error.message);
        return;
    }

    const { error: profileError } = await supabaseClient
        .from('profiles')
        .insert({
            id: data.user.id,
            full_name: document.getElementById('name').value,
            student_id: document.getElementById('student-id').value,
            year_section: document.getElementById('year-section').value,
            email: document.getElementById('email').value,
            phone: contactInput.value,
            address: document.getElementById('address').value,
            school: document.getElementById('school').value
        });

    if (profileError) {
        alert('Account created, but profile save failed: ' + profileError.message);
        return;
    }

    alert('Account created! Check your email to confirm, then sign in.');
    window.location.href = 'Account.html';
});

updateStep();
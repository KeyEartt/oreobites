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
    for (const input of inputs) {
        if (!input.reportValidity()) {
            return false;
        }
    }
    return true;
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
        }
        else if (dotNum === currentStep) {
            dot.classList.add('current');
        }
    });
    nextBtn.style.display = currentStep === totalSteps ? 'none' : 'flex';
    prevBtn.style.display = currentStep === 1 ? 'none' : 'flex';
}
nextBtn.addEventListener('click', () => {
    const currentStepEl = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    if (!currentStepEl || !validateStep(currentStepEl))
        return;
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
        const targetId = btn.dataset.target;
        if (!targetId)
            return;
        const input = document.getElementById(targetId);
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.innerHTML = isHidden ? eyeOffIcon : eyeOpenIcon;
        btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
});
updateStep();
export {};
//# sourceMappingURL=Signup.js.map
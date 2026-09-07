const steps = document.querySelectorAll<HTMLElement>('.form-step');
const dots = document.querySelectorAll<HTMLElement>('.dot');
const nextBtn = document.getElementById('next-btn') as HTMLButtonElement;
const prevBtn = document.getElementById('prev-btn') as HTMLButtonElement;
let currentStep: number = 1;
const totalSteps: number = steps.length;

const eyeOpenIcon: string = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
        <circle cx="12" cy="12" r="3"></circle>
    </svg>
`;

const eyeOffIcon: string = `
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.8 21.8 0 0 1 5.06-5.94M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.8 21.8 0 0 1-2.16 3.19"></path>
        <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path>
        <line x1="1" y1="1" x2="23" y2="23"></line>
    </svg>
`;

function validateStep(stepEl: HTMLElement): boolean {
    const inputs = stepEl.querySelectorAll<HTMLInputElement>('input[required]');
    for (const input of inputs) {
        if (!input.reportValidity()) {
            return false;
        }
    }
    return true;
}

function updateStep(): void {
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
    const currentStepEl = document.querySelector<HTMLElement>(`.form-step[data-step="${currentStep}"]`);
    if (!currentStepEl || !validateStep(currentStepEl)) return;

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

document.querySelectorAll<HTMLButtonElement>('.toggle-password').forEach(btn => {
    btn.innerHTML = eyeOpenIcon;

    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        if (!targetId) return;

        const input = document.getElementById(targetId) as HTMLInputElement;
        const isHidden: boolean = input.type === 'password';

        input.type = isHidden ? 'text' : 'password';
        btn.innerHTML = isHidden ? eyeOffIcon : eyeOpenIcon;
        btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
    });
});

updateStep();
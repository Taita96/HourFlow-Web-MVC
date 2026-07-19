function initNavToggle() {
    const toggle = document.getElementById('nav-toggle');
    const panel = document.getElementById('nav-links-mobile');

    if (!toggle || !panel) {
        return;
    }

    toggle.addEventListener('click', () => {
        const isHidden = panel.classList.toggle('hidden');
        toggle.setAttribute('aria-expanded', String(!isHidden));
    });
}

document.addEventListener('DOMContentLoaded', initNavToggle);
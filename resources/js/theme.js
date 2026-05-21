const STORAGE_KEY = 'theme';

export function getStoredTheme() {
    const stored = localStorage.getItem(STORAGE_KEY);

    if (stored === 'light' || stored === 'dark') {
        return stored;
    }

    return null;
}

export function systemPrefersDark() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

export function isDarkMode() {
    const stored = getStoredTheme();

    if (stored === 'dark') {
        return true;
    }

    if (stored === 'light') {
        return false;
    }

    return systemPrefersDark();
}

export function applyTheme(forceDark) {
    const isDark = typeof forceDark === 'boolean' ? forceDark : isDarkMode();
    const root = document.documentElement;

    root.classList.toggle('dark', isDark);
    root.style.colorScheme = isDark ? 'dark' : 'light';

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        button.setAttribute('title', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        button.setAttribute(
            'aria-label',
            isDark ? 'Switch to light mode' : 'Switch to dark mode',
        );
    });
}

export function setTheme(mode) {
    localStorage.setItem(STORAGE_KEY, mode === 'dark' ? 'dark' : 'light');
    applyTheme(mode === 'dark');
}

export function toggleTheme() {
    setTheme(isDarkMode() ? 'light' : 'dark');
}

function onThemeToggleClick(event) {
    const button = event.target.closest('[data-theme-toggle]');

    if (!button) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();
    toggleTheme();
}

export function initTheme() {
    applyTheme();

    document.removeEventListener('click', onThemeToggleClick, true);
    document.addEventListener('click', onThemeToggleClick, true);

    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if (!getStoredTheme()) {
            applyTheme();
        }
    });

    window.addEventListener('storage', (event) => {
        if (event.key === STORAGE_KEY) {
            applyTheme();
        }
    });
}

if (typeof window !== 'undefined') {
    window.toggleTheme = toggleTheme;
}

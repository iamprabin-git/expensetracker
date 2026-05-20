import './bootstrap';

import { initSiteFooter } from './site-footer';
import { initSiteHeader } from './site-header';
import { initUserSidebar } from './user-sidebar';
import { initUserNotifications } from './user-notifications';
import { initAiScan } from './ai-scan';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

function applyTheme(isDark) {
    const root = document.documentElement;

    if (isDark) {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }

    localStorage.setItem('theme', isDark ? 'dark' : 'light');

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
        button.setAttribute('title', isDark ? 'Switch to light mode' : 'Switch to dark mode');
        button.setAttribute(
            'aria-label',
            isDark ? 'Switch to light mode' : 'Switch to dark mode',
        );
    });
}

function initTheme() {
    const stored = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isDark = stored === 'dark' || (!stored && prefersDark);

    applyTheme(isDark);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            applyTheme(!document.documentElement.classList.contains('dark'));
        });
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSiteHeader();
    initSiteFooter();
    initUserSidebar();
    initUserNotifications();
    initAiScan();
});

Alpine.start();

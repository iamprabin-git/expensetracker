import './bootstrap';

import { initFlashToasts } from './toast';
import { initSiteFooter } from './site-footer';
import { initSiteHeader } from './site-header';
import { initUserSidebar } from './user-sidebar';
import { initUserNotifications } from './user-notifications';
import { initTheme } from './theme';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

function bootApp() {
    initFlashToasts();
    initTheme();
    initSiteHeader();
    initSiteFooter();
    initUserSidebar();
    initUserNotifications();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootApp);
} else {
    bootApp();
}

Alpine.start();

export function initUserSidebar() {
    const shell = document.querySelector('[data-user-shell]');
    if (!shell) {
        return;
    }

    const sidebar = shell.querySelector('[data-user-sidebar]');
    const backdrop = shell.querySelector('[data-user-sidebar-backdrop]');
    const toggles = shell.querySelectorAll('[data-user-sidebar-toggle]');

    if (!sidebar) {
        return;
    }

    let isOpen = false;

    const setOpen = (open) => {
        isOpen = open;
        shell.classList.toggle('user-shell--sidebar-open', open);
        sidebar.classList.toggle('is-open', open);
        document.body.classList.toggle('user-shell-menu-open', open);
        backdrop?.classList.toggle('is-visible', open);

        toggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute(
                'aria-label',
                open ? 'Close navigation menu' : 'Open navigation menu',
            );
        });
    };

    const close = () => setOpen(false);
    const open = () => setOpen(true);
    const toggle = () => setOpen(!isOpen);

    toggles.forEach((btn) => {
        btn.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toggle();
        });
    });

    backdrop?.addEventListener('click', close);

    sidebar.querySelectorAll('[data-user-sidebar-link]').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 991.98px)').matches) {
                close();
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen) {
            close();
        }
    });

    document.addEventListener(
        'click',
        (event) => {
            if (!isOpen) {
                return;
            }

            const target = event.target;
            const inSidebar = sidebar.contains(target);
            const onToggle = Array.from(toggles).some((btn) => btn.contains(target));
            const onThemeToggle = target.closest('[data-theme-toggle]');

            if (!inSidebar && !onToggle && !onThemeToggle) {
                close();
            }
        },
        true,
    );

    window.matchMedia('(min-width: 992px)').addEventListener('change', (event) => {
        if (event.matches) {
            close();
        }
    });
}

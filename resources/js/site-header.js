export function initSiteHeader() {
    const header = document.querySelector('[data-site-header]');
    if (!header) {
        return;
    }

    const backdrop = header.querySelector('[data-site-header-backdrop]');
    const panel = header.querySelector('[data-site-header-panel]');
    const toggles = header.querySelectorAll('[data-site-header-toggle]');

    if (!panel) {
        return;
    }

    let isOpen = false;

    const setMenuOpen = (open) => {
        isOpen = open;

        header.classList.toggle('site-header--menu-open', open);
        document.body.classList.toggle('site-header-menu-open', open);
        panel.classList.toggle('is-open', open);
        panel.setAttribute('aria-hidden', open ? 'false' : 'true');

        if (backdrop) {
            backdrop.classList.toggle('is-visible', open);
        }

        toggles.forEach((toggle) => {
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute(
                'aria-label',
                open ? 'Close navigation menu' : 'Open navigation menu',
            );
        });
    };

    const closeMenu = () => setMenuOpen(false);
    const openMenu = () => setMenuOpen(true);
    const toggleMenu = () => setMenuOpen(!isOpen);

    toggles.forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            toggleMenu();
        });
    });

    if (backdrop) {
        backdrop.addEventListener('click', (event) => {
            event.preventDefault();
            closeMenu();
        });
    }

    header.querySelectorAll('[data-site-header-link]').forEach((link) => {
        link.addEventListener('click', () => {
            closeMenu();
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen) {
            closeMenu();
        }
    });

    document.addEventListener(
        'click',
        (event) => {
            if (!isOpen) {
                return;
            }

            const target = event.target;
            const clickedInsidePanel = panel.contains(target);
            const clickedToggle = Array.from(toggles).some((toggle) => toggle.contains(target));

            if (!clickedInsidePanel && !clickedToggle) {
                closeMenu();
            }
        },
        true,
    );

    const onScroll = () => {
        header.classList.toggle('site-header--scrolled', window.scrollY > 8);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    const mq = window.matchMedia('(min-width: 992px)');

    const onBreakpoint = () => {
        if (mq.matches) {
            closeMenu();
        }
    };

    mq.addEventListener('change', onBreakpoint);
}

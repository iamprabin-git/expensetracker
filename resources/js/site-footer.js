export function initSiteFooter() {
    const accordion = document.querySelector('[data-site-footer-accordion]');
    if (!accordion) {
        return;
    }

    const items = Array.from(accordion.querySelectorAll('[data-site-footer-item]'));

    const closeItem = (item) => {
        const button = item.querySelector('[data-site-footer-toggle]');
        const panel = item.querySelector('[data-site-footer-panel]');

        button?.classList.add('collapsed');
        button?.setAttribute('aria-expanded', 'false');
        panel?.classList.remove('is-open');
    };

    const openItem = (item) => {
        const button = item.querySelector('[data-site-footer-toggle]');
        const panel = item.querySelector('[data-site-footer-panel]');

        button?.classList.remove('collapsed');
        button?.setAttribute('aria-expanded', 'true');
        panel?.classList.add('is-open');
    };

    const closeAll = () => {
        items.forEach((item) => closeItem(item));
    };

    const isItemOpen = (item) => {
        const panel = item.querySelector('[data-site-footer-panel]');

        return panel?.classList.contains('is-open') ?? false;
    };

    items.forEach((item) => {
        const button = item.querySelector('[data-site-footer-toggle]');
        const panel = item.querySelector('[data-site-footer-panel]');

        if (!button || !panel) {
            return;
        }

        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const willOpen = !isItemOpen(item);

            closeAll();

            if (willOpen) {
                openItem(item);
            }
        });

        panel.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                closeAll();
            });
        });
    });

    document.addEventListener(
        'click',
        (event) => {
            const hasOpen = items.some((item) => isItemOpen(item));

            if (!hasOpen) {
                return;
            }

            const target = event.target;

            if (accordion.contains(target)) {
                return;
            }

            closeAll();
        },
        true,
    );

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAll();
        }
    });
}

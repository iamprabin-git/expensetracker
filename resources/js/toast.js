import Toastify from 'toastify-js';
import 'toastify-js/src/toastify.css';

const DEFAULT_DURATION = 4500;

const typeStyles = {
    success: {
        background: 'oklch(0.45 0.14 155)',
        color: 'oklch(0.98 0.01 155)',
    },
    error: {
        background: 'oklch(0.52 0.2 25)',
        color: 'oklch(0.98 0.01 25)',
    },
    info: {
        background: 'oklch(0.45 0.12 264)',
        color: 'oklch(0.98 0.01 264)',
    },
    warning: {
        background: 'oklch(0.62 0.14 75)',
        color: 'oklch(0.2 0.04 75)',
    },
};

/**
 * @param {string} message
 * @param {'success'|'error'|'info'|'warning'} [type]
 * @param {Record<string, unknown>} [options]
 */
export function showToast(message, type = 'info', options = {}) {
    if (!message) {
        return;
    }

    const style = { ...typeStyles[type] ?? typeStyles.info, ...(options.style ?? {}) };

    Toastify({
        text: message,
        duration: options.duration ?? DEFAULT_DURATION,
        gravity: options.gravity ?? 'top',
        position: options.position ?? 'right',
        stopOnFocus: true,
        close: true,
        escapeMarkup: false,
        className: `app-toast app-toast--${type}`,
        style,
        offset: { x: 16, y: 16 },
        ...options,
    }).showToast();
}

export function initFlashToasts() {
    const el = document.getElementById('app-flash-toasts');

    if (!el?.textContent?.trim()) {
        return;
    }

    try {
        const items = JSON.parse(el.textContent);

        if (!Array.isArray(items)) {
            return;
        }

        items.forEach((item, index) => {
            if (!item?.message) {
                return;
            }

            setTimeout(() => {
                showToast(String(item.message), item.type ?? 'info', {
                    duration: item.duration ?? DEFAULT_DURATION,
                });
            }, index * 180);
        });
    } catch (error) {
        console.error('Failed to parse flash toasts', error);
    }
}

window.showToast = showToast;

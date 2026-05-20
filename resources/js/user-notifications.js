import { isNotificationSoundEnabled, ringNotificationBell } from './notification-sound';

const routes = {
    feed: '/notifications/feed',
    unreadCount: '/notifications/unread-count',
    read: (id) => `/notifications/${id}/read`,
    readAll: '/notifications/read-all',
};

const categoryIcons = {
    reminder: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
    account: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 19.118a7.5 7.5 0 0114.998 0',
    membership: 'M16.5 6v.75a2.25 2.25 0 01-2.25 2.25h-1.5a2.25 2.25 0 01-2.25-2.25V6M4.5 19.5h15',
    general: 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
};

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

async function fetchJson(url, options = {}) {
    const response = await fetch(url, {
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers ?? {}),
        },
        ...options,
    });

    if (response.status === 401 || response.status === 419) {
        throw new Error('session_expired');
    }

    if (response.status === 403) {
        throw new Error('forbidden');
    }

    if (!response.ok) {
        throw new Error(`Request failed: ${response.status}`);
    }

    return response.json();
}

function renderNotificationItem(notification) {
    const category = notification.category ?? 'general';
    const isUnread = !notification.read;
    const unreadClass = isUnread ? 'fb-notif-row--unread' : '';
    const copyUnreadClass = isUnread ? 'fb-notif-row__copy--unread' : '';
    const iconPath = categoryIcons[category] ?? categoryIcons.general;
    const title = escapeHtml(notification.title);
    const message = notification.message
        ? `<span class="fb-notif-row__message">${escapeHtml(notification.message)}</span>`
        : '';
    const titleHtml = isUnread
        ? `<strong>${title}</strong>`
        : `<span class="fb-notif-row__title">${title}</span>`;
    const action = notification.action_url
        ? `<a href="${escapeHtml(notification.action_url)}" class="fb-notif-row__link" data-notification-open="${escapeHtml(notification.id)}" aria-label="${title}"></a>`
        : '';
    const dot = isUnread ? '<span class="fb-notif-row__dot" aria-label="Unread"></span>' : '';

    return `
        <div class="fb-notif-row ${unreadClass}" role="listitem" data-notification-id="${escapeHtml(notification.id)}">
            ${action}
            <span class="fb-notif-row__icon fb-notif-row__icon--${escapeHtml(category)}" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" width="22" height="22">
                    <path stroke-linecap="round" stroke-linejoin="round" d="${iconPath}" />
                </svg>
            </span>
            <div class="fb-notif-row__body">
                <p class="fb-notif-row__copy ${copyUnreadClass}">
                    ${titleHtml}
                    ${message}
                    <span class="fb-notif-row__time"> · ${escapeHtml(notification.created_at_human)}</span>
                </p>
            </div>
            ${dot}
        </div>
    `;
}

function renderEmptyState() {
    return `
        <div class="user-notifications__empty">
            <p class="mb-0 fw-semibold">No notifications yet</p>
            <p class="small mb-0 mt-1">You're all caught up.</p>
        </div>
    `;
}

export function initUserNotifications() {
    const root = document.querySelector('[data-user-notifications]');
    if (!root) {
        return;
    }

    const badge = root.querySelector('[data-notifications-badge]');
    const list = root.querySelector('[data-notifications-list]');
    const loading = root.querySelector('[data-notifications-loading]');
    const markAllBtn = root.querySelector('[data-notifications-mark-all]');
    const toggle = root.querySelector('[data-notifications-toggle]');

    let pollTimer = null;
    let lastUnreadCount = null;

    const alertForNewNotifications = (count) => {
        if (lastUnreadCount !== null && count > lastUnreadCount) {
            ringNotificationBell(root);
        }

        lastUnreadCount = count;
    };

    const updateBadge = (count) => {
        if (!badge) {
            return;
        }

        if (count > 0) {
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }

        if (markAllBtn) {
            markAllBtn.classList.toggle('d-none', count === 0);
        }

        alertForNewNotifications(count);
    };

    const renderList = (notifications) => {
        if (!list) {
            return;
        }

        loading?.remove();

        if (!notifications.length) {
            list.innerHTML = renderEmptyState();
            return;
        }

        list.innerHTML = notifications.map(renderNotificationItem).join('');
    };

    const loadFeed = async () => {
        try {
            const data = await fetchJson(routes.feed);
            updateBadge(data.unread_count);
            const items = Array.isArray(data.notifications)
                ? data.notifications
                : (data.notifications?.data ?? []);
            renderList(items);
        } catch (error) {
            if (list) {
                const message = error?.message === 'forbidden'
                    ? 'Notifications are unavailable. Try refreshing the page.'
                    : 'Could not load notifications.';
                list.innerHTML = `<div class="user-notifications__empty"><p class="mb-0">${message}</p></div>`;
            }
        }
    };

    const refreshCount = async () => {
        try {
            const data = await fetchJson(routes.unreadCount);
            updateBadge(data.unread_count);
        } catch (error) {
            if (error?.message === 'session_expired') {
                window.clearInterval(pollTimer);
            }
        }
    };

    const markAsRead = async (id) => {
        await fetchJson(routes.read(id), { method: 'POST' });
        await loadFeed();
    };

    const markAllAsRead = async () => {
        await fetchJson(routes.readAll, { method: 'POST' });
        await loadFeed();
    };

    root.addEventListener('click', async (event) => {
        const openLink = event.target.closest('[data-notification-open]');
        if (openLink) {
            const id = openLink.dataset.notificationOpen;
            const item = root.querySelector(`[data-notification-id="${id}"]`);
            if (item?.classList.contains('fb-notif-row--unread')) {
                markAsRead(id).catch(() => {});
            }
        }
    });

    markAllBtn?.addEventListener('click', async (event) => {
        event.preventDefault();
        event.stopPropagation();
        await markAllAsRead();
    });

    toggle?.addEventListener('show.bs.dropdown', () => {
        loadFeed();
    });

    toggle?.addEventListener('click', () => {
        if (isNotificationSoundEnabled(root) && typeof window.AudioContext !== 'undefined') {
            const ctx = new window.AudioContext();
            if (ctx.state === 'suspended') {
                ctx.resume().catch(() => {});
            }
            ctx.close().catch(() => {});
        }
    });

    loadFeed();
    pollTimer = window.setInterval(refreshCount, 30000);

    window.addEventListener('beforeunload', () => {
        if (pollTimer) {
            window.clearInterval(pollTimer);
        }
    });
}

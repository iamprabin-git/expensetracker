const routes = {
    feed: '/notifications/feed',
    unreadCount: '/notifications/unread-count',
    read: (id) => `/notifications/${id}/read`,
    readAll: '/notifications/read-all',
    destroy: (id) => `/notifications/${id}`,
};

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

async function fetchJson(url, options = {}) {
    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            ...(options.headers ?? {}),
        },
        ...options,
    });

    if (!response.ok) {
        throw new Error(`Request failed: ${response.status}`);
    }

    return response.json();
}

function categoryIcon(category) {
    const icons = {
        reminder: 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
        account: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 19.118a7.5 7.5 0 0114.998 0',
        membership: 'M16.5 6v.75a2.25 2.25 0 01-2.25 2.25h-1.5a2.25 2.25 0 01-2.25-2.25V6M4.5 19.5h15',
        general: 'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
    };

    return icons[category] ?? icons.general;
}

function renderNotificationItem(notification) {
    const unreadClass = notification.read ? '' : 'user-notifications__item--unread';
    const action = notification.action_url
        ? `<a href="${notification.action_url}" class="user-notifications__item-link stretched-link" data-notification-open="${notification.id}"></a>`
        : '';

    return `
        <article class="user-notifications__item ${unreadClass}" role="listitem" data-notification-id="${notification.id}">
            ${action}
            <div class="user-notifications__item-icon user-notifications__item-icon--${notification.category}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="${categoryIcon(notification.category)}" />
                </svg>
            </div>
            <div class="user-notifications__item-body min-w-0">
                <p class="user-notifications__item-title mb-0">${notification.title}</p>
                <p class="user-notifications__item-message mb-1">${notification.message}</p>
                <p class="user-notifications__item-time mb-0">${notification.created_at_human}</p>
            </div>
            <button type="button" class="user-notifications__item-delete btn btn-link btn-sm p-0" data-notification-delete="${notification.id}" title="Delete" aria-label="Delete notification">&times;</button>
        </article>
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
    };

    const renderList = (notifications) => {
        if (!list) {
            return;
        }

        loading?.remove();

        if (!notifications.length) {
            list.innerHTML = '<p class="user-notifications__empty px-3 py-4 mb-0 text-center text-secondary small">No notifications yet.</p>';
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
        } catch {
            if (list) {
                list.innerHTML = '<p class="user-notifications__empty px-3 py-4 mb-0 text-center text-danger small">Could not load notifications.</p>';
            }
        }
    };

    const refreshCount = async () => {
        try {
            const data = await fetchJson(routes.unreadCount);
            updateBadge(data.unread_count);
        } catch {
            // ignore polling errors
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

    const deleteNotification = async (id) => {
        await fetchJson(routes.destroy(id), { method: 'DELETE' });
        await loadFeed();
    };

    root.addEventListener('click', async (event) => {
        const deleteBtn = event.target.closest('[data-notification-delete]');
        if (deleteBtn) {
            event.preventDefault();
            event.stopPropagation();
            await deleteNotification(deleteBtn.dataset.notificationDelete);
            return;
        }

        const openLink = event.target.closest('[data-notification-open]');
        if (openLink) {
            const id = openLink.dataset.notificationOpen;
            const item = root.querySelector(`[data-notification-id="${id}"]`);
            if (item?.classList.contains('user-notifications__item--unread')) {
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

    loadFeed();
    pollTimer = window.setInterval(refreshCount, 60000);

    window.addEventListener('beforeunload', () => {
        if (pollTimer) {
            window.clearInterval(pollTimer);
        }
    });
}

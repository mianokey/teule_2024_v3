self.addEventListener('push', function (event) {
    let payload = {};

    if (event.data) {
        try {
            payload = event.data.json();
        } catch (error) {
            payload = {
                title: 'Teule Management System',
                body: event.data.text()
            };
        }
    }

    const title = payload.title || 'Teule Management System';

    const options = {
        body: payload.body || 'You have a new notification.',
        icon: payload.icon || '/icons/icon-192.png',
        badge: payload.badge || '/icons/icon-192.png',
        data: {
            url: (payload.data && payload.data.url) || payload.url || '/'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = event.notification.data?.url || '/';
    const target = new URL(targetUrl, self.location.origin);

    if (target.origin !== self.location.origin) {
        return;
    }

    event.waitUntil(
        clients.openWindow(target.href)
    );
});

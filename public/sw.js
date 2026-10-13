self.addEventListener('push', function (event) {
    let data = {};

    if (event.data) {
        try {
            data = event.data.json();
        } catch (error) {
            data = {
                title: 'Teule Management System',
                body: event.data.text()
            };
        }
    }

    const title = data.title || 'Teule Management System';

    const options = {
        body: data.body || 'You have a new notification.',
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        data: {
            url: data.url || '/'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const target = new URL(
        event.notification.data.url || '/',
        self.location.origin
    );

    if (target.origin !== self.location.origin) {
        return;
    }

    event.waitUntil(
        clients.openWindow(target.href)
    );
});

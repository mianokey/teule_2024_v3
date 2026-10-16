 if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js')
                .then(function (registration) {
                    console.log('Teule service worker registered:', registration.scope);
                })
                .catch(function (error) {
                    console.error('Service worker registration failed:', error);
                });
        });
    }

(function () {
    'use strict';

    const root = document.getElementById('workflowNotificationDropdown');

    if (!root || root.dataset.initialized === '1') {
        return;
    }

    root.dataset.initialized = '1';

    const feedUrl = root.dataset.feedUrl;
    const historyUrl = root.dataset.historyUrl;
    const readBaseUrl = root.dataset.readUrl;
    const readAllUrl = root.dataset.readAllUrl;

    const badge = document.getElementById('workflowNotificationCount');
    const subtitle = document.getElementById('workflowNotificationSubtitle');
    const list = document.getElementById('workflowNotificationList');
    const markAllButton = document.getElementById('workflowNotificationMarkAll');

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    const soundUrl = '/sounds/teule_notification.mp3';
    const soundVolume = 0.9;

    const snapshotKey = 'teule_workflow_notification_snapshot';
    const unreadKey = 'teule_workflow_notification_unread_count';

    let audio = null;
    let soundUnlocked = false;
    let pendingSound = false;
    let initialFeedLoaded = false;
    let feedRequestInProgress = false;
    let previousUnreadCount = null;

    let knownNotificationIds = new Set();
    let storedSnapshotExists = false;

    /*
     * Restore notification state across page navigation.
     */
    try {
        const savedIds = sessionStorage.getItem(snapshotKey);
        const savedCount = sessionStorage.getItem(unreadKey);

        if (savedIds !== null) {
            const parsedIds = JSON.parse(savedIds);

            if (Array.isArray(parsedIds)) {
                knownNotificationIds = new Set(
                    parsedIds.map(String)
                );
                storedSnapshotExists = true;
            }
        }

        if (savedCount !== null) {
            previousUnreadCount = Number(savedCount);
        }
    } catch (error) {
        console.warn('Could not restore notification state:', error);
    }

    function getAudio() {
        if (!audio) {
            audio = new Audio(soundUrl);
            audio.preload = 'auto';
            audio.volume = soundVolume;
        }

        return audio;
    }

    /*
     * Unlock audio silently through the user's first interaction
     * anywhere on the page. Do not play a sound when the bell is clicked.
     */
    async function unlockSound() {
        if (soundUnlocked) {
            return;
        }

        const player = getAudio();

        try {
            player.muted = true;
            player.volume = soundVolume;

            await player.play();

            player.pause();
            player.currentTime = 0;
            player.muted = false;

            soundUnlocked = true;

            console.info('Notification audio is ready.');

            if (pendingSound) {
                pendingSound = false;
                playNotificationSound();
            }
        } catch (error) {
            player.muted = false;

            console.warn(
                'Browser has not allowed notification audio yet:',
                error
            );
        }
    }

    /*
     * Any genuine interaction can unlock audio.
     * No interaction with the notification bell is required.
     */
    document.addEventListener('pointerdown', unlockSound, {
        once: true,
        capture: true
    });

    document.addEventListener('keydown', unlockSound, {
        once: true,
        capture: true
    });

function playNotificationSound() {
    const audio = document.getElementById('workflowNotificationAudio');

    if (!audio || !soundUnlocked) {
        return;
    }

    audio.volume = 0.9;
    audio.currentTime = 0;

    audio.play().catch(error => {
        console.warn('Notification sound was blocked:', error);
    });
}


    function saveNotificationSnapshot(ids, count) {
        try {
            /*
             * Keep a recent set of IDs across page navigation.
             */
            const recentIds = Array.from(ids).slice(-100);

            sessionStorage.setItem(
                snapshotKey,
                JSON.stringify(recentIds)
            );

            sessionStorage.setItem(
                unreadKey,
                String(count)
            );
        } catch (error) {
            console.warn(
                'Could not save notification state:',
                error
            );
        }
    }

    function safeDestination(url) {
        if (!url || typeof url !== 'string') {
            return historyUrl;
        }

        try {
            const parsed = new URL(url, window.location.origin);

            return parsed.origin === window.location.origin
                ? parsed.href
                : historyUrl;
        } catch (error) {
            return historyUrl;
        }
    }

    function updateBadge(count) {
        const unread = Number(count) || 0;

        if (badge) {
            badge.textContent = unread > 99 ? '99+' : String(unread);
            badge.style.display = unread > 0 ? 'inline-block' : 'none';
        }

        if (subtitle) {
            subtitle.textContent = unread === 0
                ? 'You are all caught up'
                : unread + (
                    unread === 1
                        ? ' unread notification'
                        : ' unread notifications'
                );
        }
    }

    function createNotificationItem(notification) {
        const link = document.createElement('a');

        link.className = 'workflow-notification-item' +
            (notification.read ? '' : ' is-unread');

        link.href = safeDestination(notification.url);

        const iconBox = document.createElement('span');
        iconBox.className = 'workflow-notification-item-icon';

        const icon = document.createElement('i');
        icon.className = 'fa fa-bell';
        iconBox.appendChild(icon);

        const content = document.createElement('span');
        content.className = 'workflow-notification-item-content';

        const title = document.createElement('span');
        title.className = 'workflow-notification-item-title';
        title.textContent = notification.title || 'Notification';

        const message = document.createElement('span');
        message.className = 'workflow-notification-item-message';
        message.textContent = notification.message || '';

        const time = document.createElement('span');
        time.className = 'workflow-notification-item-time';
        time.textContent = notification.time || '';

        content.appendChild(title);
        content.appendChild(message);
        content.appendChild(time);

        link.appendChild(iconBox);
        link.appendChild(content);

        if (!notification.read) {
            const dot = document.createElement('span');
            dot.className = 'workflow-notification-unread-dot';
            link.appendChild(dot);
        }

        link.addEventListener('click', function (event) {
            if (notification.read) {
                return;
            }

            event.preventDefault();

            fetch(
                readBaseUrl + '/' +
                encodeURIComponent(notification.id) + '/read',
                {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            )
            .catch(function (error) {
                console.error(
                    'Could not mark notification as read:',
                    error
                );
            })
            .finally(function () {
                window.location.href = link.href;
            });
        });

        return link;
    }

    async function loadNotifications() {

if (feedRequestInProgress) {
    return;
}


        feedRequestInProgress = true;

        try {
            const response = await fetch(feedUrl, {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(
                    'Notification feed returned HTTP ' + response.status
                );
            }

            const result = await response.json();

            const unreadCount = Number(result.unread_count) || 0;

            const notifications = Array.isArray(result.notifications)
                ? result.notifications
                : [];

            const currentIds = notifications
                .filter(function (notification) {
                    return notification.id !== null &&
                           notification.id !== undefined;
                })
                .map(function (notification) {
                    return String(notification.id);
                });

            let hasNewNotification = false;

            if (initialFeedLoaded || storedSnapshotExists) {
                hasNewNotification = currentIds.some(function (id) {
                    return !knownNotificationIds.has(id);
                });

                if (
                    previousUnreadCount !== null &&
                    unreadCount > previousUnreadCount
                ) {
                    hasNewNotification = true;
                }
            }

            /*
             * First-ever load establishes a baseline silently.
             * Subsequent polls and page visits compare against it.
             */
            knownNotificationIds = new Set([
                ...knownNotificationIds,
                ...currentIds
            ]);

            previousUnreadCount = unreadCount;
            initialFeedLoaded = true;
            storedSnapshotExists = true;

            saveNotificationSnapshot(
                knownNotificationIds,
                unreadCount
            );

            updateBadge(unreadCount);

            if (list) {
                list.replaceChildren();

                if (notifications.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'workflow-notification-empty';
                    empty.textContent = 'No unread notifications';

                    list.appendChild(empty);
                } else {
                    notifications.forEach(function (notification) {
                        list.appendChild(
                            createNotificationItem(notification)
                        );
                    });
                }
            }

            if (hasNewNotification) {
                console.info('New notification detected.');

                playNotificationSound();
            }

        } catch (error) {
            console.error(
                'Workflow notification feed error:',
                error
            );
        } finally {
            feedRequestInProgress = false;
        }
    }

    /*
     * Mark all notifications as read without treating the operation
     * as a new notification.
     */
    if (markAllButton) {
        markAllButton.addEventListener('click', async function (event) {
            event.preventDefault();
            event.stopPropagation();

            markAllButton.disabled = true;

            try {
                const response = await fetch(readAllUrl, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) {
                    throw new Error(
                        'Unable to mark notifications as read. HTTP ' +
                        response.status
                    );
                }

                previousUnreadCount = 0;

                saveNotificationSnapshot(
                    knownNotificationIds,
                    0
                );

                await loadNotifications();

            } catch (error) {
                console.error(error);
                alert(error.message);
            } finally {
                markAllButton.disabled = false;
            }
        });
    }

    /*
     * Poll immediately and every 20 seconds, regardless of whether
     * the notification dropdown has been opened.
     */

loadNotifications();

// Refresh the bell feed every 20 seconds while the page is available.
window.setInterval(function () {
    loadNotifications();
}, 20000);

// Refresh immediately when the user returns to Teule.
document.addEventListener('visibilitychange', function () {
    if (!document.hidden) {
        loadNotifications();
    }
});

// Also refresh when the browser window regains focus.
window.addEventListener('focus', function () {
    loadNotifications();
});



})();

(function () {
    'use strict';

    const button = document.getElementById('togglePushNotifications');
    const buttonText = document.getElementById('pushNotificationButtonText');
    const status = document.getElementById('pushNotificationStatus');

    if (!button || button.dataset.initialized === '1') {
        return;
    }

    button.dataset.initialized = '1';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const publicKey = button.dataset.vapidPublicKey;
    const storeUrl = button.dataset.storeUrl;
    const deleteUrl = button.dataset.deleteUrl;

    function setStatus(message) {
        status.textContent = message;
    }

    function setButton(enabled) {
        buttonText.textContent = enabled
            ? 'Disable browser notifications'
            : 'Enable browser notifications';
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const output = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; i++) {
            output[i] = rawData.charCodeAt(i);
        }

        return output;
    }

    async function sendSubscription(url, method, data) {
        const response = await fetch(url, {
            method: method,
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });

        const result = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(
                result.message || 'The server could not save the subscription.'
            );
        }

        return result;
    }

    async function getCurrentSubscription() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
            throw new Error(
                'This browser does not support push notifications.'
            );
        }

        const registration = await navigator.serviceWorker.ready;

        return {
            registration: registration,
            subscription: await registration.pushManager.getSubscription()
        };
    }

    async function refreshButton() {
        try {
            const result = await getCurrentSubscription();
            setButton(!!result.subscription);
        } catch (error) {
            button.disabled = true;
            setStatus(error.message);
        }
    }

    button.addEventListener('click', async function () {
        button.disabled = true;

        try {
            const current = await getCurrentSubscription();

            if (current.subscription) {
                setStatus('Disabling browser notifications...');

                await sendSubscription(deleteUrl, 'DELETE', {
                    endpoint: current.subscription.endpoint
                });

                await current.subscription.unsubscribe();

                setButton(false);
                setStatus('Browser notifications have been disabled.');
                return;
            }

            if (!publicKey) {
                throw new Error('The VAPID public key is missing from the configuration.');
            }

            if (!window.isSecureContext) {
                throw new Error('Push notifications require HTTPS or localhost.');
            }

            if (!('Notification' in window)) {
                throw new Error('This browser does not support notifications.');
            }

            const permission = await Notification.requestPermission();

            if (permission !== 'granted') {
                throw new Error(
                    'Notification permission was not granted. Check your browser settings.'
                );
            }

            setStatus('Registering this browser...');

            const subscription = await current.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(publicKey)
            });

            const json = subscription.toJSON();

            await sendSubscription(storeUrl, 'POST', {
                endpoint: json.endpoint,
                keys: json.keys,
                contentEncoding: 'aes128gcm'
            });

            setButton(true);
            setStatus('Browser notifications enabled successfully.');
        } catch (error) {
            console.error('Push notification setup failed:', error);
            setStatus(error.message || 'Could not configure browser notifications.');
        } finally {
            button.disabled = false;
        }
    });

    refreshButton();
})();

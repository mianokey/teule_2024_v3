@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- PAGE HEADER --}}
    <div class="requisition-page-header">
        <div class="requisition-header-content">
            <div class="requisition-header-icon">
                <i class="fa fa-bell"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Dashboard</span>
                    <span>/</span>
                    <span>Notifications</span>
                </div>

                <h1 class="requisition-page-title">
                    My Notifications
                </h1>

                <p class="requisition-page-subtitle">
                    View updates, approvals and activities relevant to you.
                </p>
            </div>
        </div>
    </div>

    {{-- SUCCESS / ERROR MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    {{-- MAIN SECTION --}}
    <div class="requisition-section">

        {{-- SECTION HEADER --}}
        <div class="requisition-section-header">
            <div class="requisition-section-heading">
                <div class="requisition-section-icon">
                    <i class="fa fa-bell"></i>
                </div>

                <div>
                    <h5>Notification History</h5>
                    <p>
                        Review your notifications and manage their read status.
                    </p>
                </div>
            </div>

            <div class="requisition-count-badge">
                {{ $notifications->total() }}
                {{ $notifications->total() === 1 ? 'Notification' : 'Notifications' }}
            </div>
        </div>

        {{-- FILTERS --}}
        <div class="requisition-details-body">
            <form method="GET"
                  action="{{ route('workflow-notifications.index') }}">

                <div class="row g-3 align-items-end">

                    <div class="col-lg-6">
                        <label class="requisition-field-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="requisition-input"
                            placeholder="Search notification title or message..."
                        >
                    </div>

                    <div class="col-lg-3">
                        <label class="requisition-field-label">
                            Read Status
                        </label>

                        <select name="status" class="requisition-input">
                            <option value="">All notifications</option>

                            <option value="unread"
                                @selected(request('status') === 'unread')>
                                Unread
                            </option>

                            <option value="read"
                                @selected(request('status') === 'read')>
                                Read
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-3">
                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="requisition-save-button">
                                <i class="fa fa-search"></i>
                                Filter
                            </button>

                            <a href="{{ route('workflow-notifications.index') }}"
                               class="requisition-cancel-button">
                                Clear
                            </a>
                        </div>
                    </div>

                </div>
            </form>

            {{-- MARK ALL AS READ --}}
@if(auth()->user()->unreadNotifications()->exists())
    <div class="mt-3" id="markAllNotificationsContainer">
        <button
            type="button"
            id="markAllNotificationsRead"
            class="requisition-cancel-button"
        >
            <i class="fa fa-check-double"></i>
            Mark all as read
        </button>
    </div>
@endif

        </div>

        {{-- NOTIFICATION TABLE --}}
        <div class="requisition-list-table-wrapper">
            <table class="requisition-list-table">
                <thead>
                    <tr>
                        <th style="width: 55px;">#</th>
                        <th style="min-width: 170px;">Notification</th>
                        <th style="min-width: 280px;">Message</th>
                        <th style="min-width: 135px;">Date</th>
                        <th style="width: 115px;">Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($notifications as $notification)
                        @php
                            $data = $notification->data ?? [];

                            $isUnread = is_null($notification->read_at);

                            $title = $data['title'] ?? 'Notification';
                            $message = $data['message'] ?? '';
                            $icon = $data['icon'] ?? 'bell';
                            $url = $data['url'] ?? null;

                            // Only allow safe, internal application paths.
                            if (
                                !is_string($url) ||
                                !str_starts_with($url, '/') ||
                                str_starts_with($url, '//')
                            ) {
                                $url = null;
                            }

                            // Prevent invalid icon names from affecting markup.
                            if (!preg_match('/^[a-z0-9-]+$/i', $icon)) {
                                $icon = 'bell';
                            }
                        @endphp

                        <tr>
                            {{-- NUMBER --}}
                            <td>
                                <span class="requisition-list-row-number">
                                    {{ $notifications->firstItem() + $loop->index }}
                                </span>
                            </td>

                            {{-- TITLE --}}
                            <td>
                                <div class="requisition-list-number">
                                    @if($isUnread)
                                        <span style="color:#00096A;">
                                            <i class="fa fa-circle"
                                               style="font-size:7px;"
                                               title="Unread"></i>
                                        </span>
                                    @endif

                                    <i class="fa fa-{{ $icon }}"
                                       style="color:#00096A; margin-right:4px;"></i>

                                    {{ $title }}
                                </div>
                            </td>

                            {{-- MESSAGE --}}
                            <td>
                                <div class="requisition-list-purpose"
                                     title="{{ $message }}">
                                    {{ \Illuminate\Support\Str::limit($message, 140) ?: '—' }}
                                </div>
                            </td>

                            {{-- DATE --}}
                            <td>
                                <div class="requisition-list-date">
                                    {{ $notification->created_at?->format('d M Y') }}
                                    <span class="requisition-list-time">
                                    {{ $notification->created_at?->format('H:i') }}
                                </span>

                                <span class="requisition-list-meta">
                                    {{ $notification->created_at?->diffForHumans() }}
                                </span>

                                </div>

                                

                                
                            </td>

                            {{-- STATUS --}}
                            <td>
                                <span class="
                                    requisition-list-status
                                    {{ $isUnread
                                        ? 'requisition-list-status-pending'
                                        : 'requisition-list-status-approved' }}
                                ">
                                    <span class="requisition-list-status-dot"></span>
                                    {{ $isUnread ? 'Unread' : 'Read' }}
                                </span>
                            </td>

                            {{-- ACTIONS --}}
                            <td>
                                <div class="requisition-list-actions">

                                    {{-- OPEN --}}
                                    @if($url)
                                        <a href="{{ $url }}"
                                           class="requisition-list-action primary"
                                           title="Open notification"
                                           aria-label="Open notification">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endif

                                    {{-- MARK AS READ --}}

@if($isUnread)
    <button
        type="button"
        class="requisition-list-action success mark-single-notification-read"
        data-id="{{ $notification->id }}"
        data-url="{{ route('workflow-notifications.read', ['id' => $notification->id]) }}"
        title="Mark as read"
        aria-label="Mark as read"
    >
        <i class="fa fa-check"></i>
    </button>
@endif



                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="requisition-table-empty">
                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-bell-slash"></i>
                                    </div>

                                    <h5>No notifications found</h5>

                                    <p>
                                        There are no notifications matching your search.
                                    </p>

                                    @if(request()->hasAny(['search', 'status']))
                                        <a href="{{ route('workflow-notifications.index') }}"
                                           class="requisition-add-button">
                                            <i class="fa fa-refresh"></i>
                                            Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- SCROLL INDICATOR --}}
        @if($notifications->count() > 8)
            <div class="requisition-scroll-hint">
                <i class="fa fa-arrows-alt-v"></i>
                Scroll to view more notifications
            </div>
        @endif

        {{-- PAGINATION --}}
        @if($notifications->hasPages())
            <div class="requisition-pagination">
                {{ $notifications->appends(
                    request()->except('page')
                )->links() }}
            </div>
        @endif

    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

    const markAllButton = document.getElementById('markAllNotificationsRead');

    async function sendReadRequest(url) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({})
        });

        if (!response.ok) {
            throw new Error('Request failed: ' + response.status);
        }

        return response.json().catch(function () {
            return {};
        });
    }

    function updateNotificationCount() {
        const remainingUnread = document.querySelectorAll(
            '.mark-single-notification-read'
        ).length;

        // Update the page's notification count.
        const countBadge = document.querySelector('.requisition-count-badge');

        if (countBadge) {
            const total = @json($notifications->total());
            countBadge.textContent =
                total + (total === 1 ? ' Notification' : ' Notifications');
        }

        // Update the top navigation notification badge, if present.
        const navBadge = document.getElementById('workflowNotificationCount');

        if (navBadge) {
            const currentCount = parseInt(navBadge.textContent || '0', 10);
            navBadge.textContent = Math.max(0, currentCount - 1);

            if (parseInt(navBadge.textContent, 10) === 0) {
                navBadge.style.display = 'none';
            } else {
                navBadge.style.display = '';
            }
        }
    }

    document.querySelectorAll('.mark-single-notification-read')
        .forEach(function (button) {
            button.addEventListener('click', async function () {
                if (button.disabled) return;

                const row = button.closest('tr');
                const originalHtml = button.innerHTML;

                button.disabled = true;
                button.innerHTML =
                    '<i class="fa fa-spinner fa-spin"></i>';
                button.title = 'Saving...';

                try {
                    await sendReadRequest(button.dataset.url);

                    // Update this row without refreshing the page.
                    if (row) {
                        const status = row.querySelector(
                            '.requisition-list-status'
                        );

                        if (status) {
                            status.classList.remove(
                                'requisition-list-status-pending'
                            );
                            status.classList.add(
                                'requisition-list-status-approved'
                            );
                            status.innerHTML =
                                '<span class="requisition-list-status-dot"></span> Read';
                        }

                        const titleCell = row.cells[1];

                        if (titleCell) {
                            const unreadDot = titleCell.querySelector(
                                'i.fa-circle'
                            );

                            if (unreadDot) {
                                unreadDot.parentElement.remove();
                            }
                        }
                    }

                    button.remove();
                    updateNotificationCount();

                } catch (error) {
                    console.error(error);

                    button.disabled = false;
                    button.innerHTML = originalHtml;
                    button.title = 'Mark as read';

                    alert(
                        'Could not mark this notification as read. Please try again.'
                    );
                }
            });
        });

    // Mark all as read without reloading the page.
    if (markAllButton) {
        markAllButton.addEventListener('click', async function (event) {
            event.preventDefault();

            if (markAllButton.disabled) return;

            const originalHtml = markAllButton.innerHTML;

            markAllButton.disabled = true;
            markAllButton.innerHTML =
                '<i class="fa fa-spinner fa-spin"></i> Saving...';

            try {
                await sendReadRequest(
                    @json(route('workflow-notifications.read-all'))
                );

                document.querySelectorAll(
                    '.mark-single-notification-read'
                ).forEach(function (button) {
                    const row = button.closest('tr');

                    if (row) {
                        const status = row.querySelector(
                            '.requisition-list-status'
                        );

                        if (status) {
                            status.classList.remove(
                                'requisition-list-status-pending'
                            );
                            status.classList.add(
                                'requisition-list-status-approved'
                            );
                            status.innerHTML =
                                '<span class="requisition-list-status-dot"></span> Read';
                        }

                        const titleCell = row.cells[1];

                        if (titleCell) {
                            const unreadDot = titleCell.querySelector(
                                'i.fa-circle'
                            );

                            if (unreadDot) {
                                unreadDot.parentElement.remove();
                            }
                        }
                    }

                    button.remove();
                });

                const navBadge = document.getElementById(
                    'workflowNotificationCount'
                );

                if (navBadge) {
                    navBadge.textContent = '0';
                    navBadge.style.display = 'none';
                }

                markAllButton.remove();

            } catch (error) {
                console.error(error);

                markAllButton.disabled = false;
                markAllButton.innerHTML = originalHtml;

                alert(
                    'Could not mark all notifications as read. Please try again.'
                );
            }
        });
    }
});
</script>
@endsection


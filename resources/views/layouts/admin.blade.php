<!doctype html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#00096A">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="Teule">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    <title>TEULE KENYA || ADMIN</title>

    <link rel="shortcut icon" href="{{ asset('.../assets/img/logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/backend.css?v=2.0.0') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('admin-assets/vendor/line-awesome/dist/line-awesome/css/line-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/remixicon/fonts/remixicon.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/@icon/dripicons/dripicons.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fullcalendar/core/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fullcalendar/daygrid/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fullcalendar/timegrid/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/fullcalendar/list/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/vendor/mapbox/mapbox-gl.css') }}">
    <!-- Include Selectize CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin-assets/css/stores.css?v=2.0.2') }}">

    <style>
        :root { --teule-navy:#00096A; --teule-orange:#EF4700; --teule-ink:#17213f; --teule-muted:#7b849d; --teule-line:#e8ebf3; }
        .mm-sidebar { background:#fff; border-right:1px solid var(--teule-line); box-shadow:8px 0 28px rgba(13,27,78,.045); }
        .mm-sidebar-logo { min-height:82px; padding:15px 18px; border-bottom:1px solid #edf0f6; }
        .mm-sidebar-logo .header-logo { display:flex; align-items:center; gap:10px; min-width:0; text-decoration:none; }
        .mm-sidebar-logo .header-logo img { max-width:43px; max-height:43px; object-fit:contain; }
        .teule-sidebar-section-label { padding:22px 22px 8px; color:#98a0b5; font-size:9px; font-weight:800; letter-spacing:1.5px; }
        .side-menu > .teule-menu-group-label { margin:19px 12px 7px; padding:0 10px; color:#9aa2b5; font-size:9px; font-weight:800; letter-spacing:1.25px; list-style:none; pointer-events:none; }
        .side-menu > .teule-menu-group-label:first-of-type { margin-top:12px; }
        .side-menu { padding:0 12px 20px; }
        .side-menu > li { margin:3px 0; }
        .side-menu > li > a { display:flex; align-items:center; gap:10px; min-height:45px; padding:10px 12px !important; border-radius:11px; color:#59627b; font-size:12px; font-weight:700; transition:background .18s ease,color .18s ease,transform .18s ease; }
        .side-menu > li > a > span { flex:1; min-width:0; }
        .side-menu > li > a > i:first-child { display:inline-flex; align-items:center; justify-content:center; width:30px; min-width:30px; height:30px; margin:0 !important; border-radius:9px; color:var(--teule-navy); background:#f0f2fb; font-size:15px; }
        .side-menu > li > a > i:first-child svg { width:17px; height:17px; }
        .side-menu > li > a:hover { color:var(--teule-navy); background:#f5f6fc; transform:translateX(2px); }
        .side-menu > li > a[aria-expanded="true"], .side-menu > li > a.active { color:var(--teule-navy); background:#eef0fb; }
        .side-menu > li > a[aria-expanded="true"] > i:first-child, .side-menu > li > a.active > i:first-child { color:#fff; background:var(--teule-navy); }
        .side-menu .mm-arrow-right { margin-left:auto; font-size:10px; opacity:.65; }
        .side-menu .submenu, .side-menu > li > ul { margin:5px 0 9px 16px; padding:5px 0 5px 12px; border-left:1px solid #e3e7f2; }
        .side-menu .submenu li, .side-menu > li > ul li { margin:2px 0; }
        .side-menu .submenu a, .side-menu > li > ul a { display:flex; align-items:center; gap:9px; min-height:37px; padding:8px 10px !important; border-radius:8px; color:#727b93; font-size:11px; font-weight:600; transition:all .18s ease; }
        .side-menu .submenu a i, .side-menu > li > ul a i { width:17px; min-width:17px; color:#a1a8ba; text-align:center; font-size:12px; }
        .side-menu .submenu a:hover, .side-menu > li > ul a:hover { color:var(--teule-navy); background:#f6f7fc; }
        .side-menu .submenu a.active, .side-menu > li > ul a.active { color:var(--teule-navy); background:#f0f2fb; font-weight:800; }
        .side-menu .submenu a.active i, .side-menu > li > ul a.active i { color:var(--teule-orange); }
        .mm-top-navbar { background:rgba(255,255,255,.94); border-bottom:1px solid #e9ecf4; box-shadow:0 4px 20px rgba(13,27,78,.035); }
        .mm-navbar-custom { padding:0 18px; }
        .mm-navbar-logo .header-logo { display:flex; align-items:center; gap:8px; text-decoration:none; }
        .mm-navbar-logo .header-logo img { width:36px; height:36px; object-fit:contain; }
        .teule-brand-copy { display:flex; flex-direction:column; line-height:1.15; }
        .teule-brand-copy strong { color:var(--teule-navy); font-size:12px; letter-spacing:.5px; }
        .teule-brand-copy small { margin-top:4px; color:#8b93a8; font-size:9px; font-weight:600; }
        .mm-navbar-logo .wrapper-menu { display:inline-flex; align-items:center; justify-content:center; width:38px; height:38px; margin-right:8px; border-radius:10px; color:var(--teule-navy); background:#f0f2fb; cursor:pointer; }
        .mm-navbar-logo .wrapper-menu:hover { color:#fff; background:var(--teule-navy); }
        .mm-search-bar.device-search { max-width:300px; }
        .mm-search-bar.device-search .searchbox { display:flex; align-items:center; padding:0 12px; border:1px solid #e7eaf2; border-radius:10px; background:#f8f9fc; }
        .mm-search-bar.device-search .search-input { min-width:0; border:0; outline:0; background:transparent; }
        .navbar-list { gap:7px; }
        .navbar-list > li { display:flex; align-items:center; }
        .navbar-list .nav-link, .navbar-list .btn { border-radius:10px; }
        .change-mode { margin-right:8px; }
        .teule-account-nav { position:relative; margin-left:2px; }
        .teule-account-trigger { display:flex !important; align-items:center; gap:9px; min-height:48px; padding:5px 9px !important; border:1px solid #e7eaf3; border-radius:12px; background:#fff; color:var(--teule-navy) !important; text-decoration:none !important; transition:background .2s,border-color .2s,box-shadow .2s; }
        .teule-account-trigger:hover, .teule-account-nav.show .teule-account-trigger { background:#f6f7fc; border-color:#cbd2e9; box-shadow:0 4px 14px rgba(0,9,106,.08); }
        .teule-account-trigger:after { display:none !important; }
        .teule-account-avatar { position:relative; display:inline-flex; flex:0 0 38px; width:38px; height:38px; overflow:hidden; align-items:center; justify-content:center; border-radius:11px; background:linear-gradient(135deg,#00096A,#3449b8); box-shadow:0 3px 8px rgba(0,9,106,.16); }
        .teule-account-avatar img { display:block; width:100%; height:100%; object-fit:cover; }
        .teule-account-initials { display:flex; width:100%; height:100%; align-items:center; justify-content:center; color:#fff; font-size:13px; font-weight:800; letter-spacing:.5px; }
        .teule-account-online { position:absolute; right:0; bottom:0; width:10px; height:10px; border:2px solid #fff; border-radius:50%; background:#20a56b; }
        .teule-account-summary { display:flex; min-width:0; flex-direction:column; align-items:flex-start; line-height:1.25; }
        .teule-account-caption { color:#8188a0; font-size:8px; font-weight:800; letter-spacing:1px; }
        .teule-account-name { display:block; max-width:145px; overflow:hidden; color:var(--teule-navy); font-size:11px; font-weight:800; text-overflow:ellipsis; white-space:nowrap; }
        .teule-account-role { display:block; max-width:145px; overflow:hidden; color:#7b8298; font-size:9px; text-overflow:ellipsis; white-space:nowrap; }
        .teule-account-arrow { margin-left:2px; color:#7b8298; font-size:12px; transition:transform .2s; }
        .teule-account-nav.show .teule-account-arrow { transform:rotate(180deg); }
        .teule-account-menu { width:315px; max-width:calc(100vw - 20px); margin-top:10px !important; padding:0; overflow:hidden; border:1px solid #e6e9f2; border-radius:15px; background:#fff; box-shadow:0 16px 40px rgba(0,9,106,.16); }
        .teule-account-menu-top { padding:17px; background:linear-gradient(135deg,#00096A,#182b91); border-bottom:3px solid var(--teule-orange); }
        .teule-account-profile { display:flex; align-items:center; gap:12px; min-width:0; }
        .teule-account-avatar-lg { flex-basis:52px; width:52px; height:52px; border:2px solid rgba(255,255,255,.7); border-radius:14px; }
        .teule-account-details { display:flex; min-width:0; flex-direction:column; gap:3px; }
        .teule-account-details strong { overflow:hidden; color:#fff; font-size:13px; text-overflow:ellipsis; white-space:nowrap; }
        .teule-account-details small { overflow:hidden; color:rgba(255,255,255,.76); font-size:10px; text-overflow:ellipsis; white-space:nowrap; }
        .teule-account-role-badge { align-self:flex-start; max-width:210px; margin-top:3px; padding:4px 7px; overflow:hidden; border:1px solid rgba(255,255,255,.22); border-radius:20px; color:#fff; font-size:9px; text-overflow:ellipsis; white-space:nowrap; }
        .teule-account-menu-section { padding:12px 10px 8px; }
        .teule-account-section-label { display:block; padding:0 9px 7px; color:#929ab0; font-size:9px; font-weight:800; letter-spacing:1.2px; }
        .teule-account-item { display:flex !important; align-items:center; gap:10px; padding:10px 9px !important; border-radius:9px; white-space:normal; }
        .teule-account-item > i:first-child { display:flex; align-items:center; justify-content:center; width:32px; height:32px; border-radius:9px; background:#f0f2fb; color:var(--teule-navy); font-size:17px; }
        .teule-account-item > span { display:flex; min-width:0; flex:1; flex-direction:column; gap:2px; }
        .teule-account-item strong { color:#28314d; font-size:11px; }
        .teule-account-item small { color:#8b93a8; font-size:9px; }
        .teule-account-item-arrow { color:#a0a7b8; font-size:12px; }
        .teule-account-item:hover { background:#f6f7fc; }
        .teule-account-menu-footer { padding:10px; border-top:1px solid #edf0f6; background:#fbfcfe; }
        .teule-account-logout { display:flex; width:100%; align-items:center; gap:9px; padding:10px; border:0; border-radius:9px; background:#fff1ed; color:#b93418; text-align:left; }
        .teule-account-logout > i { font-size:17px; }
        .teule-account-logout > span { font-size:11px; font-weight:800; }
        .teule-account-logout > small { margin-left:auto; color:#bc7567; font-size:9px; }
        .teule-account-logout:hover { background:#ffe4dc; }
        .teule-account-footer-note { padding:12px 2px 1px; color:#9aa1b3; font-size:9px; text-align:center; }
        .teule-account-footer-note i { margin-right:4px; color:var(--teule-navy); }
        .workflow-notification-trigger { display:flex; position:relative; align-items:center; justify-content:center; width:42px; height:42px; border:1px solid #e7eaf3; background:#fff; color:var(--teule-navy); }
        .workflow-notification-trigger:hover { background:#f3f5fc; }
        .workflow-notification-count { top:2px !important; right:2px !important; }
        .full-screen > a { display:flex; align-items:center; justify-content:center; width:42px; height:42px; border:1px solid #e7eaf3; border-radius:10px; }
        .content-page { min-height:calc(100vh - 75px); }
        @media (max-width: 991.98px) {
            .mm-navbar-custom { padding:0 12px; }
            .mm-search-bar.device-search { display:none !important; }
            .navbar-collapse#navbarSupportedContent { display:flex !important; flex-basis:auto; }
            .navbar-list { flex-direction:row !important; gap:4px; }
            .navbar-toggler { display:none !important; }
            .teule-account-summary { display:none; }
            .teule-account-trigger { padding:4px !important; border-color:transparent; background:transparent; }
            .teule-account-arrow { display:none; }
            .change-mode { margin-right:2px; }
        }
        @media (max-width: 575.98px) {
            .mm-navbar-custom { padding:0 8px; }
            .mm-navbar-logo .header-logo img { width:30px; height:30px; }
            .teule-brand-copy strong { font-size:10px; }
            .teule-brand-copy small { display:none; }
            .mm-navbar-logo .wrapper-menu { width:32px; height:32px; margin-right:5px; }
            .search-content, .full-screen { display:none !important; }
            .change-mode { transform:scale(.88); transform-origin:center; }
            .navbar-list { gap:1px; }
            .teule-account-avatar { width:34px; height:34px; flex-basis:34px; }
            .teule-account-menu { position:fixed !important; top:62px !important; right:8px !important; left:auto !important; width:min(315px,calc(100vw - 16px)); margin-top:0 !important; transform:none !important; }
            .workflow-notification-menu { position:fixed !important; top:62px !important; right:8px !important; left:auto !important; width:min(360px,calc(100vw - 16px)); max-width:calc(100vw - 16px); }
            .mm-sidebar-logo { min-height:72px; }
        }
        body.color-dark .mm-sidebar, body.dark-mode .mm-sidebar { background:#111827; border-right-color:#283247; }
        body.color-dark .mm-sidebar-logo, body.dark-mode .mm-sidebar-logo { border-bottom-color:#283247; }
        body.color-dark .teule-sidebar-section-label, body.dark-mode .teule-sidebar-section-label,
        body.color-dark .side-menu > .teule-menu-group-label, body.dark-mode .side-menu > .teule-menu-group-label { color:#8792aa; }
        body.color-dark .side-menu > li > a, body.dark-mode .side-menu > li > a { color:#c4cbe0; }
        body.color-dark .side-menu > li > a > i:first-child, body.dark-mode .side-menu > li > a > i:first-child { color:#d7dcf0; background:#222d45; }
        body.color-dark .side-menu > li > a:hover, body.dark-mode .side-menu > li > a:hover,
        body.color-dark .side-menu > li > a[aria-expanded="true"], body.dark-mode .side-menu > li > a[aria-expanded="true"] { color:#fff; background:#202c49; }
        body.color-dark .side-menu .submenu, body.dark-mode .side-menu .submenu,
        body.color-dark .side-menu > li > ul, body.dark-mode .side-menu > li > ul { border-left-color:#34415b; }
        body.color-dark .side-menu .submenu a, body.dark-mode .side-menu .submenu a,
        body.color-dark .side-menu > li > ul a, body.dark-mode .side-menu > li > ul a { color:#aab4cc; }
        body.color-dark .side-menu .submenu a:hover, body.dark-mode .side-menu .submenu a:hover,
        body.color-dark .side-menu > li > ul a:hover, body.dark-mode .side-menu > li > ul a:hover { color:#fff; background:#202c49; }
        body.color-dark .mm-top-navbar, body.dark-mode .mm-top-navbar { background:rgba(17,24,39,.96); border-bottom-color:#283247; }
        body.color-dark .teule-account-trigger, body.dark-mode .teule-account-trigger { background:#172033; border-color:#303b52; }
        body.color-dark .teule-account-name, body.dark-mode .teule-account-name { color:#e5eaff; }
        body.color-dark .teule-account-menu, body.dark-mode .teule-account-menu { background:#111827; border-color:#303b52; }
        body.color-dark .teule-account-menu-section, body.dark-mode .teule-account-menu-section,
        body.color-dark .teule-account-menu-footer, body.dark-mode .teule-account-menu-footer { background:#111827; border-color:#303b52; }
        body.color-dark .teule-account-item strong, body.dark-mode .teule-account-item strong { color:#e4e9f7; }
        body.color-dark .teule-account-item:hover, body.dark-mode .teule-account-item:hover { background:#202c49; }
        body.color-dark .teule-account-footer-note, body.dark-mode .teule-account-footer-note { color:#8d98b0; }

        @media (prefers-reduced-motion: reduce) {
            .side-menu > li > a, .side-menu .submenu a, .teule-account-trigger, .teule-account-arrow { transition:none !important; }
        }
    </style>




</head>

<body class=" color-light ">
    @php
        $teuleNotificationSound = auth()->check()
            ? \Illuminate\Support\Facades\DB::table('user_details')
                ->where('user_id', auth()->id())
                ->where('key', 'notification_sound')
                ->value('value')
            : null;
        $teuleNotificationSound = is_string($teuleNotificationSound) ? basename($teuleNotificationSound) : '';
        $teuleNotificationSoundPath = $teuleNotificationSound !== ''
            && file_exists(public_path('sounds/notifications/' . $teuleNotificationSound))
                ? asset('sounds/notifications/' . rawurlencode($teuleNotificationSound))
                : asset('sounds/teule_notification.mp3');
    @endphp
    <audio id="workflowNotificationAudio" src="{{ $teuleNotificationSoundPath }}" preload="auto"></audio>

    @php
        $teuleCurrentUser = auth()->user();
        $teuleUserImagePath = $teuleCurrentUser
            ? \Illuminate\Support\Facades\DB::table('user_details')
                ->where('user_id', $teuleCurrentUser->id)
                ->where('key', 'img_url')
                ->value('value')
            : null;

        $teuleUserImagePath = is_string($teuleUserImagePath) ? trim($teuleUserImagePath) : '';
        $teuleUserImageUrl = $teuleUserImagePath !== ''
            ? (filter_var($teuleUserImagePath, FILTER_VALIDATE_URL)
                ? $teuleUserImagePath
                : asset(ltrim($teuleUserImagePath, '/')))
            : null;

        $teuleUserInitials = collect(preg_split('/\s+/', trim($teuleCurrentUser->name ?? 'User')))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');
        $teuleUserInitials = $teuleUserInitials ?: 'U';
        $teuleUserRole = $teuleCurrentUser && method_exists($teuleCurrentUser, 'getRoleNames')
            ? ($teuleCurrentUser->getRoleNames()->implode(', ') ?: 'Team Member')
            : 'Team Member';
    @endphp


    <div id="loading">
        <div id="loading-center">
        </div>
    </div>


    <div class="wrapper">

        <div class="mm-sidebar sidebar-default">

            <div class="mm-sidebar-logo d-flex align-items-center justify-content-between">

                <a href="{{ url('/admin') }}" class="header-logo">
                    <img src="{{ asset('../assets/img/logo.png') }}" class="img-fluid rounded-normal light-logo"
                        alt="logo">

                    <img src="{{ asset('../assets/img/logo.png') }}" class="img-fluid rounded-normal darkmode-logo"
                        alt="logo">
                    <span class="teule-brand-copy">
                        <strong>TEULE KENYA</strong>
                      
                    </span>
                </a>

                <div class="side-menu-bt-sidebar">
                    <i class="fa fa-bars wrapper-menu"></i>
                </div>

            </div>

            <div class="data-scrollbar" data-scroll="1">

                <nav class="mm-sidebar-menu">

                    <div class="teule-sidebar-section-label">WORKSPACE NAVIGATION</div>

                    <ul id="mm-sidebar-toggle" class="side-menu">

                        {{-- =========================================================
                        DASHBOARD
                        ========================================================== --}}
                        <li class>

                            <a href="#Dashboards" class="collapsed svg-icon" data-toggle="collapse"
                                aria-expanded="false">

                                <i class>
                                    <svg class="svg-icon" id="mm-dash" width="20" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </i>

                                <span class="ml-2">Dashboards</span>

                            </a>

                            <ul id="Dashboards" class="collapse" data-parent="#mm-sidebar-toggle">

                                <li class>

                                    <a href="{{ url('/admin') }}" class="svg-icon">

                                        <i class>

                                            <svg class="svg-icon" id="mm-dash-3" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />

                                            </svg>

                                        </i>

                                        <span>Dashboard</span>

                                    </a>

                                </li>

                            </ul>

                        </li>


                        

                        <li class="teule-menu-group-label" aria-hidden="true">CHILD & OPERATIONS</li>

                        {{-- =========================================================
                        CHILDREN
                        ========================================================== --}}
                        @if(
                        auth()->user()->can('VIEW CHILDREN') ||
                        auth()->user()->can('CREATE CHILDREN') ||
                        auth()->user()->can('EDIT CHILDREN') ||
                        auth()->user()->can('DELETE CHILDREN')
                        )

                        <li>

                            <a href="#Children" class="collapsed svg-icon" data-toggle="collapse" aria-expanded="false">

                                <i class>

                                    <svg class="svg-icon" id="mm-extra-1" width="20" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />

                                    </svg>

                                </i>

                                <span class="ml-2">Children</span>

                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>

                            </a>

                            <ul id="Children" class="submenu collapse" data-parent="#mm-sidebar-toggle">

                                @can('CREATE CHILDREN')
                                <li>

                                    <a href="{{ route('newchild.index') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>New Child</span>

                                    </a>

                                </li>
                                @endcan

                                @can('VIEW CHILDREN')
                                <li>

                                    <a href="{{ route('children.index') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>Children List</span>

                                    </a>

                                </li>

                                <li>

                                    <a href="{{ route('children.sponscard') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>Children Sponsorship Card</span>

                                    </a>

                                </li>
                                @endcan

                            </ul>

                        </li>

                        @endif

                        

                        {{-- =========================================================
                        STORES & INVENTORY
                        ========================================================== --}}

                        @if(
                        auth()->user()->can('MAKE REQUISITION') ||
                        auth()->user()->can('APPROVE STORE REQUISITIONS') ||
                        auth()->user()->can('APPROVE STORE REQUISITIONS - HOD') ||
                        auth()->user()->can('APPROVE STORE REQUISITIONS - MANAGEMENT') ||
                        auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                        )

                        <li class="nav-item">

                            <a href="#stores_inventory" class="collapsed svg-icon" data-toggle="collapse"
                                aria-expanded="false">

                                <i>
                                    <svg class="svg-icon" width="20" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0v10l-8 4m8-14l-8 4m0 0L4 7m8 4v10" />

                                    </svg>
                                </i>

                                <span class="ml-2">
                                    Stores & Inventory
                                </span>

                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>

                            </a>


                            <ul id="stores_inventory" class="submenu collapse" data-parent="#mm-sidebar-toggle">


                                {{-- =================================================
                                REQUISITIONS
                                ================================================= --}}

                                @if(
                                auth()->user()->can('MAKE REQUISITION') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - HOD') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - MANAGEMENT') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                                )

                                <li>
                                    <a href="{{ route('admin.stores.store-requisitions.index') }}" class="svg-icon">

                                        <i class="fa fa-list-alt"></i>
                                        <span>Store Requisitions</span>

                                    </a>
                                </li>

                                @endif


                                {{-- NEW REQUISITION --}}

                                @can('MAKE REQUISITION')

                                <li>
                                    <a href="{{ route('admin.stores.store-requisitions.create') }}" class="svg-icon">

                                        <i class="fa fa-plus-circle"></i>
                                        <span>New Requisition</span>

                                    </a>
                                </li>

                                @endcan



                                {{-- =================================================
                                LOCAL PURCHASE ORDERS (LPOs)
                                ================================================= --}}

                                @if(
                                auth()->user()->can('MAKE REQUISITION') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                                )

                                <li>

                                    <a href="{{ route('admin.store-lpos.index') }}" class="svg-icon">

                                        <i class="fa fa-file"></i>
                                        <span>Local Purchase Orders</span>

                                    </a>

                                </li>


                                @endif

                                @if(
                                auth()->user()->can('MAKE REQUISITION') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                                )

                                <li>

                                    <a href="{{ route('admin.suppliers.index') }}" class="svg-icon">

                                        <i class="fa fa-truck"></i>

                                        <span>Suppliers</span>

                                    </a>

                                </li>

                                @endif



                                {{-- =================================================
                                STORES
                                ================================================= --}}

                                @if(
                                auth()->user()->can('MAKE REQUISITION') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                                )

                                <li>
                                    <a href="{{ route('admin.stores.index') }}" class="svg-icon">

                                        <i class="fa fa-building"></i>
                                        <span>Stores</span>

                                    </a>
                                </li>

                                @endif


                                {{-- =================================================
                                STORE ITEMS
                                ================================================= --}}

                                @if(
                                auth()->user()->can('MAKE REQUISITION') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                                )

                                <li>
                                    <a href="{{ route('admin.store-items.index') }}" class="svg-icon">

                                        <i class="fa fa-cubes"></i>
                                        <span>Store Items</span>

                                    </a>
                                </li>

                                @endif


                                {{-- =================================================
                                ITEM CATEGORIES
                                ================================================= --}}

                                @if(
                                auth()->user()->can('MAKE REQUISITION') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                                )

                                <li>
                                    <a href="{{ route('admin.store-categories.index') }}" class="svg-icon">

                                        <i class="fa fa-tags"></i>
                                        <span>Item Categories</span>

                                    </a>
                                </li>

                                @endif


                                {{-- =================================================
                                UNITS
                                ================================================= --}}

                                @if(
                                auth()->user()->can('MAKE REQUISITION') ||
                                auth()->user()->can('APPROVE STORE REQUISITIONS - STORES')
                                )

                                <li>
                                    <a href="{{ route('admin.store-units.index') }}" class="svg-icon">

                                        <i class="fa fa-balance-scale"></i>
                                        <span>Units</span>

                                    </a>
                                </li>

                                @endif


                                {{-- =================================================
                                STOCK RECEIPTS / GOODS INWARDS
                                ================================================= --}}

                                @can('APPROVE STORE REQUISITIONS - STORES')

                                <li>
                                    <a href="{{ route('admin.store-receipts.index') }}" class="svg-icon">

                                        <i class="fa fa-download"></i>
                                        <span>Stock Receipts</span>

                                    </a>
                                </li>

                                @endcan


                                {{-- =================================================
                                STOCK BALANCES
                                ================================================= --}}

                                @can('APPROVE STORE REQUISITIONS - STORES')
                                <li>
                                    <a href="{{ route('admin.store-stock.index') }}" class="svg-icon">
                                        <i class="fa fa-database"></i>
                                        <span>Stock Balances</span>
                                    </a>
                                </li>
                                @endcan

                                @can('ADJUST STORE STOCK')
                                <li>
                                    <a href="{{ route('admin.store-stock-adjustments.create') }}" class="svg-icon">
                                        <i class="fa fa-adjust"></i>
                                        <span>Adjust Store Stock</span>
                                    </a>
                                </li>
                                @endcan


                                @can('VIEW STORE REPORTS')
                                <li>
                                    <a href="{{ route('admin.store-reports.index') }}" class="svg-icon">
                                        <i class="fa fa-chart-bar"></i>
                                        <span>Store Reports</span>
                                    </a>
                                </li>
                                @endcan

                            </ul>

                        </li>

                        @endif

                        

                        {{-- =========================================================
                        MERCHANDISE
                        ========================================================== --}}
                        @if(
                        auth()->user()->can('VIEW MERCHANDISE') ||
                        auth()->user()->can('CREATE MERCHANDISE') ||
                        auth()->user()->can('MANAGE MERCHANDISE')
                        )

                        <li class="nav-item">

                            <a href="#merchandise" class="collapsed svg-icon" data-toggle="collapse"
                                aria-expanded="false">

                                <i class>

                                    <svg class="svg-icon" id="mm-extra-1" width="20" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 012 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />

                                    </svg>

                                </i>

                                <span class="ml-2">Merchandise</span>

                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>

                            </a>

                            <ul id="merchandise" class="submenu collapse" data-parent="#mm-sidebar-toggle">


                                {{-- ALL PRODUCTS --}}
                                @if(
                                auth()->user()->can('VIEW MERCHANDISE') ||
                                auth()->user()->can('MANAGE MERCHANDISE')
                                )

                                <li>

                                    <a href="{{ route('admin.merchandise.index') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>All Products</span>

                                    </a>

                                </li>

                                @endif


                                {{-- CREATE NEW --}}
                                @if(
                                auth()->user()->can('CREATE MERCHANDISE') ||
                                auth()->user()->can('MANAGE MERCHANDISE')
                                )

                                <li>

                                    <a href="{{ route('admin.merchandise.create') }}" class="svg-icon">

                                        <i class="fa fa-plus-circle"></i>

                                        <span>Create New</span>

                                    </a>

                                </li>

                                @endif

                            </ul>

                        </li>

                        @endif

                        <li class="teule-menu-group-label" aria-hidden="true">FINANCE & GIVING</li>

                        {{-- =========================================================
                        GIVING & PAYMENTS
                        ========================================================== --}}
                        @if(
                        auth()->user()->can('VIEW DONATIONS') ||
                        auth()->user()->can('CREATE DONATIONS') ||
                        auth()->user()->can('MANAGE DONATIONS') ||
                        auth()->user()->can('VIEW DONORS') ||
                        auth()->user()->can('MANAGE DONORS') ||
                        auth()->user()->can('VIEW MPESA TRANSACTIONS') ||
                        auth()->user()->can('REVIEW MPESA TRANSACTIONS') ||
                        auth()->user()->can('VIEW DONATION COMMUNICATIONS') ||
                        auth()->user()->can('MANAGE DONATION COMMUNICATIONS')
                        )

                        <li>

                            <a href="#comms" class="collapsed svg-icon" data-toggle="collapse" aria-expanded="false">

                                <i>

                                    <svg class="svg-icon" id="mm-extra-1" width="20" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-2.21 0-4 1.343-4 3s1.79 3 4 3 4 1.343 4 3-1.79 3-4 3m0-12V5m0 14v-3m0-8a9 9 0 100 18 9 9 0 000-18z" />

                                    </svg>

                                </i>

                                <span class="ml-2">Giving & Payments</span>

                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>

                            </a>

                            <ul id="comms" class="submenu collapse" data-parent="#mm-sidebar-toggle">


                                {{-- DONATIONS --}}
                                @if(
                                auth()->user()->can('VIEW DONATIONS') ||
                                auth()->user()->can('CREATE DONATIONS') ||
                                auth()->user()->can('MANAGE DONATIONS')
                                )

                                <li>

                                    <a href="{{ route('admin.donations.index') }}" class="svg-icon">

                                        <i class="fa fa-gift"></i>

                                        <span>Donations</span>

                                    </a>

                                </li>

                                @endif


                                {{-- M-PESA --}}
                                @if(
                                auth()->user()->can('VIEW MPESA TRANSACTIONS') ||
                                auth()->user()->can('REVIEW MPESA TRANSACTIONS')
                                )

                                <li>

                                    <a href="{{ route('admin.mpesa-transactions.index') }}" class="svg-icon">

                                        <i class="fa fa-mobile"></i>

                                        <span>M-Pesa Reviews</span>

                                    </a>

                                </li>

                                @endif


                                {{-- DONORS --}}
                                @if(
                                auth()->user()->can('VIEW DONORS') ||
                                auth()->user()->can('MANAGE DONORS')
                                )

                                <li>

                                    <a href="{{ route('admin.donors.index') }}" class="svg-icon">

                                        <i class="fa fa-users"></i>

                                        <span>Donors</span>

                                    </a>

                                </li>

                                @endif


                                {{-- COMMUNICATIONS --}}
                                @if(
                                auth()->user()->can('VIEW DONATION COMMUNICATIONS') ||
                                auth()->user()->can('MANAGE DONATION COMMUNICATIONS')
                                )

                                <li>

                                    <a href="{{ route('admin.donation-communications.index') }}" class="svg-icon">

                                        <i class="fa fa-paper-plane"></i>

                                        <span>Communications</span>

                                    </a>

                                </li>

                                @endif


                                {{-- RECEIVE DONATION --}}
                                @if(
                                auth()->user()->can('CREATE DONATIONS') ||
                                auth()->user()->can('MANAGE DONATIONS')
                                )

                                <li>

                                    <a href="{{ route('admin.donations.create') }}" class="svg-icon">

                                        <i class="fa fa-plus-circle"></i>

                                        <span>Receive Donation</span>

                                    </a>

                                </li>

                                @endif

                            </ul>

                        </li>

                        @endif


                        

                        {{-- =========================================================
                        PETTY CASH
                        ========================================================== --}}
                        @if(
                        auth()->user()->can('VIEW PETTY CASH') ||
                        auth()->user()->can('CREATE PETTY CASH') ||
                        auth()->user()->can('MANAGE PETTY CASH') ||
                        auth()->user()->can('APPROVE PETTY CASH')
                        )

                        <li class="nav-item">

                            <a href="#roles_perm" class="collapsed svg-icon" data-toggle="collapse"
                                aria-expanded="false">

                                <i class>

                                    <svg class="svg-icon" id="mm-extra-1" width="20" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />

                                    </svg>

                                </i>

                                <span class="ml-2">Petty Cash</span>

                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>

                            </a>

                            <ul id="roles_perm" class="submenu collapse" data-parent="#mm-sidebar-toggle">


                                {{-- ALL PETTY CASH --}}
                                @if(
                                auth()->user()->can('VIEW PETTY CASH') ||
                                auth()->user()->can('MANAGE PETTY CASH') ||
                                auth()->user()->can('APPROVE PETTY CASH')
                                )

                                <li>

                                    <a href="{{ route('admin.pettycash.index') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>All Petty Cash</span>

                                    </a>

                                </li>

                                @endif


                                {{-- REQUEST --}}
                                @if(
                                auth()->user()->can('CREATE PETTY CASH') ||
                                auth()->user()->can('MANAGE PETTY CASH')
                                )

                                <li>

                                    <a href="{{ route('admin.pettycash.create') }}" class="svg-icon">

                                        <i class="fa fa-plus-circle"></i>

                                        <span>Request</span>

                                    </a>

                                </li>

                                @endif

                            </ul>

                        </li>

                        @endif


                        

                        <li class="teule-menu-group-label" aria-hidden="true">ADMINISTRATION</li>

                        {{-- =========================================================
                        ROLES & PERMISSIONS
                        ========================================================== --}}
                        @if(
                        auth()->user()->can('VIEW USERS') ||
                        auth()->user()->can('CREATE USERS') ||
                        auth()->user()->can('MANAGE USERS') ||
                        auth()->user()->can('VIEW ROLES') ||
                        auth()->user()->can('CREATE ROLES') ||
                        auth()->user()->can('MANAGE ROLES') ||
                        auth()->user()->can('VIEW PERMISSIONS') ||
                        auth()->user()->can('CREATE PERMISSIONS') ||
                        auth()->user()->can('MANAGE PERMISSIONS')
                        )

                        <li class="nav-item">

                            <a href="#requisition" class="collapsed svg-icon" data-toggle="collapse"
                                aria-expanded="false">

                                <i class="fa fa-user"></i>

                                <span class="ml-2">Roles & Permissions</span>

                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>

                            </a>

                            <ul id="requisition" class="submenu collapse" data-parent="#mm-sidebar-toggle">


                                {{-- ALL USERS --}}
                                @if(
                                auth()->user()->can('VIEW USERS') ||
                                auth()->user()->can('CREATE USERS') ||
                                auth()->user()->can('MANAGE USERS')
                                )

                                <li>

                                    <a href="{{ route('admin.user.index') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>All Users List</span>

                                    </a>

                                </li>

                                @endif


                                {{-- NEW USER --}}
                                @if(
                                auth()->user()->can('CREATE USERS') ||
                                auth()->user()->can('MANAGE USERS')
                                )

                                <li>

                                    <a href="{{ route('admin.user.create') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>New User</span>

                                    </a>

                                </li>

                                @endif


                                {{-- MANAGE ROLES --}}
                                @if(
                                auth()->user()->can('VIEW ROLES') ||
                                auth()->user()->can('MANAGE ROLES')
                                )

                                <li>

                                    <a href="{{ route('admin.roles.index') }}" class="svg-icon">

                                        <i class="fa fa-shield"></i>

                                        <span>Manage Roles</span>

                                    </a>

                                </li>

                                @endif


                                {{-- CREATE ROLE --}}
                                @if(
                                auth()->user()->can('CREATE ROLES') ||
                                auth()->user()->can('MANAGE ROLES')
                                )

                                <li>

                                    <a href="{{ route('admin.roles.create') }}" class="svg-icon">

                                        <i class="fa fa-plus-circle"></i>

                                        <span>Create Role</span>

                                    </a>

                                </li>

                                @endif


                                {{-- MANAGE PERMISSIONS --}}
                                @if(
                                auth()->user()->can('VIEW PERMISSIONS') ||
                                auth()->user()->can('MANAGE PERMISSIONS')
                                )

                                <li>

                                    <a href="{{ route('admin.permissions.index') }}" class="svg-icon">

                                        <i class="fa fa-key"></i>

                                        <span>Manage Permissions</span>

                                    </a>

                                </li>

                                @endif


                                {{-- CREATE PERMISSION --}}
                                @if(
                                auth()->user()->can('CREATE PERMISSIONS') ||
                                auth()->user()->can('MANAGE PERMISSIONS')
                                )

                                <li>

                                    <a href="{{ route('admin.permissions.create') }}" class="svg-icon">

                                        <i class="fa fa-plus-circle"></i>

                                        <span>Create Permission</span>

                                    </a>

                                </li>

                                @endif

                            </ul>

                        </li>

                        @endif


                        

                        {{-- =========================================================
                        SYSTEM
                        ========================================================== --}}
                        @if(
                        auth()->user()->can('VIEW SYSTEM DETAILS') ||
                        auth()->user()->can('MANAGE SYSTEM DETAILS')
                        )

                        <li>

                            <a href="#System" class="collapsed svg-icon" data-toggle="collapse" aria-expanded="false">

                                <i class>

                                    <svg class="svg-icon" id="mm-extra-1" width="20" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />

                                    </svg>

                                </i>

                                <span class="ml-2">System</span>

                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>

                            </a>

                            <ul id="System" class="submenu collapse" data-parent="#mm-sidebar-toggle">


                                {{-- SYSTEM VARIABLE LIST --}}
                                @if(
                                auth()->user()->can('VIEW SYSTEM DETAILS') ||
                                auth()->user()->can('MANAGE SYSTEM DETAILS')
                                )

                                <li>

                                    <a href="{{ route('admin.system_details.index') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>System Variable List</span>

                                    </a>

                                </li>

                                @endif


                                {{-- NEW SYSTEM DATA --}}
                                @can('MANAGE SYSTEM DETAILS')

                                <li>

                                    <a href="{{ route('admin.system_details.add') }}" class="svg-icon">

                                        <i class="fa fa-asterisk"></i>

                                        <span>New System Data</span>

                                    </a>

                                </li>

                                @endcan

                            </ul>

                        </li>

                        @endif


                        

                    </ul>

                </nav>

                <div class="pt-5 pb-2"></div>

            </div>

        </div>


        {{-- ============================================================
        TOP NAVBAR
        RESPONSIVE TEULE WORKSPACE HEADER
        ============================================================= --}}

        <div class="mm-top-navbar">

            <div class="mm-navbar-custom">

                <nav class="navbar navbar-expand-lg navbar-light p-0">

                    <div class="mm-navbar-logo d-flex align-items-center justify-content-between">

                        <i class="ri-menu-line wrapper-menu"></i>

                        <a href="{{ url('/admin') }}" class="header-logo">

                            <img src="{{ asset('../assets/img/logo.png') }}"
                                class="img-fluid rounded-normal darkmode-logo" alt="logo">

                            <span class="teule-brand-copy">
                                <strong>TEULE KENYA</strong>
                                <small>Management workspace</small>
                            </span>

                        </a>

                    </div>

                    <div class="mm-search-bar device-search m-auto">

                        <form action="#" class="searchbox">

                            <a class="search-link" href="#">
                                <i class="ri-search-line"></i>
                            </a>

                            <input type="text" class="text search-input" placeholder="Search here...">

                        </form>

                    </div>

                    <div class="d-flex align-items-center">

                        <div class="change-mode mt-3">

                          <li class="nav-item nav-icon dropdown">
                                    <div class="dropdown workflow-notification-dropdown"
                                        id="workflowNotificationDropdown"
                                        data-feed-url="{{ route('workflow-notifications.feed') }}"
                                        data-history-url="{{ route('workflow-notifications.index') }}"
                                        data-read-url="{{ url('/workflow-notifications') }}"
                                        data-read-all-url="{{ route('workflow-notifications.read-all') }}">

                                        <button type="button" class="btn workflow-notification-trigger"
                                            id="workflowNotificationToggle" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false" aria-label="Open notifications">
                                            <i class="fa fa-bell"></i>
                                            <span class="workflow-notification-count" id="workflowNotificationCount"
                                                style="display:none;">0</span>
                                        </button>

                                        <div class="dropdown-menu dropdown-menu-right workflow-notification-menu"
                                            aria-labelledby="workflowNotificationToggle">

                                            <!-- Header -->
                                            <div class="workflow-notification-menu-header">
                                                <div class="workflow-notification-heading">
                                                    <h6>Notifications</h6>
                                                    <small class="text-muted" id="workflowNotificationSubtitle">
                                                        Checking for updates...
                                                    </small>
                                                </div>

                                                <button type="button" class="workflow-notification-mark-all"
                                                    id="workflowNotificationMarkAll">
                                                    Mark all read
                                                </button>
                                            </div>

                                            <!-- Compact browser notification settings -->
<div class="workflow-push-settings">
    <div class="workflow-push-icon">
        <i class="fa fa-bell"></i>
    </div>

    <div class="workflow-push-content">
        <div class="workflow-push-title">Browser notifications</div>

        <small id="pushNotificationStatus" class="workflow-push-status">
            Receive notifications when you are not using this page.
        </small>
    </div>

    <button type="button"
            id="togglePushNotifications"
            class="btn btn-sm btn-outline-primary workflow-push-toggle"
            data-vapid-public-key="{{ config('webpush.vapid.public_key') }}"
            data-store-url="{{ route('push-subscriptions.store') }}"
            data-delete-url="{{ route('push-subscriptions.destroy') }}">
        <span id="pushNotificationButtonText">Enable</span>
    </button>
</div>

                                            <!-- Notification list -->
                                            <div class="workflow-notification-list" id="workflowNotificationList">
                                                <div class="workflow-notification-empty">
                                                    Loading notifications...
                                                </div>
                                            </div>

                                            <!-- Footer -->
                                            <a href="{{ route('workflow-notifications.index') }}"
                                                class="workflow-notification-footer">
                                                View all notifications
                                                <i class="fa fa-arrow-right ml-1"></i>
                                            </a>

                                        </div>
                                    </div>
                                </li>
                                
                           <li class="nav-item dropdown teule-account-nav">
                                    <a href="#"
                                       class="nav-link teule-account-trigger"
                                       id="teuleAccountDropdown"
                                       data-toggle="dropdown"
                                       aria-haspopup="true"
                                       aria-expanded="false">
                                        <span class="teule-account-avatar">
                                            @if($teuleUserImageUrl)
                                                <img src="{{ $teuleUserImageUrl }}" alt="{{ $teuleCurrentUser->name }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <span class="teule-account-initials" style="display:none">{{ $teuleUserInitials }}</span>
                                            @else
                                                <span class="teule-account-initials">{{ $teuleUserInitials }}</span>
                                            @endif
                                            <span class="teule-account-online" title="Signed in"></span>
                                        </span>
                                        <span class="teule-account-summary">
                                            <span class="teule-account-caption">SIGNED IN AS</span>
                                            <span class="teule-account-name">{{ $teuleCurrentUser->name ?? 'User' }}</span>
                                            <span class="teule-account-role">{{ $teuleUserRole }}</span>
                                        </span>
                                        <i class="fa fa-chevron-down teule-account-arrow" aria-hidden="true"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right teule-account-menu"
                                         aria-labelledby="teuleAccountDropdown">
                                        <div class="teule-account-menu-top">
                                            <div class="teule-account-profile">
                                                <span class="teule-account-avatar teule-account-avatar-lg">
                                                    @if($teuleUserImageUrl)
                                                        <img src="{{ $teuleUserImageUrl }}" alt="{{ $teuleCurrentUser->name }}"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <span class="teule-account-initials" style="display:none">{{ $teuleUserInitials }}</span>
                                                    @else
                                                        <span class="teule-account-initials">{{ $teuleUserInitials }}</span>
                                                    @endif
                                                </span>
                                                <div class="teule-account-details">
                                                    <strong>{{ $teuleCurrentUser->name ?? 'User' }}</strong>
                                                    <small>{{ $teuleCurrentUser->email ?? '' }}</small>
                                                    <span class="teule-account-role-badge">{{ $teuleUserRole }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="teule-account-menu-section">
                                            <span class="teule-account-section-label">ACCOUNT</span>
                                            @if(\Illuminate\Support\Facades\Route::has('admin.profile.show'))
                                                <a class="dropdown-item teule-account-item" href="{{ route('admin.profile.show') }}">
                                                    <i class="fa fa-user-circle" aria-hidden="true"></i>
                                                    <span><strong>My Profile</strong><small>View or update your details</small></span>
                                                    <i class="fa fa-angle-right teule-account-item-arrow" aria-hidden="true"></i>
                                                </a>
                                            @endif
                                        </div>

                                        <div class="teule-account-menu-footer">
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="teule-account-logout">
                                                    <i class="fa fa-power-off" aria-hidden="true"></i>
                                                    <span>Sign out</span>
                                                    <small>End your session</small>
                                                </button>
                                            </form>
                                            <div class="teule-account-footer-note">
                                                <i class="las la-shield-alt" aria-hidden="true"></i>
                                                Teule Kenya Management System
                                            </div>
                                        </div>
                                        
                                    </div>
                                </li>


                        </div>

                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-label="Toggle navigation">

                            <i class="ri-menu-3-line"></i>

                        </button>

                      
                    </div>

                </nav>

            </div>

        </div>
        <div class="content-page">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')

    <script src="{{ asset('admin-assets/js/backend-bundle.min.js') }}"></script>

    <script src="{{ asset('admin-assets/js/flex-tree.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/tree.js') }}"></script>

    <script src="{{ asset('admin-assets/js/table-treeview.js') }}"></script>

    <script src="{{ asset('admin-assets/js/masonry.pkgd.min.js') }}"></script>
    <script src="{{ asset('admin-assets/js/imagesloaded.pkgd.min.js') }}"></script>

    <script src="{{ asset('admin-assets/js/mapbox-gl.js') }}"></script>
    <script src="{{ asset('admin-assets/js/mapbox.js') }}"></script>

    <script src="{{ asset('admin-assets/vendor/fullcalendar/core/main.js') }}"></script>
    <script src="{{ asset('admin-assets/vendor/fullcalendar/daygrid/main.js') }}"></script>
    <script src="{{ asset('admin-assets/vendor/fullcalendar/timegrid/main.js') }}"></script>
    <script src="{{ asset('admin-assets/vendor/fullcalendar/list/main.js') }}"></script>

    <script src="{{ asset('admin-assets/js/sweetalert.js') }}"></script>

    <script src="{{ asset('admin-assets/js/vector-map-custom.js') }}"></script>

    <script src="{{ asset('admin-assets/js/customizer.js') }}"></script>

    <script src="{{ asset('admin-assets/js/chart-custom.js') }}"></script>
    <script src="{{ asset('admin-assets/js/charts/01.js') }}"></script>
    <script src="{{ asset('admin-assets/js/charts/02.js') }}"></script>

    <script src="{{ asset('admin-assets/js/slider.js') }}"></script>

    <script src="{{ asset('admin-assets/vendor/emoji-picker-element/index.js') }}" type="module"></script>

    <script src="{{ asset('admin-assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include Selectize JS -->
    <script src="https://cdn.jsdelivr.net/npm/selectize/dist/js/standalone/selectize.min.js"></script>





    <script>
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

    </script>


</body>


</html>
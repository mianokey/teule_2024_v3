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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Selectize CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin-assets/css/stores.css?v=2.0.1') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/teule-admin.css?v=2.0.0') }}">




    <style>
        :root {
            --teule-navy: #00096A;
            --teule-orange: #EF4700;
            --teule-ink: #18213b;
            --teule-muted: #7c849b;
            --teule-border: #e7eaf2;
            --teule-surface: #ffffff;
            --teule-soft: #f5f7fc;
        }

        /* Global navigation polish */
        .mm-sidebar {
            background: #fff;
            border-right: 1px solid var(--teule-border);
            box-shadow: 8px 0 30px rgba(0, 9, 106, .035);
        }
        .mm-sidebar-logo {
            min-height: 78px;
            padding: 15px 18px;
            border-bottom: 1px solid #edf0f7;
        }
        .mm-sidebar-logo .header-logo img { max-height: 43px; object-fit: contain; }
        .mm-sidebar-menu, .mm-sidebar .side-menu { padding: 12px 11px 22px; }
        .mm-sidebar .side-menu > li { margin: 4px 0; }
        .mm-sidebar .side-menu > li > a {
            display: flex;
            align-items: center;
            min-height: 46px;
            padding: 11px 13px;
            border: 1px solid transparent;
            border-radius: 11px;
            color: #5d6680;
            font-size: 13px;
            font-weight: 650;
            transition: color .18s ease, background .18s ease, border-color .18s ease;
        }
        .mm-sidebar .side-menu > li > a > i:first-child,
        .mm-sidebar .side-menu > li > a > i:first-child svg { flex: 0 0 22px; }
        .mm-sidebar .side-menu > li > a .svg-icon { color: currentColor; }
        .mm-sidebar .side-menu > li > a:hover,
        .mm-sidebar .side-menu > li > a.active,
        .mm-sidebar .side-menu > li > a[aria-expanded="true"] {
            color: var(--teule-navy);
            background: #f1f3fc;
            border-color: #e5e9fb;
        }
        .mm-sidebar .side-menu > li > a.active { box-shadow: inset 3px 0 0 var(--teule-orange); }
        .mm-sidebar .side-menu .submenu,
        .mm-sidebar .side-menu > li > ul {
            margin: 5px 0 10px 11px;
            padding: 5px 0 5px 10px;
            border-left: 1px solid #e5e9f3;
        }
        .mm-sidebar .side-menu .submenu li,
        .mm-sidebar .side-menu > li > ul li { margin: 2px 0; }
        .mm-sidebar .side-menu .submenu a,
        .mm-sidebar .side-menu > li > ul a {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 10px;
            border-radius: 8px;
            color: #69718a;
            font-size: 12px;
            transition: all .18s ease;
        }
        .mm-sidebar .side-menu .submenu a:hover,
        .mm-sidebar .side-menu .submenu a.active,
        .mm-sidebar .side-menu > li > ul a:hover,
        .mm-sidebar .side-menu > li > ul a.active {
            color: var(--teule-navy);
            background: #f5f6fc;
        }
        .mm-sidebar .side-menu .submenu a.active,
        .mm-sidebar .side-menu > li > ul a.active { font-weight: 750; }
        .mm-sidebar .mm-arrow-right { margin-left: auto; font-size: 10px; }

        .mm-top-navbar {
            background: rgba(255,255,255,.96);
            border-bottom: 1px solid var(--teule-border);
            box-shadow: 0 5px 24px rgba(0, 9, 106, .035);
        }
        .mm-navbar-custom { padding: 0 18px; }
        .mm-navbar-logo .header-logo { display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .mm-navbar-logo .header-logo img { max-height: 38px; width: auto; object-fit: contain; }
        .mm-navbar-logo .header-logo h4 { margin: 0; color: var(--teule-navy); font-size: 15px; letter-spacing: .35px; }
        .mm-top-navbar .nav-item.nav-icon > a,
        .mm-top-navbar .nav-item.nav-icon > button { transition: background .18s ease, color .18s ease; }

        /* Account menu */
        .teule-account-nav { position: relative; margin-left: 7px; }
        .teule-account-trigger {
            display: flex !important; align-items: center; gap: 10px;
            min-height: 48px; padding: 5px 9px !important;
            border: 1px solid var(--teule-border); border-radius: 12px;
            background: #fff; color: var(--teule-navy) !important;
            text-decoration: none !important; transition: all .18s ease;
        }
        .teule-account-trigger::after { display: none !important; }
        .teule-account-trigger:hover,
        .teule-account-nav.show .teule-account-trigger {
            background: #f6f7fc; border-color: #cfd5ec; box-shadow: 0 4px 14px rgba(0,9,106,.07);
        }
        .teule-account-avatar {
            position: relative; display: inline-flex; flex: 0 0 38px;
            width: 38px; height: 38px; overflow: hidden; align-items: center; justify-content: center;
            border-radius: 11px; background: linear-gradient(135deg, #00096A, #3548b8);
            box-shadow: 0 3px 8px rgba(0,9,106,.16);
        }
        .teule-account-avatar img { display: block; width: 100%; height: 100%; object-fit: cover; }
        .teule-account-initials {
            display: flex; width: 100%; height: 100%; align-items: center; justify-content: center;
            color: #fff; font-size: 14px; font-weight: 800; letter-spacing: .5px;
        }
        .teule-account-online { position: absolute; right: 0; bottom: 0; width: 10px; height: 10px; border: 2px solid #fff; border-radius: 50%; background: #20a56b; }
        .teule-account-summary { display: flex; min-width: 0; flex-direction: column; align-items: flex-start; line-height: 1.25; }
        .teule-account-caption { color: #8188a0; font-size: 8px; font-weight: 800; letter-spacing: 1px; }
        .teule-account-name { display: block; max-width: 145px; overflow: hidden; color: var(--teule-navy); font-size: 12px; font-weight: 800; text-overflow: ellipsis; white-space: nowrap; }
        .teule-account-role { display: block; max-width: 145px; overflow: hidden; color: var(--teule-muted); font-size: 10px; text-overflow: ellipsis; white-space: nowrap; }
        .teule-account-arrow { margin-left: 1px; color: #7b8298; font-size: 13px; transition: transform .2s ease; }
        .teule-account-nav.show .teule-account-arrow { transform: rotate(180deg); }
        .teule-account-menu {
            position: absolute; right: 0; left: auto;
            width: 320px; max-width: calc(100vw - 24px); margin-top: 10px !important; padding: 0;
            overflow: hidden; border: 1px solid #e6e9f2; border-radius: 15px; background: #fff;
            box-shadow: 0 16px 40px rgba(0,9,106,.16); z-index: 1060;
        }
        .teule-account-menu-top { padding: 17px; background: linear-gradient(135deg, #00096A, #182b91); border-bottom: 3px solid var(--teule-orange); }
        .teule-account-profile { display: flex; align-items: center; gap: 12px; min-width: 0; }
        .teule-account-avatar-lg { flex-basis: 52px; width: 52px; height: 52px; border: 2px solid rgba(255,255,255,.45); border-radius: 15px; }
        .teule-account-details { display: flex; min-width: 0; flex-direction: column; gap: 3px; }
        .teule-account-details strong { overflow: hidden; color: #fff; font-size: 14px; text-overflow: ellipsis; white-space: nowrap; }
        .teule-account-details small { overflow: hidden; color: rgba(255,255,255,.75); font-size: 11px; text-overflow: ellipsis; white-space: nowrap; }
        .teule-account-role-badge { align-self: flex-start; max-width: 100%; margin-top: 3px; padding: 3px 8px; overflow: hidden; border: 1px solid rgba(255,255,255,.2); border-radius: 20px; background: rgba(255,255,255,.12); color: #fff; font-size: 10px; text-overflow: ellipsis; white-space: nowrap; }
        .teule-account-menu-section { padding: 12px 10px 8px; }
        .teule-account-section-label { display: block; padding: 0 9px 6px; color: #8a91a6; font-size: 9px; font-weight: 800; letter-spacing: 1.1px; }
        .teule-account-item { display: flex; align-items: center; gap: 10px; padding: 10px 9px; border-radius: 9px; white-space: normal; }
        .teule-account-item:hover { background: #f4f6fc; }
        .teule-account-item-icon { display: flex; flex: 0 0 34px; width: 34px; height: 34px; align-items: center; justify-content: center; border-radius: 10px; background: #eef1fc; color: var(--teule-navy); font-size: 18px; }
        .teule-account-item-copy { display: flex; min-width: 0; flex: 1; flex-direction: column; gap: 2px; }
        .teule-account-item-copy strong { color: var(--teule-ink); font-size: 12px; font-weight: 750; }
        .teule-account-item-copy small { color: var(--teule-muted); font-size: 10px; }
        .teule-account-item-arrow { color: #a1a7b7; font-size: 12px; }
        .teule-account-menu-footer { padding: 10px 12px 12px; border-top: 1px solid #edf0f6; background: #fbfcff; }
        .teule-account-logout { display: flex; width: 100%; align-items: center; gap: 10px; padding: 9px; border: 1px solid #f3dcd5; border-radius: 10px; background: #fff8f5; text-align: left; }
        .teule-account-logout:hover { background: #fff0e9; border-color: #efb9a7; }
        .teule-account-logout-icon { display: flex; flex: 0 0 34px; width: 34px; height: 34px; align-items: center; justify-content: center; border-radius: 9px; background: #ffebe4; color: var(--teule-orange); font-size: 18px; }
        .teule-account-logout-copy { display: flex; flex: 1; flex-direction: column; gap: 2px; }
        .teule-account-logout-copy strong { color: #b63812; font-size: 12px; }
        .teule-account-logout-copy small { color: #99766c; font-size: 10px; }
        .teule-account-footer-note { display: flex; align-items: center; justify-content: center; gap: 5px; padding-top: 11px; color: #8c93a7; font-size: 10px; }

        /* Notifications and search should remain usable on small screens. */
        .workflow-notification-menu { max-width: calc(100vw - 20px); }
        .workflow-notification-dropdown { position: relative; }
        .workflow-notification-trigger { position: relative; }

        /* Responsive navbar and menu behaviour */
        @media (max-width: 1199.98px) {
            .mm-navbar-custom { padding: 0 12px; }
            .teule-account-summary { display: none; }
            .teule-account-trigger { gap: 0; padding: 5px !important; border-color: transparent; background: transparent; }
            .teule-account-trigger:hover, .teule-account-nav.show .teule-account-trigger { background: #f1f3fc; border-color: #e5e9fb; }
            .teule-account-arrow { margin-left: 5px; }
        }
        @media (max-width: 991.98px) {
            .mm-top-navbar { position: relative; z-index: 1030; }
            .mm-navbar-custom .navbar { flex-wrap: wrap; gap: 5px; }
            .mm-navbar-logo { flex: 1 1 auto; min-width: 0; }
            .mm-navbar-logo .header-logo h4 { font-size: 13px; }
            .mm-navbar-custom .navbar > .d-flex.align-items-center { margin-left: auto; }
            .mm-navbar-custom #navbarSupportedContent { flex: 0 0 100%; width: 100%; padding: 8px 0 12px; border-top: 1px solid #edf0f7; }
            .mm-navbar-custom #navbarSupportedContent .navbar-nav { display: flex; flex-direction: row; align-items: center; justify-content: flex-end; gap: 7px; flex-wrap: wrap; }
            .mm-navbar-custom #navbarSupportedContent .nav-item { margin: 2px; }
            .mm-navbar-custom #navbarSupportedContent .dropdown-menu { position: absolute; }
            .teule-account-summary { display: flex; }
            .teule-account-trigger { gap: 9px; padding: 5px 9px !important; border-color: var(--teule-border); background: #fff; }
            .teule-account-name { max-width: 180px; }
            .teule-account-role { max-width: 180px; }
        }
        @media (max-width: 575.98px) {
            .mm-navbar-custom { padding: 0 9px; }
            .mm-navbar-logo .header-logo img { max-height: 31px; }
            .mm-navbar-logo .header-logo h4 { display: none; }
            .mm-navbar-custom #navbarSupportedContent .navbar-nav { justify-content: space-between; gap: 4px; }
            .mm-navbar-custom #navbarSupportedContent .nav-item { margin: 0; }
            .teule-account-summary { display: none; }
            .teule-account-trigger { min-height: 42px; padding: 3px !important; border: 0; }
            .teule-account-avatar { width: 36px; height: 36px; flex-basis: 36px; }
            .teule-account-arrow { display: none; }
            .teule-account-menu { position: fixed !important; top: 66px !important; right: 10px !important; left: auto !important; width: min(320px, calc(100vw - 20px)); transform: none !important; }
            .workflow-notification-menu { position: fixed !important; top: 66px !important; right: 10px !important; left: auto !important; width: min(370px, calc(100vw - 20px)); transform: none !important; }
            .mm-sidebar .side-menu > li > a { min-height: 44px; }
        }

        /* Active page is highlighted by the small script at the bottom. */
        .mm-sidebar .side-menu a.active { color: var(--teule-navy) !important; font-weight: 750 !important; }
        .mm-sidebar .side-menu > li > a.active { background: #f1f3fc !important; border-color: #e5e9fb !important; box-shadow: inset 3px 0 0 var(--teule-orange); }
        .mm-sidebar .side-menu .submenu a.active { background: #f5f6fc !important; }
    </style>

</head>

<body class=" color-light ">
    <audio id="workflowNotificationAudio" src="/sounds/teule_notification.mp3" preload="auto"></audio>

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
                </a>

                <div class="side-menu-bt-sidebar">
                    <i class="fa fa-bars wrapper-menu"></i>
                </div>

            </div>

            <div class="data-scrollbar" data-scroll="1">

                <nav class="mm-sidebar-menu">

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

                    </ul>

                </nav>

                <div class="pt-5 pb-2"></div>

            </div>

        </div>


        {{-- ============================================================
        TOP NAVBAR
        KEPT EXACTLY AS YOUR EXISTING CODE
        ============================================================= --}}

        <div class="mm-top-navbar">

            <div class="mm-navbar-custom">

                <nav class="navbar navbar-expand-lg navbar-light p-0">

                    <div class="mm-navbar-logo d-flex align-items-center justify-content-between">

                        <i class="ri-menu-line wrapper-menu"></i>

                        <a href="{{ url('/admin') }}" class="header-logo">

                            <img src="{{ asset('../assets/img/logo.png') }}"
                                class="img-fluid rounded-normal darkmode-logo" alt="logo">

                            <h4 class="ml-1">
                                <b>TEULE KENYA</b>
                            </h4>

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

                        <div class="change-mode">

                            <div class="custom-control custom-switch custom-switch-icon custom-control-inline">

                                <div class="custom-switch-inner">

                                    <p class="mb-0"></p>

                                    <input type="checkbox" class="custom-control-input" id="dark-mode"
                                        data-active="true">

                                    <label class="custom-control-label" for="dark-mode" data-mode="toggle">

                                        <span class="switch-icon-left">

                                            <svg class="svg-icon" id="h-moon" height="20" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />

                                            </svg>

                                        </span>

                                        <span class="switch-icon-right">

                                            <svg class="svg-icon" id="h-sun" height="20" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />

                                            </svg>

                                        </span>

                                    </label>

                                </div>

                            </div>

                        </div>

                        <button class="navbar-toggler" type="button" data-toggle="collapse"
                            data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                            aria-label="Toggle navigation">

                            <i class="ri-menu-3-line"></i>

                        </button>

                        <div class="collapse navbar-collapse" id="navbarSupportedContent">

                            <ul class="navbar-nav ml-auto navbar-list align-items-center">

                                <li class="nav-item nav-icon search-content">

                                    <a href="#" class="search-toggle rounded" id="dropdownSearch" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">

                                        <svg class="svg-icon text-primary" id="h-suns" height="20" width="20"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">

                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />

                                        </svg>

                                    </a>

                                    <div class="mm-search-bar mm-sub-dropdown dropdown-menu"
                                        aria-labelledby="dropdownSearch">

                                        <form action="#" class="searchbox p-2">

                                            <div class="form-group mb-0 position-relative">

                                                <input type="text" class="text search-input font-size-12"
                                                    placeholder="type here to search...">

                                                <a href="#" class="search-link">
                                                    <i class="las la-search"></i>
                                                </a>

                                            </div>

                                        </form>

                                    </div>

                                </li>


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



                                <li class="nav-item nav-icon dropdown full-screen">

                                    <a href="#" class="nav-item nav-icon dropdown" id="btnFullscreen">

                                        <i class="max">

                                            <svg class="svg-icon text-primary" id="d-3-max" width="20" height="20"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="feather feather-maximize">

                                                <path
                                                    d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                                                </path>

                                            </svg>

                                        </i>

                                        <i class="min d-none">

                                            <svg class="svg-icon text-primary" id="d-3-min" width="20" height="20"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="feather feather-minimize">

                                                <path
                                                    d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3">
                                                </path>

                                            </svg>

                                        </i>

                                    </a>

                                </li>


                                {{-- =========================================================
                                     RESPONSIVE USER ACCOUNT MENU
                                     Profile photo comes from user_details: key=img_url
                                     ========================================================= --}}
                                @php
                                    $currentUser = auth()->user();
                                    $userImageValue = \Illuminate\Support\Facades\DB::table('user_details')
                                        ->where('user_id', $currentUser->id)
                                        ->where('key', 'img_url')
                                        ->value('value');

                                    $userImageValue = is_string($userImageValue) ? trim($userImageValue) : '';
                                    $userImageUrl = null;

                                    if ($userImageValue !== '') {
                                        $userImageUrl = filter_var($userImageValue, FILTER_VALIDATE_URL)
                                            ? $userImageValue
                                            : asset(ltrim($userImageValue, '/'));
                                    }

                                    $userInitials = collect(preg_split('/\s+/', trim($currentUser->name ?? 'User')))
                                        ->filter()
                                        ->take(2)
                                        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                                        ->implode('');

                                    $userInitials = $userInitials ?: 'U';
                                    $userRoleLabel = method_exists($currentUser, 'getRoleNames')
                                        ? ($currentUser->getRoleNames()->first() ?? 'Team Member')
                                        : 'Team Member';
                                @endphp

                                <li class="nav-item dropdown teule-account-nav">
                                    <a href="#"
                                       class="nav-link teule-account-trigger"
                                       id="teuleAccountDropdown"
                                       data-toggle="dropdown"
                                       aria-haspopup="true"
                                       aria-expanded="false">

                                        <span class="teule-account-avatar">
                                            @if($userImageUrl)
                                                <img src="{{ $userImageUrl }}"
                                                     alt="{{ $currentUser->name }}"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <span class="teule-account-initials" style="display:none;">{{ $userInitials }}</span>
                                            @else
                                                <span class="teule-account-initials">{{ $userInitials }}</span>
                                            @endif
                                            <span class="teule-account-online" aria-hidden="true"></span>
                                        </span>

                                        <span class="teule-account-summary">
                                            <span class="teule-account-caption">SIGNED IN AS</span>
                                            <span class="teule-account-name">{{ $currentUser->name ?? 'User' }}</span>
                                            <span class="teule-account-role">{{ $userRoleLabel }}</span>
                                        </span>

                                        <i class="fa fa-chevron-down teule-account-arrow" aria-hidden="true"></i>
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right teule-account-menu"
                                         aria-labelledby="teuleAccountDropdown">
                                        <div class="teule-account-menu-top">
                                            <div class="teule-account-profile">
                                                <span class="teule-account-avatar teule-account-avatar-lg">
                                                    @if($userImageUrl)
                                                        <img src="{{ $userImageUrl }}"
                                                             alt="{{ $currentUser->name }}"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <span class="teule-account-initials" style="display:none;">{{ $userInitials }}</span>
                                                    @else
                                                        <span class="teule-account-initials">{{ $userInitials }}</span>
                                                    @endif
                                                </span>
                                                <div class="teule-account-details">
                                                    <strong>{{ $currentUser->name ?? 'User' }}</strong>
                                                    <small>{{ $currentUser->email ?? '' }}</small>
                                                    <span class="teule-account-role-badge">{{ $userRoleLabel }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="teule-account-menu-section">
                                            <span class="teule-account-section-label">ACCOUNT</span>
                                            @if(\Illuminate\Support\Facades\Route::has('profile.edit'))
                                                <a class="dropdown-item teule-account-item" href="{{ route('profile.edit') }}">
                                                    <span class="teule-account-item-icon"><i class="las la-user-circle"></i></span>
                                                    <span class="teule-account-item-copy">
                                                        <strong>My Profile</strong>
                                                        <small>View or update your profile</small>
                                                    </span>
                                                    <i class="las la-angle-right teule-account-item-arrow"></i>
                                                </a>
                                            @endif
                                        </div>

                                        <div class="teule-account-menu-footer">
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="teule-account-logout">
                                                    <span class="teule-account-logout-icon"><i class="fa fa-power-off"></i></span>
                                                    <span class="teule-account-logout-copy">
                                                        <strong>Sign out</strong>
                                                        <small>End your current session</small>
                                                    </span>
                                                </button>
                                            </form>
                                            <div class="teule-account-footer-note">
                                                <i class="fas fa-shield" aria-hidden="true"></i>
                                                Teule Kenya Management System
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            </ul>

                        </div>

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
    <script src="{{ asset('admin-assets/js/teule-admin.js?v=2.0.0') }}"></script>





    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var currentPath = window.location.pathname.replace(/\/$/, '') || '/';
            var menuLinks = document.querySelectorAll('.mm-sidebar .side-menu a[href]');
            var bestMatch = null;
            var bestLength = -1;

            menuLinks.forEach(function (link) {
                var rawHref = link.getAttribute('href');
                if (!rawHref || rawHref.charAt(0) === '#') return;

                try {
                    var linkUrl = new URL(rawHref, window.location.origin);
                    if (linkUrl.origin !== window.location.origin) return;
                    var linkPath = linkUrl.pathname.replace(/\/$/, '') || '/';
                    var matches = currentPath === linkPath ||
                        (linkPath !== '/admin' && linkPath !== '/' && currentPath.indexOf(linkPath + '/') === 0);

                    if (matches && linkPath.length > bestLength) {
                        bestMatch = link;
                        bestLength = linkPath.length;
                    }
                } catch (error) {
                    // Ignore malformed or non-navigation hrefs.
                }
            });

            if (bestMatch) {
                bestMatch.classList.add('active');
                var parent = bestMatch.parentElement;
                while (parent && !parent.classList.contains('side-menu')) {
                    if (parent.tagName === 'UL' && parent.classList.contains('collapse')) {
                        parent.classList.add('show');
                        var trigger = parent.previousElementSibling;
                        if (trigger && trigger.matches('a[data-toggle="collapse"]')) {
                            trigger.classList.remove('collapsed');
                            trigger.setAttribute('aria-expanded', 'true');
                        }
                    }
                    parent = parent.parentElement;
                }
            }

            // Prevent a dropdown click from navigating to the page top.
            document.querySelectorAll('#teuleAccountDropdown, #dropdownSearch').forEach(function (trigger) {
                trigger.addEventListener('click', function (event) {
                    event.preventDefault();
                });
            });
        });
    </script>

</body>


</html>
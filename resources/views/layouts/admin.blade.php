<!doctype html>
<html lang="en">


<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">.
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TEULE KENYA || ADMIN</title>

    <link rel="shortcut icon" href="{{ asset('.../assets/img/logo.png') }}" />
    <link rel="stylesheet" href="{{ asset('admin-assets/css/backend-plugin.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin-assets/css/backend.css?v=1.0.0') }}">
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



</head>

<body class=" color-light ">

    <div id="loading">
        <div id="loading-center">
        </div>
    </div>


    <div class="wrapper">
        <div class="mm-sidebar  sidebar-default ">
            <div class="mm-sidebar-logo d-flex align-items-center justify-content-between">
                <a href="index.html" class="header-logo">
                    <img src="{{ asset('../assets/img/logo.png') }}" class="img-fluid rounded-normal light-logo"
                        alt="logo">
                    <img src="{{ asset('../assets/img/logo.png') }}" class="img-fluid rounded-normal darkmode-logo"
                        alt="logo">
                </a>
                <div class="side-menu-bt-sidebar">
                    <i class="fa fa-bars  wrapper-menu"></i>
                </div>
            </div>
            <div class="data-scrollbar" data-scroll="1">
                <nav class="mm-sidebar-menu">
                    <ul id="mm-sidebar-toggle" class="side-menu">
                        <li class>
                            <a class="collapsed svg-icon" data-toggle="collapse" aria-expanded="false">
                                <i class>
                                    <svg class="svg-icon" id="mm-dash" width="20" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                    </svg>
                                </i>
                                <span class="ml-2">Dashboards</span>

                            </a>
                            <ul id="Dashboards" class=" collapse" data-parent="#mm-sidebar-toggle">
                                <li class>
                                    <a href="dashboard-3.html" class="svg-icon">
                                        <i class>
                                            <svg class="svg-icon" id="mm-dash-3" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                            </svg>
                                        </i>
                                        <span>Dashboard 3</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class>
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
                                <i class="fa fa-arrow-down   mm-arrow-right arrow-hover"></i>
                            </a>
                            <ul id="Children" class="submenu collapse" data-parent="#mm-sidebar-toggle">
                                <li class>
                                    <a href="{{ route('newchild.index') }}" class="svg-icon">
                                        <i class="fa fa-asterisk"></i>
                                        <span>New Child</span>
                                    </a>
                                </li>
                                <li class>
                                    <a href="{{ route('children.index') }}" class="svg-icon">
                                        <i class="fa fa-asterisk"></i>
                                        <span>Children List</span>
                                    </a>
                                </li>

                            </ul>
                        </li>
                        <li class>
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
                                <i class="fa fa-arrow-down   mm-arrow-right arrow-hover"></i>
                            </a>
                            <ul id="System" class="submenu collapse" data-parent="#mm-sidebar-toggle">
                                <li class>
                                    <a href="{{ route('admin.system_details.index') }}" class="svg-icon">
                                        <i class="fa fa-asterisk"></i>
                                        <span>System Variable List</span>
                                    </a>
                                </li>
                                <li class>
                                    <a href="{{ route('admin.system_details.add') }}" class="svg-icon">
                                        <i class="fa fa-asterisk"></i>
                                        <span>New System Data</span>
                                    </a>
                                </li>



                            </ul>
                        </li>
                        @if(auth()->user()->can('MAKE REQUISITION'))
                        <li class="nav-item">
                            <a href="#requisition" class="collapsed svg-icon" data-toggle="collapse"
                                aria-expanded="false">
                                <i class="fa fa-user">

                                </i>
                                <span class="ml-2">Roles & Permissions</span>
                                <i class="fa fa-arrow-right mm-arrow-right arrow-active"></i>
                                <i class="fa fa-arrow-down mm-arrow-right arrow-hover"></i>
                            </a>
                            <ul id="requisition" class="submenu collapse" data-parent="#mm-sidebar-toggle">
                                <!-- All Users List -->
                                <li>
                                    <a href="{{ route('admin.user.index') }}" class="svg-icon">
                                        <i class="fa fa-asterisk"></i>
                                        <span>All Users List</span>
                                    </a>
                                </li>
                                <!-- New User -->
                                <li>
                                    <a href="{{ route('admin.user.create') }}" class="svg-icon">
                                        <i class="fa fa-asterisk"></i>
                                        <span>New User</span>
                                    </a>
                                </li>
                                <!-- Manage Roles -->
                                <li>
                                    <a href="{{ route('admin.roles.index') }}" class="svg-icon">
                                        <i class="fa fa-shield"></i>
                                        <span>Manage Roles</span>
                                    </a>
                                </li>
                                <!-- Create Role -->
                                <li>
                                    <a href="{{ route('admin.roles.create') }}" class="svg-icon">
                                        <i class="fa fa-plus-circle"></i>
                                        <span>Create Role</span>
                                    </a>
                                </li>
                                <!-- Manage Permissions -->
                                <li>
                                    <a href="{{ route('admin.permissions.index') }}" class="svg-icon">
                                        <i class="fa fa-key"></i>
                                        <span>Manage Permissions</span>
                                    </a>
                                </li>
                                <!-- Create Permission -->
                                <li>
                                    <a href="{{ route('admin.permissions.create') }}" class="svg-icon">
                                        <i class="fa fa-plus-circle"></i>
                                        <span>Create Permission</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif

                        @if(auth()->user()->can('MAKE REQUISITION'))
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
                                <!-- All Users List -->
                                <li>
                                    <a href="{{ route('admin.user.index') }}" class="svg-icon">
                                        <i class="fa fa-asterisk"></i>
                                        <span>All Petty Cash</span>
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.pettycash.create') }}" class="svg-icon">
                                        <i class="fa fa-plus-circle"></i>
                                        <span>Request</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif


                    </ul>
                </nav>

                <div class="pt-5 pb-2"></div>
            </div>
        </div>
        <div class="mm-top-navbar">
            <div class="mm-navbar-custom">
                <nav class="navbar navbar-expand-lg navbar-light p-0">
                    <div class="mm-navbar-logo d-flex align-items-center justify-content-between">
                        <i class="ri-menu-line wrapper-menu"></i>
                        <a href="index.html" class="header-logo">
                            <img src="{{ asset('../assets/img/logo.png') }}"
                                class="img-fluid rounded-normal darkmode-logo" alt="logo">
                            <h4 class="ml-1"><b>TEULE KENYA</b></h4>
                        </a>
                    </div>
                    <div class="mm-search-bar device-search m-auto">
                        <form action="#" class="searchbox">
                            <a class="search-link" href="#"><i class="ri-search-line"></i></a>
                            <input type="text" class="text search-input" placeholder="Search here...">
                        </form>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="change-mode">
                            <div class="custom-control custom-switch custom-switch-icon custom-control-inline">
                                <div class="custom-switch-inner">
                                    <p class="mb-0"> </p>
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
                                                <a href="#" class="search-link"><i class="las la-search"></i></a>
                                            </div>
                                        </form>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton2"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="svg-icon  text-primary" id="mm-mail-2"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="feather feather-mail">
                                            <path
                                                d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z">
                                            </path>
                                            <polyline points="22,6 12,13 2,6"></polyline>
                                        </svg>
                                        <span class="bg-primary"></span>
                                    </a>
                                    <div class="mm-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                        <div class="card shadow-none m-0 border-0">
                                            <div class="card-body p-0 ">
                                                <div class="cust-title p-3">
                                                    <h5 class="mb-0">All Messages</h5>
                                                </div>
                                                <div class="p-3">
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="{{asset('admin-assets/images/user/1.jpg')}}"
                                                                    alt="01">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0">Barry Emma Watson <small
                                                                        class="badge badge-success float-right">New</small>
                                                                </h6>
                                                                <small class="float-left font-size-12">12:00 PM</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="../assets/images/user/2.jpg" alt="02">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0 ">Lorem Ipsum Watson</h6>
                                                                <small class="float-left font-size-12">20 Apr</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="../assets/images/user/3.jpg" alt="03">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0 ">Why do we use it? <small
                                                                        class="badge badge-success float-right">New</small>
                                                                </h6>
                                                                <small class="float-left font-size-12">1:24 PM</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="../assets/images/user/4.jpg" alt="04">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0">Variations Passages <small
                                                                        class="badge badge-success float-right">New</small>
                                                                </h6>
                                                                <small class="float-left font-size-12">5:45 PM</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="../assets/images/user/5.jpg" alt="05">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0 ">Lorem Ipsum generators</h6>
                                                                <small class="float-left font-size-12">1 day ago</small>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <a class="d-flex justify-content-center p-2 card-footer" href="#"
                                                    role="button">
                                                    View All
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <svg class="svg-icon text-primary" id="mm-bell-2"
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                        </svg>
                                        <span class="bg-primary "></span>
                                    </a>
                                    <div class="mm-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <div class="card shadow-none m-0 border-0">
                                            <div class="card-body p-0 ">
                                                <div class="cust-title p-3">
                                                    <h5 class="mb-0">All Notifications</h5>
                                                </div>
                                                <div class="p-3">
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="{{asset('admin-assets/images/user/1.jpg')}}"
                                                                    alt="01">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0">Emma Watson Barry <small
                                                                        class="badge badge-success float-right">New</small>
                                                                </h6>
                                                                <p class="mb-0">95 MB</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="../assets/images/user/2.jpg" alt="02">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0 ">New customer is join</h6>
                                                                <p class="mb-0">Cyst Barry</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="../assets/images/user/3.jpg" alt="03">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0 ">Two customer is left</h6>
                                                                <p class="mb-0">Cyst Barry</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                    <a href="#" class="mm-sub-card">
                                                        <div class="media align-items-center">
                                                            <div class>
                                                                <img class="avatar-40 rounded-small"
                                                                    src="../assets/images/user/4.jpg" alt="04">
                                                            </div>
                                                            <div class="media-body ml-3">
                                                                <h6 class="mb-0 ">New Mail from Fenny <small
                                                                        class="badge badge-success float-right">New</small>
                                                                </h6>
                                                                <p class="mb-0">Cyst Barry</p>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                                <a class="d-flex justify-content-center p-2 card-footer" href="#"
                                                    role="button">
                                                    View All
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li class="nav-item nav-icon dropdown full-screen">
                                    <a href="#" class="nav-item nav-icon dropdown" id="btnFullscreen">
                                        <i class="max"><svg class="svg-icon  text-primary" id="d-3-max" width="20"
                                                height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-maximize">
                                                <path
                                                    d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3">
                                                </path>
                                            </svg></i>
                                        <i class="min d-none"><svg class="svg-icon  text-primary" id="d-3-min"
                                                width="20" height="20" xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="feather feather-minimize">
                                                <path
                                                    d="M8 3v3a2 2 0 0 1-2 2H3m18 0h-3a2 2 0 0 1-2-2V3m0 18v-3a2 2 0 0 1 2-2h3M3 16h3a2 2 0 0 1 2 2v3">
                                                </path>
                                            </svg></i>
                                    </a>
                                </li>
                                <li class="nav-item nav-icon dropdown">
                                    <a href="#" class="nav-item nav-icon dropdown-toggle pr-0 search-toggle"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                        <img src="{{asset('admin-assets/images/user/1.jpg')}}"
                                            class="img-fluid avatar-rounded" alt="user">
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
                                        <li class="dropdown-item d-flex svg-icon">
                                            <svg class="svg-icon mr-0 text-primary" id="h-01-p" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <a
                                                href="https://meetmighty.com/dashboards/simpled/html/app/user-profile.html">My
                                                Profile</a>
                                        </li>
                                        <li class="dropdown-item d-flex svg-icon">
                                            <svg class="svg-icon mr-0 text-primary" id="h-02-p" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <a
                                                href="https://meetmighty.com/dashboards/simpled/html/app/user-profile-edit.html">Edit
                                                Profile</a>
                                        </li>
                                        <li class="dropdown-item d-flex svg-icon">
                                            <svg class="svg-icon mr-0 text-primary" id="h-03-p" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <a
                                                href="https://meetmighty.com/dashboards/simpled/html/app/user-account-setting.html">Account
                                                Settings</a>
                                        </li>
                                        <li class="dropdown-item d-flex svg-icon">
                                            <svg class="svg-icon mr-0 text-primary" id="h-04-p" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                            <a
                                                href="https://meetmighty.com/dashboards/simpled/html/app/user-privacy-setting.html">Privacy
                                                Settings</a>
                                        </li>
                                        <li class="dropdown-item  d-flex svg-icon border-top">
                                            <svg class="svg-icon mr-0 text-primary" id="h-05-p" width="20"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>

                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                Logout
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>

                                        </li>
                                    </ul>
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

</body>


</html>
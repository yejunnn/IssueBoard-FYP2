<!DOCTYPE html>
<html lang="en" data-theme="light">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5, user-scalable=yes">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">
        <meta name="user-role" content="{{ auth()->user()?->isAdmin() ? 'admin' : 'user' }}">
        <meta name="user-department" content="{{ auth()->user()?->department_id }}">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="theme-color" content="#1e3a8a">
        <title>@yield('title', 'Issue Board')</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/components.css') }}" rel="stylesheet">
        <link href="{{ asset('css/responsive.css') }}" rel="stylesheet">
        <link href="{{ asset('css/realtime.css') }}" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/realtime.js'])

        @stack('styles')

        <style>
            .notification-badge {
                background: #dc2626;
                box-shadow: 0 2px 6px rgba(220, 38, 38, 0.4), 0 0 0 1.5px rgba(255, 255, 255, 0.9);
                font-size: 10px;
                font-weight: 700;
                z-index: 10;
                transition: all 0.2s ease;
                border: 1.5px solid rgba(255, 255, 255, 0.9);
                text-shadow: 0 1px 1px rgba(0, 0, 0, 0.3);
            }
            
            .notification-badge:hover {
                background: #b91c1c;
                box-shadow: 0 3px 8px rgba(220, 38, 38, 0.5), 0 0 0 2px rgba(255, 255, 255, 1);
                transform: scale(1.05);
            }
            
            .sidebar-link:hover .notification-badge {
                background: #b91c1c;
                box-shadow: 0 4px 12px rgba(220, 38, 38, 0.6), 0 0 0 2px rgba(255, 255, 255, 1);
            }
            
            /* Compact sidebar styling */
            .sidebar-main {
                font-size: 0.9rem;
            }
            
            .sidebar-main .sidebar-link {
                font-size: 0.9rem;
                padding: 0.5rem 0.75rem !important;
            }
            
            .sidebar-main .sidebar-link i {
                font-size: 0.9rem;
            }
            
            /* Mobile optimizations */
            @media (max-width: 768px) {
                .notification-badge {
                    min-width: 20px !important;
                    height: 20px !important;
                    font-size: 11px !important;
                    top: -2px !important;
                    right: -2px !important;
                }
                
                .sidebar-link {
                    padding: 12px 15px !important;
                    font-size: 16px !important;
                }
                
                .toast-container {
                    bottom: 20px !important;
                    left: 10px !important;
                    right: 10px !important;
                    max-width: none !important;
                }
                
                .toast {
                    margin-bottom: 10px !important;
                    padding: 12px 15px !important;
                    font-size: 14px !important;
                }
            }
            
            /* Touch-friendly interactions */
            @media (hover: none) and (pointer: coarse) {
                .btn, .sidebar-link, .card {
                    min-height: 44px !important;
                }
                
                .btn {
                    padding: 12px 20px !important;
                    font-size: 16px !important;
                }
            }
            
            /* Real-time update animations */
            .pulse-animation {
                animation: pulse 2s ease-in-out;
            }
            
            @keyframes pulse {
                0% { transform: scale(1); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
            
            .highlight-update {
                animation: highlightUpdate 3s ease-in-out;
            }
            
            @keyframes highlightUpdate {
                0% { background-color: transparent; }
                50% { background-color: rgba(255, 193, 7, 0.2); }
                100% { background-color: transparent; }
            }
            
            .highlight-assignment {
                animation: highlightAssignment 3s ease-in-out;
            }
            
            @keyframes highlightAssignment {
                0% { background-color: transparent; }
                50% { background-color: rgba(13, 110, 253, 0.2); }
                100% { background-color: transparent; }
            }
            
            .highlight-acceptance {
                animation: highlightAcceptance 3s ease-in-out;
            }
            
            @keyframes highlightAcceptance {
                0% { background-color: transparent; }
                50% { background-color: rgba(25, 135, 84, 0.2); }
                100% { background-color: transparent; }
            }
            
            /* Real-time notification styles */
            .toast-container {
                z-index: 9999 !important;
            }

            .toast {
                min-width: 300px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            /* Live indicator */
            .live-indicator {
                display: inline-block;
                width: 8px;
                height: 8px;
                background-color: #28a745;
                border-radius: 50%;
                margin-right: 8px;
                animation: livePulse 2s infinite;
            }

            @keyframes livePulse {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }

            /* Mobile Sidebar Styles */
            .sidebar-main {
                width: 260px;
                min-height: 100vh;
                position: fixed;
                left: 0;
                top: 0;
                z-index: 1040;
                padding: 1.5rem 1rem;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                display: flex;
                flex-direction: column;
                transition: transform 0.3s ease-in-out;
            }

            .main-content {
                margin-left: 260px;
                padding: 2rem;
                min-height: 100vh;
                transition: margin-left 0.3s ease-in-out;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1030;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
            }

            .sidebar-overlay.active {
                opacity: 1;
                visibility: visible;
            }

            .sidebar-link.active {
                background-color: rgba(255, 255, 255, 0.15) !important;
            }

            .hamburger-btn {
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Mobile styles */
            @media (max-width: 991.98px) {
                .mobile-header {
                    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                }

                .sidebar-main {
                    transform: translateX(-100%);
                    width: 280px;
                    padding-top: 1rem;
                }

                .sidebar-main.active {
                    transform: translateX(0);
                }

                .main-content {
                    margin-left: 0;
                    padding: 1rem;
                    padding-top: calc(56px + 1rem);
                }

                .sidebar-header {
                    padding-top: 0.5rem;
                }
            }

            /* Desktop styles */
            @media (min-width: 992px) {
                .mobile-header {
                    display: none !important;
                }

                .sidebar-close {
                    display: none !important;
                }

                .sidebar-overlay {
                    display: none !important;
                }
            }

            /* Smooth page transitions */
            .main-content {
                animation: fadeInPage 0.3s ease-out;
            }

            @keyframes fadeInPage {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    </head>
    <body>
        @php
            $user = Auth::user();
            $showSidebar = $user && ($user->isAdmin() || $user->department_id);
        @endphp
        
        @if(!$showSidebar)
        <nav class="navbar navbar-expand-lg navbar-dark bg-gradient" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="{{ route('welcome') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="IssueBoard Logo" class="me-2" style="height: 32px; width: auto; display: block;">
                    <span class="fw-bold fs-4 text-white">Issue Board</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        @auth
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('tickets.index') }}">
                                    <i class="fas fa-ticket-alt me-1"></i>All Tickets
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-1"></i>{{ $user->name }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user-cog me-2"></i>Profile
                                    </a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">
                                    <i class="fas fa-sign-in-alt me-1"></i>Login
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
        @endif



        @if($showSidebar)
        <!-- Mobile Header with Hamburger -->
        <header class="mobile-header d-lg-none bg-dark text-white py-2 px-3 position-fixed w-100" style="z-index: 1050; top: 0;">
            <div class="d-flex align-items-center justify-content-between">
                <button class="btn btn-link text-white p-0 hamburger-btn" type="button" id="sidebarToggle" aria-label="Toggle navigation">
                    <i class="fas fa-bars fa-lg"></i>
                </button>
                <a href="{{ route('welcome') }}" class="d-flex align-items-center text-decoration-none">
                    <img src="{{ asset('images/logo.png') }}" alt="IssueBoard Logo" style="height: 28px; width: auto;" />
                    <span class="fw-bold text-white ms-2">IssueBoard</span>
                </a>
                <div class="d-flex align-items-center gap-2">
                    @php $unreadCount = $user->unreadNotifications()->count(); @endphp
                    <a href="{{ route('notifications.index') }}" class="text-white position-relative">
                        <i class="fas fa-bell"></i>
                        @if($unreadCount > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>
        </header>

        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay d-lg-none" id="sidebarOverlay"></div>

        <div class="layout-main">
            <aside class="sidebar-main bg-dark text-white" id="sidebar">
                <div class="sidebar-header mb-4 d-flex align-items-center justify-content-between">
                    <a href="{{ route('welcome') }}" class="d-flex align-items-center text-decoration-none">
                        <img src="{{ asset('images/logo.png') }}" alt="IssueBoard Logo" style="height: 35px; width: auto; margin-right: 10px;" />
                        <span class="fs-5 fw-bold text-white">IssueBoard</span>
                    </a>
                    <button class="btn btn-link text-white p-0 d-lg-none sidebar-close" id="sidebarClose" aria-label="Close sidebar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <nav>
                    <ul class="list-unstyled">
                        <li class="mb-1">
                            <a href="{{ route('tickets.index') }}" class="sidebar-link d-flex align-items-center px-2 py-2 rounded text-white text-decoration-none {{ request()->routeIs('tickets.index') ? 'active' : '' }}">
                                <i class="fas fa-ticket-alt me-2"></i> All Tickets
                            </a>
                        </li>
                        @if($user->department_id && !$user->is_admin)
                        <li class="mb-1">
                            <a href="{{ route('tickets.assigned') }}" class="sidebar-link d-flex align-items-center px-2 py-2 rounded text-white text-decoration-none {{ request()->routeIs('tickets.assigned') ? 'active' : '' }}">
                                <i class="fas fa-user-check me-2"></i> My Assigned Tickets
                            </a>
                        </li>
                        @endif
                        <li class="mb-1">
                            <a href="{{ route('notifications.index') }}" class="sidebar-link d-flex align-items-center px-2 py-2 rounded text-white text-decoration-none position-relative {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                                <i class="fas fa-bell me-2"></i>
                                <span class="flex-grow-1">Notifications</span>
                                @if($unreadCount > 0)
                                    <span class="notification-badge position-absolute top-0 end-0 bg-danger text-white small fw-bold rounded-circle d-flex align-items-center justify-content-center" style="min-width: 18px; height: 18px; font-size: 0.75rem;">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                        @if($user->is_admin)
                        <li class="mb-1">
                            <a href="{{ route('admin.dashboard') }}" class="sidebar-link d-flex align-items-center px-2 py-2 rounded text-white text-decoration-none {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                                <i class="fas fa-cogs me-2"></i> Admin Panel
                            </a>
                        </li>
                        @endif
                        @if($user->department_id || $user->is_admin)
                        <li class="mb-1">
                            <a href="{{ route('profile.show') }}" class="sidebar-link d-flex align-items-center px-2 py-2 rounded text-white text-decoration-none {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                                <i class="fas fa-user me-2"></i> Profile
                            </a>
                        </li>
                        @endif
                        <li class="mt-3 pt-3 border-top border-secondary">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="sidebar-link d-flex align-items-center w-100 px-2 py-2 rounded text-white text-decoration-none border-0 bg-transparent text-start">
                                    <i class="fas fa-sign-out-alt me-2"></i> Log Out
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
                <!-- Theme Toggle & User info at bottom -->
                <div class="sidebar-footer mt-auto pt-3 border-top border-secondary">
                    <div class="d-flex align-items-center justify-content-between px-2 py-2 mb-2">
                        <div class="d-flex align-items-center">
                            <div class="user-avatar bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 36px; height: 36px;">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="user-info overflow-hidden">
                                <div class="text-white fw-semibold text-truncate" style="font-size: 0.85rem;">{{ $user->name }}</div>
                                <div class="text-white-50 text-truncate" style="font-size: 0.75rem;">{{ $user->is_admin ? 'Administrator' : ($user->department->name ?? 'User') }}</div>
                            </div>
                        </div>
                        <button type="button" class="theme-toggle text-white" id="themeToggle" title="Toggle Dark Mode">
                            <i class="fas fa-moon"></i>
                            <i class="fas fa-sun"></i>
                        </button>
                    </div>
                </div>
            </aside>
            <main class="main-content" id="mainContent">
                @yield('content')
            </main>
        </div>
        @else
            @yield('content')
        @endif
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script src="{{ asset('js/utils.js') }}"></script>
        <script src="{{ asset('js/forms.js') }}"></script>
        <script src="{{ asset('js/app.js') }}"></script>
        
        @stack('scripts')
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Backspace prevention
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Backspace' && !isInputField(e.target)) {
                        e.preventDefault();
                    }
                });

                function isInputField(element) {
                    const inputTypes = ['input', 'textarea', 'select'];
                    const contentEditable = element.contentEditable === 'true';

                    return inputTypes.includes(element.tagName.toLowerCase()) ||
                           contentEditable ||
                           element.classList.contains('form-control') ||
                           element.classList.contains('form-select') ||
                           element.classList.contains('form-textarea');
                }

                // Mobile Sidebar Toggle
                const sidebar = document.getElementById('sidebar');
                const sidebarToggle = document.getElementById('sidebarToggle');
                const sidebarClose = document.getElementById('sidebarClose');
                const sidebarOverlay = document.getElementById('sidebarOverlay');

                function openSidebar() {
                    if (sidebar) {
                        sidebar.classList.add('active');
                        sidebarOverlay?.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                }

                function closeSidebar() {
                    if (sidebar) {
                        sidebar.classList.remove('active');
                        sidebarOverlay?.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                }

                sidebarToggle?.addEventListener('click', openSidebar);
                sidebarClose?.addEventListener('click', closeSidebar);
                sidebarOverlay?.addEventListener('click', closeSidebar);

                // Close sidebar on navigation (mobile)
                document.querySelectorAll('.sidebar-link').forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 992) {
                            closeSidebar();
                        }
                    });
                });

                // Close sidebar on escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeSidebar();
                    }
                });

                // Handle window resize
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 992) {
                        closeSidebar();
                    }
                });

                // Dark Mode Toggle
                const themeToggle = document.getElementById('themeToggle');
                const htmlElement = document.documentElement;

                // Check for saved theme preference or default to light
                const savedTheme = localStorage.getItem('theme') || 'light';
                htmlElement.setAttribute('data-theme', savedTheme);

                themeToggle?.addEventListener('click', function() {
                    const currentTheme = htmlElement.getAttribute('data-theme');
                    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

                    htmlElement.setAttribute('data-theme', newTheme);
                    localStorage.setItem('theme', newTheme);

                    // Update meta theme color
                    const metaTheme = document.querySelector('meta[name="theme-color"]');
                    if (metaTheme) {
                        metaTheme.setAttribute('content', newTheme === 'dark' ? '#0f172a' : '#1e3a8a');
                    }
                });
            });
        </script>

        <!-- Initialize theme before page load to prevent flash -->
        <script>
            (function() {
                const savedTheme = localStorage.getItem('theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            })();
        </script>
    </body>
</html>

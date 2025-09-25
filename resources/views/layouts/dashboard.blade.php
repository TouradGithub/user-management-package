@extends('user-manager::layouts.app')

@section('body')
<div class="d-flex flex-column min-vh-100">
    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('user-manager.dashboard') }}">
                <i class="fas fa-users-cog me-2"></i>
                <span class="fw-bold">User Manager</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user-manager.dashboard') ? 'active' : '' }}" 
                           href="{{ route('user-manager.dashboard') }}">
                            <i class="fas fa-chart-line me-1"></i>لوحة التحكم
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user-manager.users') ? 'active' : '' }}" 
                           href="{{ route('user-manager.users') }}">
                            <i class="fas fa-users me-1"></i>المستخدمون
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user-manager.user-types') ? 'active' : '' }}" 
                           href="{{ route('user-manager.user-types') }}">
                            <i class="fas fa-tags me-1"></i>أنواع المستخدمين
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user-manager.activities') ? 'active' : '' }}" 
                           href="{{ route('user-manager.activities') }}">
                            <i class="fas fa-history me-1"></i>سجل الأنشطة
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('user-manager.sessions') ? 'active' : '' }}" 
                           href="{{ route('user-manager.sessions') }}">
                            <i class="fas fa-desktop me-1"></i>الجلسات
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <!-- Notifications -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle">3</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">الإشعارات</h6></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-user-plus text-success me-2"></i>مستخدم جديد انضم
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>محاولات دخول مشبوهة
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-chart-line text-info me-2"></i>تقرير أسبوعي متاح
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">عرض جميع الإشعارات</a></li>
                        </ul>
                    </li>

                    <!-- User Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="{{ auth()->user()->avatar_url }}" 
                                 alt="Avatar" 
                                 class="rounded-circle me-2" 
                                 width="32" 
                                 height="32">
                            <span>{{ auth()->user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">
                                {{ auth()->user()->name }}
                                <small class="d-block text-muted">{{ auth()->user()->userType?->name ?? 'مستخدم' }}</small>
                            </h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('user-manager.profile') }}">
                                    <i class="fas fa-user me-2"></i>الملف الشخصي
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2"></i>الإعدادات
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('user-manager.logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid flex-grow-1 py-4">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1 text-dark fw-bold">@yield('page-title', 'لوحة التحكم')</h1>
                <p class="text-muted mb-0">@yield('page-description', 'مرحباً بك في لوحة التحكم الخاصة بإدارة المستخدمين')</p>
            </div>
            <div>
                @yield('page-actions')
            </div>
        </div>

        <!-- Breadcrumb -->
        @hasSection('breadcrumb')
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-white p-3 rounded shadow-sm">
                    @yield('breadcrumb')
                </ol>
            </nav>
        @endif

        <!-- Page Content -->
        <div class="fade-in-up">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-top py-3 mt-auto">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <small>
                            &copy; {{ date('Y') }} User Manager Package. 
                            جميع الحقوق محفوظة.
                        </small>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        <small class="text-muted">
                            الإصدار 2.0.0 | 
                            <a href="https://github.com/tourad/laravel-user-manager" class="text-decoration-none" target="_blank">
                                <i class="fab fa-github"></i> GitHub
                            </a>
                        </small>
                    </p>
                </div>
            </div>
        </div>
    </footer>
</div>

@push('scripts')
<script>
    // Auto-refresh page data every 30 seconds for dashboard
    @if(request()->routeIs('user-manager.dashboard'))
    setInterval(function() {
        // You can add AJAX calls here to refresh specific data
        // For now, we'll just update the timestamp
        const timestamp = document.querySelector('.last-updated');
        if (timestamp) {
            timestamp.textContent = 'آخر تحديث: ' + new Date().toLocaleTimeString('ar-SA');
        }
    }, 30000);
    @endif

    // Confirm logout
    document.querySelectorAll('form[action*="logout"] button').forEach(function(button) {
        button.addEventListener('click', function(e) {
            if (!confirm('هل أنت متأكد من تسجيل الخروج؟')) {
                e.preventDefault();
            }
        });
    });

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush

@push('styles')
<style>
    .navbar .nav-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        color: white !important;
    }

    .dropdown-menu {
        min-width: 250px;
    }

    .dropdown-header {
        padding: 0.75rem 1rem;
        background-color: var(--light-color);
        margin: 0 -0.5rem;
        margin-bottom: 0.5rem;
    }

    footer {
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
    }

    .alert {
        margin-bottom: 1rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .navbar-brand span {
            display: none;
        }
    }
</style>
@endpush
@endsection
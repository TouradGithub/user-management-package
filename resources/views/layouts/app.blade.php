<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'User Manager') - {{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --success-color: #059669;
            --danger-color: #dc2626;
            --warning-color: #d97706;
            --info-color: #0284c7;
            --dark-color: #1e293b;
            --light-color: #f8fafc;
            --border-color: #e2e8f0;
        }

        body {
            background-color: var(--light-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-size: 14px;
            color: var(--dark-color);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            box-shadow: 0 2px 20px rgba(37, 99, 235, 0.15);
        }

        .navbar .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease;
        }

        .navbar .navbar-nav .nav-link:hover {
            color: white !important;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 6px;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid var(--border-color);
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--light-color);
            border: none;
            font-weight: 600;
            color: var(--dark-color);
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-color: var(--border-color);
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
        }

        .alert {
            border: none;
            border-radius: 10px;
        }

        .sidebar {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            margin-right: 1rem;
        }

        .sidebar .nav-link {
            color: var(--dark-color);
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            margin: 0.25rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #3b82f6 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .stats-card .stats-number {
            font-size: 2rem;
            font-weight: 700;
        }

        .stats-card .stats-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        /* Arabic RTL Support */
        [dir="rtl"] {
            text-align: right;
        }

        [dir="rtl"] .sidebar {
            margin-right: 0;
            margin-left: 1rem;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.5s ease;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @yield('body')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 5000);
            });
        });

        // Confirm delete actions
        function confirmDelete(message = 'Are you sure you want to delete this item?') {
            return confirm(message);
        }

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Create temporary toast
                const toast = document.createElement('div');
                toast.className = 'alert alert-success position-fixed top-0 end-0 m-3';
                toast.style.zIndex = '9999';
                toast.innerHTML = '<i class="fas fa-check-circle"></i> Copied to clipboard!';
                document.body.appendChild(toast);
                
                setTimeout(function() {
                    toast.remove();
                }, 2000);
            });
        }
    </script>
    
    @stack('scripts')
</body>
</html>
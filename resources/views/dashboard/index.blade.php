@extends('user-manager::layouts.dashboard')

@section('page-title', 'لوحة التحكم')
@section('page-description', 'نظرة عامة على إحصائيات المستخدمين والأنشطة')

@section('page-actions')
    <div class="btn-group" role="group">
        <button type="button" class="btn btn-outline-primary" onclick="refreshData()">
            <i class="fas fa-sync-alt me-1"></i>تحديث البيانات
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="exportReport()">
            <i class="fas fa-download me-1"></i>تصدير التقرير
        </button>
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item active">
        <i class="fas fa-home me-1"></i>لوحة التحكم
    </li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Statistics Cards -->
    <div class="col-12">
        <div class="row g-3">
            <!-- Total Users -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">إجمالي المستخدمين</h6>
                                <h2 class="card-title mb-0 fw-bold text-primary" id="total-users">{{ $statistics['total_users'] }}</h2>
                                <small class="text-success">
                                    <i class="fas fa-arrow-up me-1"></i>+{{ $statistics['new_users_this_month'] }} هذا الشهر
                                </small>
                            </div>
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">المستخدمون النشطون</h6>
                                <h2 class="card-title mb-0 fw-bold text-success" id="active-users">{{ $statistics['active_users'] }}</h2>
                                <small class="text-info">
                                    <i class="fas fa-clock me-1"></i>آخر 24 ساعة
                                </small>
                            </div>
                            <div class="stats-icon bg-success bg-opacity-10 text-success">
                                <i class="fas fa-user-check fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Online Users -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">متصلون الآن</h6>
                                <h2 class="card-title mb-0 fw-bold text-info" id="online-users">{{ $statistics['online_users'] }}</h2>
                                <small class="text-muted">
                                    <i class="fas fa-circle text-success me-1"></i>نشطون حالياً
                                </small>
                            </div>
                            <div class="stats-icon bg-info bg-opacity-10 text-info">
                                <i class="fas fa-globe fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Failed Logins -->
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm h-100 stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="card-subtitle mb-2 text-muted">محاولات فاشلة</h6>
                                <h2 class="card-title mb-0 fw-bold text-warning" id="failed-logins">{{ $statistics['failed_logins'] }}</h2>
                                <small class="text-danger">
                                    <i class="fas fa-exclamation-triangle me-1"></i>آخر 24 ساعة
                                </small>
                            </div>
                            <div class="stats-icon bg-warning bg-opacity-10 text-warning">
                                <i class="fas fa-shield-alt fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>إحصائيات المستخدمين - آخر 7 أيام
                </h5>
            </div>
            <div class="card-body">
                <canvas id="usersChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history text-success me-2"></i>الأنشطة الأخيرة
                </h5>
                <a href="{{ route('user-manager.activities') }}" class="btn btn-sm btn-outline-primary">
                    عرض الكل
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($recentActivities as $activity)
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex align-items-start">
                                <div class="activity-icon me-3">
                                    @if($activity->action === 'login')
                                        <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                                            <i class="fas fa-sign-in-alt fa-sm"></i>
                                        </div>
                                    @elseif($activity->action === 'logout')
                                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                            <i class="fas fa-sign-out-alt fa-sm"></i>
                                        </div>
                                    @elseif($activity->action === 'create')
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                            <i class="fas fa-plus fa-sm"></i>
                                        </div>
                                    @elseif($activity->action === 'update')
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </div>
                                    @elseif($activity->action === 'delete')
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-2">
                                            <i class="fas fa-trash fa-sm"></i>
                                        </div>
                                    @else
                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle p-2">
                                            <i class="fas fa-cog fa-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <p class="mb-1 fw-medium">{{ $activity->user->name }}</p>
                                            <p class="mb-1 text-muted small">{{ $activity->description }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="list-group-item border-0 py-4 text-center text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                            لا توجد أنشطة حديثة
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- User Types Distribution -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-pie-chart text-info me-2"></i>توزيع أنواع المستخدمين
                </h5>
            </div>
            <div class="card-body">
                <canvas id="userTypesChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Countries -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-map-marked-alt text-warning me-2"></i>أكثر البلدان نشاطاً
                </h5>
            </div>
            <div class="card-body">
                <div class="country-stats">
                    @foreach($topCountries as $country)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <span class="flag-icon flag-icon-{{ strtolower($country->country_code) }} me-3"></span>
                                <span class="fw-medium">{{ $country->country_name }}</span>
                            </div>
                            <div class="text-end">
                                <span class="fw-bold text-primary">{{ $country->users_count }}</span>
                                <div class="progress mt-1" style="width: 100px; height: 4px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: {{ ($country->users_count / $topCountries->first()->users_count) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-server text-secondary me-2"></i>معلومات النظام
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted mb-2">نسخة Laravel</h6>
                            <h5 class="text-primary mb-0">{{ app()->version() }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted mb-2">نسخة PHP</h6>
                            <h5 class="text-success mb-0">{{ PHP_VERSION }}</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted mb-2">استخدام الذاكرة</h6>
                            <h5 class="text-info mb-0">{{ round(memory_get_usage() / 1024 / 1024, 2) }} MB</h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 border rounded">
                            <h6 class="text-muted mb-2 last-updated">آخر تحديث</h6>
                            <h5 class="text-warning mb-0">{{ now()->format('H:i:s') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Users Chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    const usersChart = new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'مستخدمون جدد',
                data: @json($chartData['new_users']),
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'مستخدمون نشطون',
                data: @json($chartData['active_users']),
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // User Types Chart
    const userTypesCtx = document.getElementById('userTypesChart').getContext('2d');
    const userTypesChart = new Chart(userTypesCtx, {
        type: 'doughnut',
        data: {
            labels: @json($userTypesData['labels']),
            datasets: [{
                data: @json($userTypesData['data']),
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#dc3545',
                    '#ffc107',
                    '#6f42c1',
                    '#fd7e14'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Refresh data function
    function refreshData() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري التحديث...';
        button.disabled = true;

        // Simulate data refresh
        setTimeout(() => {
            location.reload();
        }, 2000);
    }

    // Export report function
    function exportReport() {
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري التصدير...';
        button.disabled = true;

        // Simulate export process
        setTimeout(() => {
            // You can add actual export functionality here
            alert('سيتم إضافة وظيفة التصدير قريباً');
            button.innerHTML = originalText;
            button.disabled = false;
        }, 2000);
    }
</script>
@endpush

@push('styles')
<style>
    .stats-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .activity-icon {
        width: 36px;
        height: 36px;
    }

    .activity-icon .rounded-circle {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .country-stats .flag-icon {
        width: 24px;
        height: 18px;
        border-radius: 2px;
    }

    .progress {
        background-color: rgba(0, 0, 0, 0.1);
    }

    .card {
        transition: box-shadow 0.15s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush
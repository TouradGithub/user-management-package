@extends('user-manager::layouts.dashboard')

@section('page-title', 'الملف الشخصي')
@section('page-description', 'عرض وتعديل بيانات حسابك الشخصي')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('user-manager.dashboard') }}">
            <i class="fas fa-home me-1"></i>لوحة التحكم
        </a>
    </li>
    <li class="breadcrumb-item active">الملف الشخصي</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Profile Overview -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm sticky-top" style="top: 2rem;">
            <div class="card-body text-center">
                <div class="position-relative d-inline-block mb-3">
                    <img src="{{ auth()->user()->avatar_url }}" 
                         alt="Profile Picture" 
                         class="rounded-circle border border-3 border-white shadow" 
                         width="120" 
                         height="120"
                         id="currentAvatar">
                    @if(auth()->user()->is_online)
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" 
                              style="width: 20px; height: 20px;" 
                              title="متصل الآن"></span>
                    @endif
                </div>
                
                <h4 class="card-title mb-2">{{ auth()->user()->name }}</h4>
                
                @if(auth()->user()->userType)
                    <span class="badge mb-3" 
                          style="background-color: {{ auth()->user()->userType->color }}20; color: {{ auth()->user()->userType->color }};">
                        <i class="fas fa-tag me-1"></i>{{ auth()->user()->userType->name }}
                    </span>
                @endif
                
                <p class="text-muted mb-3">{{ auth()->user()->email }}</p>
                
                @if(auth()->user()->bio)
                    <p class="small text-secondary">{{ auth()->user()->bio }}</p>
                @endif

                <div class="row text-center mt-4">
                    <div class="col-4">
                        <div class="border-end">
                            <h6 class="text-primary mb-0">{{ auth()->user()->activities()->count() }}</h6>
                            <small class="text-muted">الأنشطة</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="border-end">
                            <h6 class="text-success mb-0">{{ auth()->user()->sessions()->active()->count() }}</h6>
                            <small class="text-muted">الجلسات</small>
                        </div>
                    </div>
                    <div class="col-4">
                        <h6 class="text-info mb-0">{{ auth()->user()->created_at->diffInDays() }}</h6>
                        <small class="text-muted">يوماً</small>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-1"></i>تعديل البيانات
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fas fa-key me-1"></i>تغيير كلمة المرور
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="col-lg-8">
        <!-- Account Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user text-primary me-2"></i>معلومات الحساب
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">الاسم الكامل</label>
                        <p class="mb-0 fw-medium">{{ auth()->user()->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">البريد الإلكتروني</label>
                        <div class="d-flex align-items-center">
                            <p class="mb-0 fw-medium">{{ auth()->user()->email }}</p>
                            @if(auth()->user()->email_verified_at)
                                <span class="badge bg-success ms-2">
                                    <i class="fas fa-check-circle me-1"></i>محقق
                                </span>
                            @else
                                <span class="badge bg-warning ms-2">
                                    <i class="fas fa-exclamation-triangle me-1"></i>غير محقق
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">رقم الهاتف</label>
                        <p class="mb-0 fw-medium">{{ auth()->user()->phone ?? 'غير محدد' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">تاريخ الميلاد</label>
                        <p class="mb-0 fw-medium">
                            {{ auth()->user()->date_of_birth ? auth()->user()->date_of_birth->format('Y/m/d') : 'غير محدد' }}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">البلد</label>
                        <p class="mb-0 fw-medium">{{ auth()->user()->country ?? 'غير محدد' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">المدينة</label>
                        <p class="mb-0 fw-medium">{{ auth()->user()->city ?? 'غير محدد' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">المنطقة الزمنية</label>
                        <p class="mb-0 fw-medium">{{ auth()->user()->timezone ?? 'Asia/Riyadh' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">اللغة</label>
                        <p class="mb-0 fw-medium">{{ auth()->user()->language === 'ar' ? 'العربية' : 'English' }}</p>
                    </div>
                    @if(auth()->user()->bio)
                        <div class="col-12">
                            <label class="form-label text-muted">النبذة الشخصية</label>
                            <p class="mb-0">{{ auth()->user()->bio }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-history text-success me-2"></i>النشاطات الأخيرة
                </h5>
                <a href="{{ route('user-manager.activities') }}" class="btn btn-sm btn-outline-primary">
                    عرض جميع الأنشطة
                </a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse(auth()->user()->activities()->latest()->limit(5)->get() as $activity)
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
                                    @elseif($activity->action === 'update')
                                        <div class="bg-info bg-opacity-10 text-info rounded-circle p-2">
                                            <i class="fas fa-edit fa-sm"></i>
                                        </div>
                                    @else
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2">
                                            <i class="fas fa-cog fa-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <p class="mb-1">{{ $activity->description }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $activity->created_at->diffForHumans() }}
                                        </small>
                                        @if($activity->ip_address)
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $activity->ip_address }}
                                            </small>
                                        @endif
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

        <!-- Active Sessions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-desktop text-info me-2"></i>الجلسات النشطة
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>الجهاز</th>
                                <th>الموقع</th>
                                <th>آخر نشاط</th>
                                <th width="100">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(auth()->user()->sessions()->active()->latest()->get() as $session)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                @if(str_contains($session->device_type, 'mobile'))
                                                    <i class="fas fa-mobile-alt text-primary fa-lg"></i>
                                                @elseif(str_contains($session->device_type, 'tablet'))
                                                    <i class="fas fa-tablet-alt text-info fa-lg"></i>
                                                @else
                                                    <i class="fas fa-laptop text-success fa-lg"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="mb-0 fw-medium">{{ $session->device_type }}</p>
                                                <small class="text-muted">{{ $session->browser }} {{ $session->browser_version }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="mb-0">{{ $session->location ?? 'غير معروف' }}</p>
                                        <small class="text-muted">{{ $session->ip_address }}</small>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $session->last_activity_at->diffForHumans() }}</span>
                                        @if($session->id === session()->getId())
                                            <small class="badge bg-success ms-2">الجلسة الحالية</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($session->id !== session()->getId())
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="terminateSession('{{ $session->id }}')"
                                                    data-bs-toggle="tooltip" 
                                                    title="إنهاء الجلسة">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @else
                                            <span class="badge bg-success">نشطة</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-desktop fa-2x mb-3 d-block"></i>
                                        لا توجد جلسات نشطة
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>تعديل البيانات الشخصية
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user-manager.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Avatar -->
                        <div class="col-12 text-center">
                            <div class="mb-3">
                                <img src="{{ auth()->user()->avatar_url }}" 
                                     alt="Avatar Preview" 
                                     class="rounded-circle border" 
                                     width="100" 
                                     height="100"
                                     id="avatarPreview">
                                <div class="mt-2">
                                    <input type="file" class="form-control" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                                    <div class="form-text">اختر صورة جديدة (اختياري)</div>
                                </div>
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone }}">
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" class="form-control" name="date_of_birth" value="{{ auth()->user()->date_of_birth?->format('Y-m-d') }}">
                        </div>

                        <!-- Country -->
                        <div class="col-md-6">
                            <label class="form-label">البلد</label>
                            <select class="form-select" name="country">
                                <option value="">اختر البلد</option>
                                <option value="SA" {{ auth()->user()->country === 'SA' ? 'selected' : '' }}>السعودية</option>
                                <option value="AE" {{ auth()->user()->country === 'AE' ? 'selected' : '' }}>الإمارات</option>
                                <option value="EG" {{ auth()->user()->country === 'EG' ? 'selected' : '' }}>مصر</option>
                                <option value="JO" {{ auth()->user()->country === 'JO' ? 'selected' : '' }}>الأردن</option>
                                <option value="LB" {{ auth()->user()->country === 'LB' ? 'selected' : '' }}>لبنان</option>
                                <option value="KW" {{ auth()->user()->country === 'KW' ? 'selected' : '' }}>الكويت</option>
                                <option value="QA" {{ auth()->user()->country === 'QA' ? 'selected' : '' }}>قطر</option>
                                <option value="BH" {{ auth()->user()->country === 'BH' ? 'selected' : '' }}>البحرين</option>
                                <option value="OM" {{ auth()->user()->country === 'OM' ? 'selected' : '' }}>عمان</option>
                            </select>
                        </div>

                        <!-- City -->
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <input type="text" class="form-control" name="city" value="{{ auth()->user()->city }}">
                        </div>

                        <!-- Language -->
                        <div class="col-md-6">
                            <label class="form-label">اللغة المفضلة</label>
                            <select class="form-select" name="language">
                                <option value="ar" {{ auth()->user()->language === 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ auth()->user()->language === 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>

                        <!-- Bio -->
                        <div class="col-12">
                            <label class="form-label">النبذة الشخصية</label>
                            <textarea class="form-control" name="bio" rows="3">{{ auth()->user()->bio }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>تغيير كلمة المرور
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user-manager.profile.password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الحالية</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="current_password" required id="currentPassword">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('currentPassword')">
                                <i class="fas fa-eye" id="currentPassword-icon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" required minlength="8" id="newPassword">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('newPassword')">
                                <i class="fas fa-eye" id="newPassword-icon"></i>
                            </button>
                        </div>
                        <div class="form-text">يجب أن تحتوي على 8 أحرف على الأقل</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password_confirmation" required minlength="8" id="confirmPassword">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('confirmPassword')">
                                <i class="fas fa-eye" id="confirmPassword-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key me-1"></i>تغيير كلمة المرور
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview avatar
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatarPreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Toggle password visibility
    function togglePasswordVisibility(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            field.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    // Terminate session
    function terminateSession(sessionId) {
        if (confirm('هل أنت متأكد من إنهاء هذه الجلسة؟')) {
            fetch(`{{ route('user-manager.profile.terminate-session') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ session_id: sessionId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('حدث خطأ في إنهاء الجلسة');
                }
            })
            .catch(error => {
                alert('حدث خطأ في إنهاء الجلسة');
            });
        }
    }
</script>
@endpush

@push('styles')
<style>
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

    .sticky-top {
        z-index: 1020;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        transition: box-shadow 0.15s ease-in-out;
    }

    .border-end {
        border-right: 1px solid rgba(0, 0, 0, 0.1) !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endpush
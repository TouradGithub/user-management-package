@extends('user-manager::layouts.dashboard')

@section('page-title', 'إضافة مستخدم جديد')
@section('page-description', 'إنشاء حساب مستخدم جديد في النظام')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('user-manager.dashboard') }}">
            <i class="fas fa-home me-1"></i>لوحة التحكم
        </a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('user-manager.users') }}">المستخدمون</a>
    </li>
    <li class="breadcrumb-item active">إضافة مستخدم</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <form action="{{ route('user-manager.users.store') }}" method="POST" enctype="multipart/form-data" id="createUserForm">
            @csrf
            
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user text-primary me-2"></i>المعلومات الأساسية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Avatar Upload -->
                        <div class="col-12 text-center">
                            <div class="mb-3">
                                <div class="avatar-upload-wrapper position-relative d-inline-block">
                                    <img src="{{ asset('default-avatar.png') }}" 
                                         alt="Avatar Preview" 
                                         class="rounded-circle border" 
                                         width="120" 
                                         height="120"
                                         id="avatarPreview">
                                    <button type="button" 
                                            class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle"
                                            onclick="document.getElementById('avatar').click()">
                                        <i class="fas fa-camera"></i>
                                    </button>
                                </div>
                                <input type="file" 
                                       class="d-none" 
                                       id="avatar" 
                                       name="avatar" 
                                       accept="image/*" 
                                       onchange="previewAvatar(this)">
                                <div class="form-text mt-2">
                                    اختر صورة شخصية (اختياري) - يُفضل 300x300 بكسل
                                </div>
                                @error('avatar')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="col-md-6">
                            <label class="form-label required">الاسم الكامل</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required 
                                       placeholder="أدخل الاسم الكامل">
                            </div>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6">
                            <label class="form-label required">البريد الإلكتروني</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       placeholder="example@domain.com">
                            </div>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <input type="tel" 
                                       class="form-control @error('phone') is-invalid @enderror" 
                                       name="phone" 
                                       value="{{ old('phone') }}" 
                                       placeholder="01234567890">
                            </div>
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الميلاد</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar-alt"></i>
                                </span>
                                <input type="date" 
                                       class="form-control @error('date_of_birth') is-invalid @enderror" 
                                       name="date_of_birth" 
                                       value="{{ old('date_of_birth') }}">
                            </div>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- User Type -->
                        <div class="col-md-6">
                            <label class="form-label required">نوع المستخدم</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-tag"></i>
                                </span>
                                <select class="form-select @error('user_type_id') is-invalid @enderror" 
                                        name="user_type_id" 
                                        required
                                        onchange="updateUserTypeInfo()">
                                    <option value="">اختر نوع المستخدم</option>
                                    @foreach($userTypes as $type)
                                        <option value="{{ $type->id }}" 
                                                {{ old('user_type_id') == $type->id ? 'selected' : '' }}
                                                data-description="{{ $type->description }}"
                                                data-permissions="{{ json_encode($type->permissions) }}">
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="userTypeInfo" class="form-text mt-2"></div>
                            @error('user_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <label class="form-label">حالة الحساب</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-toggle-on"></i>
                                </span>
                                <select class="form-select @error('is_active') is-invalid @enderror" 
                                        name="is_active">
                                    <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>نشط</option>
                                    <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div class="col-md-6">
                            <label class="form-label">البلد</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-globe"></i>
                                </span>
                                <select class="form-select @error('country') is-invalid @enderror" 
                                        name="country">
                                    <option value="">اختر البلد</option>
                                    <option value="SA" {{ old('country') == 'SA' ? 'selected' : '' }}>السعودية</option>
                                    <option value="AE" {{ old('country') == 'AE' ? 'selected' : '' }}>الإمارات</option>
                                    <option value="EG" {{ old('country') == 'EG' ? 'selected' : '' }}>مصر</option>
                                    <option value="JO" {{ old('country') == 'JO' ? 'selected' : '' }}>الأردن</option>
                                    <option value="LB" {{ old('country') == 'LB' ? 'selected' : '' }}>لبنان</option>
                                    <option value="KW" {{ old('country') == 'KW' ? 'selected' : '' }}>الكويت</option>
                                    <option value="QA" {{ old('country') == 'QA' ? 'selected' : '' }}>قطر</option>
                                    <option value="BH" {{ old('country') == 'BH' ? 'selected' : '' }}>البحرين</option>
                                    <option value="OM" {{ old('country') == 'OM' ? 'selected' : '' }}>عمان</option>
                                    <option value="MA" {{ old('country') == 'MA' ? 'selected' : '' }}>المغرب</option>
                                    <option value="TN" {{ old('country') == 'TN' ? 'selected' : '' }}>تونس</option>
                                    <option value="DZ" {{ old('country') == 'DZ' ? 'selected' : '' }}>الجزائر</option>
                                </select>
                            </div>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="col-md-6">
                            <label class="form-label">المدينة</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                <input type="text" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       name="city" 
                                       value="{{ old('city') }}" 
                                       placeholder="أدخل اسم المدينة">
                            </div>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div class="col-12">
                            <label class="form-label">نبذة شخصية</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      name="bio" 
                                      rows="3" 
                                      placeholder="نبذة مختصرة عن المستخدم (اختياري)">{{ old('bio') }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-shield-alt text-success me-2"></i>إعدادات الأمان
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Password -->
                        <div class="col-md-6">
                            <label class="form-label required">كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       id="password" 
                                       required 
                                       placeholder="كلمة مرور قوية"
                                       minlength="8">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="password-icon"></i>
                                </button>
                            </div>
                            <div class="form-text">يجب أن تحتوي على 8 أحرف على الأقل</div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="col-md-6">
                            <label class="form-label required">تأكيد كلمة المرور</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" 
                                       class="form-control @error('password_confirmation') is-invalid @enderror" 
                                       name="password_confirmation" 
                                       id="password_confirmation" 
                                       required 
                                       placeholder="أعد إدخال كلمة المرور"
                                       minlength="8">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Security Options -->
                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="email_verified" 
                                               id="emailVerified" 
                                               {{ old('email_verified') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="emailVerified">
                                            تحقق من البريد الإلكتروني
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="force_password_change" 
                                               id="forcePasswordChange" 
                                               {{ old('force_password_change') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="forcePasswordChange">
                                            فرض تغيير كلمة المرور في أول دخول
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="send_welcome_email" 
                                               id="sendWelcomeEmail" 
                                               {{ old('send_welcome_email', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="sendWelcomeEmail">
                                            إرسال بريد الترحيب
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Settings -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog text-info me-2"></i>إعدادات إضافية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Language -->
                        <div class="col-md-6">
                            <label class="form-label">اللغة المفضلة</label>
                            <select class="form-select @error('language') is-invalid @enderror" 
                                    name="language">
                                <option value="ar" {{ old('language', 'ar') == 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Timezone -->
                        <div class="col-md-6">
                            <label class="form-label">المنطقة الزمنية</label>
                            <select class="form-select @error('timezone') is-invalid @enderror" 
                                    name="timezone">
                                <option value="Asia/Riyadh" {{ old('timezone', 'Asia/Riyadh') == 'Asia/Riyadh' ? 'selected' : '' }}>الرياض (GMT+3)</option>
                                <option value="Asia/Dubai" {{ old('timezone') == 'Asia/Dubai' ? 'selected' : '' }}>دبي (GMT+4)</option>
                                <option value="Africa/Cairo" {{ old('timezone') == 'Africa/Cairo' ? 'selected' : '' }}>القاهرة (GMT+2)</option>
                                <option value="Asia/Amman" {{ old('timezone') == 'Asia/Amman' ? 'selected' : '' }}>عمان (GMT+3)</option>
                            </select>
                            @error('timezone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Tags -->
                        <div class="col-12">
                            <label class="form-label">العلامات (Tags)</label>
                            <input type="text" 
                                   class="form-control @error('tags') is-invalid @enderror" 
                                   name="tags" 
                                   value="{{ old('tags') }}" 
                                   placeholder="أدخل العلامات مفصولة بفاصلة (مثال: مطور، مبرمج، محاسب)">
                            <div class="form-text">استخدم الفاصلة لفصل العلامات</div>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="create_another" id="createAnother">
                            <label class="form-check-label" for="createAnother">
                                إنشاء مستخدم آخر بعد الحفظ
                            </label>
                        </div>
                        <div class="btn-group" role="group">
                            <a href="{{ route('user-manager.users') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-right me-1"></i>رجوع
                            </a>
                            <button type="reset" class="btn btn-outline-warning">
                                <i class="fas fa-undo me-1"></i>إعادة تعيين
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>إنشاء المستخدم
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
    function togglePassword(fieldId) {
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

    // Update user type information
    function updateUserTypeInfo() {
        const select = document.querySelector('select[name="user_type_id"]');
        const infoDiv = document.getElementById('userTypeInfo');
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            const description = selectedOption.dataset.description;
            const permissions = JSON.parse(selectedOption.dataset.permissions || '[]');
            
            let info = `<strong>الوصف:</strong> ${description}`;
            if (permissions.length > 0) {
                info += `<br><strong>الصلاحيات:</strong> ${permissions.join(', ')}`;
            }
            
            infoDiv.innerHTML = info;
        } else {
            infoDiv.innerHTML = '';
        }
    }

    // Confirm password validation
    document.getElementById('password_confirmation').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
            this.setCustomValidity('كلمات المرور غير متطابقة');
            this.classList.add('is-invalid');
        } else {
            this.setCustomValidity('');
            this.classList.remove('is-invalid');
        }
    });

    // Form submission
    document.getElementById('createUserForm').addEventListener('submit', function(e) {
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>جاري الإنشاء...';
        submitButton.disabled = true;
        
        // Re-enable button after 5 seconds to handle errors
        setTimeout(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }, 5000);
    });

    // Initialize user type info on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateUserTypeInfo();
    });
</script>
@endpush

@push('styles')
<style>
    .required::after {
        content: ' *';
        color: #dc3545;
    }

    .avatar-upload-wrapper:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease-in-out;
    }

    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .btn-group .btn {
        border-radius: 0.375rem;
        margin-left: 0.25rem;
    }

    .btn-group .btn:first-child {
        margin-left: 0;
    }

    .card {
        transition: box-shadow 0.15s ease-in-out;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .input-group-text {
        background-color: rgba(13, 110, 253, 0.1);
        border-color: rgba(13, 110, 253, 0.2);
        color: var(--bs-primary);
    }
</style>
@endpush
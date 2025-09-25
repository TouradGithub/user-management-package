@extends('user-manager::layouts.app')

@section('title', 'تسجيل الدخول')

@section('body')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-6 col-sm-8">
                <div class="card shadow-lg border-0 fade-in-up">
                    <div class="card-body p-5">
                        <!-- Logo and Title -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-users-cog fa-3x text-primary"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-2">مرحباً بعودتك</h3>
                            <p class="text-muted">سجل دخولك للوصول إلى لوحة التحكم</p>
                        </div>

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

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                @foreach ($errors->all() as $error)
                                    <div>{{ $error }}</div>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('user-manager.login') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-semibold">
                                    <i class="fas fa-envelope me-2 text-primary"></i>البريد الإلكتروني
                                </label>
                                <input type="email" 
                                       class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       placeholder="أدخل بريدك الإلكتروني"
                                       required 
                                       autocomplete="email" 
                                       autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">
                                    <i class="fas fa-lock me-2 text-primary"></i>كلمة المرور
                                </label>
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="أدخل كلمة المرور"
                                           required 
                                           autocomplete="current-password">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="remember" 
                                           name="remember" 
                                           {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted" for="remember">
                                        تذكرني لمدة أطول
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid mb-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>تسجيل الدخول
                                </button>
                            </div>
                        </form>

                        <!-- Additional Links -->
                        <div class="text-center">
                            <hr class="my-4">
                            <p class="text-muted mb-0">
                                <small>
                                    <i class="fas fa-shield-alt me-1"></i>
                                    محمي بأحدث تقنيات الأمان
                                </small>
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer with System Info -->
                    <div class="card-footer bg-light text-center border-0 py-3">
                        <small class="text-muted">
                            <i class="fas fa-code me-1"></i>
                            User Manager Package v2.0.0
                        </small>
                    </div>
                </div>

                <!-- Security Notice -->
                <div class="text-center mt-4">
                    <p class="text-white-50">
                        <i class="fas fa-info-circle me-1"></i>
                        لحمايتك، سيتم تسجيل جميع محاولات تسجيل الدخول
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Toggle password visibility
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            toggleIcon.className = 'fas fa-eye-slash';
        } else {
            passwordField.type = 'password';
            toggleIcon.className = 'fas fa-eye';
        }
    }

    // Add floating label effect
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.form-control');
        
        inputs.forEach(function(input) {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                if (this.value === '') {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    });

    // Add loading state to submit button
    document.querySelector('form').addEventListener('submit', function() {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جارٍ تسجيل الدخول...';
        submitBtn.disabled = true;
    });
</script>
@endpush

@push('styles')
<style>
    .form-control:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
        border: none;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
    }

    .input-group .btn {
        border-color: #e2e8f0;
    }

    .card {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95);
    }

    @media (max-width: 576px) {
        .card-body {
            padding: 2rem 1.5rem !important;
        }
    }
</style>
@endpush
@endsection
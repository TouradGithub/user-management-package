@extends('user-manager::layouts.dashboard')

@section('page-title', 'إدارة المستخدمين')
@section('page-description', 'عرض وإدارة جميع المستخدمين في النظام')

@section('page-actions')
    <div class="btn-group" role="group">
        <a href="{{ route('user-manager.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>مستخدم جديد
        </a>
        <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-upload me-1"></i>استيراد
        </button>
        <button type="button" class="btn btn-outline-secondary" onclick="exportUsers()">
            <i class="fas fa-download me-1"></i>تصدير
        </button>
    </div>
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('user-manager.dashboard') }}">
            <i class="fas fa-home me-1"></i>لوحة التحكم
        </a>
    </li>
    <li class="breadcrumb-item active">المستخدمون</li>
@endsection

@section('content')
<div class="row g-4">
    <!-- Filters Card -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('user-manager.users') }}" class="row g-3" id="filtersForm">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="البحث بالاسم أو البريد الإلكتروني...">
                        </div>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">نوع المستخدم</label>
                        <select class="form-select" name="user_type_id">
                            <option value="">جميع الأنواع</option>
                            @foreach($userTypes as $type)
                                <option value="{{ $type->id }}" {{ request('user_type_id') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select class="form-select" name="status">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">التحقق من البريد</label>
                        <select class="form-select" name="email_verified">
                            <option value="">الكل</option>
                            <option value="1" {{ request('email_verified') === '1' ? 'selected' : '' }}>محقق</option>
                            <option value="0" {{ request('email_verified') === '0' ? 'selected' : '' }}>غير محقق</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">ترتيب حسب</label>
                        <select class="form-select" name="sort_by">
                            <option value="created_at" {{ request('sort_by', 'created_at') === 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                            <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>الاسم</option>
                            <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>البريد الإلكتروني</option>
                            <option value="last_activity" {{ request('sort_by') === 'last_activity' ? 'selected' : '' }}>آخر نشاط</option>
                        </select>
                    </div>
                    
                    <div class="col-md-1">
                        <label class="form-label">الاتجاه</label>
                        <select class="form-select" name="sort_dir">
                            <option value="desc" {{ request('sort_dir', 'desc') === 'desc' ? 'selected' : '' }}>تنازلي</option>
                            <option value="asc" {{ request('sort_dir') === 'asc' ? 'selected' : '' }}>تصاعدي</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>تطبيق المرشحات
                            </button>
                            <a href="{{ route('user-manager.users') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>إزالة المرشحات
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users text-primary me-2"></i>
                        قائمة المستخدمين ({{ $users->total() }})
                    </h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm" onchange="changePerPage(this.value)" style="width: auto;">
                            <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                                <th>المستخدم</th>
                                <th>نوع المستخدم</th>
                                <th>الحالة</th>
                                <th>آخر نشاط</th>
                                <th>الجلسات</th>
                                <th>تاريخ التسجيل</th>
                                <th width="150">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="position-relative me-3">
                                                <img src="{{ $user->avatar_url }}" 
                                                     alt="Avatar" 
                                                     class="rounded-circle" 
                                                     width="40" 
                                                     height="40">
                                                @if($user->is_online)
                                                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" 
                                                          style="width: 12px; height: 12px;" 
                                                          title="متصل الآن"></span>
                                                @endif
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-medium">{{ $user->name }}</h6>
                                                <small class="text-muted">{{ $user->email }}</small>
                                                @if(!$user->email_verified_at)
                                                    <small class="text-warning d-block">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>البريد غير محقق
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->userType)
                                            <span class="badge" 
                                                  style="background-color: {{ $user->userType->color }}20; color: {{ $user->userType->color }};">
                                                <i class="fas fa-tag me-1"></i>{{ $user->userType->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">غير محدد</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->last_activity_at)
                                            <span class="text-muted">{{ $user->last_activity_at->diffForHumans() }}</span>
                                        @else
                                            <span class="text-muted">لم يسجل دخول بعد</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $user->active_sessions_count }}</span>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $user->created_at->format('Y/m/d') }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('user-manager.users.show', $user) }}" 
                                               class="btn btn-outline-info" 
                                               data-bs-toggle="tooltip" 
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('user-manager.users.edit', $user) }}" 
                                               class="btn btn-outline-primary" 
                                               data-bs-toggle="tooltip" 
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        data-bs-toggle="tooltip" 
                                                        title="حذف" 
                                                        onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                            <h5>لا توجد مستخدمون</h5>
                                            <p>لم يتم العثور على مستخدمين يطابقون معايير البحث</p>
                                            <a href="{{ route('user-manager.users.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>إضافة مستخدم جديد
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($users->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $users->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Bulk Actions -->
    @if($users->count() > 0)
        <div class="col-12">
            <div class="card border-0 shadow-sm" id="bulkActionsCard" style="display: none;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-0">تم تحديد <span id="selectedCount">0</span> مستخدم</h6>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-success" onclick="bulkAction('activate')">
                                <i class="fas fa-check me-1"></i>تفعيل
                            </button>
                            <button type="button" class="btn btn-outline-warning" onclick="bulkAction('deactivate')">
                                <i class="fas fa-times me-1"></i>إلغاء تفعيل
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="bulkAction('export')">
                                <i class="fas fa-download me-1"></i>تصدير
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="bulkAction('delete')">
                                <i class="fas fa-trash me-1"></i>حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>استيراد المستخدمين
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('user-manager.users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ملف Excel/CSV</label>
                        <input type="file" class="form-control" name="file" accept=".xlsx,.xls,.csv" required>
                        <div class="form-text">
                            يجب أن يحتوي الملف على أعمدة: name, email, password (اختياري)
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">نوع المستخدم الافتراضي</label>
                        <select class="form-select" name="default_user_type_id">
                            <option value="">اختر نوع المستخدم</option>
                            @foreach($userTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="send_welcome_email" id="sendWelcomeEmail" checked>
                        <label class="form-check-label" for="sendWelcomeEmail">
                            إرسال بريد الترحيب للمستخدمين الجدد
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-1"></i>استيراد
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>تأكيد الحذف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من حذف المستخدم <strong id="deleteUserName"></strong>؟</p>
                <p class="text-muted">هذا الإجراء لا يمكن التراجع عنه.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>حذف
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Change per page
    function changePerPage(value) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', value);
        window.location.href = url.toString();
    }

    // Select all checkboxes
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Update bulk actions visibility
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const bulkActionsCard = document.getElementById('bulkActionsCard');
        const selectedCount = document.getElementById('selectedCount');
        
        if (checkedBoxes.length > 0) {
            bulkActionsCard.style.display = 'block';
            selectedCount.textContent = checkedBoxes.length;
        } else {
            bulkActionsCard.style.display = 'none';
        }
    }

    // Delete user
    function deleteUser(id, name) {
        document.getElementById('deleteUserName').textContent = name;
        document.getElementById('deleteForm').action = `{{ route('user-manager.users.index') }}/${id}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Bulk actions
    function bulkAction(action) {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const userIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        if (userIds.length === 0) {
            alert('يرجى تحديد مستخدم واحد على الأقل');
            return;
        }
        
        let confirmMessage = '';
        switch(action) {
            case 'activate':
                confirmMessage = `تفعيل ${userIds.length} مستخدم؟`;
                break;
            case 'deactivate':
                confirmMessage = `إلغاء تفعيل ${userIds.length} مستخدم؟`;
                break;
            case 'delete':
                confirmMessage = `حذف ${userIds.length} مستخدم نهائياً؟ لا يمكن التراجع عن هذا الإجراء.`;
                break;
            case 'export':
                confirmMessage = `تصدير بيانات ${userIds.length} مستخدم؟`;
                break;
        }
        
        if (confirm(confirmMessage)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('user-manager.users.bulk-action') }}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            form.appendChild(csrfToken);
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);
            
            userIds.forEach(id => {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'user_ids[]';
                idInput.value = id;
                form.appendChild(idInput);
            });
            
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Export users
    function exportUsers() {
        window.location.href = `{{ route('user-manager.users.export') }}?{{ http_build_query(request()->query()) }}`;
    }
</script>
@endpush

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .user-checkbox, #selectAll {
        transform: scale(1.1);
    }

    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }

    .position-relative .rounded-circle {
        border: 2px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .badge {
        font-size: 0.75em;
        font-weight: 500;
    }

    .card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush
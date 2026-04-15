@extends('teacher.layouts.app')

@section('title', 'Registration')
@section('page-title', 'Registration')

@section('content')
<!-- Excel Upload Section -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title"><i class="fas fa-users-cog" style="color: var(--primary);"></i> Bulk Student Registration</h2>
    </div>
    <div class="card-body">
        <div class="upload-grid">
            <!-- Left Side - Upload Area -->
            <div class="upload-area" id="dropZone">
                <div class="upload-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <h3>Drop your Excel file here</h3>
                <p>or click to browse</p>
                <form action="{{ route('teacher.registration.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm" class="js-loading-form">
                    @csrf
                    <input type="file" name="student_file" accept=".xlsx,.xls,.csv" required id="fileInput">
                    <button type="submit" class="btn btn-primary btn-lg js-loading-button" data-loading-text="Uploading...">
                        <i class="fas fa-upload"></i> Upload Students
                    </button>
                </form>
            </div>

            <!-- Right Side - Info -->
            <div class="upload-info">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-file-excel"></i>
                    </div>
                    <div class="info-content">
                        <h4>Excel Format</h4>
                        <p>Column headers: <code>FNAME</code>, <code>LNAME</code>, <code>MNAME</code> (optional)</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div class="info-content">
                        <h4>Auto-Generated</h4>
                        <p>Student codes, usernames, and passwords are created automatically</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="info-content">
                        <h4>Need a template?</h4>
                        <a href="{{ route('teacher.registration.template') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-file-download"></i> Download Template
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.upload-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

.upload-area {
    border: 2px dashed var(--border);
    border-radius: var(--radius);
    padding: 40px 30px;
    text-align: center;
    transition: all 0.3s ease;
    background: var(--bg-main);
}

.upload-area:hover,
.upload-area.dragover {
    border-color: var(--primary);
    background: rgba(79, 70, 229, 0.05);
}

.upload-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 20px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-icon i {
    font-size: 2rem;
    color: #fff;
}

.upload-area h3 {
    margin: 0 0 8px;
    color: var(--text-primary);
    font-size: 1.1rem;
}

.upload-area p {
    margin: 0 0 20px;
    color: var(--text-muted);
    font-size: 0.9rem;
}

.upload-area input[type="file"] {
    display: none;
}

.upload-info {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px;
    background: var(--bg-main);
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
}

.info-icon {
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-icon i {
    color: #fff;
    font-size: 1.1rem;
}

.info-content h4 {
    margin: 0 0 4px;
    font-size: 0.95rem;
    color: var(--text-primary);
}

.info-content p {
    margin: 0;
    font-size: 0.85rem;
    color: var(--text-muted);
}

.info-content code {
    background: rgba(79, 70, 229, 0.1);
    color: var(--primary);
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 0.8rem;
}

.info-content .btn {
    margin-top: 8px;
}

.btn.is-loading {
    opacity: 0.8;
    pointer-events: none;
}

@media (max-width: 768px) {
    .upload-grid {
        grid-template-columns: 1fr;
    }
    
    .upload-area {
        padding: 30px 20px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Drag and drop functionality
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    if (dropZone && fileInput) {
        // Click to browse
        dropZone.addEventListener('click', (e) => {
            if (e.target.tagName !== 'BUTTON') {
                fileInput.click();
            }
        });
        
        // File selected
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                const fileName = fileInput.files[0].name;
                dropZone.querySelector('p').textContent = `Selected: ${fileName}`;
            }
        });
        
        // Drag events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
            });
        });
        
        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.add('dragover');
            });
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => {
                dropZone.classList.remove('dragover');
            });
        });
        
        // Handle dropped file
        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const fileName = files[0].name;
                dropZone.querySelector('p').textContent = `Selected: ${fileName}`;
            }
        });
    }

    // Registered students table: select all + bulk action state
    const selectAllStudents = document.getElementById('selectAllStudents');
    const bulkApproveStudentsBtn = document.getElementById('bulkApproveStudentsBtn');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');

    function updateBulkApproveStudentsState() {
        const selectedCount = Array.from(studentCheckboxes).filter((checkbox) => checkbox.checked).length;
        if (bulkApproveStudentsBtn) {
            bulkApproveStudentsBtn.disabled = selectedCount === 0;
        }
    }

    if (selectAllStudents) {
        selectAllStudents.addEventListener('change', () => {
            studentCheckboxes.forEach((checkbox) => {
                checkbox.checked = selectAllStudents.checked;
            });
            updateBulkApproveStudentsState();
        });
    }

    studentCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', () => {
            const allChecked = studentCheckboxes.length > 0
                && Array.from(studentCheckboxes).every((item) => item.checked);

            if (selectAllStudents) {
                selectAllStudents.checked = allChecked;
            }
            updateBulkApproveStudentsState();
        });
    });

    // Form submit loading state to prevent duplicate CRUD actions
    document.querySelectorAll('.js-loading-form').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const submitter = event.submitter || form.querySelector('button[type="submit"], input[type="submit"]');
            const loadingButtons = submitter
                ? [submitter]
                : Array.from(form.querySelectorAll('button[type="submit"], input[type="submit"]'));

            loadingButtons.forEach((button) => {
                if (button.dataset.submitted === 'true') {
                    event.preventDefault();
                    return;
                }

                button.dataset.submitted = 'true';
                button.disabled = true;
                button.classList.add('is-loading');

                if (button.tagName === 'BUTTON') {
                    if (!button.dataset.originalHtml) {
                        button.dataset.originalHtml = button.innerHTML;
                    }
                    button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${button.dataset.loadingText || 'Processing...'}`;
                } else {
                    if (!button.dataset.originalValue) {
                        button.dataset.originalValue = button.value;
                    }
                    button.value = button.dataset.loadingText || 'Processing...';
                }
            });
        });
    });
});
</script>

<!-- Pending Registrations -->
<div class="card" style="margin-bottom: 24px;">
    <div class="card-header">
        <h2 class="card-title">Pending Registrations (Not Yet Claimed)</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Student Code</th>
                        <th>Username</th>
                        <th>Default Password</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingRegistrations as $registration)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-weight: 500;">{{ $registration->full_name }}</span>
                            </div>
                        </td>
                        <td>
                            <code style="padding: 4px 8px; background: #f4f4f4; border-radius: 4px; font-family: monospace;">{{ $registration->student_code }}</code>
                        </td>
                        <td>{{ $registration->username }}</td>
                        <td>
                            <span style="font-family: monospace; background: #fff3cd; padding: 2px 6px; border-radius: 4px;">{{ $registration->default_password }}</span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px;">
                                <form action="{{ route('teacher.registration.regenerate', $registration->id) }}" method="POST" style="display: inline;" class="js-loading-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-secondary js-loading-button" data-loading-text="Regenerating..." onclick="return confirm('Regenerate credentials for {{ $registration->full_name }}?')">
                                        <i class="fas fa-sync"></i> Regenerate
                                    </button>
                                </form>
                                <form action="{{ route('teacher.registration.destroy-pending', $registration->id) }}" method="POST" style="display: inline;" class="js-loading-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger js-loading-button" data-loading-text="Deleting..." onclick="return confirm('Delete pending registration for {{ $registration->full_name }}?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-clipboard-list" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No pending registrations. Upload an Excel file to add students.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Students Pending Approval -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Students Pending Approval</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div style="padding: 16px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; gap: 12px;">
            <label for="selectAllStudents" style="display: flex; align-items: center; gap: 8px; font-weight: 500; color: var(--text-primary);">
                <input type="checkbox" id="selectAllStudents">
                Select all students
            </label>
            <form action="{{ route('teacher.students.approve.bulk') }}" method="POST" id="bulkApproveStudentsForm" style="display: inline;" class="js-loading-form">
                @csrf
                <button type="submit" class="btn btn-sm btn-primary js-loading-button" id="bulkApproveStudentsBtn" data-loading-text="Approving..." disabled onclick="return confirm('Approve selected student(s)?')">
                    <i class="fas fa-check-circle"></i> Approve Selected
                </button>
            </form>
        </div>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Select</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Character</th>
                        <th>HP/AP</th>
                        <th>Status</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            @if($student->status === 'pending')
                                <input type="checkbox" class="student-checkbox" name="student_ids[]" value="{{ $student->id }}" form="bulkApproveStudentsForm">
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <img src="{{ asset('images/' . ($student->profile_pic ?? 'default-pp.png')) }}" style="width: 36px; height: 36px; border-radius: 50%;">
                                <span style="font-weight: 500;">{{ $student->name }}</span>
                            </div>
                        </td>
                        <td>{{ $student->email }}</td>
                        <td>
                            @if($student->character)
                                @if($student->character === 'mage')
                                    <span class="badge" style="text-transform: capitalize; background: #6c5ce7;">{{ $student->character }}</span>
                                @elseif($student->character === 'warrior')
                                    <span class="badge" style="text-transform: capitalize; background: #e17055;">{{ $student->character }}</span>
                                @else
                                    <span class="badge" style="text-transform: capitalize; background: #00b894;">{{ $student->character }}</span>
                                @endif
                            @else
                                <span class="badge badge-secondary">Not Selected</span>
                            @endif
                        </td>
                        <td>
                            @if($student->hp > 0 || $student->ap > 0)
                                <span style="font-size: 0.85rem;">
                                    <span style="color: #e74c3c;">HP: {{ $student->hp }}</span> / 
                                    <span style="color: #3498db;">AP: {{ $student->ap }}</span>
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($student->status === 'active')
                                <span class="badge badge-success">Active</span>
                            @elseif($student->status === 'pending')
                                <span class="badge badge-warning">Pending</span>
                            @else
                                <span class="badge badge-danger">{{ ucfirst($student->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $student->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($student->status === 'pending')
                                <form action="{{ route('teacher.students.approve', $student->id) }}" method="POST" style="display: inline;" class="js-loading-form">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary js-loading-button" data-loading-text="Approving..." onclick="return confirm('Approve {{ $student->name }}?')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                </form>
                            @else
                                <span style="color: var(--text-muted);">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-muted);">
                            <i class="fas fa-users" style="font-size: 3rem; margin-bottom: 16px; display: block;"></i>
                            No students pending approval.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

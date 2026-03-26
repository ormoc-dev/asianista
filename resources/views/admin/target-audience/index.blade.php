@extends('admin.dashboard')

@section('content')

<style>
    .page-container {
        padding: 20px;
    }

    .page-header {
        margin-bottom: 20px;
    }

    .page-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 4px;
    }

    .page-title i {
        font-size: 1.5rem;
        color: #3b82f6;
    }

    .page-title h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .page-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin-left: 32px;
    }

    .action-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
    }

    .btn-primary {
        background: #3b82f6;
        color: #fff;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-outline {
        background: #fff;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-outline:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: 0.75rem;
    }

    .card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        max-width: 800px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-title i {
        color: #3b82f6;
    }

    .card-title h3 {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0;
    }

    .card-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 6px;
    }

    .form-input {
        width: 100%;
        padding: 10px 14px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: #fff;
        font-size: 0.875rem;
        color: #1f2937;
        transition: all 0.15s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-row {
        display: flex;
        gap: 12px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .form-row .form-group {
        flex: 1;
        min-width: 150px;
        margin-bottom: 0;
    }

    .select-wrapper {
        position: relative;
    }

    .select-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }

    .select-wrapper select {
        padding-left: 32px;
    }

    /* Grade View */
    .grade-view {
        background: #f9fafb;
        border-radius: 6px;
        padding: 16px;
        margin-top: 16px;
        border: 1px solid #e5e7eb;
    }

    .grade-view-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }

    .grade-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
    }

    .grade-tag {
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 4px;
        background: #dbeafe;
        color: #1d4ed8;
        text-transform: uppercase;
        margin-left: 8px;
    }

    .grade-actions {
        display: flex;
        gap: 6px;
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #6b7280;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-icon:hover {
        background: #f9fafb;
    }

    .btn-icon.edit:hover {
        border-color: #3b82f6;
        color: #3b82f6;
    }

    .btn-icon.delete:hover {
        border-color: #ef4444;
        color: #ef4444;
    }

    .divider {
        border: none;
        border-top: 1px solid #e5e7eb;
        margin: 12px 0;
    }

    .section-list {
        margin-top: 12px;
    }

    .section-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        background: #fff;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        margin-bottom: 8px;
    }

    .section-name {
        font-size: 0.875rem;
        font-weight: 500;
        color: #1f2937;
    }

    .section-actions {
        display: flex;
        gap: 6px;
    }

    .empty-message {
        text-align: center;
        padding: 20px;
        color: #9ca3af;
        font-size: 0.875rem;
    }

    /* Modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-box {
        background: #fff;
        padding: 24px;
        border-radius: 8px;
        width: 100%;
        max-width: 360px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .modal-title {
        font-size: 1rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 12px;
    }

    .modal-title.danger {
        color: #ef4444;
    }

    .modal-body {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .modal-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
        text-align: left;
    }

    .modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        margin-top: 16px;
    }

    .btn-cancel {
        padding: 8px 16px;
        border-radius: 6px;
        border: 1px solid #e5e7eb;
        background: #fff;
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
        cursor: pointer;
    }

    .btn-cancel:hover {
        background: #f9fafb;
    }

    .btn-confirm {
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        background: #3b82f6;
        font-size: 0.875rem;
        font-weight: 500;
        color: #fff;
        cursor: pointer;
    }

    .btn-confirm:hover {
        background: #2563eb;
    }

    .btn-confirm.danger {
        background: #ef4444;
    }

    .btn-confirm.danger:hover {
        background: #dc2626;
    }

    .hidden {
        display: none !important;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }

        .form-row .btn {
            width: 100%;
            justify-content: center;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
    }
</style>

<div class="page-container">
    <div class="page-header">
        <div class="page-title">
            <i class="fas fa-users-cog"></i>
            <h2>Target Audience</h2>
        </div>
        <p class="page-subtitle">Create grades and sections for organizing students</p>
    </div>

    <div class="action-bar">
        <button type="button" class="btn btn-primary" onclick="showPanel('grade')">
            <i class="fas fa-plus"></i> Add Grade
        </button>
        <button type="button" class="btn btn-primary" onclick="showPanel('section')">
            <i class="fas fa-plus"></i> Add Section
        </button>
    </div>

    <!-- List Card -->
    <div id="listCard" class="card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-list"></i>
                <h3>Grades & Sections</h3>
            </div>
        </div>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Select Grade</label>
                <div class="select-wrapper">
                    <i class="fas fa-chevron-down"></i>
                    <select id="gradeFilter" class="form-input" onchange="showGrade()">
                        <option value="" hidden>Choose a grade...</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="gradeView" class="grade-view" style="display:none;">
                <div class="grade-view-header">
                    <div>
                        <span class="grade-name" id="gradeNameDisplay"></span>
                        <span class="grade-tag">Active</span>
                    </div>
                    <div class="grade-actions">
                        <button id="editGradeButton" class="btn-icon edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form id="deleteGradeForm" method="POST" onsubmit="event.preventDefault(); openDeleteModal(this, 'Delete this grade and all its sections?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn-icon delete" title="Delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <hr class="divider">

                <div id="sectionListContainer" class="section-list"></div>
            </div>
        </div>
    </div>

    <!-- Add Grade Card -->
    <div id="addGradeCard" class="card" style="display:none; margin-top: 20px;">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-plus"></i>
                <h3>Add Grade</h3>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="showPanel('list')">
                ← Back
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.target-audience.grade.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Grade Name</label>
                        <input type="text" name="name" placeholder="e.g., Grade 11" required class="form-input">
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Section Card -->
    <div id="addSectionCard" class="card" style="display:none; margin-top: 20px;">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-plus"></i>
                <h3>Add Section</h3>
            </div>
            <button type="button" class="btn btn-outline btn-sm" onclick="showPanel('list')">
                ← Back
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.target-audience.section.store') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Grade</label>
                        <div class="select-wrapper">
                            <i class="fas fa-layer-group"></i>
                            <select name="grade_id" required class="form-input">
                                <option value="" hidden>Select Grade</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Section Name</label>
                        <input type="text" name="name" placeholder="e.g., Section A" required class="form-input">
                    </div>
                    <button class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Grade Modal -->
<div id="editGradeModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-title">Edit Grade</div>
        <form id="editGradeForm" method="POST">
            @csrf
            @method('PUT')
            <input type="text" id="editGradeName" name="name" class="form-input" required>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('editGradeModal')">Cancel</button>
                <button type="submit" class="btn-confirm">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Section Modal -->
<div id="editSectionModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-title">Edit Section</div>
        <form id="editSectionForm" method="POST">
            @csrf
            @method('PUT')
            <label class="modal-label">Section Name</label>
            <input type="text" id="editSectionName" name="name" class="form-input" required>
            <label class="modal-label" style="margin-top: 12px;">Grade</label>
            <select id="editSectionGrade" name="grade_id" class="form-input" required>
                @foreach ($grades as $grade)
                    <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                @endforeach
            </select>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('editSectionModal')">Cancel</button>
                <button type="submit" class="btn-confirm">Save</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div id="deleteConfirmModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <div class="modal-title danger">Confirm Delete</div>
        <div class="modal-body" id="deleteConfirmMessage">Are you sure you want to delete this item?</div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-confirm danger" id="deleteConfirmOk">Delete</button>
        </div>
    </div>
</div>

<script>
function showGrade() {
    const id = document.getElementById('gradeFilter').value;
    const grades = @json($grades);

    if (!id) {
        document.getElementById('gradeView').style.display = 'none';
        return;
    }

    const grade = grades.find(g => g.id == id);

    document.getElementById('gradeView').style.display = 'block';
    document.getElementById('gradeNameDisplay').innerText = grade.name;

    document.getElementById('editGradeButton')
        .setAttribute("onclick", `openEditGrade(${grade.id}, '${grade.name.replace(/'/g, "\\'")}')`);

    document.getElementById('deleteGradeForm').action =
        "{{ url('/admin/target-audience/grade') }}/" + grade.id;

    let html = "";

    if (grade.sections.length === 0) {
        html = `<p class="empty-message">No sections yet. Add one above.</p>`;
    } else {
        grade.sections.forEach(section => {
            html += `
                <div class="section-item">
                    <span class="section-name">${section.name}</span>
                    <div class="section-actions">
                        <button type="button" class="btn-icon edit" onclick="openEditSection(${section.id}, '${section.name.replace(/'/g, "\\'")}', ${grade.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="/admin/target-audience/section/${section.id}" method="POST" onsubmit="event.preventDefault(); openDeleteModal(this, 'Delete this section?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn-icon delete">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            `;
        });
    }

    document.getElementById('sectionListContainer').innerHTML = html;
}

function openEditGrade(id, name) {
    document.getElementById('editGradeName').value = name;
    document.getElementById('editGradeForm').action = '/admin/target-audience/grade/' + id;
    document.getElementById('editGradeModal').style.display = 'flex';
}

function openEditSection(id, name, grade_id) {
    document.getElementById('editSectionName').value = name;
    document.getElementById('editSectionGrade').value = grade_id;
    document.getElementById('editSectionForm').action = '/admin/target-audience/section/' + id;
    document.getElementById('editSectionModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

function showPanel(panel) {
    const listCard = document.getElementById('listCard');
    const gradeCard = document.getElementById('addGradeCard');
    const sectionCard = document.getElementById('addSectionCard');

    listCard.style.display = (panel === 'list') ? 'block' : 'none';
    gradeCard.style.display = (panel === 'grade') ? 'block' : 'none';
    sectionCard.style.display = (panel === 'section') ? 'block' : 'none';

    let target = null;
    if (panel === 'list') target = listCard;
    if (panel === 'grade') target = gradeCard;
    if (panel === 'section') target = sectionCard;

    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

let deleteFormTarget = null;

function openDeleteModal(form, message) {
    deleteFormTarget = form;
    document.getElementById("deleteConfirmMessage").innerText = message;
    document.getElementById("deleteConfirmModal").style.display = "flex";
}

function closeDeleteModal() {
    document.getElementById("deleteConfirmModal").style.display = "none";
    deleteFormTarget = null;
}

document.addEventListener('DOMContentLoaded', function () {
    showPanel('list');

    const deleteOk = document.getElementById("deleteConfirmOk");
    if (deleteOk) {
        deleteOk.addEventListener("click", function () {
            if (deleteFormTarget) {
                deleteFormTarget.submit();
            }
            closeDeleteModal();
        });
    }
});
</script>

@endsection

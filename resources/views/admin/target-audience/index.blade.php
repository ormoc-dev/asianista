@extends('admin.dashboard')

@section('content')

<div class="ta-header">
    <div>
        <div class="ta-pill">
            <i class="fas fa-bullseye"></i>
            Targeting Hub
        </div>
        <h2>
            <i class="fas fa-users-cog"></i>
            Manage Target Audience
        </h2>
        <p>
            Create grades and sections that will serve as parties for your quests and learning adventures.
        </p>
    </div>
</div>

{{-- ACTION BUTTONS ABOVE LIST --}}
<div class="ta-actions">
    <button type="button" class="btn-primary" onclick="showPanel('grade')">
        <i class="fas fa-layer-group"></i>
        Add Grade
    </button>
    <button type="button" class="btn-primary" onclick="showPanel('section')">
        <i class="fas fa-users"></i>
        Add Section
    </button>
</div>

<div class="target-container">

    {{-- CARD: Available Grades & Sections (shown first) --}}
    <div id="listCard" class="form-container form-card">
        <div class="form-header-icon">
            <i class="fas fa-map"></i>
        </div>
        <h3>Available Grades &amp; Sections</h3>
        <p class="form-subtitle">
            Select a grade to view, edit, or remove its sections.
        </p>
        <hr>

        <!-- Filter Dropdown -->
        <div class="form-group">
            <label class="field-label">Choose Grade</label>
            <div class="select-with-icon">
                <i class="fas fa-crosshairs"></i>
                <select id="gradeFilter" class="input select-clean" onchange="showGrade()">
                    <option value="" hidden>Select Grade</option>
                    @foreach ($grades as $grade)
                        <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Display Panel -->
        <div id="gradeView" class="grade-view" style="display:none; margin-top:20px;">

            <!-- HEADER: Grade Title + Buttons -->
            <div class="grade-header-view">
                <div class="grade-title-wrap">
                    <span class="grade-tag">Active Grade</span>
                    <h4 id="gradeNameDisplay"></h4>
                </div>

                <div class="grade-action-btns">
                    <button id="editGradeButton" class="btn-edit-small" title="Edit grade">✏️</button>

                    <form id="deleteGradeForm" method="POST"
                          onsubmit="event.preventDefault(); openDeleteModal(this, 'Delete this grade and all its sections?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn-delete-small" title="Delete grade">🗑️</button>
                    </form>
                </div>
            </div>

            <hr>

            <!-- Sections List -->
            <div id="sectionListContainer" class="section-list"></div>

        </div>
    </div>

    {{-- CARD: Add Grade (hidden initially, switched via showPanel) --}}
    <div id="addGradeCard" class="form-container form-card" style="display:none;">
        <div class="form-header-icon">
            <i class="fas fa-layer-group"></i>
        </div>

        <div class="card-header-row">
            <div>
                <h3>Add Grade</h3>
                <p class="form-subtitle">Create a new grade level that can receive quests and lessons.</p>
            </div>
            <button type="button" class="btn-ghost" onclick="showPanel('list')">
                ← Back to Available Grades
            </button>
        </div>

        <hr>

        <form action="{{ route('admin.target-audience.grade.store') }}" method="POST">
            @csrf

            <div class="form-row">
                <div class="form-group flex-1">
                    <label class="field-label">
                        Grade Name
                        <span class="badge-soft">Required</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        placeholder="e.g., Grade 11, Grade 7"
                        required
                        class="input">
                </div>

                <button class="btn-primary btn-primary-inline">
                    <i class="fas fa-plus-circle"></i>
                    Add Grade
                </button>
            </div>
        </form>
    </div>

    {{-- CARD: Add Section (hidden initially, switched via showPanel) --}}
    <div id="addSectionCard" class="form-container form-card" style="display:none;">
        <div class="form-header-icon">
            <i class="fas fa-users"></i>
        </div>

        <div class="card-header-row">
            <div>
                <h3>Add Section</h3>
                <p class="form-subtitle">Attach a new section to an existing grade.</p>
            </div>
            <button type="button" class="btn-ghost" onclick="showPanel('list')">
                ← Back to Available Grades
            </button>
        </div>

        <hr>

        <form action="{{ route('admin.target-audience.section.store') }}" method="POST">
            @csrf

            <div class="form-row stacked-mobile">
                <div class="form-group flex-1">
                    <label class="field-label">Grade</label>
                    <div class="select-with-icon">
                        <i class="fas fa-layer-group"></i>
                        <select name="grade_id" required class="input select-clean">
                            <option value="" hidden>Select Grade</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group flex-1">
                    <label class="field-label">
                        Section Name
                        <span class="badge-soft">Required</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        placeholder="e.g., Section A, Phoenix"
                        required
                        class="input">
                </div>

                <button class="btn-primary btn-primary-inline">
                    <i class="fas fa-plus-circle"></i>
                    Add Section
                </button>
            </div>
        </form>
    </div>

</div>


{{-- ===========================
        EDIT GRADE MODAL
=========================== --}}
<div id="editGradeModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3>Edit Grade</h3>

        <form id="editGradeForm" method="POST">
            @csrf
            @method('PUT')

            <input type="text" id="editGradeName" name="name" class="input" required>

            <div class="modal-buttons">
                <button type="button" class="btn-cancel" onclick="closeModal('editGradeModal')">Cancel</button>
                <button type="submit" class="btn-ok">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- ===========================
        EDIT SECTION MODAL
=========================== --}}
<div id="editSectionModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3>Edit Section</h3>

        <form id="editSectionForm" method="POST">
            @csrf
            @method('PUT')

            <label class="modal-label">Section Name</label>
            <input type="text" id="editSectionName" name="name" class="input" required>

            <label class="modal-label" style="margin-top:10px;">Grade</label>
            <select id="editSectionGrade" name="grade_id" class="input" required>
                @foreach ($grades as $grade)
                    <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                @endforeach
            </select>

            <div class="modal-buttons">
                <button type="button" class="btn-cancel" onclick="closeModal('editSectionModal')">Cancel</button>
                <button type="submit" class="btn-ok">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- ===========================
        DELETE CONFIRM MODAL
=========================== --}}
<div id="deleteConfirmModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <h3 class="text-danger">Confirm Delete</h3>
        <p id="deleteConfirmMessage">Are you sure you want to delete this item?</p>

        <div class="modal-buttons">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button type="button" class="btn-ok" id="deleteConfirmOk">Delete</button>
        </div>
    </div>
</div>

{{-- ===========================
            SCRIPT
=========================== --}}
<script>
function showGrade() {
    const id = document.getElementById('gradeFilter').value;
    const grades = @json($grades);

    if (!id) {
        document.getElementById('gradeView').style.display = 'none';
        return;
    }

    const grade = grades.find(g => g.id == id);

    // Show container
    document.getElementById('gradeView').style.display = 'block';

    // Update grade name
    document.getElementById('gradeNameDisplay').innerText = grade.name;

    // Edit button
    document.getElementById('editGradeButton')
        .setAttribute("onclick", `openEditGrade(${grade.id}, '${grade.name.replace(/'/g, "\\'")}')`);

    // Delete form action
    document.getElementById('deleteGradeForm').action =
        "{{ url('/admin/target-audience/grade') }}/" + grade.id;

    // Sections
    let html = "";

    if (grade.sections.length === 0) {
        html = `<p class="no-section">No sections under this grade yet. Add one using the button above.</p>`;
    } else {
        grade.sections.forEach(section => {
            html += `
                <div class="section-chip">
                    <div class="section-main">
                        <span class="section-name">${section.name}</span>
                    </div>
                    <div class="chip-actions">
                        <button
                            type="button"
                            class="chip-edit"
                            onclick="openEditSection(${section.id}, '${section.name.replace(/'/g, "\\'")}', ${grade.id})">✏️</button>

                        <form action="/admin/target-audience/section/${section.id}"
                              method="POST"
                              onsubmit="event.preventDefault(); openDeleteModal(this, 'Delete this section?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="chip-delete">🗑️</button>
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

// Panel switcher: list / add grade / add section
function showPanel(panel) {
    const listCard = document.getElementById('listCard');
    const gradeCard = document.getElementById('addGradeCard');
    const sectionCard = document.getElementById('addSectionCard');

    if (!listCard || !gradeCard || !sectionCard) return;

    listCard.style.display    = (panel === 'list')    ? 'block' : 'none';
    gradeCard.style.display   = (panel === 'grade')   ? 'block' : 'none';
    sectionCard.style.display = (panel === 'section') ? 'block' : 'none';

    let target = null;
    if (panel === 'list')    target = listCard;
    if (panel === 'grade')   target = gradeCard;
    if (panel === 'section') target = sectionCard;

    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// ---- Custom delete modal logic ----
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

// Default state: show list and bind delete ok button
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

{{-- ===========================
              CSS
=========================== --}}
<style>
    :root {
        --primary: #002366;
        --secondary: #262840;
        --light-bg: #BFC5DB;
        --card-bg: #F1F1E0;
        --accent: #ffd43b;
        --accent-dark: #f5c400;
        --text-dark: #0b1020;
        --text-muted: #64748b;
    }

    .ta-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 16px;
    }

    .ta-header h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ta-header p {
        margin-top: 6px;
        font-size: 0.9rem;
        color: var(--text-muted);
        max-width: 520px;
    }

    .ta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(0,35,102,0.08);
        color: var(--primary);
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .ta-pill i {
        color: var(--accent-dark);
    }

    .ta-actions {
        display: flex;
        gap: 10px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .target-container {
        max-width: 980px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-container.form-card {
        background: rgba(241,241,224,0.95);
        border-radius: 18px;
        padding: 22px 22px 24px;
        box-shadow: 0 10px 24px rgba(15,23,42,0.22);
        border: 1px solid rgba(255,255,255,0.8);
        position: relative;
        overflow: hidden;
    }

    .form-card::before {
        content: "";
        position: absolute;
        right: -60px;
        top: -60px;
        width: 160px;
        height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,212,59,0.35), transparent 60%);
        opacity: 0.8;
    }

    .form-card > * {
        position: relative;
        z-index: 1;
    }

    .form-header-icon {
        width: 44px;
        height: 44px;
        border-radius: 16px;
        background: rgba(0,35,102,0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 1.2rem;
        margin-bottom: 8px;
    }

    .form-container h3 {
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 4px;
    }

    .form-subtitle {
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-bottom: 10px;
    }

    hr {
        border: none;
        border-top: 2px solid rgba(226,232,240,0.9);
        margin: 10px 0 20px;
    }

    .form-row {
        display: flex;
        gap: 10px;
        align-items: flex-end;
        flex-wrap: wrap;
    }

    .form-row.stacked-mobile {
        align-items: stretch;
    }

    .flex-1 {
        flex: 1;
        min-width: 160px;
    }

    .field-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        background: rgba(148,163,184,0.15);
        color: #475569;
    }

    .input {
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        width: 100%;
        font-size: 0.9rem;
        background: rgba(255,255,255,0.9);
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease, background 0.15s ease;
    }

    .input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 1px rgba(0,35,102,0.3);
        background: #fff;
    }

    .select-with-icon {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 3px 6px 3px 8px;
        border-radius: 999px;
        background: rgba(255,255,255,0.7);
        border: 1px solid #d1d5db;
    }

    .select-with-icon i {
        color: var(--primary);
        font-size: 0.9rem;
    }

    .select-clean {
        border: none;
        background: transparent;
        padding-left: 0;
        box-shadow: none;
    }

    .select-clean:focus {
        border: none;
        box-shadow: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        padding: 10px 20px;
        border-radius: 999px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: 0.18s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 7px 18px rgba(0,0,0,0.25);
        white-space: nowrap;
    }

    .btn-primary-inline {
        align-self: flex-end;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 11px 24px rgba(0,0,0,0.35);
    }

    .btn-edit-small {
        background-color: #3b82f6;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 999px;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: 500;
        transition: background 0.15s ease, transform 0.1s ease;
    }

    .btn-edit-small:hover {
        background:#2563eb;
        transform: translateY(-1px);
    }

    .btn-delete-small {
        background:#ef4444;
        color:white;
        border:none;
        border-radius:999px;
        cursor:pointer;
        padding:5px 10px;
        font-size: 0.75rem;
        font-weight: 500;
        transition: background 0.15s ease, transform 0.1s ease;
    }

    .btn-delete-small:hover {
        background:#dc2626;
        transform: translateY(-1px);
    }

    .grade-view {
        background: rgba(15,23,42,0.9);
        padding: 16px 16px 18px;
        border-radius: 16px;
        border: 1px solid rgba(148,163,184,0.6);
        color: #e5edff;
        box-shadow: 0 10px 24px rgba(0,0,0,0.4);
    }

    .grade-header-view {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .grade-title-wrap h4 {
        margin: 0;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .grade-tag {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        border-radius: 999px;
        background: rgba(148,163,184,0.18);
        color: #e5edff;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .grade-action-btns {
        display: flex;
        gap: 8px;
        flex-shrink: 0;
    }

    .section-list {
        margin-top: 10px;
    }

    .section-chip {
        background: rgba(15,23,42,0.85);
        padding: 10px 12px;
        border-radius: 10px;
        border: 1px solid rgba(148,163,184,0.7);
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
    }

    .section-main {
        display: flex;
        flex-direction: column;
    }

    .section-name {
        font-weight: 600;
        color: #f9fafb;
    }

    .chip-actions {
        display: flex;
        gap: 6px;
        align-items: center;
    }

    .chip-edit,
    .chip-delete {
        background: none;
        border: none;
        cursor: pointer;
        padding: 3px 6px;
        font-size: 0.9rem;
        border-radius: 999px;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .chip-edit:hover {
        background: rgba(37,99,235,0.15);
        color: #60a5fa;
    }

    .chip-delete:hover {
        background: rgba(239,68,68,0.18);
        color: #fecaca;
    }

    .no-section {
        color: #cbd5f5;
        font-style: italic;
        font-size: 0.9rem;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
    }

    .modal-box {
        background: #0b1020;
        padding: 22px 24px;
        border-radius: 18px;
        width: 340px;
        text-align: center;
        box-shadow: 0 14px 30px rgba(0,0,0,0.65);
        color: #e5edff;
        animation: popUp 0.25s ease;
    }

    .modal-box h3 {
        margin-bottom: 12px;
        font-size: 1.2rem;
        color: var(--accent);
    }

    .modal-label {
        display: block;
        text-align: left;
        margin-bottom: 4px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #e5edff;
    }

    .modal-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-cancel {
        background-color: rgba(148,163,184,0.2);
        color: #e5edff;
        padding: 8px 16px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 500;
        transition: 0.2s;
    }

    .btn-cancel:hover {
        background-color: rgba(148,163,184,0.35);
    }

    .btn-ok {
        background: linear-gradient(135deg, var(--accent), var(--accent-dark));
        color: #0b1020;
        padding: 8px 16px;
        border: none;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-ok:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(0,0,0,0.5);
    }

    .text-danger {
        color: #ff6b6b !important;
    }

    .card-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
    }

    .btn-ghost {
        background: transparent;
        border-radius: 999px;
        border: 1px solid rgba(15,23,42,0.25);
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--primary);
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.15s ease;
    }

    .btn-ghost:hover {
        background: rgba(15,23,42,0.06);
        border-color: rgba(15,23,42,0.4);
    }

    @keyframes popUp {
        from { transform: scale(0.86); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    @media (max-width: 768px) {
        .ta-header {
            flex-direction: column;
        }

        .ta-header p {
            max-width: 100%;
        }

        .ta-actions {
            flex-direction: column;
        }

        .btn-primary {
            width: 100%;
            justify-content: center;
        }

        .form-row {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-primary-inline {
            width: 100%;
            justify-content: center;
        }

        .card-header-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

@endsection

/**
 * Character-based Registration System - JavaScript Functions
 */

function registrationRefreshSectionOptions(preserveSectionId) {
    var gSelect = document.getElementById('regGradeSelect');
    var sSelect = document.getElementById('regSectionSelect');
    if (!gSelect || !sSelect || !window.__registrationGrades || !window.__registrationGrades.length) return;
    var gid = gSelect.value;
    var prev = preserveSectionId != null && preserveSectionId !== '' ? String(preserveSectionId) : sSelect.value;
    sSelect.innerHTML = '<option value="">Select section</option>';
    window.__registrationGrades.forEach(function (g) {
        if (String(g.id) !== String(gid)) return;
        (g.sections || []).forEach(function (s) {
            var o = document.createElement('option');
            o.value = s.id;
            o.textContent = s.name;
            sSelect.appendChild(o);
        });
    });
    if (prev) sSelect.value = prev;
}
window.registrationRefreshSectionOptions = registrationRefreshSectionOptions;

// Character selection for new registration flow
function selectCharacterClass(character) {
    // Remove selected class from all options
    document.querySelectorAll('.character-option').forEach(el => {
        el.style.border = '2px solid transparent';
        el.style.background = 'transparent';
    });

    // Add selected styling to chosen character
    const selectedEl = document.getElementById('char-' + character);
    if (selectedEl) {
        selectedEl.style.border = '2px solid var(--primary)';
        selectedEl.style.background = 'rgba(67, 97, 238, 0.1)';
    }

    // Set hidden input value
    const characterInput = document.getElementById('characterInput');
    if (characterInput) {
        characterInput.value = character;
    }

    // Hide error message if shown
    const errorEl = document.getElementById('characterError');
    if (errorEl) {
        errorEl.style.display = 'none';
    }
}

// Validate student code via AJAX
async function validateStudentCode() {
    const codeInput = document.getElementById('studentCodeInput');
    const messageEl = document.getElementById('codeValidationMessage');
    const code = codeInput.value.trim().toUpperCase();

    if (!code) {
        messageEl.innerHTML = '<span style="color: #e74c3c;">Please enter a student code.</span>';
        return;
    }

    messageEl.innerHTML = '<span style="color: #3498db;"><i class="fas fa-spinner fa-spin"></i> Validating...</span>';

    try {
        const validateCodeUrl = document.querySelector('meta[name="validate-code-url"]')?.getAttribute('content') || '/register/validate-code';
        const response = await fetch(validateCodeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                               document.querySelector('input[name="_token"]')?.value
            },
            body: JSON.stringify({ student_code: code })
        });

        const data = await response.json();

        if (data.success) {
            messageEl.innerHTML = '<span style="color: #27ae60;"><i class="fas fa-check"></i> Code validated!</span>';

            // Populate student info
            document.getElementById('displayStudentName').value = data.student.full_name;
            document.getElementById('displayUsername').value = data.student.username;
            document.getElementById('studentCodeHidden').value = code;
            const gSel = document.getElementById('regGradeSelect');
            const sSel = document.getElementById('regSectionSelect');
            if (gSel && data.student.grade_id) {
                gSel.value = String(data.student.grade_id);
                registrationRefreshSectionOptions(data.student.section_id);
            } else if (gSel) {
                gSel.value = '';
                if (sSel) sSel.innerHTML = '<option value="">Select section</option>';
            }

            // Show registration form after a short delay
            setTimeout(() => {
                showStudentRegistrationForm();
            }, 500);
        } else {
            messageEl.innerHTML = '<span style="color: #e74c3c;"><i class="fas fa-times"></i> ' + (data.message || 'Invalid code') + '</span>';
        }
    } catch (error) {
        messageEl.innerHTML = '<span style="color: #e74c3c;"><i class="fas fa-times"></i> Error validating code. Please try again.</span>';
        console.error('Validation error:', error);
    }
}

// Show student registration form
function showStudentRegistrationForm() {
    const codeForm = document.getElementById('studentCodeForm');
    const registerForm = document.getElementById('studentRegisterForm');

    if (codeForm) codeForm.classList.add('hidden');
    if (registerForm) registerForm.classList.remove('hidden');
}

// Go back to code entry
function backToCodeEntry() {
    const codeForm = document.getElementById('studentCodeForm');
    const registerForm = document.getElementById('studentRegisterForm');

    if (codeForm) codeForm.classList.remove('hidden');
    if (registerForm) registerForm.classList.add('hidden');

    // Clear inputs
    const codeInput = document.getElementById('studentCodeInput');
    const messageEl = document.getElementById('codeValidationMessage');
    if (codeInput) codeInput.value = '';
    if (messageEl) messageEl.innerHTML = '';
    const gSel = document.getElementById('regGradeSelect');
    const sSel = document.getElementById('regSectionSelect');
    if (gSel) gSel.value = '';
    if (sSel) sSel.innerHTML = '<option value="">Select section</option>';
}

// Show student code entry form (called when student role is selected)
function showStudentCodeForm() {
    const roleForm = document.getElementById('roleSelectionForm');
    const codeForm = document.getElementById('studentCodeForm');

    if (roleForm) roleForm.classList.add('hidden');
    if (codeForm) codeForm.classList.remove('hidden');
}

// Legacy function - kept for compatibility
function selectRole(role) {
    if (role === 'student') {
        showStudentCodeForm();
    } else if (role === 'teacher') {
        // Handle teacher registration
        const roleForm = document.getElementById('roleSelectionForm');
        const teacherForm = document.getElementById('teacherRegisterForm');

        if (roleForm) roleForm.classList.add('hidden');
        if (teacherForm) teacherForm.classList.remove('hidden');
    }
}

// Form validation before submit
function validateStudentRegistrationForm() {
    const characterInput = document.getElementById('characterInput');
    const errorEl = document.getElementById('characterError');

    if (!characterInput || !characterInput.value) {
        if (errorEl) errorEl.style.display = 'block';
        return false;
    }

    return true;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    var rg = document.getElementById('regGradeSelect');
    if (rg) {
        rg.addEventListener('change', function () { registrationRefreshSectionOptions(null); });
    }

    // Add form validation to student registration form
    const form = document.getElementById('studentRegisterFormElement');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateStudentRegistrationForm()) {
                e.preventDefault();
            }
        });
    }

    // Check if we need to show registration form (e.g., after validation error)
    const urlParams = new URLSearchParams(window.location.search);
    const showForm = urlParams.get('show_form');
    const hasErrors = document.querySelectorAll('.error').length > 0;

    if (showForm === 'register' && hasErrors) {
        // If there are errors, we might need to restore the form state
        // This is handled by the server-side rendering
    }
});

// Export functions for global access
window.selectCharacterClass = selectCharacterClass;
window.validateStudentCode = validateStudentCode;
window.showStudentRegistrationForm = showStudentRegistrationForm;
window.backToCodeEntry = backToCodeEntry;
window.showStudentCodeForm = showStudentCodeForm;
window.selectRole = selectRole;

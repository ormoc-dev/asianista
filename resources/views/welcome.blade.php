<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Level Up Asianista - Portal</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- FAVICON IN BROWSER TAB -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Route URLs for JavaScript -->
    <meta name="validate-code-url" content="{{ route('register.validate-code') }}">

</head>
@php
  $initialForm = session('show_form') ?? 'login';
  $skipSplash = session('error')
             || session('status')
             || session('success')
             || ($errors ?? collect())->any();
  $registrationGrades = $registrationGrades ?? collect();
  $registrationCharacterSkills = [];
  foreach (\App\Models\User::CHARACTER_CLASSES as $key => $data) {
      $registrationCharacterSkills[$key] = [
          'label' => $data['name'] ?? ucfirst($key),
          'abilities' => collect($data['abilities'] ?? [])
              ->map(fn ($desc, $name) => ['name' => $name, 'desc' => $desc])
              ->values()
              ->all(),
      ];
  }
@endphp
<body data-skip-splash="{{ $skipSplash ? '1' : '0' }}"
      data-initial-form="{{ $initialForm }}">

  <!-- SPLASH SCREEN -->
  <div id="splash" class="splash-screen">
    <div class="logo-circle">
      <img src="{{ asset('images/LEVEL UP ASIANISTA LOGO.png') }}" alt="Logo">
    </div>
  </div>

  <!-- MAIN CONTAINER -->
  <div id="main-container" class="container hidden">

    <!-- LOGO -->
    <div class="logo-circle top-logo">
      <img src="{{ asset('images/LEVEL UP ASIANISTA LOGO.png') }}" alt="Level Up Asianista">
    </div>

   <!-- LOGIN FORM -->
<div id="loginForm" class="card">
  <h2>Log In</h2>

  <div id="loginErrorContainer">
    @if(session('show_form') === 'login' && session('error'))
      <p class="error">{{ session('error') }}</p>
    @endif
  </div>

  <form id="loginFormElement" method="POST" action="{{ route('login') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit" class="btn" id="loginSubmitBtn">LOG IN</button>
  </form>

  <div class="links">
    <a id="showForgot" href="#">Forgot Password?</a>
    <a id="showRegister" href="#">Create Account</a>
  </div>
</div>

    <!-- ROLE SELECTION FORM -->
    <div id="roleSelectionForm" class="card hidden">
      <h2>Select Your Role</h2>
      <div class="role-options">
        <a href="#" onclick="selectRole('student')">
          <div class="role-option">
            <img src="{{ asset('images/student_icon.png') }}" alt="Student">
            <p>Student</p>
          </div>
        </a>
        <a href="#" onclick="selectRole('teacher')">
          <div class="role-option">
            <img src="{{ asset('images/teacher_icon.png') }}" alt="Teacher">
            <p>Teacher</p>
          </div>
        </a>
      </div>
    </div>

    <!-- Register Form for Teacher -->
    <div id="teacherRegisterForm" class="card hidden">
      <h2>Teacher Sign Up</h2>
      @if (session('show_form') === 'teacher_register' && session('success'))
        <p class="success">{{ session('success') }}</p>
      @endif
      <div id="teacherErrorContainer">
        @if (session('show_form') === 'teacher_register' && session('error'))
          <p class="error">{{ session('error') }}</p>
        @endif
      </div>
      <form id="teacherRegisterFormElement" method="POST" action="{{ route('register.teacher') }}" onsubmit="handleTeacherSubmit(event);">
        @csrf
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Re-type Password" required>

        <input type="hidden" name="role" value="teacher">
        <input type="hidden" name="avatar" value="default-pp.png">

        <button type="submit" id="teacherSubmitBtn" class="btn">SIGN UP</button>
      </form>
      <p class="toggle-text">
        Already have an account? <span id="showLogin">Log In</span>
      </p>
    </div>

    <!-- STUDENT CODE ENTRY FORM -->
    <div id="studentCodeForm" class="card reg-code-card reg-glass hidden">
      <div class="rcc-icon">🗝️</div>
      <h2 class="rcc-title">Enter Your <span class="text-gold">Student Code</span></h2>
      <p class="rcc-subtitle">Your teacher has given you a unique code to start your adventure</p>

      <div id="studentCodeErrorContainer">
        @if (session('show_form') === 'register' && $errors->any())
          <div class="reg-alert reg-alert-error">{{ $errors->first() }}</div>
        @endif
      </div>

      <form id="studentCodeFormElement" onsubmit="event.preventDefault(); validateStudentCode();">
        @csrf
        <div class="stu-code-field">
          <input type="text" id="studentCodeInput" name="student_code"
            placeholder="STU-XXXXXX" required autocomplete="off" maxlength="10">
        </div>
        <div id="codeValidationMessage" class="val-msg"></div>
        <button type="submit" id="studentValidateCodeBtn" class="btn btn-quest">
          <span>Validate Code</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>

      <p class="rcc-footer">Already have an account? <span id="showLogin" class="text-gold pointer">Log In</span></p>
    </div>

    <!-- STUDENT REGISTRATION FORM -->
    <div id="studentRegisterForm" class="hero-wizard-card reg-hero-shell hidden">

      <!-- LEFT: Live Character Preview Panel -->
      <div class="hero-preview-panel">
        <div class="hero-char-display">
          <img id="charPreviewImg" src="{{ asset('images/default-pp.png') }}" alt="Hero" style="display:none;">
          <div id="noCharPlaceholder" class="hero-char-placeholder">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.35"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0 1 12 0v2"/></svg>
            <p>Choose your class<br>&amp; gender to preview</p>
          </div>
        </div>
        <p class="hero-class-label" id="previewClassName"></p>
        <div id="previewSkillsWrap" class="hero-skills-preview" style="display:none;">
          <p class="hero-skills-heading">Skills</p>
          <ul id="previewSkillsList" class="hero-skills-list"></ul>
        </div>
        <div class="hero-student-badge">
          <p class="hsb-name" id="previewStudentName">-</p>
          <p class="hsb-user" id="previewStudentUser"></p>
        </div>
      </div>

      <!-- RIGHT: Form Panel -->
      <div class="hero-form-panel">

        <!-- Progress -->
        <div class="wizard-steps">
          <div class="ws-step done" id="wsStepCode"><div class="ws-dot">✓</div><span>Code</span></div>
          <div class="ws-line active" id="wsLineCodeHero"></div>
          <div class="ws-step active" id="wsStepHero"><div class="ws-dot">2</div><span>Hero</span></div>
          <div class="ws-line" id="wsLineHeroDone"></div>
          <div class="ws-step" id="wsStepDone"><div class="ws-dot">3</div><span>Done</span></div>
        </div>

        <h2 class="wizard-title">Build Your <span class="text-gold">Hero</span></h2>

        <div id="studentSuccessContainer">
          @if (session('show_form') === 'register' && session('success'))
            <div class="reg-alert reg-alert-success">{{ session('success') }}</div>
          @endif
        </div>
        <div id="studentSuccessEffects" class="hero-success-badge" aria-live="polite">
          <div class="title">Registration Complete! Your hero is ready.</div>
          <div class="desc">Next step: wait for teacher approval, then log in and begin your quests.</div>
        </div>
        <div id="studentErrorContainer">
          @if (session('show_form') === 'register' && session('error'))
            <div class="reg-alert reg-alert-error">{{ session('error') }}</div>
          @endif
        </div>

        <form id="studentRegisterFormElement" method="POST" action="{{ route('register.student') }}">
          @csrf
          <input type="hidden" name="student_code" id="studentCodeHidden">
          <input type="hidden" id="displayStudentName">
          <input type="hidden" id="displayUsername">

          <!-- Class -->
          <div class="wiz-section">
            <p class="wiz-label">⚔️ Choose Your Class</p>
            <div class="class-chips">
              @foreach ($registrationCharacterSkills as $classKey => $classMeta)
                @php
                  $abilities = $classMeta['abilities'] ?? [];
                  $skillTeaser = $abilities[0]['name'] ?? '';
                  $moreSkills = count($abilities) > 1 ? ' +' . (count($abilities) - 1) : '';
                @endphp
                <div class="class-chip" id="char-{{ $classKey }}" onclick="selectCharacterClass('{{ $classKey }}')">
                  <img src="{{ asset('images/' . $classKey . '.png') }}" alt="{{ $classMeta['label'] }}">
                  <span>{{ $classMeta['label'] }}</span>
                  @if ($skillTeaser !== '')
                    <small>{{ $skillTeaser }}{{ $moreSkills }}</small>
                  @endif
                </div>
              @endforeach
            </div>
            <input type="hidden" name="character" id="characterInput">
            <p id="characterError" class="wiz-field-err" style="display:none;">Please select a class</p>
          </div>

          <!-- Gender -->
          <div class="wiz-section">
            <p class="wiz-label">👤 Select Gender</p>
            <div class="gender-toggle-btns">
              <button type="button" class="gtoggle-btn" id="gbtn-male" onclick="selectGenderChoice('male')">
                <span class="gtoggle-icon">👨</span> Male
              </button>
              <button type="button" class="gtoggle-btn" id="gbtn-female" onclick="selectGenderChoice('female')">
                <span class="gtoggle-icon">👩</span> Female
              </button>
            </div>
            <input type="hidden" name="gender" id="genderInput">
            <p id="genderError" class="wiz-field-err" style="display:none;">Please select a gender</p>
          </div>

          <!-- Grade & section (from school roster) -->
          <div class="wiz-section">
            <p class="wiz-label">Grade &amp; section</p>
            @if($registrationGrades->isEmpty())
              <p class="wiz-field-err" style="display:block;">Grades and sections are not set up yet. Please contact your administrator.</p>
            @else
              <select name="grade_id" id="regGradeSelect" class="reg-select" required>
                <option value="">Select grade</option>
                @foreach($registrationGrades as $g)
                  <option value="{{ $g->id }}" {{ old('grade_id') == $g->id ? 'selected' : '' }}>{{ preg_replace('/^\s*id\)?>\s*/i', '', $g->name) }}</option>
                @endforeach
              </select>
              <select name="section_id" id="regSectionSelect" class="reg-select reg-select--follow" required>
                <option value="">Select section</option>
                @if(old('grade_id'))
                  @php $oldGrade = $registrationGrades->firstWhere('id', (int) old('grade_id')); @endphp
                  @if($oldGrade)
                    @foreach($oldGrade->sections as $s)
                      <option value="{{ $s->id }}" {{ old('section_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                  @endif
                @endif
              </select>
              <p class="wiz-hint">Choose the grade and section you belong to (managed by your school in the system).</p>
            @endif
            @error('grade_id')
              <p class="wiz-field-err" style="display:block;">{{ $message }}</p>
            @enderror
            @error('section_id')
              <p class="wiz-field-err" style="display:block;">{{ $message }}</p>
            @enderror
          </div>

          <!-- Password -->
          <div class="wiz-section">
            <p class="wiz-label">🔐 Password</p>
            <input type="password" name="default_password" placeholder="Default Password (from teacher)" required>
            <input type="password" name="new_password" placeholder="New Password (optional)">
            <input type="password" name="new_password_confirmation" placeholder="Confirm New Password">
            <p class="wiz-hint">Leave new password blank to keep your default</p>
          </div>

          <button type="submit" class="btn btn-quest" id="studentCompleteBtn">
            <span>Start My Journey</span>
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
          </button>
        </form>

        <p class="wiz-back"><span onclick="backToCodeEntry()" class="wiz-back-link">← Back to Code Entry</span></p>
      </div>
    </div>

    {{-- Legacy Register Form REMOVED - Using new student code flow instead --}}

<!-- FORGOT PASSWORD FORM -->
<div id="forgotForm" class="card hidden">
  <h2>Forgot Password?</h2>
  <p class="subtitle">We just need your registered email address to reset your password</p>

  <div id="forgotStatusContainer">
    @if (session('show_form') === 'forgot' && session('status'))
      <p class="success">{{ session('status') }}</p>
    @endif
  </div>

  <div id="forgotErrorContainer">
    @if (session('show_form') === 'forgot' && $errors->has('email'))
      <p class="error">{{ $errors->first('email') }}</p>
    @endif
  </div>

  <form id="forgotFormElement" method="POST" action="{{ route('password.email') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <button type="submit" class="btn" id="forgotSubmitBtn">Continue</button>
  </form>

  <a id="backToLoginFromForgot" class="skip-link" href="#">Back to Login</a>
</div>

  </div>

  <script id="registration-grades-json" type="application/json">{!! json_encode($registrationGrades, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
  <script id="registration-character-skills-json" type="application/json">{!! json_encode($registrationCharacterSkills, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
  <script>
    window.__registrationGrades = JSON.parse(document.getElementById('registration-grades-json')?.textContent || '[]');
    window.__registrationCharacterSkills = JSON.parse(document.getElementById('registration-character-skills-json')?.textContent || '{}');
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
    document.addEventListener('DOMContentLoaded', function () {
      var rg = document.getElementById('regGradeSelect');
      if (rg) {
        rg.addEventListener('change', function () { registrationRefreshSectionOptions(null); });
      }
      // Ensure buttons are never stuck in loading state after refresh/back navigation.
      setStudentValidateCodeLoading(false);
      setStudentCompleteLoading(false);
      setTeacherSubmitLoading(false);

      // Debug aid: log any server-side flashed registration errors after redirect.
      const flashedStudentErrors = Array.from(document.querySelectorAll('#studentErrorContainer .reg-alert-error, #studentErrorContainer .error'))
        .map((el) => (el.textContent || '').trim())
        .filter(Boolean);
      if (flashedStudentErrors.length) {
        console.error('[Student Submit] Server returned errors after submit:', flashedStudentErrors);
      }

      // Registration success effect after POST/redirect.
      const flashedSuccess = document.querySelector('#studentSuccessContainer .reg-alert-success');
      const successCard = document.getElementById('studentRegisterForm');
      if (flashedSuccess && successCard) {
        successCard.classList.add('success-fx');
        setTimeout(() => successCard.classList.remove('success-fx'), 950);

        const successFx = document.getElementById('studentSuccessEffects');
        if (successFx) successFx.classList.add('show');

        const stepHero = document.getElementById('wsStepHero');
        const lineHeroDone = document.getElementById('wsLineHeroDone');
        const stepDone = document.getElementById('wsStepDone');
        if (stepHero) stepHero.classList.add('done');
        if (lineHeroDone) lineHeroDone.classList.add('active', 'done-now');
        if (stepDone) {
          stepDone.classList.remove('active');
          stepDone.classList.add('done', 'done-now');
          const doneDot = stepDone.querySelector('.ws-dot');
          if (doneDot) doneDot.textContent = '✓';
        }

        const completeBtn = document.getElementById('studentCompleteBtn');
        if (completeBtn) {
          completeBtn.disabled = true;
          completeBtn.innerHTML = '<span>Journey Started</span>';
        }
      }
    });

    // --- DOM references
    const loginForm = document.getElementById('loginForm');
    const roleSelectionForm = document.getElementById('roleSelectionForm');
    const studentCodeForm = document.getElementById('studentCodeForm');
    const studentRegisterForm = document.getElementById('studentRegisterForm');
    const teacherRegisterForm = document.getElementById('teacherRegisterForm');
    const forgotForm = document.getElementById('forgotForm');
    const studentRegisterFormElement = document.getElementById('studentRegisterFormElement');
    const teacherRegisterFormElement = document.getElementById('teacherRegisterFormElement');
    const teacherErrorContainer = document.getElementById('teacherErrorContainer');
    const teacherSubmitBtn = document.getElementById('teacherSubmitBtn');
    const studentCompleteBtn = document.getElementById('studentCompleteBtn');
    const studentErrorContainer = document.getElementById('studentErrorContainer');
    const studentCodeErrorContainer = document.getElementById('studentCodeErrorContainer');
    const loginFormElement = document.getElementById('loginFormElement');
    const loginErrorContainer = document.getElementById('loginErrorContainer');
    const loginSubmitBtn = document.getElementById('loginSubmitBtn');
    const forgotFormElement = document.getElementById('forgotFormElement');
    const forgotErrorContainer = document.getElementById('forgotErrorContainer');
    const forgotStatusContainer = document.getElementById('forgotStatusContainer');
    const forgotSubmitBtn = document.getElementById('forgotSubmitBtn');
    const studentSuccessContainer = document.getElementById('studentSuccessContainer');
    const studentValidateCodeBtn = document.getElementById('studentValidateCodeBtn');

    let studentValidateCodeBtnHtml = studentValidateCodeBtn ? studentValidateCodeBtn.innerHTML : '';
    let studentCompleteBtnHtml = studentCompleteBtn ? studentCompleteBtn.innerHTML : '';
    let teacherSubmitBtnHtml = teacherSubmitBtn ? teacherSubmitBtn.innerHTML : '';

    function setStudentValidateCodeLoading(loading) {
      if (!studentValidateCodeBtn) return;
      if (loading) {
        if (!studentValidateCodeBtnHtml) studentValidateCodeBtnHtml = studentValidateCodeBtn.innerHTML;
        studentValidateCodeBtn.disabled = true;
        studentValidateCodeBtn.classList.add('btn-loading');
        studentValidateCodeBtn.innerHTML = '<span class="spinner-small"></span><span>Validating...</span>';
      } else {
        studentValidateCodeBtn.disabled = false;
        studentValidateCodeBtn.classList.remove('btn-loading');
        if (studentValidateCodeBtnHtml && studentValidateCodeBtnHtml.trim() !== '') {
          studentValidateCodeBtn.innerHTML = studentValidateCodeBtnHtml;
        }
      }
    }

    function setTeacherSubmitLoading(loading) {
      if (!teacherSubmitBtn) return;
      if (loading) {
        if (!teacherSubmitBtnHtml) teacherSubmitBtnHtml = teacherSubmitBtn.innerHTML;
        teacherSubmitBtn.disabled = true;
        teacherSubmitBtn.classList.add('btn-loading');
        teacherSubmitBtn.innerHTML = '<span class="spinner-small"></span> SIGNING UP...';
      } else {
        teacherSubmitBtn.disabled = false;
        teacherSubmitBtn.classList.remove('btn-loading');
        if (teacherSubmitBtnHtml && teacherSubmitBtnHtml.trim() !== '') {
          teacherSubmitBtn.innerHTML = teacherSubmitBtnHtml;
        }
      }
    }

    function setStudentCompleteLoading(loading) {
      if (!studentCompleteBtn) return;
      if (loading) {
        if (!studentCompleteBtnHtml) studentCompleteBtnHtml = studentCompleteBtn.innerHTML;
        studentCompleteBtn.disabled = true;
        studentCompleteBtn.classList.add('btn-loading');
        studentCompleteBtn.innerHTML = '<span class="spinner-small"></span><span>Submitting...</span>';
      } else {
        studentCompleteBtn.disabled = false;
        studentCompleteBtn.classList.remove('btn-loading');
        if (studentCompleteBtnHtml && studentCompleteBtnHtml.trim() !== '') {
          studentCompleteBtn.innerHTML = studentCompleteBtnHtml;
        }
      }
    }

    /**
     * Read Laravel / JSON error payloads so users see a real message instead of a generic one.
     */
    async function fetchErrorMessage(response, fallback) {
      const defaultMsg = fallback || 'Something went wrong. Please try again.';
      if (response.status === 419) {
        return 'Your session expired. Refresh the page and try again.';
      }
      if (response.status === 401 || response.status === 403) {
        return 'You are not allowed to complete this action. Try refreshing the page.';
      }
      const ct = (response.headers.get('content-type') || '');
      if (ct.includes('application/json')) {
        try {
          const data = await response.json();
          if (typeof data.message === 'string' && data.message.trim() !== '') {
            return data.message;
          }
          if (data.errors && typeof data.errors === 'object') {
            const parts = [];
            Object.values(data.errors).forEach((v) => {
              if (Array.isArray(v)) {
                v.forEach((m) => {
                  if (typeof m === 'string') parts.push(m);
                });
              } else if (typeof v === 'string') {
                parts.push(v);
              }
            });
            if (parts.length) return parts.join(' ');
          }
          if (typeof data.error === 'string' && data.error.trim() !== '') {
            return data.error;
          }
        } catch (e) {
          return defaultMsg;
        }
      }
      if (response.status >= 500) {
        return 'A server error occurred. Please try again in a few minutes.';
      }
      return defaultMsg;
    }

    function showCard(which) {
      loginForm.classList.add('hidden');
      roleSelectionForm.classList.add('hidden');
      studentCodeForm.classList.add('hidden');
      studentRegisterForm.classList.add('hidden');
      teacherRegisterForm.classList.add('hidden');
      forgotForm.classList.add('hidden');

      // Backend uses show_form "register" for student hero wizard; treat like student_register
      if (which === 'student_code') studentCodeForm.classList.remove('hidden');
      else if (which === 'student_register' || which === 'register') studentRegisterForm.classList.remove('hidden');
      else if (which === 'teacher_register') teacherRegisterForm.classList.remove('hidden');
      else if (which === 'forgot') forgotForm.classList.remove('hidden');
      else if (which === 'role') roleSelectionForm.classList.remove('hidden');
      else loginForm.classList.remove('hidden');

      // Allow body scroll for tall hero wizard, reset for other cards
      if (which === 'student_register' || which === 'register') {
        document.body.classList.add('wizard-active');
      } else {
        document.body.classList.remove('wizard-active');
      }

      // Reset loading indicators when switching between cards.
      if (which !== 'student_register' && which !== 'register') {
        setStudentCompleteLoading(false);
        const successFx = document.getElementById('studentSuccessEffects');
        if (successFx) successFx.classList.remove('show');
      }
      if (which !== 'student_code') {
        setStudentValidateCodeLoading(false);
      }
    }

    // Splash
    window.addEventListener('load', () => {
      const splash = document.getElementById('splash');
      const main = document.getElementById('main-container');
      const skipSplash = document.body.dataset.skipSplash === '1';
      const initialForm = document.body.dataset.initialForm || 'login';

      const reveal = () => {
        splash.style.display = 'none';
        main.classList.remove('hidden');
        showCard(initialForm);
      };

      if (skipSplash) {
        reveal();
      } else {
        setTimeout(() => {
          splash.classList.add('fade-out');
          setTimeout(reveal, 600);
        }, 1500);
      }
    });

    // Toggle links
    const showRegister = document.getElementById('showRegister');
    const showLogin = document.getElementById('showLogin');
    const showForgot = document.getElementById('showForgot');
const backToLoginFromForgot = document.getElementById('backToLoginFromForgot');

    if (showRegister) showRegister.addEventListener('click', e => { e.preventDefault(); showCard('role'); });
    if (showLogin) showLogin.addEventListener('click', e => { e.preventDefault(); showCard('login'); });
    if (showForgot) showForgot.addEventListener('click', e => { e.preventDefault(); showCard('forgot'); });
if (backToLoginFromForgot) {
  backToLoginFromForgot.addEventListener('click', e => {
    e.preventDefault();
    showCard('login');
  });
}

    // Any "#showLogin" span/link should go back to login
    document.querySelectorAll('#showLogin').forEach(el => {
      el.addEventListener('click', e => {
        e.preventDefault();
        showCard('login');
      });
    });

    // Role selection
    function selectRole(role) {
      if (role === 'teacher') {
        showCard('teacher_register');
      } else {
        showCard('student_code');
      }
    }

    // Character class selection for hero wizard
    function selectCharacterClass(character) {
      document.querySelectorAll('.class-chip').forEach(el => el.classList.remove('active'));
      const selectedEl = document.getElementById('char-' + character);
      if (selectedEl) selectedEl.classList.add('active');
      const characterInput = document.getElementById('characterInput');
      if (characterInput) characterInput.value = character;
      const errorEl = document.getElementById('characterError');
      if (errorEl) errorEl.style.display = 'none';
      updateHeroPreview();
    }

    // Gender toggle selection
    function selectGenderChoice(gender) {
      document.querySelectorAll('.gtoggle-btn').forEach(btn => btn.classList.remove('active'));
      const btn = document.getElementById('gbtn-' + gender);
      if (btn) btn.classList.add('active');
      const genderInput = document.getElementById('genderInput');
      if (genderInput) genderInput.value = gender;
      const errorEl = document.getElementById('genderError');
      if (errorEl) errorEl.style.display = 'none';
      updateHeroPreview();
    }

    // Live hero preview updater
    function updateHeroPreview() {
      const character = (document.getElementById('characterInput') || {}).value || '';
      const gender    = (document.getElementById('genderInput')    || {}).value || '';
      const previewImg   = document.getElementById('charPreviewImg');
      const placeholder  = document.getElementById('noCharPlaceholder');
      const classLabel   = document.getElementById('previewClassName');
      const skillsWrap = document.getElementById('previewSkillsWrap');
      const skillsList = document.getElementById('previewSkillsList');
      const skillsByClass = window.__registrationCharacterSkills || {};

      if (character && skillsByClass[character]) {
        if (classLabel) classLabel.textContent = skillsByClass[character].label || '';
        if (skillsWrap && skillsList) {
          skillsWrap.style.display = 'block';
          skillsList.innerHTML = '';
          const abilities = skillsByClass[character].abilities || [];
          abilities.forEach(function (ab) {
            const li = document.createElement('li');
            const nameEl = document.createElement('span');
            nameEl.className = 'hero-skill-name';
            nameEl.textContent = ab.name || '';
            li.appendChild(nameEl);
            if (ab.desc) {
              const descEl = document.createElement('span');
              descEl.className = 'hero-skill-desc';
              descEl.textContent = ab.desc;
              li.appendChild(descEl);
            }
            skillsList.appendChild(li);
          });
        }
      } else {
        if (classLabel) classLabel.textContent = '';
        if (skillsWrap) skillsWrap.style.display = 'none';
        if (skillsList) skillsList.innerHTML = '';
      }

      if (character && gender && previewImg && placeholder) {
        previewImg.src = "{{ asset('images') }}/" + gender + '_' + character + '.png';
        previewImg.style.display = 'block';
        placeholder.style.display = 'none';
      } else if (placeholder && previewImg) {
        previewImg.style.display = 'none';
        placeholder.style.display = 'flex';
      }
    }

    // Validate student code via AJAX
    async function validateStudentCode() {
      const codeInput = document.getElementById('studentCodeInput');
      const messageEl = document.getElementById('codeValidationMessage');
      const code = codeInput.value.trim().toUpperCase();

      if (!code) {
        messageEl.innerHTML = '<span class="error"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> Please enter your student code.</span>';
        return;
      }

      messageEl.innerHTML = '<span class="validating"><svg class="spinner" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"></path></svg> Validating your code...</span>';
      setStudentValidateCodeLoading(true);

      try {
        const response = await fetch("{{ route('register.validate-code') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
          },
          body: JSON.stringify({ student_code: code })
        });

        let data;
        try {
          data = await response.json();
        } catch (parseErr) {
          let errText = 'Could not read the server response. Please try again.';
          if (response.status === 419) errText = 'Your session expired. Refresh the page and try again.';
          else if (response.status >= 500) errText = 'A server error occurred. Please try again later.';
          messageEl.innerHTML = '<span class="error"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> ' + errText + '</span>';
          setStudentValidateCodeLoading(false);
          return;
        }

        if (!response.ok && data.errors && typeof data.errors === 'object') {
          const flat = Object.values(data.errors).flat().filter((m) => typeof m === 'string');
          const errMsg = flat.length ? flat[0] : (data.message || 'Could not validate this code.');
          messageEl.innerHTML = '<span class="error"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> ' + errMsg + '</span>';
          setStudentValidateCodeLoading(false);
          return;
        }

        if (data.success) {
          messageEl.innerHTML = '<span class="success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Code validated! Welcome, ' + data.student.full_name + '!</span>';

          // Populate hidden inputs
          document.getElementById('displayStudentName').value = data.student.full_name;
          document.getElementById('displayUsername').value = data.student.username;
          document.getElementById('studentCodeHidden').value = code;

          var gSel = document.getElementById('regGradeSelect');
          var sSel = document.getElementById('regSectionSelect');
          if (gSel && data.student.grade_id) {
            gSel.value = String(data.student.grade_id);
            registrationRefreshSectionOptions(data.student.section_id);
          }

          // Populate hero preview panel
          const previewName = document.getElementById('previewStudentName');
          const previewUser = document.getElementById('previewStudentUser');
          if (previewName) previewName.textContent = data.student.full_name;
          if (previewUser) previewUser.textContent = '@' + data.student.username;

          // Show registration form after a short delay
          setTimeout(() => {
            setStudentValidateCodeLoading(false);
            showCard('student_register');
          }, 800);
        } else {
          messageEl.innerHTML = '<span class="error"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> ' + (data.message || 'Invalid code') + '</span>';
          setStudentValidateCodeLoading(false);
        }
      } catch (error) {
        messageEl.innerHTML = '<span class="error"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> Connection error. Please check your network and try again.</span>';
        console.error('Validation error:', error);
        setStudentValidateCodeLoading(false);
      }
    }

    // Go back to code entry
    function backToCodeEntry() {
      showCard('student_code');
      setStudentValidateCodeLoading(false);

      // Clear inputs
      const codeInput = document.getElementById('studentCodeInput');
      const messageEl = document.getElementById('codeValidationMessage');
      const gSel = document.getElementById('regGradeSelect');
      const sSel = document.getElementById('regSectionSelect');
      if (codeInput) codeInput.value = '';
      if (messageEl) messageEl.innerHTML = '';
      if (gSel) gSel.value = '';
      if (sSel) sSel.innerHTML = '<option value="">Select section</option>';
    }

function handleTeacherSubmit(event) {
  event.preventDefault();

  teacherErrorContainer.innerHTML = '';
  const seenMessages = new Set();
  setTeacherSubmitLoading(true);

  const formData = new FormData(teacherRegisterFormElement);

  fetch("{{ route('register.teacher') }}", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json',
    },
    body: formData,
  })
    .then(async (response) => {
      if (response.status === 422) {
        const data = await response.json();
        const errors = data.errors || {};

        Object.values(errors).forEach(messages => {
          messages.forEach(message => {
            if (seenMessages.has(message)) return;
            seenMessages.add(message);
            const p = document.createElement('p');
            p.classList.add('error');
            p.textContent = message;
            teacherErrorContainer.appendChild(p);
          });
        });

        setTeacherSubmitLoading(false);
        return;
      }

      if (!response.ok) {
        const msg = await fetchErrorMessage(response, 'Registration could not be completed. Please try again.');
        const p = document.createElement('p');
        p.classList.add('error');
        p.textContent = msg;
        teacherErrorContainer.appendChild(p);
        setTeacherSubmitLoading(false);
        return;
      }

      const data = await response.json();

      if (data.success) {
        const p = document.createElement('p');
        p.classList.add('success');
        p.textContent = data.message || 'Registration successful.';
        teacherErrorContainer.appendChild(p);

        teacherRegisterFormElement.reset();
      } else {
        const p = document.createElement('p');
        p.classList.add('error');
        p.textContent = (data.message && String(data.message)) || 'Registration could not be completed.';
        teacherErrorContainer.appendChild(p);
      }

      setTeacherSubmitLoading(false);
    })
    .catch(() => {
      const p = document.createElement('p');
      p.classList.add('error');
      p.textContent = 'Unable to submit. Check your connection.';
      teacherErrorContainer.appendChild(p);
      setTeacherSubmitLoading(false);
    });
}

    // STUDENT: Form validation for hero wizard
    if (studentRegisterFormElement) {
      studentRegisterFormElement.addEventListener('submit', function(event) {
        console.log('[Student Submit] Start button clicked');

        // Run native form validation first so we don't lock button on invalid required fields.
        if (!studentRegisterFormElement.checkValidity()) {
          console.warn('[Student Submit] Native form validation failed', {
            student_code: document.getElementById('studentCodeHidden')?.value || null,
            grade_id: document.getElementById('regGradeSelect')?.value || null,
            section_id: document.getElementById('regSectionSelect')?.value || null,
            character: document.getElementById('characterInput')?.value || null,
            gender: document.getElementById('genderInput')?.value || null,
          });
          event.preventDefault();
          studentRegisterFormElement.reportValidity();
          setStudentCompleteLoading(false);
          return false;
        }

        const characterInput = document.getElementById('characterInput');
        const genderInput    = document.getElementById('genderInput');
        const charErrorEl    = document.getElementById('characterError');
        const genderErrorEl  = document.getElementById('genderError');
        let valid = true;

        if (!characterInput || !characterInput.value) {
          console.warn('[Student Submit] Missing character selection');
          event.preventDefault();
          if (charErrorEl) charErrorEl.style.display = 'block';
          valid = false;
        }
        if (!genderInput || !genderInput.value) {
          console.warn('[Student Submit] Missing gender selection');
          event.preventDefault();
          if (genderErrorEl) genderErrorEl.style.display = 'block';
          valid = false;
        }
        if (!valid) {
          console.warn('[Student Submit] Blocked before POST due to custom validation');
          const firstErr = document.querySelector('.wiz-field-err[style*="block"]');
          if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
          console.log('[Student Submit] Validation passed, posting form', {
            action: studentRegisterFormElement.action,
            method: (studentRegisterFormElement.method || 'POST').toUpperCase(),
            student_code: document.getElementById('studentCodeHidden')?.value || null,
            grade_id: document.getElementById('regGradeSelect')?.value || null,
            section_id: document.getElementById('regSectionSelect')?.value || null,
            character: document.getElementById('characterInput')?.value || null,
            gender: document.getElementById('genderInput')?.value || null,
          });
          setStudentCompleteLoading(true);
        }
        return valid;
      });
    }

if (loginFormElement) {
  loginFormElement.addEventListener('submit', handleLoginSubmit);
}

async function handleLoginSubmit(event) {
  event.preventDefault();

  // clear old errors
  loginErrorContainer.innerHTML = '';
  const seenMessages = new Set();
  let data = null;

  // Show loading state
  const originalBtnText = loginSubmitBtn.innerHTML;
  loginSubmitBtn.disabled = true;
  loginSubmitBtn.classList.add('btn-loading');
  loginSubmitBtn.innerHTML = '<span class="spinner-small"></span> LOGGING IN...';

  const formData = new FormData(loginFormElement);

  try {
    const response = await fetch("{{ route('login') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: formData,
    });

    if (response.status === 422) {
      data = await response.json();
      const errors = data.errors || {};

      Object.values(errors).forEach(messages => {
        messages.forEach(message => {
          if (seenMessages.has(message)) return;
          seenMessages.add(message);
          const p = document.createElement('p');
          p.classList.add('error');
          p.textContent = message;
          loginErrorContainer.appendChild(p);
        });
      });

      loginSubmitBtn.disabled = false;
      return;
    }

    if (!response.ok) {
      const msg = await fetchErrorMessage(response, 'Login could not be completed. Please try again.');
      const p = document.createElement('p');
      p.classList.add('error');
      p.textContent = msg;
      loginErrorContainer.appendChild(p);
      loginSubmitBtn.disabled = false;
      return;
    }

    data = await response.json();

    if (data.success && data.redirect) {
      // successful login → go to dashboard
      window.location.href = data.redirect;
      return;
    }

    // Safety: if backend sent errors in a non-422 response
    if (data.errors) {
      Object.values(data.errors).forEach(messages => {
        messages.forEach(message => {
          if (seenMessages.has(message)) return;
          seenMessages.add(message);
          const p = document.createElement('p');
          p.classList.add('error');
          p.textContent = message;
          loginErrorContainer.appendChild(p);
        });
      });
    }
  } catch (e) {
    const p = document.createElement('p');
    p.classList.add('error');
    p.textContent = 'Unable to log in. Please check your connection and try again.';
    loginErrorContainer.appendChild(p);
  } finally {
    // Restore button state if not redirected
    if (!data || !data.redirect || window.location.href.indexOf(data.redirect) === -1) {
        loginSubmitBtn.disabled = false;
        loginSubmitBtn.classList.remove('btn-loading');
        loginSubmitBtn.innerHTML = 'LOG IN';
    }
  }
}

if (forgotFormElement) {
  forgotFormElement.addEventListener('submit', handleForgotSubmit);
}

async function handleForgotSubmit(event) {
  event.preventDefault();

  // clear old messages
  forgotErrorContainer.innerHTML = '';
  forgotStatusContainer.innerHTML = '';

  const seenMessages = new Set();
  if (forgotSubmitBtn) forgotSubmitBtn.disabled = true;

  const formData = new FormData(forgotFormElement);

  try {
    const response = await fetch("{{ route('password.email') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: formData,
    });

    if (response.status === 422) {
      const data = await response.json();
      const errors = data.errors || {};

      Object.values(errors).forEach(messages => {
        messages.forEach(message => {
          if (seenMessages.has(message)) return;
          seenMessages.add(message);

          const p = document.createElement('p');
          p.classList.add('error');
          p.textContent = message;
          forgotErrorContainer.appendChild(p);
        });
      });

      if (forgotSubmitBtn) forgotSubmitBtn.disabled = false;
      return;
    }

    if (!response.ok) {
      const msg = await fetchErrorMessage(response, 'We could not process your request. Please try again.');
      const p = document.createElement('p');
      p.classList.add('error');
      p.textContent = msg;
      forgotErrorContainer.appendChild(p);
      if (forgotSubmitBtn) forgotSubmitBtn.disabled = false;
      return;
    }

    const data = await response.json();

    if (data.success) {
      const p = document.createElement('p');
      p.classList.add('success');
      p.textContent = data.message || 'Password reset link sent to your email.';
      forgotStatusContainer.appendChild(p);

      // optional: keep email field OR clear it – your call
      forgotFormElement.reset();
    } else if (data.errors) {
      Object.values(data.errors).forEach(messages => {
        messages.forEach(message => {
          if (seenMessages.has(message)) return;
          seenMessages.add(message);

          const p = document.createElement('p');
          p.classList.add('error');
          p.textContent = message;
          forgotErrorContainer.appendChild(p);
        });
      });
    }
  } catch (e) {
    const p = document.createElement('p');
    p.classList.add('error');
    p.textContent = 'Unable to process your request. Please check your connection and try again.';
    forgotErrorContainer.appendChild(p);
  } finally {
    if (forgotSubmitBtn) forgotSubmitBtn.disabled = false;
  }
}

  </script>

</body>
</html>

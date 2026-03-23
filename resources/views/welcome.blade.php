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
<div id="teacherErrorContainer"></div>
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
    <div id="studentCodeForm" class="card reg-code-card hidden">
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
        <button type="submit" class="btn btn-quest">
          <span>Validate Code</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>

      <p class="rcc-footer">Already have an account? <span id="showLogin" class="text-gold pointer">Log In</span></p>
    </div>

    <!-- STUDENT REGISTRATION FORM -->
    <div id="studentRegisterForm" class="hero-wizard-card hidden">

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
        <div class="hero-stat-row" id="previewStatHP" style="display:none;">
          <div class="hstat-head"><span>❤️ HP</span><span id="previewHPVal">0</span></div>
          <div class="hstat-track"><div class="hstat-fill hp-fill" id="previewHPBar" style="width:0%"></div></div>
        </div>
        <div class="hero-stat-row" id="previewStatAP" style="display:none;">
          <div class="hstat-head"><span>⚡ AP</span><span id="previewAPVal">0</span></div>
          <div class="hstat-track"><div class="hstat-fill ap-fill" id="previewAPBar" style="width:0%"></div></div>
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
          <div class="ws-step done"><div class="ws-dot">✓</div><span>Code</span></div>
          <div class="ws-line active"></div>
          <div class="ws-step active"><div class="ws-dot">2</div><span>Hero</span></div>
          <div class="ws-line"></div>
          <div class="ws-step"><div class="ws-dot">3</div><span>Done</span></div>
        </div>

        <h2 class="wizard-title">Build Your <span class="text-gold">Hero</span></h2>

        <div id="studentSuccessContainer">
          @if (session('show_form') === 'register' && session('success'))
            <div class="reg-alert reg-alert-success">{{ session('success') }}</div>
          @endif
        </div>
        <div id="studentErrorContainer"></div>

        <form id="studentRegisterFormElement" method="POST" action="{{ route('register.student') }}">
          @csrf
          <input type="hidden" name="student_code" id="studentCodeHidden">
          <input type="hidden" id="displayStudentName">
          <input type="hidden" id="displayUsername">

          <!-- Class -->
          <div class="wiz-section">
            <p class="wiz-label">⚔️ Choose Your Class</p>
            <div class="class-chips">
              <div class="class-chip" id="char-mage" onclick="selectCharacterClass('mage')">
                <img src="{{ asset('images/mage.png') }}" alt="Mage">
                <span>Mage</span>
                <small>HP 30 | AP 50</small>
              </div>
              <div class="class-chip" id="char-warrior" onclick="selectCharacterClass('warrior')">
                <img src="{{ asset('images/warrior.png') }}" alt="Warrior">
                <span>Warrior</span>
                <small>HP 80 | AP 30</small>
              </div>
              <div class="class-chip" id="char-healer" onclick="selectCharacterClass('healer')">
                <img src="{{ asset('images/healer.png') }}" alt="Healer">
                <span>Healer</span>
                <small>HP 50 | AP 35</small>
              </div>
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

  <script>
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

    function showCard(which) {
      loginForm.classList.add('hidden');
      roleSelectionForm.classList.add('hidden');
      studentCodeForm.classList.add('hidden');
      studentRegisterForm.classList.add('hidden');
      teacherRegisterForm.classList.add('hidden');
      forgotForm.classList.add('hidden');

      if (which === 'student_code') studentCodeForm.classList.remove('hidden');
      else if (which === 'student_register') studentRegisterForm.classList.remove('hidden');
      else if (which === 'teacher_register') teacherRegisterForm.classList.remove('hidden');
      else if (which === 'forgot') forgotForm.classList.remove('hidden');
      else if (which === 'role') roleSelectionForm.classList.remove('hidden');
      else loginForm.classList.remove('hidden');

      // Allow body scroll for tall hero wizard, reset for other cards
      if (which === 'student_register') {
        document.body.classList.add('wizard-active');
      } else {
        document.body.classList.remove('wizard-active');
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
      const hpStat = document.getElementById('previewStatHP');
      const apStat = document.getElementById('previewStatAP');
      const hpVal  = document.getElementById('previewHPVal');
      const apVal  = document.getElementById('previewAPVal');
      const hpBar  = document.getElementById('previewHPBar');
      const apBar  = document.getElementById('previewAPBar');

      const stats = {
        mage:    { label: '🧙 Mage',    hp: 30, ap: 50 },
        warrior: { label: '⚔️ Warrior', hp: 80, ap: 30 },
        healer:  { label: '💚 Healer',  hp: 50, ap: 35 }
      };
      const maxHP = 100, maxAP = 100;

      if (character && stats[character]) {
        if (classLabel) classLabel.textContent = stats[character].label;
        if (hpStat && apStat) {
          hpStat.style.display = 'block';
          apStat.style.display = 'block';
          if (hpVal) hpVal.textContent = stats[character].hp;
          if (apVal) apVal.textContent = stats[character].ap;
          if (hpBar) hpBar.style.width = (stats[character].hp / maxHP * 100) + '%';
          if (apBar) apBar.style.width = (stats[character].ap / maxAP * 100) + '%';
        }
      }

      if (character && gender && previewImg && placeholder) {
        previewImg.src = '{{ asset('images') }}/' + gender + '_' + character + '.png';
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

      try {
        const response = await fetch('{{ route('register.validate-code') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ student_code: code })
        });

        const data = await response.json();

        if (data.success) {
          messageEl.innerHTML = '<span class="success"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg> Code validated! Welcome, ' + data.student.full_name + '!</span>';

          // Populate hidden inputs
          document.getElementById('displayStudentName').value = data.student.full_name;
          document.getElementById('displayUsername').value = data.student.username;
          document.getElementById('studentCodeHidden').value = code;

          // Populate hero preview panel
          const previewName = document.getElementById('previewStudentName');
          const previewUser = document.getElementById('previewStudentUser');
          if (previewName) previewName.textContent = data.student.full_name;
          if (previewUser) previewUser.textContent = '@' + data.student.username;

          // Show registration form after a short delay
          setTimeout(() => {
            showCard('student_register');
          }, 800);
        } else {
          messageEl.innerHTML = '<span class="error"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg> ' + (data.message || 'Invalid code') + '</span>';
        }
      } catch (error) {
        messageEl.innerHTML = '<span class="error"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg> Connection error. Please try again.</span>';
        console.error('Validation error:', error);
      }
    }

    // Go back to code entry
    function backToCodeEntry() {
      showCard('student_code');

      // Clear inputs
      const codeInput = document.getElementById('studentCodeInput');
      const messageEl = document.getElementById('codeValidationMessage');
      if (codeInput) codeInput.value = '';
      if (messageEl) messageEl.innerHTML = '';
    }

function handleTeacherSubmit(event) {
  event.preventDefault();

  teacherErrorContainer.innerHTML = '';
  const seenMessages = new Set();
  teacherSubmitBtn.disabled = true;

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

        teacherSubmitBtn.disabled = false;
        return;
      }

      if (!response.ok) {
        const p = document.createElement('p');
        p.classList.add('error');
        p.textContent = 'An unexpected error occurred. Please try again.';
        teacherErrorContainer.appendChild(p);
        teacherSubmitBtn.disabled = false;
        return;
      }

      const data = await response.json();

      if (data.success) {
        const p = document.createElement('p');
        p.classList.add('success');
        p.textContent = data.message || 'Registration successful.';
        teacherErrorContainer.appendChild(p);

        teacherRegisterFormElement.reset();
      }

      teacherSubmitBtn.disabled = false;
    })
    .catch(() => {
      const p = document.createElement('p');
      p.classList.add('error');
      p.textContent = 'Unable to submit. Check your connection.';
      teacherErrorContainer.appendChild(p);
      teacherSubmitBtn.disabled = false;
    });
}

    // STUDENT: Form validation for hero wizard
    if (studentRegisterFormElement) {
      studentRegisterFormElement.addEventListener('submit', function(event) {
        const characterInput = document.getElementById('characterInput');
        const genderInput    = document.getElementById('genderInput');
        const charErrorEl    = document.getElementById('characterError');
        const genderErrorEl  = document.getElementById('genderError');
        let valid = true;

        if (!characterInput || !characterInput.value) {
          event.preventDefault();
          if (charErrorEl) charErrorEl.style.display = 'block';
          valid = false;
        }
        if (!genderInput || !genderInput.value) {
          event.preventDefault();
          if (genderErrorEl) genderErrorEl.style.display = 'block';
          valid = false;
        }
        if (!valid) {
          const firstErr = document.querySelector('.wiz-field-err[style*="block"]');
          if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
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
      const p = document.createElement('p');
      p.classList.add('error');
      p.textContent = 'An unexpected error occurred. Please try again.';
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
      const p = document.createElement('p');
      p.classList.add('error');
      p.textContent = 'An unexpected error occurred. Please try again.';
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

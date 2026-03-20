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

    <div id="registerForm" class="card hidden">
  <h2>Student Sign Up</h2>

  {{-- Success from full registration redirect --}}
  <div id="studentSuccessContainer">
    @if (session('show_form') === 'register' && session('success'))
      <p class="success">{{ session('success') }}</p>
    @endif
  </div>

  {{-- IMPORTANT: no Blade validation errors here; step 1 is handled via AJAX --}}
  <div id="studentErrorContainer"></div>
  <form id="registerFormElement" method="POST" action="{{ route('register.student') }}">
    @csrf
    <input type="text" id="registrationCode" name="registration_code" placeholder="Registration Code" required>
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="password_confirmation" placeholder="Re-type Password" required>

    <input type="hidden" name="role" id="roleInput">
    <input type="hidden" name="character" id="characterInput">
    <input type="hidden" name="gender" id="genderInput">
    <input type="hidden" name="avatar" id="avatarInput">

    <button type="submit" id="studentNextBtn" class="btn">NEXT</button>
  </form>
  <p class="toggle-text">
    Already have an account? <span id="showLogin">Log In</span>
  </p>
</div>


    <!-- CHARACTER SELECTION FORM -->
    <div id="characterSelectionForm" class="card hidden">
      <h2>Choose Your Character</h2>
      <div class="character-options">
        <a href="#" onclick="selectCharacter('healer')">
          <div class="character-option">
            <img src="{{ asset('images/healer.png') }}" alt="Healer">
            <p>Healer</p>
          </div>
        </a>
        <a href="#" onclick="selectCharacter('mage')">
          <div class="character-option">
            <img src="{{ asset('images/mage.png') }}" alt="Mage">
            <p>Mage</p>
          </div>
        </a>
        <a href="#" onclick="selectCharacter('warrior')">
          <div class="character-option">
            <img src="{{ asset('images/warrior.png') }}" alt="Warrior">
            <p>Warrior</p>
          </div>
        </a>
      </div>
    </div>

    <!-- GENDER SELECTION FORM -->
    <div id="genderSelectionForm" class="card hidden">
      <h2>Choose Your Gender</h2>
      <div class="gender-options" id="genderOptionsContainer">
        <!-- injected dynamically -->
      </div>
    </div>

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
    const registerForm = document.getElementById('registerForm');
    const teacherRegisterForm = document.getElementById('teacherRegisterForm');
    const forgotForm = document.getElementById('forgotForm');
    const characterSelectionForm = document.getElementById('characterSelectionForm');
    const genderSelectionForm = document.getElementById('genderSelectionForm');
    const registrationCodeInput = document.getElementById('registrationCode');
    const roleInput = document.getElementById('roleInput');
    const registerFormElement = document.getElementById('registerFormElement');
    const teacherRegisterFormElement = document.getElementById('teacherRegisterFormElement');
    const teacherErrorContainer = document.getElementById('teacherErrorContainer');
const teacherSubmitBtn = document.getElementById('teacherSubmitBtn');
    const avatarInput = document.getElementById('avatarInput');
    const studentNextBtn = document.getElementById('studentNextBtn');
    const studentErrorContainer = document.getElementById('studentErrorContainer');
    const loginFormElement = document.getElementById('loginFormElement');
const loginErrorContainer = document.getElementById('loginErrorContainer');
const loginSubmitBtn = document.getElementById('loginSubmitBtn');
const forgotFormElement = document.getElementById('forgotFormElement');
const forgotErrorContainer = document.getElementById('forgotErrorContainer');
const forgotStatusContainer = document.getElementById('forgotStatusContainer');
const forgotSubmitBtn = document.getElementById('forgotSubmitBtn');
const studentSuccessContainer = document.getElementById('studentSuccessContainer');


    let bypassAjaxValidation = false; // when true, final submit will skip step-1 AJAX

    function showCard(which) {
      loginForm.classList.add('hidden');
      roleSelectionForm.classList.add('hidden');
      registerForm.classList.add('hidden');
      teacherRegisterForm.classList.add('hidden');
      characterSelectionForm.classList.add('hidden');
      genderSelectionForm.classList.add('hidden');
      forgotForm.classList.add('hidden');

      if (which === 'register') registerForm.classList.remove('hidden');
      else if (which === 'teacher_register') teacherRegisterForm.classList.remove('hidden');
      else if (which === 'forgot') forgotForm.classList.remove('hidden');
      else if (which === 'role') roleSelectionForm.classList.remove('hidden');
      else if (which === 'character') characterSelectionForm.classList.remove('hidden');
      else if (which === 'gender') genderSelectionForm.classList.remove('hidden');
      else loginForm.classList.remove('hidden');
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
      showCard(role === 'teacher' ? 'teacher_register' : 'register');
      roleInput.value = role;

      if (role === 'student') {
        registrationCodeInput.classList.remove('hidden');
        registrationCodeInput.required = true;
      } else {
        registrationCodeInput.classList.add('hidden');
        registrationCodeInput.required = false;
      }
    }

    // Gender options for each character
    function showGenderOptions(character) {
      const genderOptions = {
        'healer': [
          { id: 'male_healer', text: 'Male Healer', img: 'male_healer.png' },
          { id: 'female_healer', text: 'Female Healer', img: 'female_healer.png' }
        ],
        'mage': [
          { id: 'male_mage', text: 'Male Mage', img: 'male_mage.png' },
          { id: 'female_mage', text: 'Female Mage', img: 'female_mage.png' }
        ],
        'warrior': [
          { id: 'male_warrior', text: 'Male Warrior', img: 'male_warrior.png' },
          { id: 'female_warrior', text: 'Female Warrior', img: 'female_warrior.png' }
        ]
      };

      const container = document.getElementById('genderOptionsContainer');
      container.innerHTML = '';

      if (genderOptions[character]) {
        genderOptions[character].forEach(option => {
          const optionElement = document.createElement('a');
          optionElement.href = "#";
          optionElement.onclick = (e) => { e.preventDefault(); selectGender(option.id); };
          optionElement.innerHTML = `
            <div class="gender-option">
              <img src="{{ asset('images') }}/${option.img}" alt="${option.text}">
              <p>${option.text}</p>
            </div>
          `;
          container.appendChild(optionElement);
        });
      }
    }

    function getAvatarForGender(gender) {
      switch (gender) {
        case 'female_healer': return 'female_healer.png';
        case 'male_healer': return 'male_healer.png';
        case 'female_mage': return 'female_mage.png';
        case 'male_mage': return 'male_mage.png';
        case 'female_warrior': return 'female_warrior.png';
        case 'male_warrior': return 'male_warrior.png';
        default: return 'default-pp.png';
      }
    }

    // Final step: gender selection → real form submit
    function selectGender(gender) {
      document.getElementById('genderInput').value = gender;
      if (avatarInput) {
        avatarInput.value = getAvatarForGender(gender);
      }
      // From now on, skip AJAX validation and do a normal POST
      bypassAjaxValidation = true;
      registerFormElement.submit();
    }

    function selectCharacter(character) {
      document.getElementById('characterInput').value = character;
      showGenderOptions(character);
      showCard('gender');
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

    // STUDENT: Step-1 AJAX validation
registerFormElement.addEventListener('submit', handleStudentSubmit);

async function handleStudentSubmit(event) {
  if (bypassAjaxValidation) {
    // final submit, let the browser do a normal POST
    return;
  }

  event.preventDefault();

  // clear old success + errors whenever user tries again
  if (studentSuccessContainer) {
    studentSuccessContainer.innerHTML = '';
  }
  studentErrorContainer.innerHTML = '';
  const seenMessages = new Set();

  // disable button to avoid spamming
  studentNextBtn.disabled = true;

  const formData = new FormData(registerFormElement);

  try {
    const response = await fetch("{{ route('register.student.validate') }}", {
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
          studentErrorContainer.appendChild(p);
        });
      });

      studentNextBtn.disabled = false;
      return;
    }

    if (!response.ok) {
      const p = document.createElement('p');
      p.classList.add('error');
      p.textContent = 'An unexpected error occurred. Please try again.';
      studentErrorContainer.appendChild(p);
      studentNextBtn.disabled = false;
      return;
    }

    const data = await response.json();
    if (data.success) {
      // Step 1 valid → move to character selection
      showCard('character');
    } else if (data.errors) {
      Object.values(data.errors).forEach(messages => {
        messages.forEach(message => {
          if (seenMessages.has(message)) return;
          seenMessages.add(message);
          const p = document.createElement('p');
          p.classList.add('error');
          p.textContent = message;
          studentErrorContainer.appendChild(p);
        });
      });
    }
  } catch (e) {
    const p = document.createElement('p');
    p.classList.add('error');
    p.textContent = 'Unable to validate your registration. Please check your connection and try again.';
    studentErrorContainer.appendChild(p);
  } finally {
    studentNextBtn.disabled = false;
  }
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

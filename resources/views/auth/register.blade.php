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

<!-- REGISTER FORM -->
<div id="registerForm" class="card hidden">
  <h2>Sign Up</h2>
  @if (session('show_form') === 'register' && session('success'))
    <p class="success">{{ session('success') }}</p>
  @endif
  @if (session('show_form') === 'register')
    @if ($errors->has('registration_code'))
      <p class="error">{{ $errors->first('registration_code') }}</p>
    @elseif ($errors->any())
      <p class="error">{{ $errors->first() }}</p>
    @endif
  @endif
  <form id="registerFormElement" method="POST" action="{{ route('register') }}" onsubmit="event.preventDefault(); handleSubmit();">
    @csrf
    <input type="text" id="registrationCode" name="registration_code" placeholder="Registration Code" class="hidden" required>
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="password" name="password_confirmation" placeholder="Re-type Password" required>

    <input type="hidden" name="role" id="roleInput">
    <input type="hidden" name="character" id="characterInput">
    <input type="hidden" name="gender" id="genderInput">

    <button type="submit" class="btn">NEXT</button>
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
  <div class="gender-options" id="genderOptionsContainer"></div>
</div>

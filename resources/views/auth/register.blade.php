@php
  $registrationGrades = $registrationGrades ?? collect();
@endphp

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

<!-- STUDENT CODE ENTRY FORM -->
<div id="studentCodeForm" class="card hidden">
  <h2>Enter Your Student Code</h2>
  <p style="margin-bottom: 20px; color: var(--text-muted);">Enter the student code provided by your teacher to begin registration.</p>

  @if (session('show_form') === 'register')
    @if ($errors->any())
      <p class="error">{{ $errors->first() }}</p>
    @endif
  @endif

  <form id="studentCodeFormElement" onsubmit="event.preventDefault(); validateStudentCode();">
    @csrf
    <input type="text" id="studentCodeInput" name="student_code" placeholder="Student Code (e.g., STU-XXXXXX)" required style="text-transform: uppercase;">
    <div id="codeValidationMessage" style="margin-top: 10px; font-size: 0.9rem;"></div>
    <button type="submit" class="btn" style="margin-top: 20px;">Validate Code</button>
  </form>

  <p class="toggle-text">
    Already have an account? <span id="showLogin">Log In</span>
  </p>
</div>

<!-- STUDENT REGISTRATION FORM -->
<div id="studentRegisterForm" class="card hidden">
  <h2>Complete Your Registration</h2>

  @if (session('show_form') === 'register' && session('success'))
    <p class="success">{{ session('success') }}</p>
  @endif

  <form id="studentRegisterFormElement" method="POST" action="{{ route('register.student') }}">
    @csrf

    <!-- Student Info (Read-only) -->
    <div style="background: var(--bg-main); padding: 16px; border-radius: var(--radius-sm); margin-bottom: 20px;">
      <div style="margin-bottom: 12px;">
        <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 4px;">Student Name</label>
        <input type="text" id="displayStudentName" readonly style="background: #e9ecef; font-weight: 600;">
      </div>
      <div>
        <label style="display: block; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 4px;">Username</label>
        <input type="text" id="displayUsername" readonly style="background: #e9ecef;">
      </div>
    </div>

    <!-- Hidden fields -->
    <input type="hidden" name="student_code" id="studentCodeHidden">

    <!-- Character Selection -->
    <div style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 12px; font-weight: 500;">Choose Your Character Class</label>
      <div class="character-options" style="display: flex; gap: 16px; justify-content: center;">
        <div class="character-option" onclick="selectCharacterClass('mage')" id="char-mage" style="cursor: pointer; border: 2px solid transparent; border-radius: var(--radius-sm); padding: 12px; transition: all 0.3s;">
          <img src="{{ asset('images/mage.png') }}" alt="Mage" style="width: 80px; height: 80px;">
          <p style="margin-top: 8px; font-weight: 600;">Mage</p>
          <small style="color: var(--text-muted);">HP: 30 | AP: 50</small>
        </div>
        <div class="character-option" onclick="selectCharacterClass('warrior')" id="char-warrior" style="cursor: pointer; border: 2px solid transparent; border-radius: var(--radius-sm); padding: 12px; transition: all 0.3s;">
          <img src="{{ asset('images/warrior.png') }}" alt="Warrior" style="width: 80px; height: 80px;">
          <p style="margin-top: 8px; font-weight: 600;">Warrior</p>
          <small style="color: var(--text-muted);">HP: 80 | AP: 30</small>
        </div>
        <div class="character-option" onclick="selectCharacterClass('healer')" id="char-healer" style="cursor: pointer; border: 2px solid transparent; border-radius: var(--radius-sm); padding: 12px; transition: all 0.3s;">
          <img src="{{ asset('images/healer.png') }}" alt="Healer" style="width: 80px; height: 80px;">
          <p style="margin-top: 8px; font-weight: 600;">Healer</p>
          <small style="color: var(--text-muted);">HP: 50 | AP: 35</small>
        </div>
      </div>
      <input type="hidden" name="character" id="characterInput" required>
      <div id="characterError" class="error" style="display: none; margin-top: 8px;">Please select a character class.</div>
    </div>

    <!-- Gender Selection -->
    <div style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 12px; font-weight: 500;">Choose Your Gender</label>
      <div style="display: flex; gap: 16px; justify-content: center;">
        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 12px 24px; border: 2px solid var(--border-color); border-radius: var(--radius-sm); transition: all 0.3s;">
          <input type="radio" name="gender" value="male" required style="margin: 0;">
          <span>Male</span>
        </label>
        <label style="cursor: pointer; display: flex; align-items: center; gap: 8px; padding: 12px 24px; border: 2px solid var(--border-color); border-radius: var(--radius-sm); transition: all 0.3s;">
          <input type="radio" name="gender" value="female" required style="margin: 0;">
          <span>Female</span>
        </label>
      </div>
    </div>

    <div style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 8px; font-weight: 500;">Grade &amp; section</label>
      @if($registrationGrades->isEmpty())
        <p class="error" style="margin-top: 8px;">Grades and sections are not set up yet. Please contact your administrator.</p>
      @else
        <select name="grade_id" id="regGradeSelect" required
          style="width: 100%; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
          <option value="">Select grade</option>
          @foreach($registrationGrades as $g)
            <option value="{{ $g->id }}" {{ old('grade_id') == $g->id ? 'selected' : '' }}>{{ preg_replace('/^\s*id\)?>\s*/i', '', $g->name) }}</option>
          @endforeach
        </select>
        <select name="section_id" id="regSectionSelect" required
          style="width: 100%; margin-top: 10px; padding: 10px 12px; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
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
        <small style="color: var(--text-muted); display: block; margin-top: 6px;">Choose the grade and section you belong to (school roster). If your teacher assigned them to your code, they may fill in after you validate.</small>
      @endif
      @error('grade_id')
        <p class="error" style="margin-top: 8px;">{{ $message }}</p>
      @enderror
      @error('section_id')
        <p class="error" style="margin-top: 8px;">{{ $message }}</p>
      @enderror
    </div>

    <!-- Password Section -->
    <div style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 12px; font-weight: 500;">Password</label>
      <input type="password" name="default_password" placeholder="Default Password (from teacher)" required style="margin-bottom: 12px;">
      <input type="password" name="new_password" placeholder="New Password (optional)" style="margin-bottom: 12px;">
      <input type="password" name="new_password_confirmation" placeholder="Confirm New Password">
      <small style="color: var(--text-muted); display: block; margin-top: 8px;">Leave new password blank to use the default password.</small>
    </div>

    <button type="submit" class="btn">Complete Registration</button>
  </form>

  <p class="toggle-text">
    <span onclick="backToCodeEntry()" style="cursor: pointer; color: var(--primary);">&larr; Back to Code Entry</span>
  </p>
</div>

<!-- CHARACTER SELECTION FORM (Legacy - kept for compatibility) -->
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

<!-- GENDER SELECTION FORM (Legacy - kept for compatibility) -->
<div id="genderSelectionForm" class="card hidden">
  <h2>Choose Your Gender</h2>
  <div class="gender-options" id="genderOptionsContainer"></div>
</div>

<script id="registration-grades-json" type="application/json">{!! json_encode($registrationGrades, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script>
  window.__registrationGrades = JSON.parse(document.getElementById('registration-grades-json')?.textContent || '[]');
</script>
<script src="{{ asset('js/auth-scripts.js') }}" defer></script>

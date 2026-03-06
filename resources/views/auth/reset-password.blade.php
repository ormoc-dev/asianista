<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Level Up Asianista</title>

    <!-- Same CSS as other pages -->
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>

<body>

  <div class="container">

    <!-- LOGO -->
    <div class="logo-circle">
      <img src="{{ asset('images/LEVEL UP ASIANISTA LOGO.png') }}" alt="Logo">
    </div>

    <!-- RESET PASSWORD CARD -->
    <div class="card">
      <h2>Reset Password</h2>

      {{-- AJAX will write here --}}
      <div id="resetSuccessContainer">
        @if (session('status'))
          <p class="success">{{ session('status') }}</p>
        @endif
      </div>

      {{-- AJAX will write here --}}
      <div id="resetErrorContainer">
        @if ($errors->any())
          <p class="error">{{ $errors->first() }}</p>
        @endif
      </div>

      <form id="resetPasswordForm" method="POST" action="{{ route('password.update') }}">
        @csrf

        <!-- Required token -->
        <input type="hidden" name="token" value="{{ $token }}">

        <!-- Required email (comes from link) -->
        <input type="hidden" name="email" value="{{ request('email') }}">

        <input
          type="password"
          name="password"
          id="resetPassword"
          placeholder="New Password"
          required
        >

        <input
          type="password"
          name="password_confirmation"
          id="resetPasswordConfirm"
          placeholder="Confirm Password"
          required
        >

        <button type="submit" class="btn" id="resetBtn">Reset Password</button>
      </form>

      <div class="links" style="margin-top: 15px;">
        <a href="/">Back to Login</a>
      </div>
    </div>

  </div>

  <script>
    const resetForm = document.getElementById('resetPasswordForm');
    const resetBtn = document.getElementById('resetBtn');
    const resetErrorContainer = document.getElementById('resetErrorContainer');
    const resetSuccessContainer = document.getElementById('resetSuccessContainer');

    if (resetForm) {
      resetForm.addEventListener('submit', handleResetSubmit);
    }

    async function handleResetSubmit(e) {
      e.preventDefault();

      // Clear previous messages
      resetErrorContainer.innerHTML = '';
      // Keep previous success unless new one arrives
      // resetSuccessContainer.innerHTML = '';

      const seenMessages = new Set();
      resetBtn.disabled = true;

      const formData = new FormData(resetForm);

      try {
        const response = await fetch("{{ route('password.update') }}", {
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
              resetErrorContainer.appendChild(p);
            });
          });

          resetBtn.disabled = false;
          return;
        }

        if (!response.ok) {
          const p = document.createElement('p');
          p.classList.add('error');
          p.textContent = 'An unexpected error occurred. Please try again.';
          resetErrorContainer.appendChild(p);
          resetBtn.disabled = false;
          return;
        }

        const data = await response.json();

        // Success path: Laravel default status is "passwords.reset"
        if (data.status === 'passwords.reset' || data.success) {
          resetForm.reset();

          resetSuccessContainer.innerHTML = '';
          const p = document.createElement('p');
          p.classList.add('success');
          p.textContent = data.message || 'Password has been reset successfully.';
          resetSuccessContainer.appendChild(p);

          // Optional: redirect back to login after a short delay
          setTimeout(() => {
            window.location.href = '/';
          }, 1500);
        } else if (data.errors) {
          Object.values(data.errors).forEach(messages => {
            messages.forEach(message => {
              if (seenMessages.has(message)) return;
              seenMessages.add(message);

              const p = document.createElement('p');
              p.classList.add('error');
              p.textContent = message;
              resetErrorContainer.appendChild(p);
            });
          });
        }

      } catch (err) {
        const p = document.createElement('p');
        p.classList.add('error');
        p.textContent = 'Unable to reset password. Please check your connection and try again.';
        resetErrorContainer.appendChild(p);
      } finally {
        resetBtn.disabled = false;
      }
    }
  </script>

</body>
</html>

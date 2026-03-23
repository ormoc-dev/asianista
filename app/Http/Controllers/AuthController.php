<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\RegistrationCode;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showHome()
    {
        return view('welcome');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showStudentRegister()
    {
        return view('auth.student-register');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    private function getProfilePicForGender(?string $gender): string
    {
        switch ($gender) {
            case 'female_healer':  return 'female_healer.png';
            case 'male_healer':    return 'male_healer.png';
            case 'female_mage':    return 'female_mage.png';
            case 'male_mage':      return 'male_mage.png';
            case 'female_warrior': return 'female_warrior.png';
            case 'male_warrior':   return 'male_warrior.png';
            default:               return 'default-pp.png';
        }
    }

    public function showTeacherRegister()
    {
        return view('auth.teacher-register');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Teacher login handling
            if ($user->role === 'teacher') {

                if ($user->status === 'pending') {
                    Auth::logout();
                    $message = 'Your account is pending approval. Please wait for the admin’s review.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors'  => ['credentials' => [$message]],
                        ], 422);
                    }

                    return back()
                        ->with('error', $message)
                        ->with('show_form', 'login')
                        ->withInput();
                }

                if ($user->status === 'rejected') {
                    Auth::logout();
                    $message = 'Your registration request was declined by the administrator.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors'  => ['credentials' => [$message]],
                        ], 422);
                    }

                    return back()
                        ->with('error', $message)
                        ->with('show_form', 'login')
                        ->withInput();
                }

                // Approved teacher
                $redirectUrl = route('teacher.dashboard');

            // Student login handling (now also status-gated)
            } elseif ($user->role === 'student') {

                if ($user->status === 'pending') {
                    Auth::logout();
                    $message = 'Your account is pending teacher approval.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors'  => ['credentials' => [$message]],
                        ], 422);
                    }

                    return back()
                        ->with('error', $message)
                        ->with('show_form', 'login')
                        ->withInput();
                }

                if ($user->status === 'rejected') {
                    Auth::logout();
                    $message = 'Your registration request was declined by the teacher.';

                    if ($request->expectsJson()) {
                        return response()->json([
                            'success' => false,
                            'errors'  => ['credentials' => [$message]],
                        ], 422);
                    }

                    return back()
                        ->with('error', $message)
                        ->with('show_form', 'login')
                        ->withInput();
                }

                // Approved student
                $redirectUrl = route('student.dashboard');

            } elseif ($user->role === 'admin') {
                $redirectUrl = route('admin.dashboard');
            } else {
                // Unknown role – log out and treat as invalid
                Auth::logout();
                $redirectUrl = null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => true,
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl);
        }

        // Invalid credentials
        $message = 'Invalid credentials';

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'errors'  => ['credentials' => [$message]],
            ], 422);
        }

        return back()
            ->with('error', $message)
            ->with('show_form', 'login')
            ->withInput();
    }

    public function registerStudent(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'student_code' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $code = RegistrationCode::where('student_code', $value)->first();

                    if (!$code) {
                        return $fail('The student code is invalid.');
                    }

                    if ($code->used) {
                        return $fail('This student code has already been used.');
                    }
                },
            ],
            'character' => 'required|string|in:mage,warrior,healer',
            'gender'    => 'required|string|in:male,female',
            'default_password' => 'required|string',
            'new_password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('show_form', 'register');
        }

        // Get the registration code record
        $registrationCode = RegistrationCode::where('student_code', $request->student_code)->first();

        // Verify default password
        if ($request->default_password !== $registrationCode->default_password) {
            return back()
                ->withErrors(['default_password' => 'The default password is incorrect.'])
                ->withInput()
                ->with('show_form', 'register');
        }

        $profilePic = $this->getProfilePicForGender($request->gender . '_' . $request->character);

        // Determine final password
        $finalPassword = $request->new_password ?? $request->default_password;

        // Create the user with character stats
        $user = User::create([
            'name'        => $registrationCode->full_name,
            'first_name'  => $registrationCode->first_name,
            'last_name'   => $registrationCode->last_name,
            'middle_name' => $registrationCode->middle_name,
            'email'       => $registrationCode->username . '@asianista.com', // Auto-generated email
            'username'    => $registrationCode->username,
            'password'    => bcrypt($finalPassword),
            'role'        => 'student',
            'character'   => $request->character,
            'gender'      => $request->gender,
            'profile_pic' => $profilePic,
            'status'      => 'pending',
            'hp'          => 0, // Will be set by initializeCharacterStats
            'ap'          => 0,
        ]);

        // Initialize character stats (HP/AP)
        $user->initializeCharacterStats($request->character);
        $user->save();

        // Mark the code as used and link to user
        $registrationCode->update([
            'used' => true,
            'user_id' => $user->id,
            'character' => $request->character,
            'gender' => $request->gender,
        ]);

        // Show message on welcome page
        return redirect('/')
            ->with('success', 'Registration submitted. Awaiting teacher approval.')
            ->with('show_form', 'register');
    }

    /**
     * Validate student code via AJAX
     */
    public function validateStudentCode(Request $request)
    {
        $request->validate([
            'student_code' => 'required|string',
        ]);

        $code = RegistrationCode::where('student_code', $request->student_code)->first();

        if (!$code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid student code.',
            ], 404);
        }

        if ($code->used) {
            return response()->json([
                'success' => false,
                'message' => 'This code has already been used.',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'student' => [
                'first_name' => $code->first_name,
                'last_name' => $code->last_name,
                'middle_name' => $code->middle_name,
                'full_name' => $code->full_name,
                'username' => $code->username,
            ],
        ]);
    }

    public function validateStudentStepOne(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'registration_code' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $code = RegistrationCode::where('code', $value)->first();

                    if (!$code) {
                        return $fail('The registration code is invalid.');
                    }

                    if ($code->used) {
                        return $fail('This registration code has already been used.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        return response()->json(['success' => true]);
    }

    public function registerTeacher(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        if ($validator->fails()) {
            // If request is AJAX (Accept: application/json), return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            // Fallback: normal redirect
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('show_form', 'teacher_register');
        }

        $defaultProfilePic = 'default-pp.png';

        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => bcrypt($request->password),
            'role'        => 'teacher',
            'profile_pic' => $defaultProfilePic,
            'status'      => 'pending',
        ]);

        $message = 'Registration submitted. Awaiting admin approval.';

        // AJAX success response
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        // Fallback: normal redirect
        return redirect('/')
            ->with('show_form', 'teacher_register')
            ->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            $message = __($status);

            // AJAX / JSON request
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            }

            // Fallback: classic redirect
            return back()
                ->with(['status' => $message])
                ->with('show_form', 'forgot');
        }

        // Failure (user not found, etc.)
        $errorMessage = __($status);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'errors'  => [
                    'email' => [$errorMessage],
                ],
            ], 422);
        }

        return back()
            ->withErrors(['email' => $errorMessage])
            ->with('show_form', 'forgot');
    }

    public function showResetForm($token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        // AJAX validation failure
        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput();
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        // AJAX success or failure
        if ($request->expectsJson()) {
            if ($status === Password::PASSWORD_RESET) {
                return response()->json([
                    'success' => true,
                    'status'  => 'passwords.reset',
                    'message' => __($status),
                ]);
            }

            return response()->json([
                'success' => false,
                'errors'  => [
                    'email' => [__($status)],
                ],
            ], 422);
        }

        // Normal (non-AJAX) fallback
        return $status === Password::PASSWORD_RESET
            ? redirect('/')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}

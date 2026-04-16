<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\RegistrationCode;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showHome()
    {
        $registrationGrades = Grade::query()
            ->with(['sections' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('welcome', compact('registrationGrades'));
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
        $registrationGrades = Grade::query()
            ->with(['sections' => fn ($q) => $q->orderBy('name')])
            ->orderBy('name')
            ->get();

        return view('auth.register', compact('registrationGrades'));
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
                    Log::notice('auth.login.blocked', array_merge([
                        'reason' => 'teacher_pending_approval',
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ], $this->authRequestMeta($request)));
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
                    Log::notice('auth.login.blocked', array_merge([
                        'reason' => 'teacher_rejected',
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ], $this->authRequestMeta($request)));
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
                    Log::notice('auth.login.blocked', array_merge([
                        'reason' => 'student_pending_approval',
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ], $this->authRequestMeta($request)));
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
                    Log::notice('auth.login.blocked', array_merge([
                        'reason' => 'student_rejected',
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ], $this->authRequestMeta($request)));
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
                Log::warning('auth.login.blocked', array_merge([
                    'reason' => 'unknown_role',
                    'role' => $user->role,
                    'user_id' => $user->id,
                    'email' => $user->email,
                ], $this->authRequestMeta($request)));
                Auth::logout();
                $redirectUrl = null;
            }

            if ($redirectUrl === null) {
                Log::warning('auth.login.failed_after_attempt', array_merge([
                    'email' => $credentials['email'] ?? null,
                ], $this->authRequestMeta($request)));

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'errors'  => ['credentials' => ['Invalid credentials']],
                    ], 422);
                }

                return back()
                    ->with('error', 'Invalid credentials')
                    ->with('show_form', 'login')
                    ->withInput();
            }

            Log::info('auth.login.success', array_merge([
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ], $this->authRequestMeta($request)));

            if ($request->expectsJson()) {
                return response()->json([
                    'success'  => true,
                    'redirect' => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl);
        }

        // Invalid credentials
        Log::warning('auth.login.failed_invalid_credentials', array_merge([
            'email' => $credentials['email'] ?? null,
        ], $this->authRequestMeta($request)));

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
            Log::notice('auth.student.register.validation_failed', array_merge([
                'errors' => $validator->errors()->toArray(),
                'student_code_prefix' => $this->maskStudentCode($request->input('student_code')),
            ], $this->authRequestMeta($request)));

            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('show_form', 'register');
        }

        // Get the registration code record
        $registrationCode = RegistrationCode::where('student_code', $request->student_code)->first();

        // Verify default password
        if ($request->default_password !== $registrationCode->default_password) {
            Log::notice('auth.student.register.wrong_default_password', array_merge([
                'registration_code_id' => $registrationCode->id,
                'student_code_prefix' => $this->maskStudentCode($request->student_code),
            ], $this->authRequestMeta($request)));

            return back()
                ->withErrors(['default_password' => 'The default password is incorrect.'])
                ->withInput()
                ->with('show_form', 'register');
        }

        $request->validate([
            'grade_id' => 'required|integer|exists:grades,id',
            'section_id' => 'required|integer|exists:sections,id',
        ]);

        $gradeId = (int) $request->input('grade_id');
        $sectionId = (int) $request->input('section_id');

        $schoolSection = Section::query()
            ->where('id', $sectionId)
            ->where('grade_id', $gradeId)
            ->first();

        if (!$schoolSection) {
            return back()
                ->withErrors(['section_id' => 'Choose a section that belongs to the selected grade.'])
                ->withInput()
                ->with('show_form', 'register');
        }

        $profilePic = $this->getProfilePicForGender($request->gender . '_' . $request->character);

        // Determine final password
        $finalPassword = $request->new_password ?? $request->default_password;

        try {
            // Create the user with character stats
            $user = User::create([
                'name'        => $registrationCode->full_name,
                'first_name'  => $registrationCode->first_name,
                'last_name'   => $registrationCode->last_name,
                'middle_name' => $registrationCode->middle_name,
                'grade_id'    => $gradeId,
                'section_id'  => $sectionId,
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
                'grade_id' => $gradeId,
                'section_id' => $sectionId,
            ]);
        } catch (\Throwable $e) {
            report($e);
            Log::error('auth.student.register.exception', array_merge([
                'exception' => $e->getMessage(),
                'student_code_prefix' => $this->maskStudentCode($request->input('student_code')),
            ], $this->authRequestMeta($request)));

            $msg = config('app.debug')
                ? $e->getMessage()
                : 'Registration could not be completed. Please try again or contact your teacher.';

            return back()
                ->with('error', $msg)
                ->withInput()
                ->with('show_form', 'register');
        }

        Log::info('auth.student.register.success', array_merge([
            'user_id' => $user->id,
            'email' => $user->email,
            'student_code_prefix' => $this->maskStudentCode($request->student_code),
            'character' => $request->character,
            'gender' => $request->gender,
        ], $this->authRequestMeta($request)));

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

        try {
            $code = RegistrationCode::where('student_code', $request->student_code)->first();

            if (!$code) {
                Log::info('auth.student.code.invalid', array_merge([
                    'student_code_prefix' => $this->maskStudentCode($request->student_code),
                ], $this->authRequestMeta($request)));

                return response()->json([
                    'success' => false,
                    'message' => 'Invalid student code.',
                ], 404);
            }

            if ($code->used) {
                Log::notice('auth.student.code.already_used', array_merge([
                    'student_code_prefix' => $this->maskStudentCode($request->student_code),
                    'registration_code_id' => $code->id,
                ], $this->authRequestMeta($request)));

                return response()->json([
                    'success' => false,
                    'message' => 'This code has already been used.',
                ], 400);
            }

            Log::info('auth.student.code.validated', array_merge([
                'registration_code_id' => $code->id,
                'student_code_prefix' => $this->maskStudentCode($request->student_code),
            ], $this->authRequestMeta($request)));

            return response()->json([
                'success' => true,
                'student' => [
                    'first_name' => $code->first_name,
                    'last_name' => $code->last_name,
                    'middle_name' => $code->middle_name,
                    'full_name' => $code->full_name,
                    'username' => $code->username,
                    'grade_id' => $code->grade_id,
                    'section_id' => $code->section_id,
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);
            Log::error('auth.student.code.exception', array_merge([
                'exception' => $e->getMessage(),
                'student_code_prefix' => $this->maskStudentCode($request->input('student_code')),
            ], $this->authRequestMeta($request)));

            return response()->json([
                'success' => false,
                'message' => config('app.debug')
                    ? $e->getMessage()
                    : 'Unable to validate the code right now. Please try again.',
            ], 500);
        }
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
            Log::notice('auth.teacher.register.validation_failed', array_merge([
                'errors' => $validator->errors()->toArray(),
                'email' => $request->input('email'),
            ], $this->authRequestMeta($request)));

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

        try {
            $user = User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => bcrypt($request->password),
                'role'        => 'teacher',
                'profile_pic' => $defaultProfilePic,
                'status'      => 'pending',
            ]);
        } catch (\Throwable $e) {
            report($e);
            Log::error('auth.teacher.register.exception', array_merge([
                'exception' => $e->getMessage(),
                'email' => $request->input('email'),
            ], $this->authRequestMeta($request)));

            $msg = config('app.debug')
                ? $e->getMessage()
                : 'Registration could not be completed. Please try again or contact support.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 500);
            }

            return back()
                ->with('error', $msg)
                ->withInput()
                ->with('show_form', 'teacher_register');
        }

        Log::info('auth.teacher.register.success', array_merge([
            'user_id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ], $this->authRequestMeta($request)));

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

            Log::info('auth.password.forgot.link_sent', array_merge([
                'email' => $request->input('email'),
            ], $this->authRequestMeta($request)));

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

        Log::notice('auth.password.forgot.failed', array_merge([
            'email' => $request->input('email'),
            'status' => $status,
        ], $this->authRequestMeta($request)));

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
                Log::info('auth.password.reset.success', array_merge([
                    'email' => $request->input('email'),
                ], $this->authRequestMeta($request)));

                return response()->json([
                    'success' => true,
                    'status'  => 'passwords.reset',
                    'message' => __($status),
                ]);
            }

            Log::notice('auth.password.reset.failed', array_merge([
                'email' => $request->input('email'),
                'status' => $status,
            ], $this->authRequestMeta($request)));

            return response()->json([
                'success' => false,
                'errors'  => [
                    'email' => [__($status)],
                ],
            ], 422);
        }

        // Normal (non-AJAX) fallback
        if ($status === Password::PASSWORD_RESET) {
            Log::info('auth.password.reset.success', array_merge([
                'email' => $request->input('email'),
            ], $this->authRequestMeta($request)));

            return redirect('/')->with('status', __($status));
        }

        Log::notice('auth.password.reset.failed', array_merge([
            'email' => $request->input('email'),
            'status' => $status,
        ], $this->authRequestMeta($request)));

        return back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * @return array<string, mixed>
     */
    private function authRequestMeta(Request $request): array
    {
        return array_filter([
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent() ? Str::limit($request->userAgent(), 200) : null,
        ]);
    }

    private function maskStudentCode(?string $code): ?string
    {
        if ($code === null || $code === '') {
            return null;
        }

        $code = strtoupper(trim($code));

        return Str::limit($code, 4, '…');
    }
}

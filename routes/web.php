<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // ✅ Added
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;

use App\Http\Controllers\TeacherLessonController;
use App\Http\Controllers\StudentLessonController;
use App\Http\Controllers\AdminLessonController;

use App\Http\Controllers\TeacherQuizController;
use App\Http\Controllers\StudentQuizController;
use App\Http\Controllers\AdminQuizController;

use App\Http\Controllers\StudentGamificationController;
use App\Http\Controllers\TeacherGamificationController;
use App\Http\Controllers\AdminGamificationController;
//new
use App\Http\Controllers\TeacherRegistrationController;
use App\Http\Controllers\TeacherQuestController;
use App\Http\Controllers\StudentQuestController;
use App\Http\Controllers\TargetAudienceController;
use App\Http\Controllers\StudentMessagesController;
use App\Http\Controllers\StudentMessageController;
use App\Http\Controllers\AdminUserManagementController;
use App\Http\Controllers\AIAssistantController;


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [AuthController::class, 'showHome'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Dashboard and Role-based Routes
|--------------------------------------------------------------------------
*/

// Group routes for student dashboard
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/registration', [StudentController::class, 'registration'])->name('registration');
    Route::get('/lessons', [StudentController::class, 'lessons'])->name('lessons');
    Route::get('/gamification', [StudentController::class, 'gamification'])->name('gamification');
    Route::get('/ai-support', [StudentController::class, 'aiSupport'])->name('ai-support');
    Route::get('/performance', [StudentController::class, 'performance'])->name('performance');
    Route::get('/feedback', [StudentController::class, 'feedback'])->name('feedback');
    Route::get('/motivation', [StudentController::class, 'motivation'])->name('motivation');
});

// Teacher routes
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    Route::get('/registration', [TeacherController::class, 'registration'])->name('registration');
    Route::get('/lessons', [TeacherController::class, 'lessons'])->name('lessons');
    Route::get('/quizzes', [TeacherController::class, 'quizzes'])->name('quizzes');
    Route::get('/gamification', [TeacherController::class, 'gamification'])->name('gamification');
    Route::get('/ai-track', [TeacherController::class, 'aiTrack'])->name('ai-track');
    Route::get('/performance', [TeacherController::class, 'performance'])->name('performance');
    Route::get('/feedback', [TeacherController::class, 'feedback'])->name('feedback');
    Route::get('/reports', [TeacherController::class, 'reports'])->name('reports');
    Route::get('/content-review', [TeacherController::class, 'contentReview'])->name('content-review');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/user-management', [AdminUserManagementController::class, 'index'])->name('user-management');
    Route::get('/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
    Route::get('/gamification', [AdminController::class, 'gamification'])->name('gamification');
    Route::get('/ai-management', [AdminController::class, 'aiManagement'])->name('ai-management');
    Route::get('/data', [AdminController::class, 'data'])->name('data');
    Route::get('/security', [AdminController::class, 'security'])->name('security');
    // show
    Route::get('/user-management/{user}', [AdminUserManagementController::class, 'show'])
        ->name('user-management.show');

    // edit
    Route::get('/user-management/{user}/edit', [AdminUserManagementController::class, 'edit'])
        ->name('user-management.edit');

    // update
    Route::put('/user-management/{user}', [AdminUserManagementController::class, 'update'])
        ->name('user-management.update');

    // delete
    Route::delete('/user-management/{user}', [AdminUserManagementController::class, 'destroy'])
        ->name('user-management.destroy');
});

/*
|--------------------------------------------------------------------------
| Lesson & Module Access
|--------------------------------------------------------------------------
*/

// TEACHER LESSON ROUTES
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/lessons', [TeacherLessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/create', [TeacherLessonController::class, 'create'])->name('lessons.create');
    Route::post('/lessons/store', [TeacherLessonController::class, 'store'])->name('lessons.store');
    Route::get('/lessons/{id}/edit', [TeacherLessonController::class, 'edit'])->name('lessons.edit');
    Route::post('/lessons/{id}/update', [TeacherLessonController::class, 'update'])->name('lessons.update');
    Route::delete('/lessons/{id}', [TeacherLessonController::class, 'destroy'])->name('lessons.destroy');
});
Route::get('/download/{id}', [TeacherLessonController::class, 'download'])->name('teacher.lessons.download');

// STUDENT LESSON ROUTES
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/lessons', [StudentLessonController::class, 'index'])->name('lessons');
    Route::get('/lessons/download/{id}', [StudentLessonController::class, 'download'])->name('lessons.download');
});

// ADMIN LESSON ROUTES
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
    Route::patch('/lessons/approve/{id}', [AdminLessonController::class, 'approve'])->name('lessons.approve');
    Route::patch('/lessons/reject/{id}', [AdminLessonController::class, 'reject'])->name('lessons.reject');
});

/*
|--------------------------------------------------------------------------
| Quiz Routes
|--------------------------------------------------------------------------
*/

// TEACHER
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/quizzes', [TeacherQuizController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/create', [TeacherQuizController::class, 'create'])->name('quizzes.create');
    Route::post('/quizzes/store', [TeacherQuizController::class, 'store'])->name('quizzes.store');
    Route::get('/quizzes/{quiz}/edit', [TeacherQuizController::class, 'edit'])->name('quizzes.edit');
    Route::put('/quizzes/{quiz}', [TeacherQuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [TeacherQuizController::class, 'destroy'])->name('quizzes.destroy');
});
//new
// Teacher Student Registration
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/registration', [TeacherRegistrationController::class, 'index'])->name('registration');
    Route::get('/registration/generate-code', [TeacherRegistrationController::class, 'generateCode'])->name('registration.generate-code');
    // Students management
Route::get('/students/{student}/edit', [TeacherRegistrationController::class, 'edit'])->name('student.edit');
Route::delete('/students/{student}', [TeacherRegistrationController::class, 'destroy'])->name('student.delete');
});


//new


// STUDENT
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/take/{id}', [StudentQuizController::class, 'take'])->name('quizzes.take');
    Route::post('/quizzes/submit/{id}', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
});

// AI ASSISTANT ROUTES
Route::prefix('teacher/ai')->name('teacher.ai.')->group(function () {
    Route::post('/generate-quest', [AIAssistantController::class, 'generateQuest'])->name('generate-quest');
    Route::post('/generate-question', [AIAssistantController::class, 'generateQuestion'])->name('generate-question');
});

Route::prefix('student/ai')->name('student.ai.')->group(function () {
    Route::post('/chat', [AIAssistantController::class, 'studentChat'])->name('chat');
});

// ADMIN
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/quizzes', [AdminQuizController::class, 'index'])->name('quizzes');
    Route::post('/quizzes/{id}/approve', [AdminQuizController::class, 'approve'])->name('quizzes.approve');
    Route::post('/quizzes/{id}/reject', [AdminQuizController::class, 'reject'])->name('quizzes.reject');
});

/*
|--------------------------------------------------------------------------
| Gamification Routes
|--------------------------------------------------------------------------
*/

// STUDENT
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/gamification', [StudentGamificationController::class, 'index'])->name('gamification');
});

// TEACHER
Route::prefix('teacher/gamification')->name('teacher.gamification.')->group(function () {
    Route::get('/', [TeacherGamificationController::class, 'index'])->name('index');
    Route::get('/create', [TeacherGamificationController::class, 'create'])->name('create');
    Route::post('/', [TeacherGamificationController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [TeacherGamificationController::class, 'edit'])->name('edit');
    Route::put('/{id}', [TeacherGamificationController::class, 'update'])->name('update');
    Route::delete('/{id}', [TeacherGamificationController::class, 'destroy'])->name('destroy');
});

// ADMIN
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/gamification', [AdminGamificationController::class, 'index'])->name('gamification');
    Route::post('/gamification/update', [AdminGamificationController::class, 'update'])->name('gamification.update');
});

/*
|--------------------------------------------------------------------------
| Password Reset (Fix for RouteNotFoundException)
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', function () {
    return view('auth.forgot-password'); // Create this view later
})->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

// Show the Reset Password form
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');

// Handle form submission to actually reset password
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::post('/register/student', [AuthController::class, 'registerStudent'])->name('register.student');
Route::post('/register/teacher', [AuthController::class, 'registerTeacher'])->name('register.teacher');
Route::post('/register/student/validate', [AuthController::class, 'validateStudentStepOne'])
    ->name('register.student.validate');

Route::get('/profile', [UserController::class, 'showProfile'])->name('profile');

Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/quest', [TeacherQuestController::class, 'index'])->name('quest');
    Route::get('/quest/create', [TeacherQuestController::class, 'create'])->name('quest.create');
    Route::post('/quest', [TeacherQuestController::class, 'store'])->name('quest.store');
    Route::get('/quest/{quest}', [TeacherQuestController::class, 'show'])->name('quest.show');
});

Route::prefix('student')->name('student.')->group(function () {
    Route::get('/quest', [StudentQuestController::class, 'index'])->name('quest');
    Route::get('/quest/{quest}', [StudentQuestController::class, 'show'])->name('quest.show');
    Route::post('/quest/{quest}/start', [StudentQuestController::class, 'start'])->name('quest.start');
    Route::get('/quest/{quest}/play/{question?}', [StudentQuestController::class, 'play'])->name('quest.play');
    Route::post('/quest/{quest}/submit/{question}', [StudentQuestController::class, 'submitStep'])->name('quest.submit');
});

Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])
        ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');

// ADMIN TARGET AUDIENCE ROUTES
Route::prefix('admin')->name('admin.')->group(function () {

    Route::prefix('target-audience')->group(function () {

        Route::get('/', [TargetAudienceController::class, 'index'])
            ->name('target-audience');

        // ADD
        Route::post('/grade', [TargetAudienceController::class, 'storeGrade'])
            ->name('target-audience.grade.store');

        Route::post('/section', [TargetAudienceController::class, 'storeSection'])
            ->name('target-audience.section.store');

        // EDIT
        Route::put('/grade/{id}', [TargetAudienceController::class, 'updateGrade'])
            ->name('target-audience.grade.update');

        Route::put('/section/{id}', [TargetAudienceController::class, 'updateSection'])
            ->name('target-audience.section.update');

        // DELETE
        Route::delete('/grade/{id}', [TargetAudienceController::class, 'deleteGrade'])
            ->name('target-audience.grade.delete');

        Route::delete('/section/{id}', [TargetAudienceController::class, 'deleteSection'])
            ->name('target-audience.section.delete');
    });

});

Route::prefix('student')->name('student.')->group(function () {

    Route::get('/messages', [StudentMessagesController::class, 'index'])
        ->name('messages');

    Route::post('/messages/start', [StudentMessagesController::class, 'start'])
        ->name('messages.start');

    Route::post('/messages/{conversation}/send', [StudentMessagesController::class, 'send'])
        ->name('messages.send');

    Route::delete('/messages/{conversation}', [StudentMessagesController::class, 'destroy'])
        ->name('messages.destroy');

});


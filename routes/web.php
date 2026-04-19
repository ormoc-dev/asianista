<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; // ✅ Added
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;

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
use App\Http\Controllers\TeacherMessagesController;
use App\Http\Controllers\AdminUserManagementController;
use App\Http\Controllers\AIAssistantController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\TeacherReportsController;


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

Route::middleware(['auth'])->group(function () {
    // Group routes for student dashboard
    Route::prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', \App\Http\Livewire\Student\Dashboard::class)->name('dashboard');
        Route::get('/registration', [StudentController::class, 'registration'])->name('registration');
        Route::get('/gamification', [StudentController::class, 'gamification'])->name('gamification');
        Route::get('/ai-support', [StudentController::class, 'aiSupport'])->name('ai-support');
        Route::get('/performance', [StudentController::class, 'performance'])->name('performance');
        Route::get('/feedback', [StudentController::class, 'feedback'])->name('feedback');
        Route::get('/motivation', [StudentController::class, 'motivation'])->name('motivation');

        Route::get('/messages', [StudentMessagesController::class, 'index'])->name('messages');
        Route::get('/messages/poll', [StudentMessagesController::class, 'poll'])->name('messages.poll');
        Route::get('/messages/thread/{conversation}', [StudentMessagesController::class, 'thread'])->name('messages.thread');
        Route::post('/messages/start', [StudentMessagesController::class, 'start'])->name('messages.start');
        Route::post('/messages/{conversation}/send', [StudentMessagesController::class, 'send'])->name('messages.send');
        Route::delete('/messages/{conversation}', [StudentMessagesController::class, 'destroy'])->name('messages.destroy');
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
        Route::post('/feedback/send', [TeacherController::class, 'sendFeedback'])->name('feedback.send');
        Route::get('/reports/student/{id}', [TeacherReportsController::class, 'studentDetail'])->name('reports.student');
        // Route::get('/reports', [TeacherController::class, 'reports'])->name('reports'); // Replaced by TeacherReportsController
        Route::get('/content-review', [TeacherController::class, 'contentReview'])->name('content-review');
        Route::get('/ai-support', [TeacherController::class, 'aiSupport'])->name('ai-support');

        Route::get('/messages', [TeacherMessagesController::class, 'index'])->name('messages');
        Route::get('/messages/poll', [TeacherMessagesController::class, 'poll'])->name('messages.poll');
        Route::get('/messages/thread/{conversation}', [TeacherMessagesController::class, 'thread'])->name('messages.thread');
        Route::post('/messages/start', [TeacherMessagesController::class, 'start'])->name('messages.start');
        Route::post('/messages/{conversation}/send', [TeacherMessagesController::class, 'send'])->name('messages.send');
        Route::delete('/messages/{conversation}', [TeacherMessagesController::class, 'destroy'])->name('messages.destroy');
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/user-management', [AdminUserManagementController::class, 'index'])->name('user-management');
        Route::get('/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
        Route::get('/lessons/{lesson}', [AdminLessonController::class, 'show'])->name('lessons.show');
        Route::get('/gamification', [AdminController::class, 'gamification'])->name('gamification');
        Route::get('/ai-management', [AdminController::class, 'aiManagement'])->name('ai-management');
        Route::get('/data', [AdminController::class, 'data'])->name('data');
        Route::get('/security', [AdminController::class, 'security'])->name('security');
        
        // Random Events Management (Admin Only)
        Route::get('/random-events', [App\Http\Controllers\AdminRandomEventController::class, 'index'])->name('random-events.index');
        Route::get('/random-events/create', [App\Http\Controllers\AdminRandomEventController::class, 'create'])->name('random-events.create');
        Route::post('/random-events', [App\Http\Controllers\AdminRandomEventController::class, 'store'])->name('random-events.store');
        Route::get('/random-events/{randomEvent}', [App\Http\Controllers\AdminRandomEventController::class, 'show'])->name('random-events.show');
        Route::get('/random-events/{randomEvent}/edit', [App\Http\Controllers\AdminRandomEventController::class, 'edit'])->name('random-events.edit');
        Route::put('/random-events/{randomEvent}', [App\Http\Controllers\AdminRandomEventController::class, 'update'])->name('random-events.update');
        Route::delete('/random-events/{randomEvent}', [App\Http\Controllers\AdminRandomEventController::class, 'destroy'])->name('random-events.destroy');
        Route::patch('/random-events/{randomEvent}/toggle', [App\Http\Controllers\AdminRandomEventController::class, 'toggleActive'])->name('random-events.toggle');
        Route::post('/random-events/generate-ai', [AIAssistantController::class, 'generateRandomEvent'])->name('random-events.generate-ai');
        
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
    Route::get('/lessons/{lesson}/download', [TeacherLessonController::class, 'download'])->name('lessons.download');
});

// STUDENT LESSON ROUTES
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/lessons', [StudentLessonController::class, 'index'])->name('lessons');
    Route::get('/lessons/{id}', [StudentLessonController::class, 'show'])->name('lessons.show');
    Route::get('/lessons/download/{id}', [StudentLessonController::class, 'download'])->name('lessons.download');
});

// ADMIN LESSON ROUTES
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/lessons', [AdminLessonController::class, 'index'])->name('lessons.index');
    Route::get('/lessons/{lesson}', [AdminLessonController::class, 'show'])->name('lessons.show');
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
    Route::get('/quizzes/{quiz}/scores', [TeacherQuizController::class, 'scores'])->name('quizzes.scores');
    Route::put('/quizzes/{quiz}', [TeacherQuizController::class, 'update'])->name('quizzes.update');
    Route::delete('/quizzes/{quiz}', [TeacherQuizController::class, 'destroy'])->name('quizzes.destroy');
});
//new
// Teacher Student Registration
Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/registration', [TeacherRegistrationController::class, 'index'])->name('registration');
    Route::get('/registration/generate-code', [TeacherRegistrationController::class, 'generateCode'])->name('registration.generate-code');
    // Opening /upload in the browser is a GET; uploads must use the form (POST). Avoid a blank error page.
    Route::get('/registration/upload', function () {
        return redirect()
            ->route('teacher.registration')
            ->with('info', 'Upload only works when you pick a file on the Registration page and click Upload. This address cannot be opened directly in the address bar.');
    })->name('registration.upload.get');
    Route::post('/registration/upload', [TeacherRegistrationController::class, 'uploadExcel'])->name('registration.upload');
    Route::get('/registration/template', [TeacherRegistrationController::class, 'downloadTemplate'])->name('registration.template');
    Route::post('/registration/{id}/regenerate', [TeacherRegistrationController::class, 'regenerateCredentials'])->name('registration.regenerate');
    Route::post('/students/{id}/approve', [TeacherRegistrationController::class, 'approveStudent'])->name('students.approve');
    Route::post('/students/approve/bulk', [TeacherRegistrationController::class, 'bulkApproveStudents'])->name('students.approve.bulk');
    Route::get('/students/approved', [TeacherRegistrationController::class, 'approvedStudents'])->name('students.approved');
    Route::delete('/registration/pending/{id}', [TeacherRegistrationController::class, 'destroyPending'])->name('registration.destroy-pending');
    // Students management
    Route::get('/students/{student}/edit', [TeacherRegistrationController::class, 'edit'])->name('student.edit');
    Route::delete('/students/{student}', [TeacherRegistrationController::class, 'destroy'])->name('student.delete');
});


//new


// STUDENT
Route::prefix('student')->name('student.')->group(function () {
    Route::get('/quizzes', [StudentQuizController::class, 'index'])->name('quizzes');
    Route::get('/quizzes/history', [StudentQuizController::class, 'history'])->name('quizzes.history');
    Route::get('/quizzes/take/{id}', [StudentQuizController::class, 'take'])->name('quizzes.take');
    Route::post('/quizzes/submit/{id}', [StudentQuizController::class, 'submit'])->name('quizzes.submit');
    Route::get('/quizzes/result/{id}', [StudentQuizController::class, 'result'])->name('quizzes.result');
});

// AI ASSISTANT ROUTES
Route::prefix('teacher/ai')->name('teacher.ai.')->group(function () {
    Route::post('/generate-quest', [AIAssistantController::class, 'generateQuest'])->name('generate-quest');
    Route::post('/generate-question', [AIAssistantController::class, 'generateQuestion'])->name('generate-question');
    Route::post('/generate-lesson', [AIAssistantController::class, 'generateLessonContent'])->name('generate-lesson');
    Route::post('/generate-quiz', [AIAssistantController::class, 'generateQuizQuestions'])->name('generate-quiz');
});

Route::prefix('student/ai')->name('student.ai.')->group(function () {
    Route::post('/chat', [AIAssistantController::class, 'studentChat'])->name('chat');
});

Route::prefix('teacher/ai')->name('teacher.ai.')->group(function () {
    Route::post('/chat', [AIAssistantController::class, 'teacherChat'])->name('chat');
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
Route::post('/register/validate-code', [AuthController::class, 'validateStudentCode'])
    ->name('register.validate-code');

// Route::get('/profile', [UserController::class, 'showProfile'])->name('profile'); // TODO: Create UserController

Route::prefix('teacher')->name('teacher.')->group(function () {
    Route::get('/quest/clone-library', [TeacherQuestController::class, 'cloneLibrary'])->name('quest.clone-library');
    Route::post('/quest/{quest}/clone', [TeacherQuestController::class, 'cloneQuest'])->name('quest.clone');
    Route::get('/quest', [TeacherQuestController::class, 'index'])->name('quest');
    Route::get('/quest/create', [TeacherQuestController::class, 'create'])->name('quest.create');
    Route::post('/quest', [TeacherQuestController::class, 'store'])->name('quest.store');
    Route::get('/quest/{quest}/edit', [TeacherQuestController::class, 'edit'])->name('quest.edit');
    Route::put('/quest/{quest}', [TeacherQuestController::class, 'update'])->name('quest.update');
    Route::get('/quest/{quest}', [TeacherQuestController::class, 'show'])->name('quest.show');
    
    // Random Events (draw only; pool is managed in admin)
    Route::get('/random-events', [App\Http\Controllers\TeacherRandomEventController::class, 'index'])->name('random-events.index');
    Route::post('/random-events/draw', [App\Http\Controllers\TeacherRandomEventController::class, 'drawRandom'])->name('random-events.draw');
    
    // Reports Routes
    Route::get('/reports/scores', [App\Http\Controllers\TeacherReportsController::class, 'scores'])->name('reports.scores');
    Route::get('/reports/student/{student}', [App\Http\Controllers\TeacherReportsController::class, 'studentDetail'])->name('reports.student');
    Route::put('/reports/student/{student}/xp', [App\Http\Controllers\TeacherReportsController::class, 'updateXp'])->name('reports.student.xp');
});

Route::prefix('student')->name('student.')->group(function () {
    Route::get('/quest', [StudentQuestController::class, 'index'])->name('quest');
    Route::get('/quest/{quest}', [StudentQuestController::class, 'show'])->name('quest.show');
    Route::post('/quest/{quest}/start', [StudentQuestController::class, 'start'])->name('quest.start');
    Route::get('/quest/{quest}/play/{question?}', [StudentQuestController::class, 'play'])->name('quest.play');
    Route::post('/quest/{quest}/submit/{question}', [StudentQuestController::class, 'submitStep'])->name('quest.submit');
    Route::post('/quest/{quest}/use-power/{attempt}', [StudentQuestController::class, 'usePower'])->name('quest.use-power');
    Route::post('/quest/{quest}/timeout/{question}', [StudentQuestController::class, 'timeOut'])->name('quest.timeout');
    
    // Random Events - Student endpoints
    Route::get('/events/check', [App\Http\Controllers\StudentEventController::class, 'checkNewEvent'])->name('events.check');
    Route::post('/events/acknowledge', [App\Http\Controllers\StudentEventController::class, 'acknowledgeEvent'])->name('events.acknowledge');
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



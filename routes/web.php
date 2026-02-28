<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\CourseController as PublicCourseController;
use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\PublicChatbotController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\StorageController;

// Serve uploaded files from storage. Use /files/... so the request always hits Laravel
// (no public/files folder), fixing broken images on admin when public/storage junction fails.
Route::get('files/{path}', [StorageController::class, 'show'])->where('path', '.*')->name('storage.serve');

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/courses', [PublicCourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{id}', [PublicCourseController::class, 'show'])->name('courses.show');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
Route::get('/sitemap.xml', [\App\Http\Controllers\Public\SitemapController::class, 'index'])->name('sitemap');
Route::view('/privacy-policy', 'public.privacy')->name('privacy');
Route::view('/return-refund-policy', 'public.refund')->name('refund');
Route::view('/shipping-service-policy', 'public.shipping')->name('shipping');
Route::view('/terms-conditions', 'public.terms')->name('terms');
Route::view('/faqs', 'public.faqs')->name('faqs');

// Public Chatbot Routes
Route::post('/chatbot/send', [PublicChatbotController::class, 'sendMessage'])->name('public.chatbot.send');

// Authentication Routes
Route::get('/login', [\App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'login'])
    ->name('login.post');

Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])
    ->name('register')
    ->middleware('guest');

Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'register'])
    ->name('register.post');

Route::post('/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Payment webhook (no auth required, but should be secured with signature verification)
Route::post('/payments/webhook', [\App\Http\Controllers\Student\PaymentController::class, 'webhook'])
    ->name('payments.webhook');

// Password Reset Routes
Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordController::class, 'showForgotPasswordForm'])
    ->name('password.request')
    ->middleware('guest');

Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordController::class, 'sendResetLink'])
    ->name('password.email')
    ->middleware('guest');

Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordController::class, 'showResetForm'])
    ->name('password.reset')
    ->middleware('guest');

Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordController::class, 'reset'])
    ->name('password.update')
    ->middleware('guest');

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin', 'device'])->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Admin\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Admin\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::match(['put', 'post'], '/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');

    // Payment Methods routes
    Route::prefix('settings/payment-methods')->name('settings.payment-methods.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\PaymentMethodController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Languages routes
    Route::prefix('settings/languages')->name('settings.languages.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\LanguageController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\LanguageController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\LanguageController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\LanguageController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\LanguageController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\LanguageController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/set-default', [\App\Http\Controllers\Admin\LanguageController::class, 'setDefault'])->name('set-default');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\LanguageController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Resource routes
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class); // Admins only
    Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class);
    Route::resource('students', \App\Http\Controllers\Admin\StudentController::class);
    Route::post('students/bulk/send-email', [\App\Http\Controllers\Admin\StudentController::class, 'bulkSendEmail'])->name('students.bulk.send-email');
    Route::post('students/bulk/send-sms', [\App\Http\Controllers\Admin\StudentController::class, 'bulkSendSMS'])->name('students.bulk.send-sms');
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    Route::get('courses/subjects-by-grade', [\App\Http\Controllers\Admin\CourseController::class, 'getSubjectsByGrade'])->name('courses.subjects-by-grade');
    Route::post('courses/{id}/approve', [\App\Http\Controllers\Admin\CourseController::class, 'approve'])->name('courses.approve');
    Route::post('courses/{id}/reject', [\App\Http\Controllers\Admin\CourseController::class, 'reject'])->name('courses.reject');
    Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);
    Route::put('payments/{id}/status', [\App\Http\Controllers\Admin\PaymentController::class, 'updateStatus'])->name('payments.update-status');
    Route::get('devices', [\App\Http\Controllers\Admin\DeviceController::class, 'index'])->name('devices.index');
    Route::post('devices/{id}/reset', [\App\Http\Controllers\Admin\DeviceController::class, 'resetDevice'])->name('devices.reset');
    Route::post('devices/{id}/approve-reset', [\App\Http\Controllers\Admin\DeviceController::class, 'approveReset'])->name('devices.approve-reset');
    Route::post('devices/{id}/reject-reset', [\App\Http\Controllers\Admin\DeviceController::class, 'rejectReset'])->name('devices.reject-reset');
    Route::post('devices/{id}/block', [\App\Http\Controllers\Admin\DeviceController::class, 'blockDevice'])->name('devices.block');
    Route::post('devices/{id}/unblock', [\App\Http\Controllers\Admin\DeviceController::class, 'unblockDevice'])->name('devices.unblock');
    Route::post('devices/user/{userId}/reset', [\App\Http\Controllers\Admin\DeviceController::class, 'resetUserDevices'])->name('devices.reset-user');
    Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
});

// Teacher Routes
Route::prefix('teacher')->name('teacher.')->middleware(['auth', 'role:teacher', 'device'])->group(function () {
    Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Teacher\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Teacher\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\Teacher\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Teacher\SettingsController::class, 'update'])->name('settings.update');

    // Resource routes
    Route::resource('courses', \App\Http\Controllers\Teacher\CourseController::class);
    Route::resource('lessons', \App\Http\Controllers\Teacher\LessonController::class);

    // Chapter routes (nested under courses)
    Route::post('courses/{bookId}/chapters', [\App\Http\Controllers\Teacher\ChapterController::class, 'store'])->name('courses.chapters.store');
    Route::get('courses/{bookId}/chapters/{chapterId}', [\App\Http\Controllers\Teacher\ChapterController::class, 'show'])->name('courses.chapters.show');
    Route::put('courses/{bookId}/chapters/{chapterId}', [\App\Http\Controllers\Teacher\ChapterController::class, 'update'])->name('courses.chapters.update');
    Route::delete('courses/{bookId}/chapters/{chapterId}', [\App\Http\Controllers\Teacher\ChapterController::class, 'destroy'])->name('courses.chapters.destroy');

    // Lesson routes (nested under chapters)
    Route::post('courses/{bookId}/chapters/{chapterId}/lessons', [\App\Http\Controllers\Teacher\LessonController::class, 'store'])->name('courses.chapters.lessons.store');
    Route::put('courses/{bookId}/chapters/{chapterId}/lessons/{lessonId}', [\App\Http\Controllers\Teacher\LessonController::class, 'update'])->name('courses.chapters.lessons.update');
    Route::delete('courses/{bookId}/chapters/{chapterId}/lessons/{lessonId}', [\App\Http\Controllers\Teacher\LessonController::class, 'destroy'])->name('courses.chapters.lessons.destroy');

    // Topic routes (nested under lessons)
    Route::get('courses/{bookId}/chapters/{chapterId}/lessons/{lessonId}/topics', [\App\Http\Controllers\Teacher\TopicController::class, 'index'])->name('courses.chapters.lessons.topics.index');
    Route::post('courses/{bookId}/chapters/{chapterId}/lessons/{lessonId}/topics', [\App\Http\Controllers\Teacher\TopicController::class, 'store'])->name('courses.chapters.lessons.topics.store');
    Route::put('courses/{bookId}/chapters/{chapterId}/lessons/{lessonId}/topics/{topicId}', [\App\Http\Controllers\Teacher\TopicController::class, 'update'])->name('courses.chapters.lessons.topics.update');
    Route::delete('courses/{bookId}/chapters/{chapterId}/lessons/{lessonId}/topics/{topicId}', [\App\Http\Controllers\Teacher\TopicController::class, 'destroy'])->name('courses.chapters.lessons.topics.destroy');

    Route::get('/students', [\App\Http\Controllers\Teacher\StudentController::class, 'index'])->name('students.index');
    Route::get('/students/{id}', [\App\Http\Controllers\Teacher\StudentController::class, 'show'])->name('students.show');
    Route::get('/chatbot', [\App\Http\Controllers\Teacher\ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot', [\App\Http\Controllers\Teacher\ChatbotController::class, 'send'])->name('chatbot.send');

    // Device routes
    Route::get('/devices', [\App\Http\Controllers\Teacher\DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices/request-reset', [\App\Http\Controllers\Teacher\DeviceController::class, 'requestReset'])->name('devices.request-reset');

    // Video upload routes
    Route::get('/lessons/{lessonId}/upload-video', [\App\Http\Controllers\Teacher\VideoUploadController::class, 'showUploadForm'])->name('lessons.upload-video');
    Route::post('/lessons/{lessonId}/upload-video', [\App\Http\Controllers\Teacher\VideoUploadController::class, 'uploadLessonVideo'])->name('lessons.upload-video.post');
    Route::post('/topics/{topicId}/upload-video', [\App\Http\Controllers\Teacher\VideoUploadController::class, 'uploadTopicVideo'])->name('topics.upload-video.post');
    Route::delete('/videos/{type}/{id}', [\App\Http\Controllers\Teacher\VideoUploadController::class, 'deleteVideo'])->name('videos.delete');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'role:student', 'device'])->group(function () {
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [\App\Http\Controllers\Student\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\Student\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\Student\ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Settings routes
    Route::get('/settings', [\App\Http\Controllers\Student\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [\App\Http\Controllers\Student\SettingsController::class, 'update'])->name('settings.update');

    // Course routes
    Route::get('/courses', [\App\Http\Controllers\Student\CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/my', [\App\Http\Controllers\Student\CourseController::class, 'myCourses'])->name('courses.my');
    Route::get('/courses/{id}', [\App\Http\Controllers\Student\CourseController::class, 'show'])->name('courses.show');
    Route::post('/courses/{id}/enroll', [\App\Http\Controllers\Student\CourseController::class, 'enroll'])->name('courses.enroll');

    // Learning routes
    Route::get('/learning/{bookId}', [\App\Http\Controllers\Student\LearningController::class, 'index'])
        ->middleware('content.access')
        ->name('learning.index');
    Route::get('/learning/{bookId}/lesson/{lessonId}', [\App\Http\Controllers\Student\LearningController::class, 'show'])
        ->middleware('content.access')
        ->name('learning.lesson');
    Route::get('/learning/{bookId}/lesson/{lessonId}/topic/{topicId}', [\App\Http\Controllers\Student\LearningController::class, 'showTopic'])
        ->middleware('content.access')
        ->name('learning.topic');
    Route::post('/learning/progress', [\App\Http\Controllers\Student\LearningController::class, 'updateProgress'])->name('learning.progress');

    // Payment routes
    Route::get('/payments', [\App\Http\Controllers\Student\PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [\App\Http\Controllers\Student\PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/status', [\App\Http\Controllers\Student\PaymentController::class, 'status'])->name('payments.status');
    Route::get('/payments/callback', [\App\Http\Controllers\Student\PaymentController::class, 'callback'])->name('payments.callback');
    Route::get('/payments/invoice/{paymentId}', [\App\Http\Controllers\Student\PaymentController::class, 'invoice'])->name('payments.invoice');

    // Chatbot routes
    Route::get('/chatbot', [\App\Http\Controllers\Student\ChatbotController::class, 'index'])->name('chatbot.index');
    Route::post('/chatbot', [\App\Http\Controllers\Student\ChatbotController::class, 'send'])->name('chatbot.send');

    // Device routes
    Route::get('/devices', [\App\Http\Controllers\Student\DeviceController::class, 'index'])->name('devices.index');
    Route::post('/devices/request-reset', [\App\Http\Controllers\Student\DeviceController::class, 'requestReset'])->name('devices.request-reset');
});

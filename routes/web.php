<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 2. Protected Routes (Authenticated Users)
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard ---
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Announcements ---
    Route::get('/announcements/{announcement}/pdf', [AnnouncementController::class, 'showPdf'])
        ->name('announcements.pdf');
    Route::resource('announcements', AnnouncementController::class);

  // --- Member Self-Service Profile & Agri-Resume ---
    // --- Member Self-Service Profile & Agri-Resume ---
    Route::prefix('member')->name('member.')->group(function () {
        // Profile Show & Edit
        Route::get('/profile', [MemberController::class, 'showProfile'])->name('profile.show');
        Route::get('/profile/edit', [MemberController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [MemberController::class, 'updateProfile'])->name('profile.update');

        // Agri-Resume Management (singular: member.agri-resume.*)
        Route::prefix('agri-resume')->name('agri-resume.')->group(function () {
            Route::get('/', [MemberController::class, 'showAgriResumePreview'])->name('show');
            Route::get('/preview', [MemberController::class, 'showAgriResumePreview'])->name('preview');
            Route::get('/download', [MemberController::class, 'downloadProfilePdf'])->name('download');
        });
    });

    // --- Members Directory & Management ---
    Route::prefix('members')->name('members.')->group(function () {
        // Custom sub-routes (MUST precede Route::resource)
        Route::post('/{member}/verify', [MemberController::class, 'verify'])->name('verify');
        
        // Agri-Resume routes for individual member lookup & plural alias
        Route::get('/agri-resume/preview', [MemberController::class, 'showAgriResumePreview'])->name('agri-resume.preview');
        Route::get('/agri-resume/show', [MemberController::class, 'showAgriResumePreview'])->name('agri-resume.show');
        Route::get('/{member}/agri-resume', [MemberController::class, 'showMemberAgriResume'])->name('agri-resume');
        
        Route::get('/{member}/download-id', [MemberController::class, 'downloadIdCard'])->name('download-id');
        Route::get('/{member}/file', [MemberController::class, 'downloadUploadedFile'])->name('file');
    });
    Route::resource('members', MemberController::class);

    // --- Organization Management ---
    Route::patch('/organizations/{organization}/verify', [OrganizationController::class, 'toggleVerify'])
        ->name('organizations.verify');
    Route::resource('organizations', OrganizationController::class);

    // --- Profile Management ---
    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::delete('/', 'destroy')->name('destroy');
    });

    // --- User Management (Manager Access) ---
    Route::middleware('is_manager')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/{user}/accept', [UserController::class, 'accept'])->name('users.accept');

        // Admin-Only User Edit & Deletion Actions
        Route::middleware('is_admin')->group(function () {
            Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });
});

require __DIR__ . '/auth.php';
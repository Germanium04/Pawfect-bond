<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PetLover\PetLoverController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ================================================================
// Public Routes
// ================================================================
Route::get('/', fn() => view('authentication.login'));

Route::get('/register', [UserController::class, 'showRegister'])->name('register');
Route::post('/register', [UserController::class, 'register'])->name('register.post');

Route::get('/login', [UserController::class, 'showLogin'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('login.post');

Route::post('/logout', [UserController::class, 'logout'])->name('logout');

// ================================================================
// Admin Routes
// ================================================================
Route::middleware(['auth', 'role:admin', 'prevent-back'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard',        [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/about-me',         [UserController::class,  'aboutMe'])->name('about-me');
    Route::put('/profile',          [UserController::class,  'updateProfile'])->name('profile.update');

    Route::get('/user-management',  [AdminController::class, 'userManagement'])->name('user-management');
    Route::get('/pet-listing',      [AdminController::class, 'petListing'])->name('pet-listing');
    Route::get('/reports-flags',    [AdminController::class, 'reportsAndFlags'])->name('reports-flags');
    Route::patch('/reports-flags/{id}/action', [AdminController::class, 'reportAction'])->name('report-action');

    // Notifications
    Route::post('/notifications/mark-read', [AdminController::class, 'notifications_mark_read'])->name('notifications.mark-read');
});

// ================================================================
// Pet Lover Routes
// ================================================================
Route::middleware(['auth', 'role:pet_lover', 'prevent-back'])->prefix('pet-lover')->name('petlover.')->group(function () {
    Route::get('/dashboard',    [PetLoverController::class, 'dashboard'])->name('dashboard');
    Route::get('/about-me',     [UserController::class,    'aboutMe'])->name('about-me');
    Route::put('/profile',      [UserController::class,    'updateProfile'])->name('profile.update');

    Route::get('/pet-marketplace', [PetLoverController::class, 'pet_marketplace'])->name('pet-marketplace');

    Route::get('/rehoming-center',  [PetLoverController::class, 'rehoming_center'])->name('rehoming-center');
    Route::post('/rehoming-center', [PetLoverController::class, 'post_pet'])->name('post-pet.post');

    Route::get('/adoption-tracker', [PetLoverController::class, 'adoption_tracker'])->name('adoption-tracker');

    // Adoption request actions
    Route::post('/adoption-request',              [PetLoverController::class, 'adoption_request'])->name('adoption-request');
    Route::patch('/adoption-request/{id}/accept', [PetLoverController::class, 'adoption_accept'])->name('adoption-accept');
    Route::patch('/adoption-request/{id}/decline',[PetLoverController::class, 'adoption_decline'])->name('adoption-decline');
    Route::delete('/adoption-request/{id}/cancel',[PetLoverController::class, 'adoption_cancel'])->name('adoption-cancel');

    // Community inbox
    Route::get('/community-inbox',                    [PetLoverController::class, 'community_inbox'])->name('community-inbox');
    Route::get('/community-inbox/messages/{userId}',  [PetLoverController::class, 'community_inbox_messages'])->name('community-inbox.messages');
    Route::post('/community-inbox/send',              [PetLoverController::class, 'community_inbox_send'])->name('community-inbox.send');
    Route::post('/community-inbox/read/{userId}',     [PetLoverController::class, 'community_inbox_mark_read'])->name('community-inbox.read');
    Route::post('/community-inbox/send-image',        [PetLoverController::class, 'community_inbox_send_image'])->name('community-inbox.send-image');
    Route::post('/community-inbox/archive',           [PetLoverController::class, 'community_inbox_archive'])->name('community-inbox.archive');
    Route::post('/community-inbox/block',             [PetLoverController::class, 'community_inbox_block'])->name('community-inbox.block');

    // Notifications
    Route::post('/notifications/mark-read', [PetLoverController::class, 'notifications_mark_read'])->name('notifications.mark-read');
});

// ================================================================
// Shared Authenticated Routes (both roles)
// ================================================================
Route::middleware(['auth', 'prevent-back'])->group(function () {
    // Report filing — works for both pet lovers and admins
    Route::post('/report', [ReportController::class, 'store'])->name('report.store');
});
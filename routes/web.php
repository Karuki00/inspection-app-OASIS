<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FacilityController;
use App\Http\Controllers\Admin\InspectionReportController;
use App\Http\Controllers\Admin\InspectionScheduleController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ProblemController;
use App\Http\Controllers\Admin\SecurityAccountController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LoginController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.submit');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::get('/forgot-password', [LoginController::class, 'showForgotPasswordForm'])
    ->name('password.request');

Route::get('register', function () {
    return view('auth.register');
})->name('register');

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    Route::resource('/security-accounts', SecurityAccountController::class)
        ->parameters(['security-accounts' => 'user'])
        ->except(['show']);

    Route::resource('facilities', FacilityController::class)->except(['show']);

    Route::get('/facility-categories/create', [FacilityController::class, 'createCategory'])
        ->name('facilities.categories.create');

    Route::post('/facility-categories', [FacilityController::class, 'storeCategory'])
        ->name('facilities.categories.store');

    Route::get('/facility-categories/{category}/edit', [FacilityController::class, 'editCategory'])
        ->name('facilities.categories.edit');

    Route::put('/facility-categories/{category}', [FacilityController::class, 'updateCategory'])
        ->name('facilities.categories.update');

    Route::delete('/facility-categories/{category}', [FacilityController::class, 'destroyCategory'])
        ->name('facilities.categories.destroy');

    Route::resource('inspection-schedules', InspectionScheduleController::class)
        ->except(['show']);

    Route::resource('locations', LocationController::class)
        ->except(['show']);

    Route::get('/inspection-reports', [InspectionReportController::class, 'index'])
        ->name('inspection-reports.index');

    Route::get('/inspection-reports/{inspection}/pdf', [InspectionReportController::class, 'pdf'])
        ->name('inspection-reports.pdf');

    Route::post('/inspection-reports/import', [InspectionReportController::class, 'import'])
        ->name('inspection-reports.import');

    Route::post('/inspection-reports/import-zip', [InspectionReportController::class, 'importZip'])
        ->name('inspection-reports.import-zip');

    Route::get('/problems', [ProblemController::class, 'index'])
        ->name('problems.index');
});

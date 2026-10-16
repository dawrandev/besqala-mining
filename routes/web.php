<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\AboutContentController;
use App\Http\Controllers\Admin\LeadershipController as AdminLeadershipController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\App\ApplicationController;
use App\Http\Controllers\App\CabinetController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\OversightController;
use App\Http\Controllers\App\PermitController;
use App\Http\Controllers\App\QueueController;
use App\Http\Controllers\App\ReportController;
use App\Http\Controllers\App\ResidentController;
use App\Http\Controllers\App\ReviewController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LeadershipController;
use App\Http\Controllers\Public\NewsController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\RegistryController;
use App\Http\Controllers\Public\VerifyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public site
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/leadership', [LeadershipController::class, 'index'])->name('leadership');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [NewsController::class, 'show'])->name('news.show');
Route::get('/registry', [RegistryController::class, 'index'])->name('registry');
Route::get('/verify', [VerifyController::class, 'index'])->name('verify');
Route::get('/verify/{number}/document.pdf', [DocumentController::class, 'verified'])->name('verify.pdf');

Route::post('/locale/{locale}', [LocaleController::class, 'update'])->name('locale.update');

/*
|--------------------------------------------------------------------------
| Guest authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated application
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('app')->name('app.')->group(function (): void {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Applicant / resident
    Route::middleware('role:applicant')->group(function (): void {
        Route::get('/applications', [ApplicationController::class, 'index'])->name('applications');
        Route::get('/new', [ApplicationController::class, 'create'])->name('applications.create');
        Route::post('/applications', [ApplicationController::class, 'store'])->name('applications.store');
        Route::get('/cabinet', [CabinetController::class, 'index'])->name('cabinet');
        Route::get('/cabinet/{resident}', [CabinetController::class, 'show'])->name('cabinet.show');
    });

    // Direksiya
    Route::middleware('role:direksiya')->group(function (): void {
        Route::get('/queue', [QueueController::class, 'index'])->name('queue');
        Route::get('/residents', [ResidentController::class, 'index'])->name('residents');
        Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    });

    // Expert
    Route::middleware('role:expert')->group(function (): void {
        Route::get('/review', [ReviewController::class, 'index'])->name('review');
    });

    // Authority
    Route::middleware('role:authority')->group(function (): void {
        Route::get('/permits', [PermitController::class, 'index'])->name('permits');
        Route::get('/oversight', [OversightController::class, 'index'])->name('oversight');
    });

    // Content admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function (): void {
        Route::get('/news', [AdminNewsController::class, 'index'])->name('news');
        Route::get('/news/list', [AdminNewsController::class, 'list'])->name('news.list');
        Route::get('/news/{news}/edit', [AdminNewsController::class, 'edit'])->name('news.edit');
        Route::post('/news', [AdminNewsController::class, 'store'])->name('news.store');
        Route::put('/news/{news}', [AdminNewsController::class, 'update'])->name('news.update');
        Route::delete('/news/{news}', [AdminNewsController::class, 'destroy'])->name('news.destroy');

        Route::get('/leadership', [AdminLeadershipController::class, 'index'])->name('leadership');
        Route::get('/leadership/list', [AdminLeadershipController::class, 'list'])->name('leadership.list');
        Route::get('/leadership/{leader}/edit', [AdminLeadershipController::class, 'edit'])->name('leadership.edit');
        Route::post('/leadership', [AdminLeadershipController::class, 'store'])->name('leadership.store');
        Route::put('/leadership/{leader}', [AdminLeadershipController::class, 'update'])->name('leadership.update');
        Route::delete('/leadership/{leader}', [AdminLeadershipController::class, 'destroy'])->name('leadership.destroy');

        Route::get('/about', [AboutContentController::class, 'index'])->name('about');
        Route::put('/about', [AboutContentController::class, 'update'])->name('about.update');
    });

    // Application detail is reachable by staff and the owning applicant.
    Route::get('/a/{application}', [ApplicationController::class, 'show'])->name('applications.show');
    Route::post('/a/{application}/action', [ApplicationController::class, 'action'])->name('applications.action');

    // Document PDF download (staff or owning applicant; policy-guarded).
    Route::get('/documents/{document}/pdf', [DocumentController::class, 'download'])->name('documents.download');

    // Officially issue (freeze) a draft certificate — Directorate only (policy-guarded).
    Route::post('/documents/{document}/issue', [DocumentController::class, 'issue'])->name('documents.issue');
});

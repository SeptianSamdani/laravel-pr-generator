<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PrPdfController;
use App\Livewire\Dashboard;
use App\Livewire\PrForm;
use App\Livewire\PrList;
use App\Livewire\PrDetail;
use App\Livewire\PrApproval;

/*
|--------------------------------------------------------------------------
| Guest Routes (Unauthenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    
    Route::get('/dashboard', Dashboard::class
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Purchase Requisition Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('pr')->name('pr.')->group(function () {
        
        // View PRs - All authenticated users
        Route::get('/', PrList::class)
            ->middleware('permission:pr.view')
            ->name('index');
        
        // Create PR - Staff, Admin, Super Admin
        Route::get('/create', PrForm::class)
            ->middleware('permission:pr.create')
            ->name('create');
        
        // View PR Detail
        Route::get('/{id}', PrDetail::class)
            ->middleware('permission:pr.view')
            ->name('show');
        
        // Edit PR - Staff, Admin, Super Admin
        Route::get('/{id}/edit', PrForm::class)
            ->middleware('permission:pr.edit')
            ->name('edit');

        // Download PDF
        Route::get('/{id}/pdf', [PrPdfController::class, 'download'])
            ->middleware('permission:pr.download')
            ->name('pdf');

        // Preview PDF (view di browser)
        Route::get('/{id}/preview', [PrPdfController::class, 'preview'])
            ->middleware('permission:pr.download')
            ->name('preview');

    });

    /*
    |--------------------------------------------------------------------------
    | Approval Routes (Manager, Admin, Super Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('approval')->middleware('permission:pr.approve')->name('approval.')->group(function () {
        Route::get('/', PrApproval::class)->name('index');
    });

    /*
    |--------------------------------------------------------------------------
    | Reports Routes (Manager, Admin, Super Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('reports')->middleware('permission:report.view')->name('reports.')->group(function () {
        Route::get('/', function () {
            return view('reports.index');
        })->name('index');
    });

    /*
    |--------------------------------------------------------------------------
    | Settings Routes (Admin, Super Admin)
    |--------------------------------------------------------------------------
    */
    Route::prefix('settings')->middleware('permission:settings.view')->name('settings.')->group(function () {
        Route::get('/', function () {
            return view('settings.index');
        })->name('index');
    });
});
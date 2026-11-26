<?php

use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SanctionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\InstituteController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Officer\AnnouncementController as OfficerAnnouncementController;
use App\Http\Controllers\Officer\StudentController;
use App\Http\Controllers\Officer\EventController as OfficerEventController;
use App\Http\Controllers\Officer\EventSessionController;
use App\Http\Controllers\Officer\PenaltyController as OfficerPenaltyController;
use App\Http\Controllers\Student\AttendanceController as StudentAttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('landing-page'))->name('landing');
Route::get('/main', [MainController::class, 'view'])->middleware('auth')->name('main');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::resource('sanctions', SanctionController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        \Illuminate\Support\Facades\Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        // CHANGED: use the named route instead of the hardcoded '/'
        return redirect()->route('landing')->with('success', 'You have been logged out.');
    })->name('logout');

    // Shared Event Routes (Officer, Adviser, Student)
    Route::middleware('role:Officer,Adviser,Student')->group(function () {
        Route::get('events', [EventController::class, 'index'])->name('events.index');
        Route::get('events/{event}', [EventController::class, 'show'])->name('events.show');

        // Shared Announcement Routes (Officer, Adviser, Student can read)
        Route::get('announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
        Route::get('announcements/{announcement}', [AnnouncementController::class, 'show'])->name('announcements.show');
        
        // Shared Attendance, Penalties & Reports (Officer & Adviser)
        Route::middleware('role:Officer,Adviser')->group(function () {
            Route::get('events/{event}/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('events/{event}/penalties', [PenaltyController::class, 'index'])->name('penalties.index');
            
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
            Route::get('reports/{event}', [ReportController::class, 'show'])->name('reports.show');
        });
    });
});

// Role-Specific Dashboard Routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    
    Route::resource('users', UserController::class)->except(['show']);
    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');

    Route::resource('institutes', InstituteController::class)->except(['show']);
    Route::resource('programs', ProgramController::class)->except(['show']);
});

Route::middleware(['auth', 'role:Officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'officer'])->name('dashboard');
    
    Route::resource('students', StudentController::class)->except(['show']);
    Route::resource('events', OfficerEventController::class)->except(['index', 'show']);

    // Officer Announcements: only create, store, edit, update, destroy (list and show are shared above)
    Route::resource('announcements', OfficerAnnouncementController::class)->except(['index', 'show']);

    Route::patch('sessions/{session}/toggle', [EventSessionController::class, 'toggle'])->name('sessions.toggle');

    // Officer Attendance Management
    Route::post('events/{event}/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::put('attendance/{attendance}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('attendance/{attendance}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');

    // Officer Penalty Management
    Route::post('events/{event}/penalties/generate', [OfficerPenaltyController::class, 'generate'])->name('penalties.generate');
    Route::put('penalties/{penalty}', [OfficerPenaltyController::class, 'update'])->name('penalties.update');
    Route::delete('penalties/{penalty}', [OfficerPenaltyController::class, 'destroy'])->name('penalties.destroy');
});

Route::middleware(['auth', 'role:Adviser'])->prefix('adviser')->name('adviser.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'adviser'])->name('dashboard');
});

Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');
    
    Route::post('sessions/{session}/check-in', [StudentAttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('sessions/{session}/check-out', [StudentAttendanceController::class, 'checkOut'])->name('attendance.check-out');
});
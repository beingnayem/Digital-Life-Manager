<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AccountDeletionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\MoodController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\AuditLogController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('tasks', TaskController::class)->except(['show']);
    Route::resource('expenses', ExpenseController::class)->except(['show']);
    Route::resource('notes', NoteController::class)->except(['show']);
    Route::post('/notes/{note}/toggle-pin', [NoteController::class, 'togglePin'])->name('notes.togglePin');
    Route::post('/notes/{note}/archive', [NoteController::class, 'archive'])->name('notes.archive');
    Route::resource('moods', MoodController::class)->except(['show']);
    Route::resource('budgets', BudgetController::class)->except(['show']);

    // Global search
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/profile/account-deletion/request', [AccountDeletionController::class, 'request'])
        ->name('profile.account-deletion.request');

    Route::get('/mail/test', function () {
        Mail::raw('This is a test email from Digital Life Manager SMTP configuration.', function ($message) {
            $message->to(auth()->user()->email)
                ->subject('SMTP Test Email - Digital Life Manager');
        });

        return back()->with('status', 'Test email sent successfully to '.auth()->user()->email);
    })->name('mail.test');

    // Audit logs (read-only)
    Route::resource('audit-logs', AuditLogController::class)->only(['index', 'show']);
});

Route::get('/profile/account-deletion/confirm/{deletion}', [AccountDeletionController::class, 'confirm'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('account-deletion.confirm');

require __DIR__.'/auth.php';

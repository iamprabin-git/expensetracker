<?php

use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/features', [PageController::class, 'features'])->name('features');
Route::get('/pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');

Route::middleware(['auth', 'user.panel'])->group(function () {
    Route::get('/account/pending', [AccountStatusController::class, 'pending'])->name('account.pending');
    Route::get('/account/expired', [AccountStatusController::class, 'expired'])->name('account.expired');
});

Route::middleware(['auth', 'verified', 'user.panel'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'indexPage'])->name('index');
    Route::get('/feed', [NotificationController::class, 'index'])->name('feed');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
});

Route::middleware(['auth', 'verified', 'user.panel', 'user.approved', 'membership.active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis.index');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('/transactions/statement', function (Request $request) {
        return redirect()->route('reports.show', array_merge(
            ['report' => 'transaction-statement'],
            $request->query()
        ));
    })->name('transactions.statement');
    Route::get('/transactions/statement/pdf', function (Request $request) {
        return redirect()->route('reports.pdf', array_merge(
            ['report' => 'transaction-statement'],
            $request->query()
        ));
    })->name('transactions.statement.pdf');
    Route::resource('transactions', TransactionController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::post('reminders/{reminder}/toggle', [ReminderController::class, 'toggle'])->name('reminders.toggle');
    Route::resource('reminders', ReminderController::class)->except(['show']);
});

Route::middleware(['auth', 'verified', 'user.panel'])->prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::put('/profile', [SettingsController::class, 'updateProfile'])->name('profile.update');
    Route::put('/avatar', [SettingsController::class, 'updateAvatar'])->name('avatar.update');
    Route::delete('/avatar', [SettingsController::class, 'destroyAvatar'])->name('avatar.destroy');
    Route::put('/email', [SettingsController::class, 'updateEmail'])->name('email.update');
    Route::put('/password', [SettingsController::class, 'updatePassword'])->name('password.update');
    Route::put('/preferences', [SettingsController::class, 'updatePreferences'])->name('preferences.update');
    Route::delete('/account', [SettingsController::class, 'destroyAccount'])->name('account.destroy');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/profile', '/settings')->name('profile.edit');
    Route::patch('/profile', fn () => redirect()->route('settings.index'))->name('profile.update');
    Route::delete('/profile', fn () => redirect()->route('settings.index'))->name('profile.destroy');
});

require __DIR__.'/auth.php';

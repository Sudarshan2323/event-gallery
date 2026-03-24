<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Breeze navigation expects a "dashboard" route.
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('events', EventController::class);
    Route::resource('photos', PhotoController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('/photos/{photo}/download', [PhotoController::class, 'download'])->name('photos.download');

    Route::get('/qrcodes', [QrCodeController::class, 'index'])->name('qrcodes.index');
    Route::get('/qrcodes/download', [QrCodeController::class, 'download'])->name('qrcodes.download');
    Route::post('/qrcodes/events/{event}/regenerate', [QrCodeController::class, 'regenerateEvent'])->name('qrcodes.events.regenerate');
    Route::post('/qrcodes/photos/{photo}/regenerate', [QrCodeController::class, 'regeneratePhoto'])->name('qrcodes.photos.regenerate');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/users', [SettingsController::class, 'storeUser'])->name('settings.users.store');
    Route::patch('/settings/users/{user}', [SettingsController::class, 'updateUser'])->name('settings.users.update');
    Route::delete('/settings/users/{user}', [SettingsController::class, 'destroyUser'])->name('settings.users.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

use App\Http\Controllers\GuestController;
Route::get('/event/{slug}', [GuestController::class, 'showEvent'])->name('guest.event.show');
Route::get('/event/{slug}/download', [GuestController::class, 'downloadEvent'])->name('guest.event.download');
Route::get('/event/{slug}/booth', [GuestController::class, 'booth'])->name('guest.event.booth');
Route::post('/event/{slug}/booth', [GuestController::class, 'boothUpload'])->name('guest.event.booth.upload')->middleware('throttle:20,1');
Route::get('/event/{event}/slideshow', [GuestController::class, 'slideshowById'])->whereNumber('event')->name('guest.event.slideshow');
Route::get('/event/{slug}/slideshow', [GuestController::class, 'slideshow'])->name('guest.event.slideshow.slug');
Route::get('/photo/{id}', [GuestController::class, 'showPhoto'])->name('guest.photo.show');
Route::get('/photo/{id}/download', [GuestController::class, 'downloadPhoto'])->name('guest.photo.download');
Route::get('/qrcode/download', [GuestController::class, 'downloadQr'])->name('guest.qrcode.download');

require __DIR__.'/auth.php';

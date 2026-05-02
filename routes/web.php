<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BarangayPopulationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventImageController;
use App\Http\Controllers\PublicYouthController;
use App\Http\Controllers\SKController;
use App\Http\Controllers\SkMonitoringController;
use App\Http\Controllers\YouthController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
});

/*
|--------------------------------------------------------------------------
| KK REGISTER (Protected by Toggle)
|--------------------------------------------------------------------------
*/

Route::get('/kk/register', function () {
    return view('kk-register');
})->name('kk.register');


Route::post('/kk-register', [PublicYouthController::class, 'store'])
    ->name('kk.register.store');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Youth Management
    |--------------------------------------------------------------------------
    */

    Route::get('/youth', [YouthController::class, 'index'])->name('youth.index');
    Route::get('/youth/create', [YouthController::class, 'create']);
    Route::post('/youth', [YouthController::class, 'store']);
    Route::put('/youth/{id}', [YouthController::class, 'update']);

    Route::patch('/youth/{id}/archive', [YouthController::class, 'archive']);
    Route::get('/youth/{id}/restore', [YouthController::class, 'restore']);
    Route::post('/youth/{id}/delete', [YouthController::class, 'delete']);

    Route::get('/youth/{id}/pdf', [YouthController::class, 'exportPDF']);
    Route::get('/youth/{id}/print', [YouthController::class, 'printView'])
        ->name('youth.print');

    /*
    |--------------------------------------------------------------------------
    | SK Management (Admin)
    |--------------------------------------------------------------------------
    */

    Route::get('/sk/create', [SKController::class, 'create'])->name('sk.create');
    Route::post('/sk', [SKController::class, 'store'])->name('sk.store');
    Route::get('/sk/manage', [SKController::class, 'index'])->name('sk.manage');
    Route::post('/sk/{id}/toggle', [SKController::class, 'toggle'])->name('sk.toggle');

    /*
    |--------------------------------------------------------------------------
    | Announcements
    |--------------------------------------------------------------------------
    */

    Route::resource('announcements', AnnouncementController::class);
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])
        ->name('announcements.create');
    Route::post('/announcements/store', [AnnouncementController::class, 'store'])
        ->name('announcements.store');

    /*
    |--------------------------------------------------------------------------
    | Account
    |--------------------------------------------------------------------------
    */

    Route::get('/account/edit', [AccountController::class, 'edit'])
        ->name('account.edit');
    Route::patch('/account', [AccountController::class, 'update'])
        ->name('account.update');

    /*
    |--------------------------------------------------------------------------
    | Admin Protection Toggle
    |--------------------------------------------------------------------------
    */

    Route::post('/admin/toggle-protection', function (Request $request) {

        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (!$user instanceof User || !$user->isAdmin()) {
            abort(403);
        }

        if (!Hash::check($request->input('password'), $user->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        $user->action_protection = !$user->action_protection;
        $user->save();

        return back()->with('success', 'Protection mode updated.');

    })->name('admin.toggle.protection');

    /*
    |--------------------------------------------------------------------------
    | KK Register Toggle (Admin Only)
    |--------------------------------------------------------------------------
    */

    Route::post('/admin/toggle-kk-register', function (Request $request) {

        /** @var \App\Models\User|null $admin */
        $admin = Auth::user();

        if (!$admin instanceof User || !$admin->isAdmin()) {
            return response()->json(['success' => false]);
        }

        if (!Hash::check($request->input('password'), $admin->password)) {
            return response()->json(['success' => false]);
        }

        $admin->kk_register_enabled = !$admin->kk_register_enabled;
        $admin->save();

        return response()->json([
            'success' => true,
            'enabled' => $admin->kk_register_enabled
        ]);

    })->name('admin.toggle.kk');

    /*
    |--------------------------------------------------------------------------
    | Mail Preview (Admin Only)
    |--------------------------------------------------------------------------
    */

    Route::get('/mail/preview/sk-created', function () {

        $user = Auth::user();

        if (!$user || !$user->isAdmin()) {
            abort(403);
        }

        $dummy = new User([
            'name' => 'SK Preview',
            'email' => 'preview@example.com',
            'barangay' => 'Awang'
        ]);

        return new App\Mail\SKCreated($dummy, 'examplepass');

    })->name('mail.preview.sk');

    Route::post('/mail/test', [SKController::class, 'testMail'])
        ->name('mail.test');

});

require __DIR__ . '/auth.php';


Route::resource('events', EventController::class);
Route::get('/events', [EventController::class, 'publicIndex']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/events', [EventController::class, 'index'])
    ->name('events.index');



Route::post('/events', [EventController::class, 'store'])
    ->name('events.store');
Route::delete('/event-images/{image}', [EventImageController::class, 'destroy'])
    ->name('event-images.destroy');

Route::delete('/attachments/{id}', [YouthController::class, 'deleteAttachment']);



// Barangay Population

Route::post('/barangay-population/update', [BarangayPopulationController::class, 'update']);
Route::post('/barangay-population/update-all', [BarangayPopulationController::class, 'updateAll']);


Route::middleware(['auth'])->group(function () {

    Route::get('/sk/monitoring', [SkMonitoringController::class, 'index'])
        ->name('sk.monitoring');
    Route::get('/admin/monitoring', [SkMonitoringController::class, 'index'])
        ->name('admin.monitoring');

    Route::post('/sk/report', [SkMonitoringController::class, 'store'])
        ->name('sk.report.store');

    Route::post('/admin/report/update', [SkMonitoringController::class, 'updateStatus'])
        ->name('admin.report.update');

    Route::delete('/sk/report/{id}', [SkMonitoringController::class, 'destroy'])
        ->name('sk.report.delete');
    Route::put('/sk/report/{id}', [SkMonitoringController::class, 'update'])
        ->name('sk.report.update');

    Route::post('/admin/nudge', [SkMonitoringController::class, 'sendNudge'])
        ->name('admin.nudge.send');
    Route::get('/sk/nudges', [SkMonitoringController::class, 'getNudges'])
        ->name('sk.nudges.get');
    Route::post('/sk/nudges/clear/{id}', [SkMonitoringController::class, 'clearNudge'])
        ->name('sk.nudges.clear');
    Route::post('/sk/nudges/clear-all', [SkMonitoringController::class, 'clearAllNudges'])
        ->name('sk.nudges.clear-all');
    Route::get('/sk/check-report/{categoryId}', [SkMonitoringController::class, 'checkReport'])
        ->name('sk.report.check');
});


// =========================
// CATEGORY PAGE
// =========================
Route::get('/admin/categories', [CategoryController::class, 'index'])
    ->name('admin.categories.index');

Route::post('/admin/categories', [CategoryController::class, 'store'])
    ->name('admin.categories.store');

Route::post('/admin/categories/update', [CategoryController::class, 'update'])
    ->name('admin.categories.update');

Route::post('/admin/categories/archive/{id}', [CategoryController::class, 'archive'])
    ->name('admin.categories.archive');
Route::post('/admin/categories/toggle/{id}', [CategoryController::class, 'toggle'])
    ->name('admin.categories.toggle');
Route::delete('/admin/categories/delete/{id}', [CategoryController::class, 'destroy'])
    ->name('admin.categories.delete');
// DEADLINE (single deadline per category)
Route::post('/categories/deadline', [CategoryController::class, 'storeDeadline'])
    ->name('categories.deadline.store');

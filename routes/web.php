<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventImageController;
use App\Http\Controllers\PublicYouthController;
use App\Http\Controllers\SKController;
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

        $user = auth()->user();

        if (!$user->isAdmin()) {
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

        $admin = auth()->user();

        if (!$admin || !$admin->isAdmin()) {
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

    Route::get('/mail/test', [SKController::class, 'testMail'])
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

<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\YouthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SKController;
use App\Http\Controllers\AccountController;
use App\Models\User;

Route::get('/', function () {
    return view('home'); // resources/views/home.blade.php
});


Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Youth
    Route::get('/youth', [YouthController::class, 'index'])->name('youth.index');

    Route::get('/youth/create', [YouthController::class, 'create']);
    Route::post('/youth', [YouthController::class, 'store']);
    Route::get('/youth/{id}/pdf', [YouthController::class, 'exportPDF']);
    // ✅ UPDATE (for modal edit)
    Route::put('/youth/{id}', [YouthController::class, 'update']);

    // ✅ ARCHIVE
    Route::patch('/youth/{id}/archive', [YouthController::class, 'archive']);
    Route::get('/youth/{id}/restore', [YouthController::class, 'restore']);
    Route::get('/youth/{id}/archive', [YouthController::class, 'archive']);
    Route::post('/youth/{id}/delete', [YouthController::class, 'delete']);


    // ✅ PDF
    Route::get('/youth/{id}/pdf', [YouthController::class, 'exportPDF']);
    // SK management (admin only)
    Route::get('/sk/create', [App\Http\Controllers\SKController::class, 'create'])->name('sk.create');
    Route::post('/sk', [App\Http\Controllers\SKController::class, 'store'])->name('sk.store');

    // Account editing
    Route::get('/account/edit', [App\Http\Controllers\AccountController::class, 'edit'])->name('account.edit');
    Route::patch('/account', [App\Http\Controllers\AccountController::class, 'update'])->name('account.update');

    // Mailable preview (admin only)
    Route::get('/mail/preview/sk-created', function () {
        $user = Auth::user();
        if (!$user || !$user->isAdmin()) {
            abort(403);
        }
        $dummy = new App\Models\User([
            'name' => 'SK Preview',
            'email' => 'preview@example.com',
            'barangay' => 'Awang'
        ]);

        return new App\Mail\SKCreated($dummy, 'examplepass');
    })->name('mail.preview.sk');

    // Admin mail test
    Route::get('/mail/test', [App\Http\Controllers\SKController::class, 'testMail'])->name('mail.test');

    Route::post('/admin/toggle-protection', function (\Illuminate\Http\Request $request) {

        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        if (!\Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Incorrect password.');
        }

        $user->action_protection = !$user->action_protection;
        $user->save();

        return back()->with('success', 'Protection mode updated.');

    })->name('admin.toggle.protection')->middleware('auth');

});

require __DIR__ . '/auth.php';

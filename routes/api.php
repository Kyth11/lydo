<?php

use App\Models\Youth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/youth', fn() => Youth::all());
    Route::post('/youth', fn(Request $r) => Youth::create($r->all()));
});

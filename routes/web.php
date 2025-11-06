<?php

use Illuminate\Support\Facades\Route;

// SPA catch-all route - must be last and exclude API routes
Route::get('/{any}', function () {
    return view('app'); // App.vue
})->where('any', '^(?!api).*$'); // Negative lookahead to exclude routes starting with 'api'

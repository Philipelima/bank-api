<?php

use Illuminate\Support\Facades\Route;


Route::get('/documentation', function () {
    return redirect('/api/documentation');
});
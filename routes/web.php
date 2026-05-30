<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('Resorts', function(){
    return view('Resorts');
});

Route::get('Contact', function(){
    return view('Contact');
});

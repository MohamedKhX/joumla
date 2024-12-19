<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/test', function () {
   Mail::to('muhamedkhx2@gmail.com')
    ->send(new \App\Mail\StoreActive());
});

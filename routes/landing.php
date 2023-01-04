<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'nox::landing', ['title' => config('app.name', 'Nox')])->middleware('web');

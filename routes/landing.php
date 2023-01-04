<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'nox::landing')->middleware('web');

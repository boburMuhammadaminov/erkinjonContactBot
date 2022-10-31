<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('/5689231162:AAGaQJFKwadscNRz1x6eVwYc8xp3kPkUpUM', [\App\Http\Controllers\ContactBotController::class, 'index']);

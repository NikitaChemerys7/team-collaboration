<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;

Route::apiResource('/settings', SettingsController::class);
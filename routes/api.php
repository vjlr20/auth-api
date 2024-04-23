<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Importamos los controladores
use App\Http\Controllers\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// http://localhost:8000/api/auth/register

Route::post('/auth/register', array(
    AuthController::class, // Controlador que se usa
    'register' // Método que se ejecuta
));

// http://localhost:8000/api/auth/login

Route::post('/auth/login', array(
    AuthController::class, // Controlador que se usa
    'login' // Método que se ejecuta
));

// http://localhost:8000/api/auth/profile
Route::get('/auth/profile', array(
    AuthController::class, // Controlador que se usa
    'profile' // Método que se ejecuta
))->middleware('auth:api'); // Indicamos que necesita token

// http://localhost:8000/api/auth/logout
Route::get('/auth/logout', array(
    AuthController::class, // Controlador que se usa
    'logout' // Método que se ejecuta
))->middleware('auth:api'); // Indicamos que necesita token

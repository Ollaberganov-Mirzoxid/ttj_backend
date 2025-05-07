<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MasulController;
use App\Http\Controllers\API\ArizaController;
use App\Http\Controllers\API\StatisticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/users', [AuthController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/masullar', [MasulController::class, 'index']);
    Route::post('/masullar', [MasulController::class, 'store']);
    Route::put('/masullar/{id}', [MasulController::class, 'update']);
    Route::delete('/masullar/{id}', [MasulController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->group(function () {
    // Arizani yaratish hamma uchun
    Route::post('ariza', [ArizaController::class, 'store']);
    // Arizalarni ko‘rish (super admin va masullar uchun)
    Route::get('arizalar', [ArizaController::class, 'index']);
    // Arizani tasdiqlash (super admin va masullar uchun)
    Route::put('ariza/approve/{id}', [ArizaController::class, 'approve']);
    // Arizani rad etish (super admin va masullar uchun)
    Route::put('ariza/reject/{id}', [ArizaController::class, 'reject']);
    // Arizalarni ko‘rish (har bir foydalanuvchi o'zi yuborgan ariza uchun)
    Route::get('arizalar/my', [ArizaController::class, 'myApplications']);
});

// routes/web.php yoki api.php (agar bu API bo‘lsa)
Route::get('statistics', [StatisticsController::class, 'index']);
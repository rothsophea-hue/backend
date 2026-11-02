<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationController;
Route::post('/images', [ImageController::class, 'store']);
Route::delete('/images/{filename}', [ImageController::class, 'destroy']);




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/inventory/private-check', function () {
    return response()->json(['message' => 'Authenticated']);
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');
//notification
Route::post("/send mail", [NotificationController::class, 'sendMail']);


require __DIR__ . '/apis/productApi.php';
require __DIR__ . '/apis/cartItemApi.php';
require __DIR__ . '/apis/orderItemApi.php';
require __DIR__ . '/apis/addressApi.php';
require __DIR__ . '/apis/categoryApi.php';
require __DIR__ . '/apis/paymentApi.php';
require __DIR__ . '/apis/userApi.php';
require __DIR__ . '/apis/cartApi.php';
require __DIR__ . '/apis/orderApi.php';
// require __DIR__ . '/apis/imageApi.php';


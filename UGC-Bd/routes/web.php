<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ChatController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/register-form', function () {
    return view('register');
});
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::get('password/reset/{token}', function ($token) {
    return view('reset', ['token' => $token]);
})->name('password.reset');
Route::post('/password/email', [AuthController::class, 'resetPassword'])->name('password.update');
Route::get('/password/reset', function () {
    return view('password.email');
})->name('password.reset.form');
Route::post('/password/reset', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/auth/instagram', [AuthController::class, 'redirectToInstagram'])->name('instagram.login');
Route::get('/auth/instagram/callback', [AuthController::class, 'handleInstagramCallback']);

Route::get('/profile', [AuthController::class, 'showProfileForm'])->name('profile.show');
Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'deleteService'])->name('services.delete');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{user}', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
});


Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');

Route::get('/profile/{userId}/services', [ServiceController::class, 'userServices'])->name('user.services');


Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');

Route::post('/chat/{user}/send', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
Route::get('/chat/{userId}', [ChatController::class, 'showChat'])->name('chat.show');


Route::middleware(['auth'])->group(function () {
    Route::get('/support/chat', [ChatController::class, 'supportShow'])->name('chat.supportShow');
    Route::post('/support/chat/send', [ChatController::class, 'sendSupportMessage'])->name('chat.sendSupportMessage');
});


Route::get('/support/chats', [ChatController::class, 'supportIndex'])->name('chat.supportIndex');
Route::get('/support/chat/{chatId}', [ChatController::class, 'viewSupportChat'])->name('chat.viewSupportChat');
Route::post('/support/chat/{chatId}/send', [ChatController::class, 'sendSupportMessageToUser'])->name('chat.sendSupportMessageToUser');


Route::get('/chat/{user}', [ChatController::class, 'show'])->name('chat.show');

Route::post('/chat/{user}/send', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
Route::get('/chat/{userId}', [ChatController::class, 'showChat'])->name('chat.show');


Route::middleware(['auth'])->group(function () {
    Route::get('/support/chat', [ChatController::class, 'supportShow'])->name('chat.supportShow');
    Route::post('/support/chat/send', [ChatController::class, 'sendSupportMessage'])->name('chat.sendSupportMessage');
});


Route::get('/support/chats', [ChatController::class, 'supportIndex'])->name('chat.supportIndex');
Route::get('/support/chat/{chatId}', [ChatController::class, 'viewSupportChat'])->name('chat.viewSupportChat');
Route::post('/support/chat/{chatId}/send', [ChatController::class, 'sendSupportMessageToUser'])->name('chat.sendSupportMessageToUser');

Route::get('/orders/create', [OrdersController::class, 'create'])->name('orders.create');
Route::post('/orders', [OrdersController::class, 'store'])->name('orders.store');
Route::get('/orders/success', [OrdersController::class, 'success'])->name('orders.success');


Route::get('orders/my-orders', [OrdersController::class, 'myServiceOrders'])->name('orders.myOrders');

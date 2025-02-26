<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ListingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\BlogController;



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

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::get('/auth/facebook', [AuthController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('/auth/facebook/callback', [AuthController::class, 'handleFacebookCallback']);

Route::get('/auth/instagram', [AuthController::class, 'redirectToInstagram'])->name('instagram.login');
Route::get('/auth/instagram/callback', [AuthController::class, 'handleInstagramCallback']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');


Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('reset-password', [ResetPasswordController::class, 'reset']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/profile', [UserProfileController::class, 'show']);
    Route::post('/profile', [UserProfileController::class, 'update']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    Route::get('/listings', [ListingController::class, 'index']);
});

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;

Route::middleware('auth:sanctum')->group(function () {

    Route::get('chats', [ChatController::class, 'index']);
    Route::get('chats/{chat_id}/messages', [MessageController::class, 'showMessages']);
    Route::post('chats/{chat_id}/messages', [MessageController::class, 'sendMessage']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/my-services', [ServiceController::class, 'myServices']);
    Route::put('/my-services/{id}', [ServiceController::class, 'updateService']);
    Route::delete('/my-services/{id}', [ServiceController::class, 'deleteService']);
});

Route::get('/user/{userId}/services', [ServiceController::class, 'getUserServices']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/chats', [ChatController::class, 'index'])->name('chat.index');

    Route::get('/chats/{user}', [ChatController::class, 'show'])->name('chat.show');

    Route::post('/chats/{user}/messages', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');


    // Route::middleware('auth:sanctum')->get('/my-services', [ServiceController::class, 'myServices']);
    // Route::delete('/services/{id}', [ServiceController::class, 'deleteService']);
    // Route::post('/services/{id}', [ServiceController::class, 'updateService']);
    // Route::get('/services/{id}', [ServiceController::class, 'show']);
    // Route::post('/services/update-status', [ServiceController::class, 'updateStatus']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/services', [ServiceController::class, 'store']);
        Route::get('/services/my-services', [ServiceController::class, 'myServices']);
        Route::delete('/services/{id}', [ServiceController::class, 'deleteService']);
        Route::post('/services/{id}', [ServiceController::class, 'updateService']);
        Route::get('/services', [ServiceController::class, 'getAllServices']);
    });
});

Route::get('/services/{id}', [ServiceController::class, 'show']);

Route::get('/services/update-status/published', [ServiceController::class, 'getPublishedServices']);


use App\Http\Controllers\WishlistController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/wishlist', [WishlistController::class, 'addToWishlist']);
    Route::get('/wishlist', [WishlistController::class, 'getWishlist']);
});

Route::get('/platforms/{service_id}', [ServiceController::class, 'getPlatformsByServiceId']);

use App\Http\Controllers\LikeController;

Route::middleware('auth:sanctum')->group(function () {

    Route::post('likes/toggle', [LikeController::class, 'toggleLike']);
});
Route::get('likes/{serviceId}', [LikeController::class, 'getLikes']);

Route::get('/profiles/public/all', [UserProfileController::class, 'getAllUniqueProfiles']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('services/{service}/messages', [MessageController::class, 'sendMessage']);
    Route::get('services/{service}/messages', [MessageController::class, 'showMessages']);
});
Route::post('services/{serviceId}/messages', [MessageController::class, 'store']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/services/{serviceId}/messages', [MessageController::class, 'showMessages']);
    Route::post('/services/{serviceId}/messages', [MessageController::class, 'sendMessage']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chats', [ChatController::class, 'index'])->name('chat.index');

    Route::get('/chats/{user}', [ChatController::class, 'show'])->name('chat.show');

    Route::post('/chats/send-message', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');

    Route::get('/support-chat', [ChatController::class, 'supportShow'])->name('chat.supportShow');

    Route::post('/support-chat/send-message', [ChatController::class, 'sendSupportMessage'])->name('chat.sendSupportMessage');


    Route::get('/user-chats', [ChatController::class, 'getUserChats'])->name('chat.getUserChats');


    Route::get('/chats/{chatId}/messages', [ChatController::class, 'getChatMessages'])->name('chat.getChatMessages');
});


Route::middleware('auth:sanctum')->post('/chates/{chat_id}/messages', [MessageController::class, 'sendMessageToChat']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/support/chat', [ChatController::class, 'supportShow'])->name('support.chat');

    Route::post('/support/chat/message', [ChatController::class, 'sendSupportMessage'])->name('support.chat.message');

    Route::get('/support/chats', [ChatController::class, 'supportIndex'])->name('support.chats');

    Route::get('/support/chat/{chatId}', [ChatController::class, 'viewSupportChat'])->name('support.chat.view');

    Route::post('/support/chat/{chatId}/message', [ChatController::class, 'sendSupportMessageToUser'])->name('support.chat.send');
});
Route::middleware('auth:sanctum')->post('support/chat/message', [ChatController::class, 'sendSupportMessage']);

Route::middleware('auth:sanctum')->post('/support/message', [MessageController::class, 'sendSupportMessage']);
Route::get('/support/chat', [MessageController::class, 'getSupportChat'])->middleware('auth:sanctum');




Route::middleware('auth:sanctum')->get('/support/chat-list', [ChatController::class, 'chatList']);


Route::middleware('auth:sanctum')->get('/support/chates/{chatId}', [ChatController::class, 'chatDetails']);


Route::middleware('auth:sanctum')->post('/support/chates/{chatId}/message', [ChatController::class, 'sendSupportReply']);



Route::middleware('auth:sanctum')->get('/chats', [ChatController::class, 'index'])->name('chats.index');

Route::middleware('auth')->get('/chats/{chatId}', [ChatController::class, 'show'])->name('chats.show');

Route::middleware('auth')->post('/chats/{chatId}/messages', [MessageController::class, 'sendMessage2'])->name('messages.send');

Route::middleware('auth')->post('/services/{serviceId}/messages', [MessageController::class, 'sendMessage'])->name('service.messages.send');

Route::middleware('auth')->get('/services/{serviceId}/messages', [MessageController::class, 'showMessages'])->name('service.messages.show');

Route::middleware('auth')->get('/user/chats', [ChatController::class, 'getUserChats'])->name('user.chats');

Route::middleware('auth')->get('/chats/{chatId}/messages', [ChatController::class, 'getChatMessages'])->name('chat.messages');


Route::middleware('auth')->get('/chat-list', [ChatController::class, 'chatList'])->name('chat.list');

Route::middleware('auth')->post('/messages/send', [MessageController::class, 'sendMessage'])->name('messages.service.send');



Route::middleware('auth:sanctum')->post('/orders', [OrdersController::class, 'store']);
Route::middleware('auth:sanctum')->post('/orders/{orderId}/status', [OrdersController::class, 'updateStatus']);
Route::middleware('auth:sanctum')->get('/orders', [OrdersController::class, 'index'])->name('orders.index');
Route::get('orders/{orderId}/details', [OrdersController::class, 'showOrderDetails']);
Route::get('/api/order/{id}', [OrdersController::class, 'getOrderDetails']);
Route::get('servicess/{id}', [ServiceController::class, 'shows'])->name('servicess.shows');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrdersController::class, 'index']);
    Route::get('/orders/{orderId}', [OrdersController::class, 'showOrderDetails']);
});

Route::post('/orders', [OrdersController::class, 'store'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->get('/ordersuser', [OrdersController::class, 'getUserOrders']);

Route::middleware('auth:sanctum')->get('/ordersuser/{id}', [OrdersController::class, 'getOrderDetails2']);

Route::middleware('auth:sanctum')->get('/stats', [OrdersController::class, 'getStats']);

Route::middleware('auth:sanctum')->get('/statistics', [OrdersController::class, 'getStatistics']);

Route::middleware('auth:sanctum')->get('/statistics-data', [OrdersController::class, 'getChartData']);

Route::middleware('auth:sanctum')->get('/chart-data', [OrdersController::class, 'getChartData']);





Route::get('/blogs', [BlogController::class, 'index']);
Route::get('/blog/{id}', [BlogController::class, 'show']);

Route::middleware(['auth:sanctum', 'check.support'])->group(function () {
    Route::put('/services/update-status', [ServiceController::class, 'updateStatus']);
    Route::get('/business-users', [UserProfileController::class, 'getBusinessUsers']);
    Route::get('/creators', [UserProfileController::class, 'getCreators']);
    Route::get('/chates/{chatId}', [ChatController::class, 'showseport'])->name('chats.show');
});
Route::middleware(['auth:sanctum', 'check.support'])->group(function () {
    Route::get('/admin/monthly-user-stats', [OrdersController::class, 'getMonthlyUserStats']);
    Route::get('/admin/stats', [OrdersController::class, 'getDashboardStats']);
    Route::patch('/user/status/{id}', [UserProfileController::class, 'toggleUserStatus']);
    Route::delete('/user/{id}', [UserProfileController::class, 'deleteUser']);
    Route::get('/admin/services', [ServiceController::class, 'getAllServices']);
    Route::patch('/user/status/{id}', [UserProfileController::class, 'toggleUserStatus']);
    Route::get('/admin/support-chats', [ChatController::class, 'supportIndex'])->name('chat.supportIndex');
    Route::get('/admin/support-chats/{chatId}', [ChatController::class, 'viewSupportChat'])->name('chat.viewSupportChat');
    Route::post('/admin/support-chats/{chatId}/send-message', [ChatController::class, 'sendSupportMessageToUser'])->name('chat.sendSupportMessageToUser');
    Route::get('/admin/support-chats', [ChatController::class, 'indexSupportChats']);
    Route::get('/admin/support-messages', [ChatController::class, 'getSupportMessages']);
    Route::get('/support-chats', [ChatController::class, 'getSupportChats']);
    Route::patch('/user/type/{userId}', [UserProfileController::class, 'changeUserType']);
});
Route::get('/user-details/{id}', [UserProfileController::class, 'getUserDetailsWithServices']);
Route::get('/profile-details/{id}', [UserProfileController::class, 'getProfileWithDetails']);
Route::get('/user-services/{userId}', [ServiceController::class, 'getUserPublishedServices']);

Route::apiResource('blogs', BlogController::class);

Route::get('/profiles/{userId}', [UserProfileController::class, 'getProfileByUserId']);



Route::middleware('auth:sanctum')->get('/user-type', function (Request $request) {
    return response()->json([
        'user_type' => $request->user()->user_type,
    ]);
});

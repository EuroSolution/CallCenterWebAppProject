<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\RestaurantController;
use App\Http\Controllers\Api\Admin\StaffController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function() {
    Route::post('login', [AuthController::class, 'login']);
});
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('update-fcm-token', [AuthController::class, 'updateFcmToken']);
    Route::post('logout', [AuthController::class, 'logout']);
});
/*
 *  ADMIN ROUTES
 * */
Route::middleware('auth:sanctum')->prefix('admin')->group(function (){
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    Route::match(['get', 'post'],'/setting', [AdminController::class, 'setting']);
    Route::get('/notifications', [AdminController::class, 'showNotification']);
    Route::post('update-notification', [AdminController::class, 'updateNotification']);

    Route::get('restaurants', [RestaurantController::class, 'index']);
    Route::post('restaurants/create',[RestaurantController::class, 'create']);
    Route::post('restaurants/edit/{id}',[RestaurantController::class, 'edit']);
    Route::delete('restaurants/destroy/{id}', [RestaurantController::class, 'destroy']);

    Route::get('staff/member', [StaffController::class, 'index']);
    Route::match(['get','post'],'add/staff',[StaffController::class, 'add']);
    Route::match(['get','post'],'/staff/edit/{id}',[StaffController::class, 'edit']);
    Route::delete('staff/destroy/{id}', [StaffController::class, 'destroy']);

});

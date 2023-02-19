<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\RestaurantController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\Api\CallCenter\CategoryController;
use App\Http\Controllers\Api\CallCenter\OrdersController;
use App\Http\Controllers\Api\CallCenter\ProductController;
use App\Http\Controllers\Api\Restaurant\DashboardController As RestaurantDashboard;
use App\Http\Controllers\Api\CallCenter\DashboardController As CallCenterDashboard;
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
    Route::post('restaurants/edit',[RestaurantController::class, 'edit']);
    Route::delete('restaurants/delete', [RestaurantController::class, 'destroy']);

    Route::get('staff/member', [StaffController::class, 'index']);
    Route::post('staff/create', [StaffController::class, 'add']);
    Route::post('staff/edit', [StaffController::class, 'edit']);
    Route::delete('staff/delete', [StaffController::class, 'destroy']);

});

/*
 *  RESTAURANT ROUTES
 * */
Route::prefix('restaurant')->middleware('auth:sanctum')->group(function (){
    Route::get('/dashboard', [RestaurantDashboard::class, 'index']);
    Route::get('orders', [RestaurantDashboard::class, 'orders']);
    Route::get('order/detail', [RestaurantDashboard::class, 'showOrderDetail']);
    Route::post('order/update-status', [RestaurantDashboard::class, 'changeOrderStatus']);
});

/*
 *  CALL CENTER USER ROUTES
 * */
Route::prefix('call-center')->middleware('auth:sanctum')->group(function (){
    Route::get('/dashboard', [CallCenterDashboard::class, 'index']);

    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('main-categories', [CategoryController::class, 'getMainCategories']);
    Route::post('category/add', [CategoryController::class, 'add']);
    Route::post('category/edit', [CategoryController::class, 'edit']);
    Route::get('category/show', [CategoryController::class, 'show']);
    Route::delete('category/delete', [CategoryController::class, 'destroy']);

    Route::get('products', [ProductController::class, 'index']);
    Route::post('product/add', [ProductController::class, 'add']);
    Route::post('product/edit', [ProductController::class, 'edit']);
    Route::post('product/show', [ProductController::class, 'show']);
    Route::delete('product/delete', [ProductController::class, 'destroy']);
    Route::get('products-by-restaurant/{restaurantId}', [ProductController::class, 'getProductsByRestaurantId']);

    Route::get('orders', [OrdersController::class, 'index']);
    Route::post('order/add', [OrdersController::class, 'add']);
    Route::post('order/edit', [OrdersController::class, 'edit']);
    Route::get('order/show', [OrdersController::class, 'show']);
    Route::delete('orders/delete', [OrdersController::class, 'destroy']);
    Route::post('order/change-order-status', [OrdersController::class, 'changeOrderStatus']);
    Route::post('order/search', [OrdersController::class, 'searchOrder']);
});

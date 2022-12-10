<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CallCenter\CategoryController;
use App\Http\Controllers\CallCenter\OrdersController;
use App\Http\Controllers\CallCenter\ProductController;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CallCenter\DashboardController As CallCenterDashboard;
use App\Http\Controllers\Restaurant\DashboardController As RestaurantDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::patch('/fcm-token', [AdminController::class, 'updateToken'])->name('fcmToken');
Route::get('/send-notification',[AdminController::class,'sendNotification']);

Route::get('/', function () {
    return redirect('login');
})->name('login');
Route::get('logout', function (){
    auth()->logout();
    return redirect('login');
})->name('logout');

Route::get('getProductsByRestaurantId/{id}', [ProductController::class, 'getProductsByRestaurantId'])->name('getProductsByRestaurantId');
Route::get('getProductSizes/{id}', [ProductController::class, 'getProductSizes'])->name('getProductSizes');

Route::match(['get', 'post'], 'login', [AuthController::class, 'login'])->name('admin.login');
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function (){
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::match(['get', 'post'],'/setting', [AdminController::class, 'setting'])->name('setting');
    Route::get('/notifications', [AdminController::class, 'showNotification'])->name('notification');
    Route::post('update-notification', [AdminController::class, 'updateNotification'])->name('updateNotification');

    Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants');
    Route::match(['get','post'],'restaurants/create',[RestaurantController::class, 'create'])->name('addRestaurant');
    Route::match(['get','post'],'restaurants/edit/{id}',[RestaurantController::class, 'edit'])->name('editRestaurant');
    Route::delete('restaurants/destroy/{id}', [RestaurantController::class, 'destroy'])->name('destroyRestaurant');

    Route::get('staff/member', [StaffController::class, 'index'])->name('staffMember');
    Route::match(['get','post'],'add/staff',[StaffController::class, 'add'])->name('addStaff');
    Route::match(['get','post'],'/staff/edit/{id}',[StaffController::class, 'edit'])->name('staffEdit');
    Route::delete('staff/destroy/{id}', [StaffController::class, 'destroy'])->name('staffDestroy');

});
Route::prefix('call-center')->name('callCenter.')->middleware('auth')->group(function (){
    Route::get('/dashboard', [CallCenterDashboard::class, 'index'])->name('dashboard');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::match(['get', 'post'],'/category/add', [CategoryController::class, 'add'])->name('addCategory');
    Route::match(['get', 'post'],'/category/edit/{id}', [CategoryController::class, 'edit'])->name('editCategory');
    Route::get('/category/show/{id}', [CategoryController::class, 'show'])->name('showCategory');
    Route::delete('categories/destroy/{id}', [CategoryController::class, 'destroy'])->name('destroyCategory');

    Route::get('products', [ProductController::class, 'index'])->name('products');
    Route::match(['get', 'post'],'/product/add', [ProductController::class, 'add'])->name('addProduct');
    Route::match(['get', 'post'],'/product/edit/{id}', [ProductController::class, 'edit'])->name('editProduct');
    Route::get('/product/show/{id}', [ProductController::class, 'show'])->name('showProduct');
    Route::delete('products/destroy/{id}', [ProductController::class, 'destroy'])->name('destroyProduct');

    Route::get('orders', [OrdersController::class, 'index'])->name('orders');
    Route::match(['get', 'post'],'/order/add', [OrdersController::class, 'add'])->name('addOrder');
    Route::match(['get', 'post'],'/order/edit/{id}', [OrdersController::class, 'edit'])->name('editOrder');
    Route::get('order/show/{id}', [OrdersController::class, 'show'])->name('showOrder');
    Route::delete('orders/destroy/{id}', [OrdersController::class, 'destroy'])->name('destroyOrder');
    Route::post('order/changeOrderStatus/{id}', [OrdersController::class, 'changeOrderStatus'])->name('changeOrderStatus');
    Route::get('search-order', [OrdersController::class, 'searchOrder'])->name('searchOrder');
});

Route::prefix('restaurant')->name('restaurant.')->middleware('auth')->group(function (){
    Route::get('/dashboard', [RestaurantDashboard::class, 'index'])->name('dashboard');
    Route::get('orders', [RestaurantDashboard::class, 'orders'])->name('orders');
    Route::get('order/show/{id}', [RestaurantDashboard::class, 'showOrderDetail'])->name('showOrder');
    Route::post('order/changeOrderStatus/{id}', [RestaurantDashboard::class, 'changeOrderStatus'])->name('changeOrderStatus');
});


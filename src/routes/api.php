<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController,
    CartController,
    OrderController,
    ProductController,
    ProductGalleryController,
    UserController,
    ProductCategory,
    ProductCategoryController
};

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

Route::group(['prefix' => 'v1'], function(){
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');  
    Route::post('register', [AuthController::class, 'register'])->name('auth.register');  
    Route::group([
        'middleware' => 'jwt.verify',
    ], function(){
        Route::post('refresh-token', [AuthController::class, 'refresh'])->name('auth.refreshToken');  
        Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');  
        Route::apiResources([
            'users' => UserController::class,
            'products' => ProductController::class,
            'order' => OrderController::class,
            'products-galleries' => ProductGalleryController::class,
            'products-categories' => ProductCategoryController::class,
            'cart' => CartController::class
        ]);
    });
    Route::post('callback', [OrderController::class, 'midrans_callback'])->name('midrans_callback.post');  
});

Route::fallback(function() {
    return response()->json([
        'message' => 'No Route matched with those values'
    ], 404);
});
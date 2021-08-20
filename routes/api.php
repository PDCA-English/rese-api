<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ReservationsController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\FavoritesController;

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

Route::post('/register', [RegisterController::class, 'post']);
Route::post('/registerShop', [RegisterController::class, 'registerShop']);
Route::post('/login', [LoginController::class, 'post']);
Route::post('/logout', [LogoutController::class, 'post']);
Route::get('/user', [UsersController::class, 'get']);
Route::get('/getShopInfo', [UsersController::class, 'getShopInfo']);
Route::get('/getshops', [UsersController::class, 'getshops']);
Route::get('/getShopDetail', [UsersController::class, 'getShopDetail']);
Route::put('/user', [UsersController::class, 'put']);
Route::put('/updateShopInfo', [UsersController::class, 'updateShopInfo']);
Route::delete('/deleteShop', [UsersController::class, 'deleteShop']);
Route::get('/getSlot', [ReservationsController::class, 'getSlot']);
Route::get('/deleteReservation', [ReservationsController::class, 'deleteReservation']);
Route::delete('/deleteReservation', [ReservationsController::class, 'deleteReservation']);
Route::get('/getAvailableTableId', [ReservationsController::class, 'getAvailableTableId']);
Route::get('/getMyReservation', [ReservationsController::class, 'getMyReservation']);
Route::get('/confirmDateTime', [ReservationsController::class, 'confirmDateTime']);
Route::post('/confirmDateTime', [ReservationsController::class, 'confirmDateTime']);
Route::get('/favorite', [FavoritesController::class, 'get']);
Route::get('/getMyFavorite', [FavoritesController::class, 'getMyFavorite']);
Route::post('/favorite', [FavoritesController::class, 'post']);
Route::delete('/favorite', [FavoritesController::class, 'delete']);
Route::get('/getTables', [TablesController::class, 'getTables']);
Route::post('/regTables', [TablesController::class, 'regTables']);
Route::delete('/deleteTable', [TablesController::class, 'deleteTable']);
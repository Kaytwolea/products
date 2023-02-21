<?php

use App\Http\Controllers\ProductsController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('create', [ProductsController::class, 'Addproducts']);
Route::get('getproducts', [ProductsController::class, 'Getproducts']);
Route::patch('editproduct/{$id}', [ProductsController::class, 'Editproduct']);
Route::post('signup', [ProductsController::class, 'Signup']);
Route::middleware('auth:api', 'verified')->prefix('auth')->group(function(){
    Route::get('getuser', [ProductsController::class, 'getUser'])->middleware('auth:api');
});
Route::post('sendcode', [ProductsController::class, 'SendPhoneCode'])->middleware('auth:api');
Route::post('confirmcode', [ProductsController::class, 'Confirmcode'])->middleware('auth:api');
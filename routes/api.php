<?php

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

Route::prefix('users')->group(function () {
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login');
    Route::get('/getall', [\App\Http\Controllers\AuthController::class, 'getUsers'])->name('getUsers');
    Route::get('/oneuser', [\App\Http\Controllers\AuthController::class, 'getUserDetails'])->name('getUserDetails');
    Route::put('/update', [\App\Http\Controllers\AuthController::class, 'updateUserDetails'])->name('updateUserDetails');
    Route::put('/changepassword', [\App\Http\Controllers\AuthController::class, 'changePassword'])->name('changePassword');    
    Route::delete('/delete', [\App\Http\Controllers\AuthController::class, 'deleteUser'])->name('deleteUser');

});

Route::prefix('recipes')->group(function () {
    Route::post('/create', [App\Http\Controllers\RecipeController::class, 'create'])->name('create');
    Route::get('/userrecipes', [\App\Http\Controllers\RecipeController::class, 'allUserRecipes'])->name('allUserRecipes');
    Route::get('/onerecipe/{recipeId}', [\App\Http\Controllers\RecipeController::class, 'userRecipe'])->name('userRecipe');
    Route::put('/edit/{recipeId}', [\App\Http\Controllers\RecipeController::class, 'edit'])->name('edit');
    Route::delete('/delete/{recipeId}', [\App\Http\Controllers\RecipeController::class, 'delete'])->name('delete');

});

Route::fallback(function (){
    abort(404, 'API resource not found');
});

Route::post('settings', function (Request $request) {
    $request->validate([ 'entry' => 'required|string|min:5' ]);
    // return 'OK';
    return response()->json('OK');
});

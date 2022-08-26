<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EditProfileController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Middleware\JsonResponseMiddleware;
use App\Models\Post;
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

// Auth Routes

Route::group([
    'prefix' => 'auth'
], function ()
{
    # code...
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/generate_otp', [ForgotPasswordController::class, 'generate_otp']);
    Route::post('/verify_otp', [ForgotPasswordController::class, 'verify_otp']);
    Route::post('/create_password', [ForgotPasswordController::class, 'create_password']);
    
});

Route::group([
    'prefix' => 'user'
], function ()
{
    # code...
    Route::put('/bio', [EditProfileController::class, 'bio']);
    
    Route::post('/skill', [EditProfileController::class, 'skill']);
    Route::post('/skill_delete', [EditProfileController::class, 'skill_delete']);
    
    Route::post('/interst', [EditProfileController::class, 'interest']);
    Route::post('/interest_delete', [EditProfileController::class, 'interest_delete']);

});




// Route::get('/posts/{post}', [PostController::class, 'edit']);

// Route::resource('posts', 'Banc')
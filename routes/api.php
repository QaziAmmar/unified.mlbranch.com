<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\EditProfileController;
use App\Http\Controllers\EmailListController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PSBController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Middleware\JsonResponseMiddleware;
use App\Models\EmailList;
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
], function () {
    # code...
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/generate_otp', [ForgotPasswordController::class, 'generate_otp']);
    Route::post('/verify_otp', [ForgotPasswordController::class, 'verify_otp']);
    Route::post('/create_password', [ForgotPasswordController::class, 'create_password']);
});

Route::group([
    'prefix' => 'user'
], function () {
    # code...
    Route::put('/bio', [EditProfileController::class, 'bio']);

    Route::post('/skill', [EditProfileController::class, 'skill']);
    Route::post('/skill_delete', [EditProfileController::class, 'skill_delete']);

    Route::post('/interst', [EditProfileController::class, 'interest']);
    Route::post('/interest_delete', [EditProfileController::class, 'interest_delete']);
    // update user profile image
    Route::put('/edit_profile_image', [EditProfileController::class, 'edit_profile_image']);
    // profile sub images.
    Route::post('/add_profile_sub_images', [EditProfileController::class, 'add_profile_sub_images']);
    Route::post('/delete_profile_sub_images', [EditProfileController::class, 'delete_profile_sub_images']);
    // add user education
    Route::post('/add_education', [EditProfileController::class, 'add_education']);
    Route::post('/delete_education', [EditProfileController::class, 'delete_education']);

    Route::post('/test', [EditProfileController::class, 'test']);
});


Route::group([
    'prefix' => 'business'
], function () {
    # code...
    Route::post('/create', [BusinessController::class, 'create']);
    Route::get('/show/{user_id}', [BusinessController::class, 'show']);
    Route::put('/update', [BusinessController::class, 'update']);

    Route::post('/add_external_link', [BusinessController::class, 'add_external_link']);
    Route::post('/delete_external_link', [BusinessController::class, 'delete_external_link']);
});


Route::group([
    'prefix' => 'product'
], function () {
    # code...
    Route::post('/create', [ProductController::class, 'create']);
    Route::get('/detail', [ProductController::class, 'detail']);
    Route::get('/all_products', [ProductController::class, 'all_products']);
});

Route::group([
    'prefix' => 'psb'
], function () {
    Route::post('/create', [PSBController::class, 'create']);
    Route::get('/detail', [PSBController::class, 'detail']);
    Route::get('/all_psbs', [PSBController::class, 'all_psbs']);
});

Route::group([
    'prefix' => 'shop'
], function () {
    Route::get('/detail', [ShopController::class, 'detail']);
});

Route::group([
    'prefix' => 'subscription'
], function () {
    Route::post('/create', [SubscriptionController::class, 'create']);
    Route::post('/unsubscribed', [SubscriptionController::class, 'unsubscribed']);
    Route::get('/subscription_status', [SubscriptionController::class, 'subscription_status']);
});




# code...
Route::post('/email_list', [EmailListController::class, 'email_list']);




// Route::get('/posts/{post}', [PostController::class, 'edit']);

// Route::resource('posts', 'Banc')
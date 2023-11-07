<?php
use App\Http\Controllers\AuthController;
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

Route::group([

    'middleware' => 'api', 'cors',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/dislike/{post}', [AuthController::class,'dislike']);
    Route::post('/like/{post}', [AuthController::class,'like']);
    Route::post('login', [AuthController::class,'login']);
    Route::post('register', [AuthController::class,'register']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);  
    Route::post('/create-blog', [AuthController::class,'createPost']);
    Route::get('/posts', [AuthController::class,'postIndex']);
    Route::post('/delete-post', [AuthController::class,'deletePost']);
    Route::patch('/update-post/{post}', [AuthController::class,'postEdit']);//put or patch
    Route::post('/logout', [AuthController::class,'logout']);
    Route::get('/get-post/{post}', [AuthController::class,'getPost']);
    Route::get('/myposts', [AuthController::class,'Myposts']);

    // Route::delete('/delete-post/{post}', [AuthController::class,'deletePost']);

    // Route::post('logout', 'AuthController@logout');
    // Route::post('refresh', 'AuthController@refresh');
    // Route::post('me', 'AuthController@me');

});
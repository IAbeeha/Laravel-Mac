<?php
use App\Models\Post;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


// header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Methods: *');
// header("Access-Control-Allow-Headers: Content-Type, Authorization");
Route::get('/', function () {
    $posts = Post::all();
    return view('home', ['posts' => $posts]);
});

Route::post('/register', [UserController::class, 'register']);
Route::get('/edit_user/{user}', [UserController::class, 'editPage']);
Route::put('/edit_user/{user}', [UserController::class, 'userEdit']);
Route::delete('/delete_user/{user}', [UserController::class, 'deleteUser']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/login', [UserController::class, 'login']);

//Post controller action
Route::post('/create_post', [PostController::class, 'createPost']);
Route::get('/edit_post/{post}', [PostController::class, 'editPage']);
Route::put('/edit_post/{post}', [PostController::class, 'postEdit']);
Route::delete('/delete_post/{post}', [PostController::class, 'deletePost']);
<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [BlogController::class, 'index'])->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Salvesta kommentaar sisseloginud kasutajalt
Route::post('/blog/{slug}/comments', [CommentController::class, 'store'])->middleware(['auth'])->middleware(['auth'])->name('comments.store');

//Admin route
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:Admin|Moderator'])->group(function(){
    //Admin avaleht
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Postused Crud
    Route::resource('posts', PostController::class)->except(['show']);

    //Kiirtegevused public ja unpublish
    Route::patch('posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
    Route::patch('posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');

    
});
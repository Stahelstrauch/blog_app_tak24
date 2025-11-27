<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentModerationController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Author\AuthorDashboardController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CommentController;
use GuzzleHttp\Middleware;
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

    // Kommentaarid
    Route::get('comments', [CommentModerationController::class, 'index'])->name('comments.index');
    Route::patch('comments/{comment}/status', [CommentModerationController::class, 'updateStatus'])->name('comments.updateStatus');
    Route::delete('comments/{comment}', [CommentModerationController::class, 'destroy'])->name('comments.destroy');

    //Userite haldamine
    Route::resource('users', UserController::class)->except(['show'])->middleware('role:Admin');

    // Kodutöö osa, kommentaaride taastamine ja jäädavalt kustutamine
    Route::patch('comments/{comment}/restore', [CommentModerationController::class, 'restore'])->name('comments.restore')->middleware('role:Admin'); // taastamine ainult adminile
    Route::delete('comments/{comment}/force', [CommentModerationController::class, 'forceDestroy'])->name('comments.forceDestroy')->middleware('role:Admin'); // jäädavalt kustutamine ainult Adminile

    Route::patch('posts/{post}/restore', [PostController::class, 'restore'])->name('posts.restore')->middleware('role:Admin'); // taastamine ainult adminile
    Route::delete('posts/{post}/force', [PostController::class, 'forceDestroy'])->name('posts.forceDestroy')->middleware('role:Admin'); // jäädavalt kustutamine ainult Adminile

    // Kategooriad
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Sildid
    Route::resource('tags', TagController::class)->except(['show']);
});

//Autori vaadete osa
Route::prefix('author')->name('author.')->middleware(['auth', 'role:Author|Admin'])->group(function(){
    Route::get('/', [AuthorDashboardController::class, 'index'])->name('dashboard');

    Route::resource('posts', \App\Http\Controllers\Author\PostController::class)->except(['show']);

});
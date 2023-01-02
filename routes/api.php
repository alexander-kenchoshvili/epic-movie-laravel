<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Broadcast\CommentController;
use App\Http\Controllers\Broadcast\LikeController;
use App\Http\Controllers\Broadcast\NotificationController;
use App\Http\Controllers\MovieQuote\MovieController;
use App\Http\Controllers\MovieQuote\QuoteController;
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

Route::controller(AuthController::class)->group(function () {
	Route::post('register', 'register')->name('register');
	Route::post('login', 'login')->name('login');
	Route::get('me', 'me')->middleware('jwt.auth')->name('me');
	Route::post('email-verify', 'emailVerify')->name('verification.verify');
	Route::get('logout', [AuthController::class, 'logout'])->middleware('jwt.auth')->name('logout');
	Route::post('updatecruds', [AuthController::class, 'updateCrudentials'])->name('update.crudentials');
});

Route::controller(PasswordResetController::class)->group(function () {
	Route::post('forgot-password', 'reset')->name('password.email');
	Route::get('reset-password/{token}', 'newPasswordForm')->name('password.reset');
	Route::post('reset-password', 'resetPassword')->name('password.update');
});

Route::controller(GoogleController::class)->group(function () {
	Route::get('google-auth', 'login')->name('google.auth');
	Route::get('google', 'callback')->name('google.callback');
});

Route::controller(MovieController::class)->group(function () {
	Route::post('/movies', 'store')->name('movies.create');
	Route::get('/movie', 'allMovies')->name('movies');
	Route::get('singlemovie/{movie:id}', 'singleMovie')->name('movie');
	Route::post('updatemovie/{movie:id}', 'update')->middleware('jwt.auth')->name('movie.update');
	Route::post('delete/{movie:id}', 'destroy')->middleware('jwt.auth')->name('movie.delete');
});
Route::controller(QuoteController::class)->group(function () {
	Route::post('/quotes/{movie:id}', 'store')->name('quotes.create');
	Route::get('/quotes', 'allQuotes')->name('quotes');
	Route::get('/quote/{quote:id}', 'singleQuote')->name('quote');
	Route::post('/updatequote/{quote:id}', 'update')->middleware('jwt.auth')->name('quote.update');
	Route::post('/deletequote/{quote:id}', 'destroy')->middleware('jwt.auth')->name('quote.delete');
	Route::post('/search', 'search')->name('search');
	Route::post('/getQuotes', 'getQuotes')->name('get.quote');
});
Route::post('/comment/{quote:id}', [CommentController::class, 'store'])->name('comment');
Route::post('/like/{quote:id}', [LikeController::class, 'store'])->name('like');
Route::get('/notification', [NotificationController::class, 'index'])->name('notification');
Route::post('/marked', [NotificationController::class, 'update'])->name('update');

Route::controller(EmailController::class)->group(function () {
	Route::post('email/store', 'store')->name('email.store');
	Route::post('email/verified', 'verify')->name('email.verify');
	Route::post('email/{email}', 'destroy')->name('delete.email');
	Route::post('primary/update', 'makePrimaryEmail')->name('primary.email');
});

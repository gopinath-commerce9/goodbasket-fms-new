<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Modules\Base\Http\Middleware\ValidUserDivertMiddleware;

Route::prefix('userauth')->group(function() {
    Route::middleware([ValidUserDivertMiddleware::class . ':auth-user'])->group(function() {
        Route::get('/', 'UserAuthController@index')->name('userauth.index');
        Route::get('/login', 'UserAuthController@login')->name('userauth.login');
        Route::post('/authenticate', 'UserAuthController@authenticate')->name('userauth.authenticate');
    });
    Route::get('/logout', 'UserAuthController@logout')->name('userauth.logout');
});

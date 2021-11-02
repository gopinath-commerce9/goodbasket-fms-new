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

use Modules\Base\Http\Middleware\BlockInvalidUserMiddleware;
use Modules\UserRole\Http\Middleware\AuthUserRolePathResolver;

Route::prefix('supervisor')->middleware([
    BlockInvalidUserMiddleware::class . ':auth-user',
    AuthUserRolePathResolver::class . ':auth-user',
])->group(function() {
    Route::get('/', 'SupervisorController@index')
        ->name('supervisor.index');
    Route::get('/dashboard', 'SupervisorController@dashboard')
        ->name('supervisor.dashboard');
    Route::get('/order-view/{orderId}', 'SupervisorController@viewOrder')
        ->name('supervisor.viewOrder');
    Route::post('/find-order', 'SupervisorController@searchOrderByIncrementId')
        ->name('supervisor.searchOrderByIncrementId');
    Route::post('/filter-order', 'SupervisorController@searchOrderByFilters')
        ->name('supervisor.searchOrderByFilters');
});

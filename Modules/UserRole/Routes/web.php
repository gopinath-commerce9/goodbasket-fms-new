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

use Modules\UserRole\Http\Middleware\AuthUserPermissionResolver;

Route::prefix('userrole')->group(function() {

    Route::get('/', function () {
        return redirect()->route('roles.index');
    });

    Route::get('/roles', 'UserRoleController@index')
        ->name('roles.index')
        ->middleware([AuthUserPermissionResolver::class . ':user-roles.view']);
    Route::get('/roles/view/{roleId}', 'UserRoleController@show')
        ->name('roles.view')
        ->middleware([AuthUserPermissionResolver::class . ':user-roles.view']);
    Route::get('/roles/new', 'UserRoleController@create')
        ->name('roles.new')
        ->middleware([AuthUserPermissionResolver::class . ':user-roles.create']);
    Route::post('/roles/store', 'UserRoleController@store')
        ->name('roles.store')
        ->middleware([AuthUserPermissionResolver::class . ':user-roles.create']);
    Route::get('/roles/edit/{roleId}', 'UserRoleController@edit')
        ->name('roles.edit')
        ->middleware([AuthUserPermissionResolver::class . ':user-roles.update']);
    Route::post('/roles/update/{roleId}', 'UserRoleController@update')
        ->name('roles.update')
        ->middleware([AuthUserPermissionResolver::class . ':user-roles.update']);
    Route::get('/roles/delete/{roleId}', 'UserRoleController@destroy')
        ->name('roles.delete')
        ->middleware([AuthUserPermissionResolver::class . ':user-roles.delete']);

});

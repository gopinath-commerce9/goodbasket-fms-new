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

Route::prefix('dashboard')->middleware([BlockInvalidUserMiddleware::class . ':auth-user'])->group(function() {
    Route::get('/', 'DashboardController@index')->name('dashboard.index');
    Route::get('/delivery-details', 'DashboardController@deliveryDetails')
        ->name('dashboard.deliveryDetails');
    Route::get('/order-view/{orderId}', 'DashboardController@viewOrder')
        ->name('dashboard.viewOrder');
    Route::post('/get-vendor-status', 'DashboardController@getVendorStatus')
        ->name('dashboard.getVendorStatus');
    Route::post('/fetch-channel-orders', 'DashboardController@fetchChannelOrders')
        ->name('dashboard.fetchChannelOrders');
    Route::post('/find-order', 'DashboardController@searchOrderByIncrementId')
        ->name('dashboard.searchOrderByIncrementId');
    Route::get('/download-items-date-csv', 'DashboardController@downloadItemsDateCsv')
        ->name('dashboard.downloadItemsDateCsv');
    Route::get('/download-items-schedule-csv', 'DashboardController@downloadItemsScheduleCsv')
        ->name('dashboard.downloadItemsScheduleCsv');
    Route::post('/export-orderwise-items', 'DashboardController@exportOrderWiseItems')
        ->name('dashboard.exportOrderWiseItems');
});

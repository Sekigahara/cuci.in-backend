<?php

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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', 'AuthController@login');
    Route::post('/register', 'AuthController@register');
    
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('/logout', 'AuthController@logout');
        Route::get('/info', 'AuthController@user');

        //User
        Route::get('/user', 'UserController@getAllUser');
        Route::get('/user/{user}', 'UserController@showByUsername');
        Route::get('/user/id/{id}', 'UserController@showById');

        // Transaction
        Route::get('/transaction/owner/{user}', 'TransactionController@index');
        Route::get('/transaction/{transaction}', 'TransactionController@show');
        Route::get('/transaction/customer/{transaction}', 'TransactionController@showTransactionByCustomerId');
        Route::get('/transaction/history/{user}', 'TransactionController@history');
        // Route::get('/transaction/{transaction}', 'TransactionController@showTransactionByCustomerId');
        Route::post('/transaction', 'TransactionController@store');
        Route::put('/transaction/{transaction}', 'TransactionController@update');
        Route::delete('/transaction/{transaction}', 'TransactionController@destroy');
        
        // Laundry Type
        Route::get('/laundry/type', 'LaundryTypeController@index');
        Route::get('/laundry/type/{type}', 'LaundryTypeController@show');
        Route::post('/laundry/type', 'LaundryTypeController@store');
        Route::put('/laundry/type/{type}', 'LaundryTypeController@update');
        Route::delete('/laundry/type/{type}', 'LaundryTypeController@destroy');
        
        // Outlet
        Route::get('/outlet', 'OutletController@index');
        Route::get('/outlet/{outlet}', 'OutletController@show');
        Route::post('/outlet', 'OutletController@store');
        Route::put('/outlet/{outlet}', 'OutletController@update');
        Route::delete('/outlet/{outlet}', 'OutletController@destroy');
      
        // Customer
        Route::get('/customer', 'CustomerController@index');
        Route::post('/customer', 'CustomerController@store');
        Route::get('/customer/{customer}', 'CustomerController@show');
        Route::get('/customer/user/{customer}', 'CustomerController@showCustomerByUserId');
        Route::put('/customer/{customer}', 'CustomerController@update');
        Route::delete('customer/{customer}', 'CustomerController@destroy');

        // Owner
        Route::get('/owner', 'OwnerController@index');
        Route::post('/owner', 'OwnerController@store');
        Route::get('/owner/{owner}', 'OwnerController@show');
        Route::get('/owner/user_id/{owner}', 'OwnerController@showByUserId');
        Route::put('/owner/{owner}', 'OwnerController@update');
        Route::delete('/owner/{owner}', 'OwnerController@destroy');

        // Outlet Google
        Route::get('/outlet/google', 'OutletGoogleController@index');
        Route::post('/outlet/google', 'OutletGoogleController@store');

        //Apply Mitra
        Route::post('/partnership/apply', 'PartnershipController@store');
        Route::get('/partnership/status/{user_id}', 'PartnershipController@status');

        //Download Desktop App
        Route::get('/download', function () {
            $file = public_path() . "/file/desktop.msi";

            $headers = array(
                'Content-Type: application/x-msdownload'
            );

            return response()->download($file, "Cuci-In.msi", $headers);
        });
    });
});

<?php

use Illuminate\Support\Facades\Route;

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

Route::namespace('\App\Http\Controllers')->group(function () {
    Route::middleware('guest')->group(function () {
        /** AUTH */
        Route::get('/', 'AuthController@index')->name('home');
        Route::post('/login', 'AuthController@login')->name('login');
    });

    Route::middleware('auth')->group(function () {
        /** AUTH LOGOUT */
        Route::get('/logout', 'AuthController@logout')->name('logout');

        /** DASHBOARD */
        Route::prefix('dashboard/')->name('dashboard.')->group(function () {
            Route::get('/', 'DashboardController@index')->name('index');
        });

        /** NOTICE */
        Route::group(['prefix' => 'notice','as' => 'notice.'], function () {
            Route::get('/', 'NoticeController@index')->name('index');
            Route::get('/create', 'NoticeController@create')->name('create');
            Route::get('/{id}/edit', 'NoticeController@edit')->name('edit');
            Route::post('/store', 'NoticeController@store')->name('store');
            Route::post('/{id}/update', 'NoticeController@update')->name('update');
            Route::post('/{id}/destroy', 'NoticeController@destroy')->name('destroy');
            Route::post('/upload', 'NoticeController@upload')->name('upload');
            Route::post('/uploadTrumbowygImage', 'NoticeController@uploadTrumbowygImage')->name('uploadTrumbowygImage');
        });

        /** RECIPE */
        Route::group(['prefix' => 'recipe', 'as' => 'recipe.'], function () {
            Route::get('/', 'RecipeController@index')->name('index');
            Route::get('/create', 'RecipeController@create')->name('create');
            Route::get('/{id}/edit', 'RecipeController@edit')->name('edit');
            Route::post('/store', 'RecipeController@store')->name('store');
            Route::post('/{id}/update', 'RecipeController@update')->name('update');
            Route::post('/{id}/destroy', 'RecipeController@destroy')->name('destroy');
            Route::post('/upload', 'RecipeController@upload')->name('upload');
            Route::post('/uploadTrumbowygImage', 'RecipeController@uploadTrumbowygImage')->name('uploadTrumbowygImage');
        });

        /** PRODUCT INFORMATION */
        Route::group(['prefix' => 'product-information', 'as' => 'productInformation.'], function () {
            Route::get('/', 'ProductInformationController@index')->name('index');
            Route::get('/create', 'ProductInformationController@create')->name('create');
            Route::get('/{id}/edit', 'ProductInformationController@edit')->name('edit');
            Route::post('/store', 'ProductInformationController@store')->name('store');
            Route::post('/{id}/update', 'ProductInformationController@update')->name('update');
            Route::post('/{id}/destroy', 'ProductInformationController@destroy')->name('destroy');
            Route::post('/upload', 'ProductInformationController@upload')->name('upload');
            Route::post('/uploadTrumbowygImage', 'ProductInformationController@uploadTrumbowygImage')->name('uploadTrumbowygImage');
        });

        /** COLUMN MANAGEMENT */
        Route::group(['prefix' => 'column','as' => 'column.'], function () {
            Route::get('/', 'ColumnController@index')->name('index');
            Route::get('/create', 'ColumnController@create')->name('create');
            Route::get('/{id}/edit', 'ColumnController@edit')->name('edit');
            Route::post('/store', 'ColumnController@store')->name('store');
            Route::post('/{id}/update', 'ColumnController@update')->name('update');
            Route::post('/{id}/destroy', 'ColumnController@destroy')->name('destroy');
            Route::post('/upload', 'ColumnController@upload')->name('upload');
            Route::post('/uploadTrumbowygImage', 'ColumnController@uploadTrumbowygImage')->name('uploadTrumbowygImage');
        });

        /** MESSAGE MANAGEMENT */
        Route::group(['prefix' => 'message','as' => 'message.'], function () {
            Route::get('/', 'MessageController@index')->name('index');
            Route::get('/create', 'MessageController@create')->name('create');
            Route::get('/{id}/edit', 'MessageController@edit')->name('edit');
            Route::post('/store', 'MessageController@store')->name('store');
            Route::post('/{id}/update', 'MessageController@update')->name('update');
            Route::post('/{id}/destroy', 'MessageController@destroy')->name('destroy');
            Route::post('/upload', 'MessageController@upload')->name('upload');
            Route::post('/uploadTrumbowygImage', 'MessageController@uploadTrumbowygImage')->name('uploadTrumbowygImage');
        });

        /** ADMIN USER MANAGEMENT */
        Route::prefix('admin/')->name('admin.')->group(function () {
            Route::get('/', 'AdminController@index')->name('index');
            Route::get('/{admin}/edit', 'AdminController@edit')->name('edit');
        });
    });
});

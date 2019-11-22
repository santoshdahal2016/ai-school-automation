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

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



Route::get('/api', function (){
    return null;
});
Route::namespace('Api')->prefix('api')->name('api.')->group(function () {

    /*Admin Route*/
    Route::resource('/users', 'UsersController', ['except' => ['show', 'create', 'store']]);
    Route::resource('/candidates', 'CandidateController', ['except' => ['show', 'create', 'store','delete']]);

    Route::get('/candidates/receipt/{id}', 'CandidateController@receipt')->name('pdf.receipt');
    Route::get('/candidates/confirmation/{id}', 'CandidateController@confirmation')->name('pdf.confirmation');
    Route::get('/candidates/send_email/{id}', 'CandidateController@send')->name('pdf.send');

    Route::post('/candidates/import_csv', 'CandidateController@csvLoad');

    /*User Route*/
    Route::get('/profile/get-me', 'ProfileController@getMe')->name('get.profile');
    Route::post('/profile/change-password', 'ProfileController@changePassword')->name('change.password');
    Route::post('/profile/change-email', 'ProfileController@changeEmail')->name('change.email');
});


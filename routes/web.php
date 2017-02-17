<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

 // Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

// Registration Routes...
Route::get('register-employee', 'Auth\RegisterController@showEmployeeRegistrationForm');
Route::get('register-admin', 'Auth\RegisterController@showAdminRegistrationForm');

Route::post('register-employee', 'Auth\RegisterController@registerEmployee');
Route::post('register-admin', 'Auth\RegisterController@registerAdmin');

// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/home', 'HomeController@index');

Route::group(['middleware'=>'auth'], function () {
    Route::resource('users', 'UserController');
    Route::get('employees/checklist/{id}/{source}', 'EmployeeChecklistController@checklist')->name('employees.checklist');
    Route::get('employees/get-checklist/{id}', 'EmployeeChecklistController@getChecklist');
    Route::post('employees/save-checklist', 'EmployeeChecklistController@saveChecklist');
    Route::post('employees/upload-checklist-file', 'EmployeeChecklistController@uploadChecklistFile');
    Route::post('employees/delete-uploaded-checklist-file', 'EmployeeChecklistController@deleteUploadedChecklistFile');
});

Route::group(['middleware'=>'role:admin'], function(){
    Route::get('employees/','EmployeeController@index')->name('employees.index');
    Route::get('employees/{id}/edit', 'EmployeeController@edit')->name('employees.edit');
    Route::patch('employees/{id}', 'EmployeeController@update')->name('employees.update');
    Route::delete('employees/{id}','EmployeeController@destroy')->name('employees.destroy');
});
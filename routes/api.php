<?php
Route::get('me','User\MeController@getMe');

Route::get('designs','Designs\DesignController@index');
Route::get('designs/{id}','Designs\DesignController@findDesign');

Route::get('users','User\UserController@index');

Route::group(['middleware' => ['auth:api']],function(){
    Route::post('logout','Auth\LoginController@logout');

    Route::put('settings/profile','User\SettingsController@updateProfile');
    Route::put('settings/password','User\SettingsController@updatePassword');

    Route::post('designs','Designs\UploadController@upload');
    Route::put('designs/{id}','Designs\DesignController@update');
    Route::delete('designs/{id}','Designs\DesignController@destroy');

    Route::post('designs/{id}/comments', 'Designs\CommentController@store');
    Route::put('comments/{id}', 'Designs\CommentController@update');
    Route::delete('comments/{id}', 'Designs\CommentController@destroy');
});

Route::group(['middleware' => ['guest:api']],function(){
    Route::post('register','Auth\RegisterController@register');
    Route::post('verification/verify/{user}','Auth\VerificationController@verify')->name('verification.verify');
    Route::post('verification/resend','Auth\VerificationController@resend');
    Route::post('login','Auth\LoginController@login');
    Route::post('password/email','Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset','Auth\ResetPasswordController@reset');
});
<?php

Route::get('user/profile',
          ['middleware' => 'cnp.auth', function () {

    $id = Auth::user()->id;
    $controller = app()->make('DemocracyApps\CNP\Http\Controllers\UserController');
    return $controller->callAction('show', $parameters=array('id'=>$id));
}]);

Route::get('user/email_changed', function() {
    return view('user.email_changed');
});
Route::post('user/email_changed', function() {
    return redirect()->intended('/');
});


Route::get('/user/noconfirm', function() {
    return view('user.noconfirm', array());
});
Route::get('/confirm/resend', function() {
    return "Need to implement confirmation email resend.";
});

// The {id} routes need to follow routes above
Route::get('user/{id}/edit', 'UserController@edit');
Route::get('user/{id}', 'UserController@show');
Route::put('user/{id}', 'UserController@update');

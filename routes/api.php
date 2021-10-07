<?php

use Illuminate\Http\Request;

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
Route::post('/login','Api\AuthController@login');
Route::post('/register','Api\AuthController@register');
Route::get('/logout','Api\AuthController@logout');

Route::get('/test',function(){
    return response()->json([
        "test" => "ok"
    ]);
});

Route::post('/posts','Api\PostsController@storePost')->middleware('jwtAuth');
Route::delete('/posts/{id}','Api\PostsController@deletePost')->middleware('jwtAuth');
Route::patch('/posts/{id}','Api\PostsController@updatePost')->middleware('jwtAuth');
Route::get('/posts','Api\PostsController@getPosts')->middleware('jwtAuth');
Route::get('/posts/{id}','Api\PostsController@getPost')->middleware('jwtAuth');
Route::post('/posts/{id}/comment','Api\CommentsController@storeComment')->middleware('jwtAuth');
Route::patch('/posts/{post_id}/comment/{comment_id}','Api\CommentsController@updateComment')->middleware('jwtAuth');
Route::delete('/posts/{post_id}/comment/{comment_id}','Api\CommentsController@deleteComment')->middleware('jwtAuth');
Route::get('/posts/{post_id}/comments','Api\CommentsController@getComments')->middleware('jwtAuth');
Route::get('/posts/{id}/like','Api\LikesController@like')->middleware('jwtAuth');

Route::post('/user-info','Api\AuthController@saveUserInfo')->middleware('jwtAuth');


// Route::post('/comments','Api\CommentsController@storeComment')->middleware('jwtAuth');
// Route::delete('/comments/{id}','Api\CommentsController@deleteComment')->middleware('jwtAuth');
// Route::patch('/comments/{id}','Api\CommentsController@updateComment')->middleware('jwtAuth');
// Route::get('/comments','Api\CommentsController@getComments')->middleware('jwtAuth');


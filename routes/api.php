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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/statistics', 'DashboardController@index');

/*
    |--------------------------------------------------------------------------
    | Article routes
    |--------------------------------------------------------------------------
*/
Route::get("/articles/all", "ArticlesController@all");
Route::resource("articles", "ArticlesController")->names([
    "index" => "api.articles.index",
    "store"  => "api.articles.store",
    "edit" => "api.articles.edit",
    "update" => "api.articles.update",
    "destroy" => "api.articles.destroy",
]);

Route::get("{category}/{article}", "ArticlesController@show")->name("api.articles.show");

/*
    |--------------------------------------------------------------------------
    | Comments routes
    |--------------------------------------------------------------------------
*/
Route::get("/comments", "CommentsController@index")->name("api.comments.index");
Route::post("{category}/{article}/comments", "CommentsController@store")->name("api.comments.store");
Route::delete("comments/{comment}", "CommentsController@destroy")->name("api.comments.destroy");

/*
    |--------------------------------------------------------------------------
    | Categories routes
    |--------------------------------------------------------------------------
    |
*/

Route::apiResource("categories", "CategoriesController")->names([
    "index" => "api.categories.index",
    "store"  => "api.categories.store",
    "show" => "api.categories.show",
    "update" => "api.categories.update",
    "destroy" => "api.categories.destroy",
]);
Route::get("/categories/{category}/articles", "CategoriesController@getArticles")->name("api.categories.articles");


/*
    |--------------------------------------------------------------------------
    | Tags routes
    |--------------------------------------------------------------------------
*/
Route::apiResource("tags", "TagsController")->names([
    "index"     => "api.tags.index",
    "show"      => "api.tags.show",
    "update"    => "api.tags.update",
    "destroy"   => "api.tags.destroy",
])->except(["store"]);
Route::get("/tags/{tag}/articles", "TagsController@getArticles")->name("api.tags.articles");




Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login')->name("auth.login");
    Route::post('logout', 'AuthController@logout')->name("auth.logout");
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});
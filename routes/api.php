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

Route::group(['prefix' => '/v1', 'namespace' => '\Api'], function () {
    /**
     * 获取快3彩票数据
     */
    Route::get('lotteryList', 'CaipiaoController@lotteryList');
    Route::get('test', 'CaipiaoController@test');
});

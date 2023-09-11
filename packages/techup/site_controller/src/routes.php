<?php

use Illuminate\Support\Facades\Route;
use Techup\SiteController\SiteControllerController;
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
Route::group(['middleware' => ['auth:api'], 'prefix'=>'site-controller', 'as' => 'site_controller.'], function () {
	Route::post('deployments', [SiteControllerController::class, 'postDeployments'])->name('postDeployments');

	Route::post('deploy-ssl', [SiteControllerController::class, 'postDeploySsl'])->name('postDeploySsl');

});

<?php

use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\MailSendingHistoryController;
use App\Http\Controllers\Api\MailTemplateController;
use App\Http\Controllers\Api\SmtpAccountController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WebsiteController;
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

//User
Route::group(['as' => 'user.'], function () {

    Route::get('/users', [UserController::class, 'index'])->name('index');
    Route::post('/user', [UserController::class, 'store'])->name('store');
    Route::get('/user/{id}', [UserController::class, 'show'])->name('show');
    Route::put('/user/{id}', [UserController::class, 'edit'])->name('edit');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('destroy');
});

//Website
Route::group(['as' => 'website.'], function () {

    Route::get('/websites', [WebsiteController::class, 'index'])->name('index');
    Route::post('/website', [WebsiteController::class, 'store'])->name('store');
    Route::get('/website/{id}', [WebsiteController::class, 'show'])->name('show');
    Route::put('/website/{id}', [WebsiteController::class, 'edit'])->name('edit');
    Route::delete('/website/{id}', [WebsiteController::class, 'destroy'])->name('destroy');
});

//SmtpAccount
Route::group(['as' => 'smtp-account.'], function () {

    Route::get('/smtp-accounts', [SmtpAccountController::class, 'index'])->name('index');
    Route::post('/smtp-account', [SmtpAccountController::class, 'store'])->name('store');
    Route::get('/smtp-account/{id}', [SmtpAccountController::class, 'show'])->name('show');
    Route::put('/smtp-account/{id}', [SmtpAccountController::class, 'edit'])->name('edit');
    Route::delete('/smtp-account/{id}', [SmtpAccountController::class, 'destroy'])->name('destroy');

    //This API will use smtp_account from smtp_account_uuid to send emails.
    Route::post('/email/send', [SmtpAccountController::class, 'sendEmail'])->name('sendEmail');

    //This API will use smtp_account from smtp_account_uuid and mail_template from mail_template_uuid to send emails.
    Route::post('/email/send-template', [SmtpAccountController::class, 'sendTemplate'])->name('sendTemplate');
});

//MailTemplate
Route::group(['as' => 'mail-template.'], function () {

    Route::get('/mail-templates', [MailTemplateController::class, 'index'])->name('index');
    Route::post('/mail-template', [MailTemplateController::class, 'store'])->name('store');
    Route::get('/mail-template/{id}', [MailTemplateController::class, 'show'])->name('show');
    Route::put('/mail-template/{id}', [MailTemplateController::class, 'edit'])->name('edit');
    Route::delete('/mail-template/{id}', [MailTemplateController::class, 'destroy'])->name('destroy');
});

//Campaign
Route::group(['as' => 'campaign.'], function () {

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('index');
    Route::post('/campaign', [CampaignController::class, 'store'])->name('store');
    Route::get('/campaign/{id}', [CampaignController::class, 'show'])->name('show');
    Route::put('/campaign/{id}', [CampaignController::class, 'edit'])->name('edit');
    Route::delete('/campaign/{id}', [CampaignController::class, 'destroy'])->name('destroy');
});

//Email
Route::group(['as' => 'email.'], function () {

    Route::get('/emails', [EmailController::class, 'index'])->name('index');
    Route::post('/email', [EmailController::class, 'store'])->name('store');
    Route::get('/email/{id}', [EmailController::class, 'show'])->name('show');
    Route::put('/email/{id}', [EmailController::class, 'edit'])->name('edit');
    Route::delete('/email/{id}', [EmailController::class, 'destroy'])->name('destroy');
});

//MailSendingHistory
Route::group(['as' => 'mail-sending-history.'], function () {

    Route::get('/mail-sending-histories', [MailSendingHistoryController::class, 'index'])->name('index');
    Route::post('/mail-sending-history', [MailSendingHistoryController::class, 'store'])->name('store');
    Route::get('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'show'])->name('show');
    Route::put('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'edit'])->name('edit');
    Route::delete('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'destroy'])->name('destroy');
});

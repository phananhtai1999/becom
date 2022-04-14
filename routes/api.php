<?php

use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\MailSendingHistoryController;
use App\Http\Controllers\Api\MailTemplateController;
use App\Http\Controllers\Api\SmtpAccountController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\WebsiteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UploadImgController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\User\UserDetailController;
use App\Http\Controllers\Api\User\UserConfigController;
use App\Http\Controllers\Api\ConfigController;

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

Route::group(['as' => 'auth.'], function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');
    Route::get('/me', [AuthController::class, 'me'])->name('me')->middleware('auth:api');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forget-password', [AuthController::class, 'forgetPassword'])->name('forget-password');
    Route::post('/recovery-password', [AuthController::class, 'recoveryPassword'])->name('recovery-password');
});

Route::post('/upload-img', [UploadImgController::class, 'upload'])->name('upload')->middleware('auth:api');

Route::group(['middleware' => ['auth:api'], 'as' => 'user.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/users', [UserController::class, 'index'])->name('index');
        Route::post('/user', [UserController::class, 'store'])->name('store');
        Route::get('/user/{id}', [UserController::class, 'show'])->name('show');
        Route::put('/user/{id}', [UserController::class, 'edit'])->name('edit');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::post('/user/verify-my-email', [UserController::class, 'verifyMyEmail'])->name('verifyMyEmail');
        Route::put('/user/verify-my-email/{pin}', [UserController::class, 'checkVerificationCode'])->name('checkVerificationCode');
        Route::put('/my/profile', [UserController::class, 'editMyProfile'])->name('editMyProfile');
    });
});

Route::get('/user/show-by-username/{username}', [UserController::class, 'showByUserName'])->name('user.showByUserName');

Route::group(['middleware' => ['auth:api', 'role:admin'], 'as' => 'role.'], function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('index');
    Route::post('/role', [RoleController::class, 'store'])->name('store');
    Route::get('/role/{id}', [RoleController::class, 'show'])->name('show');
    Route::put('/role/{id}', [RoleController::class, 'edit'])->name('edit');
    Route::delete('/role/{id}', [RoleController::class, 'destroy'])->name('destroy');
});

Route::group(['middleware' => ['auth:api'], 'as' => 'user-detail.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/user-details', [UserDetailController::class, 'index'])->name('index');
        Route::post('/user-detail', [UserDetailController::class, 'store'])->name('store');
        Route::get('/user-detail/{id}', [UserDetailController::class, 'show'])->name('show');
        Route::put('/user-detail/{id}', [UserDetailController::class, 'edit'])->name('edit');
        Route::delete('/user-detail/{id}', [UserDetailController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::put('/my/user-detail/upsert', [UserDetailController::class, 'upsertMyUserDetail'])->name('upsertMyUserDetail');
    });
});

Route::group(['middleware' => ['auth:api'], 'as' => 'user-config.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/user-configs', [UserConfigController::class, 'index'])->name('index');
        Route::post('/user-config', [UserConfigController::class, 'store'])->name('store');
        Route::get('/user-config/{id}', [UserConfigController::class, 'show'])->name('show');
        Route::put('/user-config/{id}', [UserConfigController::class, 'edit'])->name('edit');
        Route::delete('/user-config/{id}', [UserConfigController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        //Create “Store my user config”, “Update my user config”, (Upsert) this api only creates the user config for the current user.
        Route::put('/my/user-config/upsert', [UserConfigController::class, 'upsertMyUserConfig'])->name('upsertMyUserConfig');
    });
});

Route::group(['middleware' => ['auth:api', 'role:admin'], 'as' => 'config.admin'], function () {
    Route::get('/configs', [ConfigController::class, 'index'])->name('index');
    Route::post('/config', [ConfigController::class, 'store'])->name('store');
    Route::put('/config/upsert', [ConfigController::class, 'upsertConfig'])->name('upsertConfig');
    Route::get('/config/{id}', [ConfigController::class, 'show'])->name('show');
    Route::put('/config/{id}', [ConfigController::class, 'edit'])->name('edit');
    Route::delete('/config/{id}', [ConfigController::class, 'destroy'])->name('destroy');
});

Route::get('/configs/all', [ConfigController::class, 'loadAllConfig'])->name('config.loadAllConfig');

//Website
Route::group(['middleware' => ['auth:api'], 'as' => 'website.'], function () {

    Route::get('/websites', [WebsiteController::class, 'index'])->name('index');
    Route::post('/website', [WebsiteController::class, 'store'])->name('store');
    Route::get('/website/{id}', [WebsiteController::class, 'show'])->name('show');
    Route::put('/website/{id}', [WebsiteController::class, 'edit'])->name('edit');
    Route::delete('/website/{id}', [WebsiteController::class, 'destroy'])->name('destroy');

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/websites', [WebsiteController::class, 'indexMyWebsite'])->name('index');
        Route::post('/my/website', [WebsiteController::class, 'storeMyWebsite'])->name('store');
        Route::get('/my/website/{id}', [WebsiteController::class, 'showMyWebsite'])->name('show');
        Route::put('/my/website/{id}', [WebsiteController::class, 'editMyWebsite'])->name('edit');
        Route::delete('/my/website/{id}', [WebsiteController::class, 'destroyMyWebsite'])->name('destroy');
    });
});

//SmtpAccount
Route::group(['middleware' => ['auth:api'], 'as' => 'smtp-account.'], function () {

    Route::get('/smtp-accounts', [SmtpAccountController::class, 'index'])->name('index');
    Route::post('/smtp-account', [SmtpAccountController::class, 'store'])->name('store');
    Route::get('/smtp-account/{id}', [SmtpAccountController::class, 'show'])->name('show');
    Route::put('/smtp-account/{id}', [SmtpAccountController::class, 'edit'])->name('edit');
    Route::delete('/smtp-account/{id}', [SmtpAccountController::class, 'destroy'])->name('destroy');

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/smtp-accounts', [SmtpAccountController::class, 'indexMySmtpAccount'])->name('index');
        Route::post('/my/smtp-account', [SmtpAccountController::class, 'storeMySmtpAccount'])->name('store');
        Route::get('/my/smtp-account/{id}', [SmtpAccountController::class, 'showMySmtpAccount'])->name('show');
        Route::put('/my/smtp-account/{id}', [SmtpAccountController::class, 'editMySmtpAccount'])->name('edit');
        Route::delete('/my/smtp-account/{id}', [SmtpAccountController::class, 'destroyMySmtpAccount'])->name('destroy');
    });

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
Route::group(['middleware' => ['auth:api'], 'as' => 'campaign.'], function () {

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('index');
    Route::post('/campaign', [CampaignController::class, 'store'])->name('store');
    Route::get('/campaign/{id}', [CampaignController::class, 'show'])->name('show');
    Route::put('/campaign/{id}', [CampaignController::class, 'edit'])->name('edit');
    Route::delete('/campaign/{id}', [CampaignController::class, 'destroy'])->name('destroy');

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/campaigns', [CampaignController::class, 'indexMyCampaign'])->name('index');
        Route::post('/my/campaign', [CampaignController::class, 'storeMyCampaign'])->name('store');
        Route::get('/my/campaign/{id}', [CampaignController::class, 'showMyCampaign'])->name('show');
        Route::put('/my/campaign/{id}', [CampaignController::class, 'editMyCampaign'])->name('edit');
        Route::delete('/my/campaign/{id}', [CampaignController::class, 'destroyMyCampaign'])->name('destroy');
    });

    //Upsert-campaign-link-tracking
    Route::get('/upsert-campaign-link-tracking', [CampaignController::class, 'upsertCampaignLinkTrackingTotalClick'])->name('upsert-campaign-link-tracking');
});

//Create Increment Campaign total open
Route::get('/campaign-tracking/increment/total-open', [CampaignController::class, 'incrementCampaignTrackingTotalOpen'])->name('campaignTracking.incrementTotalOpen');

//Email
Route::group(['middleware' => ['auth:api'], 'as' => 'email.'], function () {

    Route::get('/emails', [EmailController::class, 'index'])->name('index');
    Route::post('/email', [EmailController::class, 'store'])->name('store');
    Route::get('/email/{id}', [EmailController::class, 'show'])->name('show');
    Route::put('/email/{id}', [EmailController::class, 'edit'])->name('edit');
    Route::delete('/email/{id}', [EmailController::class, 'destroy'])->name('destroy');

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/emails', [EmailController::class, 'indexMyEmail'])->name('index');
        Route::post('/my/email', [EmailController::class, 'storeMyEmail'])->name('store');
        Route::get('/my/email/{id}', [EmailController::class, 'showMyEmail'])->name('show');
        Route::put('/my/email/{id}', [EmailController::class, 'editMyEmail'])->name('edit');
        Route::delete('/my/email/{id}', [EmailController::class, 'destroyMyEmail'])->name('destroy');
    });
});

//MailSendingHistory
Route::group(['as' => 'mail-sending-history.'], function () {

    Route::get('/mail-sending-histories', [MailSendingHistoryController::class, 'index'])->name('index');
    Route::post('/mail-sending-history', [MailSendingHistoryController::class, 'store'])->name('store');
    Route::get('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'show'])->name('show');
    Route::put('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'edit'])->name('edit');
    Route::delete('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'destroy'])->name('destroy');
});

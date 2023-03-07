<?php

use App\Http\Controllers\Api\ActivityHistoryController;
use App\Http\Controllers\Api\ArticleCategoryController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CreditPackageController;
use App\Http\Controllers\Api\EmailController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\MailOpenTrackingController;
use App\Http\Controllers\Api\MailSendingHistoryController;
use App\Http\Controllers\Api\MailTemplateController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\Payment\PaymentController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PlatformPackageController;
use App\Http\Controllers\Api\PositionController;
use App\Http\Controllers\Api\RemindController;
use App\Http\Controllers\Api\ScenarioController;
use App\Http\Controllers\Api\SectionTemplateController;
use App\Http\Controllers\Api\SmtpAccountController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\SubscriptionPlanController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\WebsiteController;
use App\Http\Controllers\Api\WebsitePageCategoryController;
use App\Http\Controllers\Api\WebsitePageController;
use App\Http\Controllers\Api\WebsiteVerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UploadImgController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\User\UserDetailController;
use App\Http\Controllers\Api\User\UserConfigController;
use App\Http\Controllers\Api\ConfigController;
use App\Http\Controllers\Api\AuthBySocialNetworkController;
use App\Http\Controllers\Api\SmtpAccountEncryptionController;
use App\Http\Controllers\Api\SupportMultipleLanguagesController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactListController;
use App\Http\Controllers\Api\CreditHistoryController;
use App\Http\Controllers\Api\UserCreditHistoryController;
use App\Http\Controllers\Api\CreditTransactionHistoryController;
use App\Http\Controllers\Api\PaymentMethodController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\MomoController;
use App\Http\Controllers\Api\PaypalController;

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
    Route::get('/check-token', [AuthController::class, 'checkToken'])->name('check-token');
});

//Social API
Route::get('/auth/url/{driver}', [AuthBySocialNetworkController::class, 'loginUrl'])->name('loginUrl');
Route::get('/auth/callback/google', [AuthBySocialNetworkController::class, 'loginByGoogleCallback'])->name('loginByGoogleCallback');
Route::get('/auth/callback/facebook', [AuthBySocialNetworkController::class, 'loginByFacebookCallback'])->name('loginByFacebookCallback');
Route::get('/auth/callback/linkedin', [AuthBySocialNetworkController::class, 'loginByLinkedinCallback'])->name('loginByLinkedinCallback');
Route::get('/auth/callback/github', [AuthBySocialNetworkController::class, 'loginByGithubCallback'])->name('loginByGithubCallback');

Route::post('/upload-img', [UploadImgController::class, 'upload'])->name('upload')->middleware('auth:api');

Route::group(['middleware' => ['auth:api'], 'as' => 'user.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/users', [UserController::class, 'index'])->name('index');
        Route::post('/user', [UserController::class, 'store'])->name('store');
        Route::put('/user/ban/{id}', [UserController::class, 'ban'])->name('ban');
        Route::get('/user/{id}', [UserController::class, 'show'])->name('show');
        Route::put('/user/{id}', [UserController::class, 'edit'])->name('edit');
        Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::post('/user/verify-my-email', [UserController::class, 'verifyMyEmail'])->name('verifyMyEmail');
        Route::put('/user/verify-my-email/{pin}', [UserController::class, 'checkVerificationCode'])->name('checkVerificationCode');
        Route::put('/my/profile', [UserController::class, 'editMyProfile'])->name('editMyProfile');
    });

    Route::post('/user/change-password', [UserController::class, 'changePassword'])->name('changePassword');
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
});

Route::get('/configs/all', [ConfigController::class, 'loadAllConfig'])->name('config.loadAllConfig');

//Website
Route::group(['middleware' => ['auth:api'], 'as' => 'website.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/websites', [WebsiteController::class, 'index'])->name('index');
        Route::post('/website', [WebsiteController::class, 'store'])->name('store');
        Route::get('/website/{id}', [WebsiteController::class, 'show'])->name('show');
        Route::put('/website/{id}', [WebsiteController::class, 'edit'])->name('edit');
        Route::delete('/website/{id}', [WebsiteController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/websites', [WebsiteController::class, 'indexMy'])->name('index');
        Route::post('/my/website', [WebsiteController::class, 'storeMyWebsite'])->name('store');
        Route::get('/my/website/{id}', [WebsiteController::class, 'showMyWebsite'])->name('show');
        Route::put('/my/website/{id}', [WebsiteController::class, 'editMyWebsite'])->name('edit');
        Route::delete('/my/website/{id}', [WebsiteController::class, 'destroyMyWebsite'])->name('destroy');
    });

    Route::post('/website-verification/dns-record', [WebsiteController::class, 'verifyByDnsRecord'])->name('verifyByDnsRecord');
    Route::post('/website-verification/html-tag', [WebsiteController::class, 'verifyByHtmlTag'])->name('verifyByHtmlTag');
    Route::post('/website-verification/html-file', [WebsiteController::class, 'verifyByHtmlFile'])->name('verifyByHtmlFile');
    Route::get('/verification-download/{token}', [WebsiteController::class, 'downloadHtmlFile'])->name('downloadHtmlFile');
});


//SmtpAccount
Route::group(['middleware' => ['auth:api'], 'as' => 'smtp-account.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/smtp-accounts', [SmtpAccountController::class, 'index'])->name('index');
        Route::post('/smtp-account', [SmtpAccountController::class, 'store'])->name('store');
        Route::get('/smtp-account/{id}', [SmtpAccountController::class, 'show'])->name('show');
        Route::put('/smtp-account/{id}', [SmtpAccountController::class, 'edit'])->name('edit');
        Route::delete('/smtp-account/{id}', [SmtpAccountController::class, 'destroy'])->name('destroy');
        Route::get('/get-mail-mailer-type/smtp-account', [SmtpAccountController::class, 'getMailMailerSmtpAccount'])->name('get-mail-mailer-smtp-account');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/smtp-accounts', [SmtpAccountController::class, 'indexMy'])->name('index');
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
Route::group(['middleware' => ['auth:api'], 'as' => 'mail-template.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/mail-templates', [MailTemplateController::class, 'index'])->name('index');
        Route::get('/mail-template/{id}', [MailTemplateController::class, 'show'])->name('show');
        Route::post('/mail-template', [MailTemplateController::class, 'store'])->name('store');
        Route::put('/mail-template/{id}', [MailTemplateController::class, 'edit'])->name('edit');
        Route::delete('/mail-template/{id}', [MailTemplateController::class, 'destroy'])->name('destroy');
        Route::post('/accept-publish', [MailTemplateController::class, 'acceptPublishMailtemplate'])->name('accept-publish');
    });

    Route::group(['middleware' => ['role:admin,editor']], function () {
        Route::get('/unpublished-mail-templates', [MailTemplateController::class, 'indexUnpublishedMailTemplate'])->name('index-unpublished');
        Route::get('/unpublished-mail-template/{id}', [MailTemplateController::class, 'showUnpublishedMailTemplate'])->name('show-unpublished');
        Route::post('/unpublished-mail-template', [MailTemplateController::class, 'storeUnpublishedMailTemplate'])->name('store-unpublished');
        Route::put('/unpublished-mail-template/{id}', [MailTemplateController::class, 'editUnpublishedMailTemplate'])->name('edit-unpublished');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/mail-templates', [MailTemplateController::class, 'indexMy'])->name('index');
        Route::post('/my/mail-template', [MailTemplateController::class, 'storeMyMailTemplate'])->name('store');
        Route::get('/my/mail-template/{id}', [MailTemplateController::class, 'showMyMailTemplate'])->name('show');
        Route::put('/my/mail-template/{id}', [MailTemplateController::class, 'editMyMailTemplate'])->name('edit');
        Route::delete('/my/mail-template/{id}', [MailTemplateController::class, 'destroyMyMailTemplate'])->name('destroy');
    });

    Route::get('/mail-templates-default', [MailTemplateController::class, 'getMailTemplatesDefault'])->name('getMailTemplatesDefault');
});

//Campaign
Route::group(['middleware' => ['auth:api'], 'as' => 'campaign.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/campaigns', [CampaignController::class, 'index'])->name('index');
        Route::post('/campaign', [CampaignController::class, 'store'])->name('store');
        Route::get('/campaign/{id}', [CampaignController::class, 'show'])->name('show');
        Route::put('/campaign/{id}', [CampaignController::class, 'edit'])->name('edit');
        Route::delete('/campaign/{id}', [CampaignController::class, 'destroy'])->name('destroy');
        Route::post('/emails/send-campaign', [CampaignController::class, 'sendEmailsByCampaign'])->name('sendEmailsByCampaign');
        Route::post('/test-send-campaign', [CampaignController::class, 'testSendEmailByCampaign'])->name('testSendEmailByCampaign');
        Route::post('/emails/status-campaign', [CampaignController::class, 'statusCampaign'])->name('statusCampaign');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/campaigns', [CampaignController::class, 'indexMyCampaign'])->name('index');
        Route::post('/my/campaign', [CampaignController::class, 'storeMyCampaign'])->name('store');
        Route::get('/my/campaign/{id}', [CampaignController::class, 'showMyCampaign'])->name('show');
        Route::put('/my/campaign/{id}', [CampaignController::class, 'editMyCampaign'])->name('edit');
        Route::delete('/my/campaign/{id}', [CampaignController::class, 'destroyMyCampaign'])->name('destroy');
        Route::post('/my/emails/send-campaign', [CampaignController::class, 'sendEmailByMyCampaign'])->name('sendEmailByMyCampaign');
        Route::post('my/test-send-campaign/', [CampaignController::class, 'testSendEmailByMyCampaign'])->name('testSendEmailByCampaign');
        Route::post('my/emails/status-campaign', [CampaignController::class, 'statusMyCampaign'])->name('statusMyCampaign');
    });

    //Upsert-campaign-link-tracking
    Route::get('/upsert-campaign-link-tracking', [CampaignController::class, 'upsertCampaignLinkTrackingTotalClick'])->name('upsert-campaign-link-tracking');
    //Load-campaign-tracking analytic
    Route::get('/campaign-tracking/analytic', [CampaignController::class, 'loadAnalyticData'])->name('loadAnalyticData');
});

//Create Increment Campaign total open
Route::get('/campaign-tracking/increment/total-open', [CampaignController::class, 'incrementCampaignTrackingTotalOpen'])->name('campaignTracking.incrementTotalOpen');

//Email
Route::group(['middleware' => ['auth:api'], 'as' => 'email.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/emails', [EmailController::class, 'index'])->name('index');
        Route::post('/email', [EmailController::class, 'store'])->name('store');
        Route::get('/email/{id}', [EmailController::class, 'show'])->name('show');
        Route::put('/email/{id}', [EmailController::class, 'edit'])->name('edit');
        Route::delete('/email/{id}', [EmailController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/emails', [EmailController::class, 'indexMyEmail'])->name('index');
        Route::post('/my/email', [EmailController::class, 'storeMyEmail'])->name('store');
        Route::get('/my/email/{id}', [EmailController::class, 'showMyEmail'])->name('show');
        Route::put('/my/email/{id}', [EmailController::class, 'editMyEmail'])->name('edit');
        Route::delete('/my/email/{id}', [EmailController::class, 'destroyMyEmail'])->name('destroy');
    });
});

//MailSendingHistory
Route::group(['middleware' => ['auth:api'], 'as' => 'mail-sending-history.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/mail-sending-histories', [MailSendingHistoryController::class, 'index'])->name('index');
        Route::post('/mail-sending-history', [MailSendingHistoryController::class, 'store'])->name('store');
        Route::get('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'show'])->name('show');
        Route::put('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'edit'])->name('edit');
        Route::delete('/mail-sending-history/{id}', [MailSendingHistoryController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/mail-sending-histories', [MailSendingHistoryController::class, 'indexMy'])->name('index');
        Route::get('/my/mail-sending-history/{id}', [MailSendingHistoryController::class, 'showMyMailSendingHistory'])->name('show');
    });
});

//SmtpAccountEncryption
Route::group(['middleware' => ['auth:api'], 'as' => 'smtp-account-encryption.'], function () {
    Route::get('/smtp-account-encryptions', [SmtpAccountEncryptionController::class, 'index'])->name('index');
    Route::post('/smtp-account-encryption', [SmtpAccountEncryptionController::class, 'store'])->name('store');
    Route::get('/smtp-account-encryption/{id}', [SmtpAccountEncryptionController::class, 'show'])->name('show');
    Route::put('/smtp-account-encryption/{id}', [SmtpAccountEncryptionController::class, 'edit'])->name('edit');
    Route::delete('/smtp-account-encryption/{id}', [SmtpAccountEncryptionController::class, 'destroy'])->name('destroy');
});

//WebsiteVerification
Route::group(['middleware' => ['auth:api'], 'as' => 'website-verification.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/website-verifications', [WebsiteVerificationController::class, 'index'])->name('index');
        Route::get('/website-verification/{id}', [WebsiteVerificationController::class, 'show'])->name('show');
        Route::delete('/website-verification/{id}', [WebsiteVerificationController::class, 'destroy'])->name('destroy');
    });
});

//Contact
Route::group(['middleware' => ['auth:api'], 'as' => 'contact.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/contacts', [ContactController::class, 'index'])->name('index');
        Route::post('/contact', [ContactController::class, 'store'])->name('store');
        Route::get('/contact/{id}', [ContactController::class, 'show'])->name('show');
        Route::put('/contact/{id}', [ContactController::class, 'edit'])->name('edit');
        Route::delete('/contact/{id}', [ContactController::class, 'destroy'])->name('destroy');
        Route::get('/custom-filter-default', [ContactController::class, 'customFilterDefault'])->name('custom-filter-default');
        Route::get('/select-all-contact', [ContactController::class, 'selectAllContact'])->name('select-all-contact');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/contacts', [ContactController::class, 'indexMyContact'])->name('index');
        Route::post('/my/contact', [ContactController::class, 'storeMyContact'])->name('store');
        Route::get('/my/contact/{id}', [ContactController::class, 'showMyContact'])->name('show');
        Route::put('/my/contact/{id}', [ContactController::class, 'editMyContact'])->name('edit');
        Route::delete('/my/contact/{id}', [ContactController::class, 'destroyMyContact'])->name('destroy');
        Route::get('/my/select-all-contact', [ContactController::class, 'selectAllMyContact'])->name('select-all-contact');
    });

    Route::group(['middleware' => ['role:editor'], 'as' => 'editor.'], function () {
        Route::get('/dynamic-content-contact', [ContactController::class, 'dynamicContentContact'])->name('dynamic-content-contact');
    });
});

//Contact List
Route::group(['middleware' => ['auth:api'], 'as' => 'contact-list.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/contact-lists', [ContactListController::class, 'index'])->name('index');
        Route::post('/contact-list', [ContactListController::class, 'storeAndImportFile'])->name('store-and-importFile');
        Route::get('/contact-list/{id}', [ContactListController::class, 'show'])->name('show');
        Route::put('/contact-list/{id}', [ContactListController::class, 'edit'])->name('edit');
        Route::delete('/contact-list/{id}', [ContactListController::class, 'destroy'])->name('destroy');
        Route::delete('/contact-list/remove-contact/{id}/{contact_id}', [ContactListController::class, 'removeContactFromContactList'])->name('remove-contact');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/contact-lists', [ContactListController::class, 'indexMy'])->name('index');
        Route::post('/my/contact-list', [ContactListController::class, 'storeMyContactListAndImportFile'])->name('store-my-contact-list-and-import-file');
        Route::get('/my/contact-list/{id}', [ContactListController::class, 'showMyContactList'])->name('show');
        Route::put('/my/contact-list/{id}', [ContactListController::class, 'editMyContactList'])->name('edit');
        Route::delete('/my/contact-list/{id}', [ContactListController::class, 'destroyMyContactList'])->name('destroy');
        Route::delete('/my/contact-list/remove-contact/{id}/{contact_id}', [ContactListController::class, 'removeMyContactFromContactList'])->name('remove-contact');

    });
});

//Credit History
Route::group(['middleware' => ['auth:api'], 'as' => 'user-use-credit-history.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/credit-histories', [CreditHistoryController::class, 'index'])->name('index');
        Route::post('/credit-history', [CreditHistoryController::class, 'store'])->name('store');
        Route::get('/credit-history/{id}', [CreditHistoryController::class, 'show'])->name('show');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/credit-histories', [CreditHistoryController::class, 'indexMy'])->name('index');
        Route::get('/my/credit-history/{id}', [CreditHistoryController::class, 'showMyCreditHistory'])->name('show');
    });
});

//Transaciton CreditHistory View
Route::group(['middleware' => ['auth:api'], 'as' => 'credit-transaction-histories.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/credit-transaction-histories', [CreditTransactionHistoryController::class, 'index'])->name('credit-transaction-history-view');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/credit-transaction-histories', [CreditTransactionHistoryController::class, 'indexMyCreditTransactionHistoryView'])->name('credit-transaction-history-view');
    });
});

//User Credit History
Route::group(['middleware' => ['auth:api'], 'as' => 'user-credit-history.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/user-credit-histories', [UserCreditHistoryController::class, 'index'])->name('index');
        Route::post('/user-credit-history', [UserCreditHistoryController::class, 'store'])->name('store');
        Route::get('/user-credit-history/{id}', [UserCreditHistoryController::class, 'show'])->name('show');
        Route::put('/user-credit-history/{id}', [UserCreditHistoryController::class, 'edit'])->name('edit');
        Route::delete('/user-credit-history/{id}', [UserCreditHistoryController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/user-credit-histories', [UserCreditHistoryController::class, 'indexMy'])->name('index');
        Route::get('/my/user-credit-history/{id}', [UserCreditHistoryController::class, 'showMyUserCreditHistory'])->name('show');
    });
});

Route::group(['middleware' => ['auth:api'], 'as' => 'mail-open-tracking.'], function () {
    Route::get('/mail-open-tracking/report-campaigns', [MailOpenTrackingController::class, 'reportAnalyticDataCampaigns'])->name('reportAnalyticDataCampaigns');
    Route::get('/mail-open-tracking/report-campaign/{id}', [MailOpenTrackingController::class, 'reportAnalyticDataCampaign'])->name('reportAnalyticDataCampaign');
});

// Import File
Route::group(['middleware' => ['auth:api'], 'as' => 'import-file.'], function () {
    Route::post('/import-excel-or-csv-file', [ContactController::class, 'importExcelOrCsvFile'])->name('import-excel-or-csv-file');
    Route::post('/import-json-file', [ContactController::class, 'importJsonFile'])->name('import-json-file');
    Route::post('/download-template-excel', [ContactController::class, 'templateExcel'])->name('template-excel');
});

// Set Cookie-Change Lang
Route::get('/support-multiple-languages', [SupportMultipleLanguagesController::class, 'setCookie'])->name('set-cookie');
// Mail Open Tracking
Route::get('/mail-open-tracking/{id}', [MailSendingHistoryController::class, 'mailOpenTracking'])->name('mail-open-tracking');
//Chart
Route::group(['middleware' => ['auth:api'], 'as' => 'chart.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/user-chart', [UserController::class, 'userChart'])->name('user-chart');
        Route::get('/email-chart', [MailSendingHistoryController::class, 'emailChart'])->name('email-chart');
        Route::get('/campaign-chart', [CampaignController::class, 'campaignChart'])->name('campaign-chart');
        Route::get('/credit-chart', [CreditHistoryController::class, 'creditChart'])->name('credit-chart');
        Route::get('/smtp-account-chart', [SmtpAccountController::class, 'smtpAccountChart'])->name('smtpAccountChart');
        Route::get('/point-contact-chart', [ContactController::class, 'pointsContactChart'])->name('pointsContactChart');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/credit-chart', [CreditHistoryController::class, 'myCreditChart'])->name('myCreditChart');
        Route::get('/my/campaign-chart', [CampaignController::class, 'myCampaignChart'])->name('my-campaign-chart');
        Route::get('/my/email-chart', [MailSendingHistoryController::class, 'myEmailChart'])->name('email-chart');
        Route::get('/my/contact-chart', [ContactController::class, 'myContactChart'])->name('myContactChart');
        Route::get('/my/point-contact-chart', [ContactController::class, 'myPointsContactChart'])->name('myPointsContactChart');
    });
});

Route::group(['middleware' => ['auth:api', 'role:admin'], 'as' => 'payment-method.'], function () {
    Route::post('/payment-method', [PaymentMethodController::class, 'store'])->name('store');
    Route::put('/payment-method/{id}', [PaymentMethodController::class, 'edit'])->name('edit');
    Route::delete('/payment-method/{id}', [PaymentMethodController::class, 'destroy'])->name('destroy');
});

Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-method.index');
Route::get('/payment-method/{id}', [PaymentMethodController::class, 'show'])->name('payment-method.show');

Route::group(['middleware' => ['auth:api'], 'as' => 'order.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('index');
        Route::get('/order/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/order', [OrderController::class, 'store'])->name('store');
        Route::put('/order/{id}', [OrderController::class, 'edit'])->name('edit');
        Route::delete('/order/{id}', [OrderController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/orders', [OrderController::class, 'indexMy'])->name('index');
        Route::get('/my/order/{id}', [OrderController::class, 'showMyOrder'])->name('show');
        Route::put('/my/order/{id}', [OrderController::class, 'editMyOrder'])->name('edit');
        Route::delete('/my/order/{id}', [OrderController::class, 'destroyMyOrder'])->name('destroy');
    });
});

//Momo
Route::get('/momo/success-transaction', [MomoController::class, 'successTransaction'])->name('momo.successTransaction');

//Paypal
Route::get('/paypal/success-transaction', [PayPalController::class, 'successTransaction'])->name('paypal.successTransaction');
Route::get('/paypal/cancel-transaction', [PayPalController::class, 'cancelTransaction'])->name('paypal.cancelTransaction');

// checkout
Route::group(['middleware' => ['auth:api'], 'as' => 'checkout.'], function () {
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::post('/payment-again', [CheckoutController::class, 'paymentAgain'])->name('paymentAgain');
});

//Scenario
Route::group(['middleware' => ['auth:api'], 'as' => 'scenario.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('scenarios', [ScenarioController::class, 'index'])->name('index');
        Route::post('scenario', [ScenarioController::class, 'storeScenario'])->name('storeScenario');
        Route::get('scenario/{id}', [ScenarioController::class, 'show'])->name('showMyScenario');
        Route::put('scenario/{id}', [ScenarioController::class, 'editMyScenario'])->name('editMyScenario');
    });
    Route::group(['as' => 'my.'], function () {
        Route::get('my/scenarios', [ScenarioController::class, 'indexMy'])->name('indexMy');
        Route::post('my/scenario', [ScenarioController::class, 'storeMyScenario'])->name('storeMyScenario');
        Route::get('my/scenario/{id}', [ScenarioController::class, 'showMyScenario'])->name('showMyScenario');
        Route::put('my/scenario/{id}', [ScenarioController::class, 'editMyScenario'])->name('editMyScenario');
    });
});

//Website_page_categories
Route::group(['middleware' => ['auth:api'], 'as' => 'website_page_category.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::post('/website-page-category', [WebsitePageCategoryController::class, 'store'])->name('store');
        Route::put('/website-page-category/{id}', [WebsitePageCategoryController::class, 'edit'])->name('edit');
        Route::delete('/website-page-category/{id}', [WebsitePageCategoryController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/website-page-categories', [WebsitePageCategoryController::class, 'index'])->name('index');
        Route::get('/website-page-category/{id}', [WebsitePageCategoryController::class, 'show'])->name('show');
    });

});

//Website_pages
Route::group(['middleware' => ['auth:api'], 'as' => 'website_page'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('website-pages', [WebsitePageController::class, 'index'])->name('index');
        Route::post('website-page', [WebsitePageController::class, 'store'])->name('store');
        Route::get('website-page/{id}', [WebsitePageController::class, 'show'])->name('show');
        Route::put('website-page/{id}', [WebsitePageController::class, 'edit'])->name('edit');
        Route::delete('/website-page/{id}', [WebsitePageController::class, 'destroy'])->name('destroy');
        Route::get('/unpublished-website-pages', [WebsitePageController::class, 'indexUnpublishedWebsitePage'])->name('index-unpublished');
        Route::get('/unpublished-website-page/{id}', [WebsitePageController::class, 'showUnpublishedWebsitePage'])->name('show-unpublished');
        Route::post('website-page/accept-publish', [WebsitePageController::class, 'acceptPublishWebsitePage'])->name('acceptPublishWebsitePage');
    });
    Route::group(['as' => 'my.'], function () {
        Route::get('my/website-pages', [WebsitePageController::class, 'indexMy'])->name('indexMyWebsitePage');
        Route::post('my/website-page', [WebsitePageController::class, 'storeMyWebsitePage'])->name('storeMyWebsitePage');
        Route::get('my/website-page/{id}', [WebsitePageController::class, 'showMyWebsitePage'])->name('showMyWebsitePage');
        Route::put('my/website-page/{id}', [WebsitePageController::class, 'editMyWebsitePage'])->name('editMyWebsitePage');
        Route::delete('my/website-page/{id}', [WebsitePageController::class, 'destroyMyWebsitePage'])->name('destroyMyWebsitePage');
    });
});

//Platform Package
Route::group(['middleware' => ['auth:api'], 'as' => 'platformPackage.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::post('/platform-package', [PlatformPackageController::class, 'store']);
        Route::get('/platform-packages', [PlatformPackageController::class, 'index']);
        Route::delete('/platform-package/{id}', [PlatformPackageController::class, 'destroy']);
    });
    Route::get('/platform-package/{id}', [PlatformPackageController::class, 'show']);
    Route::get('/my-platform-package', [PlatformPackageController::class, 'myPlatformPackage']);
});

//Cache config
Route::group(['middleware' => ['auth:api'], 'as' => 'platformPackage.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::put('/cache-platform-config/{membership_package_uuid}', [ConfigController::class, 'editCachePlatformConfig']);
        Route::get('/cache-platform-config/{membership_package_uuid}', [ConfigController::class, 'getCachePlatformConfig']);
        Route::get('/platform-packages', [PlatformPackageController::class, 'index']);
        Route::delete('/platform-package/{id}', [PlatformPackageController::class, 'destroy']);
    });
    Route::get('/platform-package/{id}', [PlatformPackageController::class, 'show']);
    Route::get('/my-platform-package', [PlatformPackageController::class, 'myPlatformPackage']);
});


//Credit Package
Route::group(['middleware' => ['auth:api'], 'as' => 'creditPackage.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::post('/credit-package', [CreditPackageController::class, 'store']);
        Route::get('/credit-package/{id}', [CreditPackageController::class, 'show']);
        Route::put('/credit-package/{id}', [CreditPackageController::class, 'edit']);
        Route::get('/credit-packages', [CreditPackageController::class, 'index']);
        Route::delete('/credit-package/{id}', [CreditPackageController::class, 'destroy']);
    });
});
Route::group(['middleware' => ['auth:api'], 'as' => 'subscriptionPlan.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::post('/subscription-plan', [SubscriptionPlanController::class, 'store']);
        Route::get('/subscription-plan/{id}', [SubscriptionPlanController::class, 'show']);
        Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index']);
        Route::delete('/subscription-plan/{id}', [SubscriptionPlanController::class, 'destroy']);
    });
});
Route::group(['middleware' => ['auth:api'], 'as' => 'permission.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::post('/permission', [PermissionController::class, 'store']);
        Route::get('/permission/{id}', [PermissionController::class, 'show']);
        Route::put('/permission/{id}', [PermissionController::class, 'edit']);
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::delete('/permission/{id}', [PermissionController::class, 'destroy']);
    });
});

//Payment and subscription
Route::group(['middleware' => ['auth:api'], 'as' => 'payment.'], function () {
    Route::post('/payment', [PaymentController::class, 'payment']);
    Route::post('/upgrade-user', [PaymentController::class, 'upgradeUser']);
});

Route::get('/paypal/success-payment', [\App\Http\Controllers\Api\Payment\PaypalController::class, 'successPayment'])->name('paypal.successPayment');
Route::get('/paypal/success-payment-subscription', [\App\Http\Controllers\Api\Payment\PaypalController::class, 'successPaymentSubscription'])->name('paypal.successPaymentSubscription');
Route::get('/paypal/cancel-payment', [\App\Http\Controllers\Api\Payment\PaypalController::class, 'cancelPayment'])->name('paypal.cancelPayment');
Route::get('/paypal/cancel-payment-subscription', [\App\Http\Controllers\Api\Payment\PaypalController::class, 'cancelPaymentSubscription'])->name('paypal.cancelPaymentSubscription');

//Section Template
Route::group(['middleware' => ['auth:api'], 'as' => 'section-template'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('section-templates', [SectionTemplateController::class, 'index'])->name('index');
        Route::post('section-template', [SectionTemplateController::class, 'store'])->name('store');
        Route::get('section-template/{id}', [SectionTemplateController::class, 'show'])->name('show');
        Route::put('section-template/{id}', [SectionTemplateController::class, 'edit'])->name('edit');
        Route::delete('/section-template/{id}', [SectionTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/unpublished-section-templates', [SectionTemplateController::class, 'indexUnpublishedSectionTemplate'])->name('index-unpublished');
        Route::get('/unpublished-section-template/{id}', [SectionTemplateController::class, 'showUnpublishedSectionTemplate'])->name('show-unpublished');
        Route::post('section-template/accept-publish', [SectionTemplateController::class, 'acceptPublishSectionTemplate'])->name('acceptPublishSectionTemplate');
    });
    Route::group(['as' => 'my.'], function () {
        Route::get('my/section-templates', [SectionTemplateController::class, 'indexMy'])->name('indexMySectionTemplate');
        Route::post('my/section-template', [SectionTemplateController::class, 'storeMySectionTemplate'])->name('storeMySectionTemplate');
        Route::get('my/section-template/{id}', [SectionTemplateController::class, 'showMySectionTemplate'])->name('showMySectionTemplate');
        Route::put('my/section-template/{id}', [SectionTemplateController::class, 'editMySectionTemplate'])->name('editMySectionTemplate');
        Route::delete('my/section-template/{id}', [SectionTemplateController::class, 'destroyMySectionTemplate'])->name('destroyMySectionTemplate');
    });
});

//Form
Route::group(['middleware' => ['auth:api'], 'as' => 'form.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/forms', [FormController::class, 'index'])->name('index');
        Route::post('/form', [FormController::class, 'store'])->name('store');
        Route::get('/form/{id}', [FormController::class, 'show'])->name('show');
        Route::put('/form/{id}', [FormController::class, 'edit'])->name('edit');
        Route::delete('/form/{id}', [FormController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/forms', [FormController::class, 'indexMy'])->name('index');
        Route::post('/my/form', [FormController::class, 'storeMyForm'])->name('store');
        Route::get('/my/form/{id}', [FormController::class, 'showMyForm'])->name('show');
        Route::put('/my/form/{id}', [FormController::class, 'editMyForm'])->name('edit');
        Route::delete('/my/form/{id}', [FormController::class, 'destroyMyForm'])->name('destroy');
    });
});

Route::post('/form/submit-contact', [FormController::class, 'submitContact'])->name('form.submitContact');


//Language
Route::group(['middleware' => ['auth:api'], 'as' => 'language.'], function () {

    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::post('/language', [LanguageController::class, 'store'])->name('store');
        Route::put('/language/{id}', [LanguageController::class, 'edit'])->name('edit');
        Route::delete('/language/{id}', [LanguageController::class, 'destroy'])->name('destroy');
        Route::get('/show-translates-json', [LanguageController::class, 'showTranslates']);
        Route::post('/save-translates-json', [LanguageController::class, 'saveTranslates']);
    });
});

Route::get('/languages-support', [LanguageController::class, 'languageSupport'])->name('language.languageSupport');
Route::get('/languages', [LanguageController::class, 'index'])->name('language.index');
Route::get('/language/{id}', [LanguageController::class, 'show'])->name('language.show');

//Article Category
Route::group(['middleware' => ['auth:api'], 'as' => 'article-category.'], function () {
    Route::group(['middleware' => ['role:admin,editor'], 'as' => 'author.'], function () {
        Route::post('/article-category', [ArticleCategoryController::class, 'store'])->name('store');
        Route::put('/article-category/{id}', [ArticleCategoryController::class, 'edit'])->name('edit');
        Route::delete('/article-category/{id}', [ArticleCategoryController::class, 'destroy'])->name('destroy');
        Route::get('/article-categories', [ArticleCategoryController::class, 'index'])->name('index');
        Route::get('/article-category/{id}', [ArticleCategoryController::class, 'show'])->name('show');
    });
});
Route::get('public/article-categories', [ArticleCategoryController::class, 'index'])->name('article-categories-public.index');
Route::get('public/article-category/{id}', [ArticleCategoryController::class, 'show'])->name('article-categories-public.show');


Route::group(['middleware' => ['auth:api'], 'as' => 'group.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/groups', [GroupController::class, 'index'])->name('index');
        Route::post('/group', [GroupController::class, 'store'])->name('store');
        Route::get('/group/{id}', [GroupController::class, 'show'])->name('show');
        Route::put('/group/{id}', [GroupController::class, 'edit'])->name('edit');
        Route::delete('/group/{id}', [GroupController::class, 'destroy'])->name('destroy');
    });
});

//Article
Route::group(['middleware' => ['auth:api'], 'as' => 'article.'], function () {

    Route::group(['middleware' => ['role:admin,editor'], 'as' => 'author.'], function () {
        Route::post('/article', [ArticleController::class, 'store'])->name('store');
        Route::put('/article/{id}', [ArticleController::class, 'edit'])->name('edit');
        Route::delete('/article/{id}', [ArticleController::class, 'destroy'])->name('destroy');
        Route::get('/articles', [ArticleController::class, 'index'])->name('article.index');
        Route::get('/article/{id}', [ArticleController::class, 'show'])->name('article.show');
    });
});
Route::get('public/articles', [ArticleController::class, 'index'])->name('article-public.index');
Route::get('public/article/{id}', [ArticleController::class, 'show'])->name('article-public.index');


Route::group(['middleware' => ['auth:api'], 'as' => 'status.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/status', [StatusController::class, 'index'])->name('index');
        Route::post('/status', [StatusController::class, 'store'])->name('store');
        Route::get('/status/{id}', [StatusController::class, 'show'])->name('show');
        Route::put('/status/{id}', [StatusController::class, 'edit'])->name('edit');
        Route::delete('/status/{id}', [StatusController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/status', [StatusController::class, 'indexMyStatus'])->name('index');
        Route::post('/my/status', [StatusController::class, 'storeMyStatus'])->name('store');
        Route::get('/my/status/{id}', [StatusController::class, 'showMyStatus'])->name('show');
        Route::put('/my/status/{id}', [StatusController::class, 'editMyStatus'])->name('edit');
        Route::delete('/my/status/{id}', [StatusController::class, 'destroyMyStatus'])->name('destroy');
    });
});

Route::group(['middleware' => ['auth:api'], 'as' => 'company.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/companies', [CompanyController::class, 'index'])->name('index');
        Route::post('/company', [CompanyController::class, 'store'])->name('store');
        Route::get('/company/{id}', [CompanyController::class, 'show'])->name('show');
        Route::put('/company/{id}', [CompanyController::class, 'edit'])->name('edit');
        Route::delete('/company/{id}', [CompanyController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/companies', [CompanyController::class, 'indexMyCompany'])->name('index');
        Route::post('/my/company', [CompanyController::class, 'storeMyCompany'])->name('store');
        Route::get('/my/company/{id}', [CompanyController::class, 'showMyCompany'])->name('show');
        Route::put('/my/company/{id}', [CompanyController::class, 'editMyCompany'])->name('edit');
        Route::delete('/my/company/{id}', [CompanyController::class, 'destroyMyCompany'])->name('destroy');
    });
});

Route::group(['middleware' => ['auth:api'], 'as' => 'position.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/positions', [PositionController::class, 'index'])->name('index');
        Route::post('/position', [PositionController::class, 'store'])->name('store');
        Route::get('/position/{id}', [PositionController::class, 'show'])->name('show');
        Route::put('/position/{id}', [PositionController::class, 'edit'])->name('edit');
        Route::delete('/position/{id}', [PositionController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/positions', [PositionController::class, 'indexMyPosition'])->name('index');
        Route::post('/my/position', [PositionController::class, 'storeMyPosition'])->name('store');
        Route::get('/my/position/{id}', [PositionController::class, 'showMyPosition'])->name('show');
        Route::put('/my/position/{id}', [PositionController::class, 'editMyPosition'])->name('edit');
        Route::delete('/my/position/{id}', [PositionController::class, 'destroyMyPosition'])->name('destroy');
    });
});

Route::group(['middleware' => ['auth:api'], 'as' => 'note.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/notes', [NoteController::class, 'index'])->name('index');
        Route::post('/note', [NoteController::class, 'store'])->name('store');
        Route::get('/note/{id}', [NoteController::class, 'show'])->name('show');
        Route::put('/note/{id}', [NoteController::class, 'edit'])->name('edit');
        Route::delete('/note/{id}', [NoteController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/notes', [NoteController::class, 'indexMyNote'])->name('index');
        Route::post('/my/note', [NoteController::class, 'storeMyNote'])->name('store');
        Route::get('/my/note/{id}', [NoteController::class, 'showMyNote'])->name('show');
        Route::put('/my/note/{id}', [NoteController::class, 'editMyNote'])->name('edit');
        Route::delete('/my/note/{id}', [NoteController::class, 'destroyMyNote'])->name('destroy');
    });
});

Route::group(['middleware' => ['auth:api'], 'as' => 'remind.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/reminds', [RemindController::class, 'index'])->name('index');
        Route::post('/remind', [RemindController::class, 'store'])->name('store');
        Route::get('/remind/{id}', [RemindController::class, 'show'])->name('show');
        Route::put('/remind/{id}', [RemindController::class, 'edit'])->name('edit');
        Route::delete('/remind/{id}', [RemindController::class, 'destroy'])->name('destroy');
    });

    Route::group(['as' => 'my.'], function () {
        Route::get('/my/reminds', [RemindController::class, 'indexMyRemind'])->name('index');
        Route::post('/my/remind', [RemindController::class, 'storeMyRemind'])->name('store');
        Route::get('/my/remind/{id}', [RemindController::class, 'showMyRemind'])->name('show');
        Route::put('/my/remind/{id}', [RemindController::class, 'editMyRemind'])->name('edit');
        Route::delete('/my/remind/{id}', [RemindController::class, 'destroyMyRemind'])->name('destroy');
    });
});

Route::group(['middleware' => ['auth:api'], 'as' => 'activity_history.'], function () {
    Route::group(['middleware' => ['role:admin'], 'as' => 'admin.'], function () {
        Route::get('/activity-histories', [ActivityHistoryController::class, 'index'])->name('index');
        Route::post('/activity-history', [ActivityHistoryController::class, 'store'])->name('store');
        Route::get('/activity-history/{id}', [ActivityHistoryController::class, 'show'])->name('show');
        Route::put('/activity-history/{id}', [ActivityHistoryController::class, 'edit'])->name('edit');
        Route::delete('/activity-history/{id}', [ActivityHistoryController::class, 'destroy'])->name('destroy');
    });
});



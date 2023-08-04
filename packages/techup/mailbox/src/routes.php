<?php

use Illuminate\Support\Facades\Route;
use Techup\Mailbox\MailboxController;
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
Route::group(['middleware' => ['auth:api'], 'prefix'=>'mailbox', 'as' => 'mailbox.'], function () {
	Route::post('config', [MailboxController::class, 'postConfig'])->name('postConfig');

	Route::get('configs', [MailboxController::class, 'getConfigs'])->name('getConfigs');

	Route::post('email_account/create', [MailboxController::class, 'postEmailAccountcreate'])->name('postEmailAccountcreate');

	Route::get('emails', [MailboxController::class, 'getEmails'])->name('getEmails');

	Route::post('folder', [MailboxController::class, 'postFolder'])->name('postFolder');

	Route::get('folders', [MailboxController::class, 'getFolders'])->name('getFolders');

	Route::get('mail-box/', [MailboxController::class, 'getMailBox'])->name('getMailBox');

	Route::post('send-email/', [MailboxController::class, 'postSendEmail'])->name('postSendEmail');

	Route::get('sents/', [MailboxController::class, 'getSents'])->name('getSents');

	Route::delete("attachments/delete/{id}", [MailboxController::class, 'deleteAttachmentsdeleteid'])->name('deleteAttachmentsdeleteid');

	Route::get("attachments/get-by-email/{id}", [MailboxController::class, 'getAttachmentsgetByEmailid'])->name('getAttachmentsgetByEmailid');

	Route::get("attachments/get-by-sent/{id}", [MailboxController::class, 'getAttachmentsgetBySentid'])->name('getAttachmentsgetBySentid');

	Route::delete("config/{id}", [MailboxController::class, 'deleteConfigid'])->name('deleteConfigid');

	Route::get("config/{id}", [MailboxController::class, 'getConfigid'])->name('getConfigid');

	Route::put("config/{id}", [MailboxController::class, 'putConfigid'])->name('putConfigid');

	Route::delete("conversation/delete/{id}", [MailboxController::class, 'deleteConversationdeleteid'])->name('deleteConversationdeleteid');

	Route::get("conversation/{id}", [MailboxController::class, 'getConversationid'])->name('getConversationid');

	Route::get("email/get-email-conversation/{id}", [MailboxController::class, 'getEmailgetEmailConversationid'])->name('getEmailgetEmailConversationid');

	Route::delete("email/{id}", [MailboxController::class, 'deleteEmailid'])->name('deleteEmailid');

	Route::get("email/{id}", [MailboxController::class, 'getEmailid'])->name('getEmailid');

	Route::put("email/{id}", [MailboxController::class, 'putEmailid'])->name('putEmailid');

	Route::delete("folder/{id}", [MailboxController::class, 'deleteFolderid'])->name('deleteFolderid');

	Route::get("folder/{id}", [MailboxController::class, 'getFolderid'])->name('getFolderid');

	Route::put("folder/{id}", [MailboxController::class, 'putFolderid'])->name('putFolderid');

	Route::delete("send-email-address/delete/{id}", [MailboxController::class, 'deleteSendEmailAddressdeleteid'])->name('deleteSendEmailAddressdeleteid');

	Route::get("send-email-address/{id}", [MailboxController::class, 'getSendEmailAddressid'])->name('getSendEmailAddressid');

	Route::delete("sents/delete/{id}", [MailboxController::class, 'deleteSentsdeleteid'])->name('deleteSentsdeleteid');

});

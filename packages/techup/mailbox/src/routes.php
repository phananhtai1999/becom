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

	Route::get('email/count-unread', [MailboxController::class, 'getEmailcountUnread'])->name('getEmailcountUnread');

	Route::post('email/delete-emails', [MailboxController::class, 'postEmaildeleteEmails'])->name('postEmaildeleteEmails');

	Route::post('email/restore-emails', [MailboxController::class, 'postEmailrestoreEmails'])->name('postEmailrestoreEmails');

	Route::post('email/update-read', [MailboxController::class, 'postEmailupdateRead'])->name('postEmailupdateRead');

	Route::post('email_account/create', [MailboxController::class, 'postEmailAccountcreate'])->name('postEmailAccountcreate');

	Route::get('emails', [MailboxController::class, 'getEmails'])->name('getEmails');

	Route::get('emails/trash', [MailboxController::class, 'getEmailstrash'])->name('getEmailstrash');

	Route::post('folder', [MailboxController::class, 'postFolder'])->name('postFolder');

	Route::get('folders', [MailboxController::class, 'getFolders'])->name('getFolders');

	Route::get('mail-box/', [MailboxController::class, 'getMailBox'])->name('getMailBox');

	Route::post('send-email/', [MailboxController::class, 'postSendEmail'])->name('postSendEmail');

	Route::post('sent', [MailboxController::class, 'postSent'])->name('postSent');

	Route::get('sent-email-address', [MailboxController::class, 'getSentEmailAddress'])->name('getSentEmailAddress');

	Route::post('sent-email-address', [MailboxController::class, 'postSentEmailAddress'])->name('postSentEmailAddress');

	Route::post('sent/delete-sents', [MailboxController::class, 'postSentdeleteSents'])->name('postSentdeleteSents');

	Route::get('sents', [MailboxController::class, 'getSents'])->name('getSents');

	Route::delete("attachments/delete/{id}", [MailboxController::class, 'deleteAttachmentsdeleteid'])->name('deleteAttachmentsdeleteid');

	Route::get("attachments/get-by-email/{id}", [MailboxController::class, 'getAttachmentsgetByEmailid'])->name('getAttachmentsgetByEmailid');

	Route::get("attachments/get-by-sent/{id}", [MailboxController::class, 'getAttachmentsgetBySentid'])->name('getAttachmentsgetBySentid');

	Route::delete("config/{id}", [MailboxController::class, 'deleteConfigid'])->name('deleteConfigid');

	Route::get("config/{id}", [MailboxController::class, 'getConfigid'])->name('getConfigid');

	Route::put("config/{id}", [MailboxController::class, 'putConfigid'])->name('putConfigid');

	Route::get("email/get-email-conversation/{id}", [MailboxController::class, 'getEmailgetEmailConversationid'])->name('getEmailgetEmailConversationid');

	Route::delete("email/{id}", [MailboxController::class, 'deleteEmailid'])->name('deleteEmailid');

	Route::get("email/{id}", [MailboxController::class, 'getEmailid'])->name('getEmailid');

	Route::put("email/{id}", [MailboxController::class, 'putEmailid'])->name('putEmailid');

	Route::delete("folder/{id}", [MailboxController::class, 'deleteFolderid'])->name('deleteFolderid');

	Route::get("folder/{id}", [MailboxController::class, 'getFolderid'])->name('getFolderid');

	Route::put("folder/{id}", [MailboxController::class, 'putFolderid'])->name('putFolderid');

	Route::delete("sent-email-address/{id}", [MailboxController::class, 'deleteSentEmailAddressid'])->name('deleteSentEmailAddressid');

	Route::get("sent-email-address/{id}", [MailboxController::class, 'getSentEmailAddressid'])->name('getSentEmailAddressid');

	Route::put("sent-email-address/{id}", [MailboxController::class, 'putSentEmailAddressid'])->name('putSentEmailAddressid');

	Route::delete("sent/{id}", [MailboxController::class, 'deleteSentid'])->name('deleteSentid');

	Route::get("sent/{id}", [MailboxController::class, 'getSentid'])->name('getSentid');

	Route::put("sent/{id}", [MailboxController::class, 'putSentid'])->name('putSentid');

});

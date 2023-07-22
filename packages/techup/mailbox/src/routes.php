<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Techup\Mailbox\Facades\Mailbox;
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
	Route::get('email', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::getEmail($user_uuid);
	});

	Route::post('email/add-to-star', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$email_id = $request->get('email_id');
	    return Mailbox::postEmailaddToStar($user_uuid, $email_id);
	});

	Route::post('email/remove-star', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$email_id = $request->get('email_id');
	    return Mailbox::postEmailremoveStar($user_uuid, $email_id);
	});

	Route::get('sents', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::getSents($user_uuid);
	});

	Route::post('send-email', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$subject = $request->get('subject');
		$body = $request->get('body');
		$status = $request->get('status');
		$email_address = $request->get('email_address');
		$type = $request->get('type');
		$files = $request->get('files');
	    return Mailbox::postSendEmail($user_uuid, $subject, $body, $status, $email_address, $type, $files);
	});

	Route::get('mail-box', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::getMailBox($user_uuid);
	});

	Route::post('email_account/create', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$email_address = $request->get('email_address');
		$password = $request->get('password');
		$user_id = $request->get('user_id');
		$app_id = $request->get('app_id');
	    return Mailbox::postEmailAccountcreate($user_uuid, $email_address, $password, $user_id, $app_id);
	});

	Route::post('config/create', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$expiration_date = $request->get('expiration_date');
		$api_key = $request->get('api_key');
	    return Mailbox::postConfigcreate($user_uuid, $expiration_date, $api_key);
	});

	Route::get("email/get-email-conversation/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$Page = $request->get('Page');
		$PerPage = $request->get('PerPage');
	    return Mailbox::getEmailgetEmailConversationid($user_uuid, $id, $Page, $PerPage);
	});

	Route::delete("email/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::deleteEmaildeleteid($user_uuid, $id);
	});

	Route::get("conversation/{email_id}", function (Request $request, $email_id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::getConversationemailId($user_uuid, $email_id);
	});

	Route::delete("conversation/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::deleteConversationdeleteid($user_uuid, $id);
	});

	Route::delete("sents/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::deleteSentsdeleteid($user_uuid, $id);
	});

	Route::get("send-email-address/{email_id}", function (Request $request, $email_id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::getSendEmailAddressemailId($user_uuid, $email_id);
	});

	Route::delete("send-email-address/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::deleteSendEmailAddressdeleteid($user_uuid, $id);
	});

	Route::get("attachments/get-by-email/{email_id}", function (Request $request, $email_id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::getAttachmentsgetByEmailemailId($user_uuid, $email_id);
	});

	Route::get("attachments/get-by-sent/{sent_id}", function (Request $request, $sent_id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::getAttachmentsgetBySentsentId($user_uuid, $sent_id);
	});

	Route::delete("attachments/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	    return Mailbox::deleteAttachmentsdeleteid($user_uuid, $id);
	});

});

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
		$data = Mailbox::getEmail($user_uuid);
	    return response()->json($data->json(), $data->status());
	});

	Route::post('email/add-to-star', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$email_id = $request->get('email_id');
	    $data = Mailbox::postEmailaddToStar($user_uuid, $email_id);
	    return response()->json($data->json(), $data->status());
	});

	Route::post('email/remove-star', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$email_id = $request->get('email_id');
	    $data = Mailbox::postEmailremoveStar($user_uuid, $email_id);
	    return response()->json($data->json(), $data->status());
	});

	Route::get('sents', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getSents($user_uuid);
	    return response()->json($data->json(), $data->status());
	});

	Route::post('send-email', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$subject = $request->get('subject');
		$body = $request->get('body');
		$status = $request->get('status');
		$email_address = $request->get('email_address');
		$type = $request->get('type');
		$files = $request->get('files');
	    $data = Mailbox::postSendEmail($user_uuid, $subject, $body, $status, $email_address, $type, $files);
	    return response()->json($data->json(), $data->status());
	});

	Route::get('mail-box', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getMailBox($user_uuid);
	    return response()->json($data->json(), $data->status());
	});

	Route::post('email_account/create', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$email_address = $request->get('email_address');
		$password = $request->get('password');
		$user_id = $request->get('user_id');
		$app_id = $request->get('app_id');
	    $data = Mailbox::postEmailAccountcreate($user_uuid, $email_address, $password, $user_id, $app_id);
	    return response()->json($data->json(), $data->status());
	});

	Route::post('config/create', function (Request $request) {
		$user_uuid = auth()->user()->getkey();
		$expiration_date = $request->get('expiration_date');
		$api_key = $request->get('api_key');
	    $data = Mailbox::postConfigcreate($user_uuid, $expiration_date, $api_key);
	    return response()->json($data->json(), $data->status());
	});

	Route::get("email/get-email-conversation/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$Page = $request->get('Page');
		$PerPage = $request->get('PerPage');
		$data = Mailbox::getEmailgetEmailConversationid($user_uuid, $id, $Page, $PerPage);
	    return response()->json($data->json(), $data->status());
	});

	Route::delete("email/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteEmaildeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	});

	Route::get("conversation/{email_id}", function (Request $request, $email_id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getConversationemailId($user_uuid, $email_id);
	    return response()->json($data->json(), $data->status());
	});

	Route::delete("conversation/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteConversationdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	});

	Route::delete("sents/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteSentsdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	});

	Route::get("send-email-address/{email_id}", function (Request $request, $email_id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getSendEmailAddressemailId($user_uuid, $email_id);
	    return response()->json($data->json(), $data->status());
	});

	Route::delete("send-email-address/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteSendEmailAddressdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	});

	Route::get("attachments/get-by-email/{email_id}", function (Request $request, $email_id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getAttachmentsgetByEmailemailId($user_uuid, $email_id);
	    return response()->json($data->json(), $data->status());
	});

	Route::get("attachments/get-by-sent/{sent_id}", function (Request $request, $sent_id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getAttachmentsgetBySentsentId($user_uuid, $sent_id);
	    return response()->json($data->json(), $data->status());
	});

	Route::delete("attachments/delete/{id}", function (Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteAttachmentsdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	});

});

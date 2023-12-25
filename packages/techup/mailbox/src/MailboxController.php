<?php

namespace Techup\Mailbox;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Techup\Mailbox\Facades\Mailbox;
class MailboxController  extends Controller
{


	public function postConfig(Request $request) {
		$user_uuid = auth()->userId();
		$default_value = $request->get('default_value');
		$key = $request->get('key');
		$value = $request->get('value');
	    $data = Mailbox::postConfig($user_uuid, $default_value, $key, $value);
	    return response()->json($data->json(), $data->status());

	}


	public function getConfigs(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getConfigs($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function getEmailSearchs(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getEmailSearchs($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function getEmailcountUnread(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getEmailcountUnread($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function postEmaildeleteEmails(Request $request) {
		$user_uuid = auth()->userId();
		$ids = $request->get('ids');
	    $data = Mailbox::postEmaildeleteEmails($user_uuid, $ids);
	    return response()->json($data->json(), $data->status());

	}


	public function postEmailrestoreEmails(Request $request) {
		$user_uuid = auth()->userId();
		$ids = $request->get('ids');
	    $data = Mailbox::postEmailrestoreEmails($user_uuid, $ids);
	    return response()->json($data->json(), $data->status());

	}


	public function postEmailupdateRead(Request $request) {
		$user_uuid = auth()->userId();
		$ids = $request->get('ids');
	    $data = Mailbox::postEmailupdateRead($user_uuid, $ids);
	    return response()->json($data->json(), $data->status());

	}


	public function postEmailupdateUnread(Request $request) {
		$user_uuid = auth()->userId();
		$ids = $request->get('ids');
	    $data = Mailbox::postEmailupdateUnread($user_uuid, $ids);
	    return response()->json($data->json(), $data->status());

	}


	public function postEmailAccountcreate(Request $request) {
		$user_uuid = auth()->userId();
		$email_address = $request->get('email_address');
		$password = $request->get('password');
	    $data = Mailbox::postEmailAccountcreate($user_uuid, $email_address, $password);
	    return response()->json($data->json(), $data->status());

	}


	public function getEmails(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getEmails($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function getEmailstrash(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getEmailstrash($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function postFolder(Request $request) {
		$user_uuid = auth()->userId();
		$title = $request->get('title');
	    $data = Mailbox::postFolder($user_uuid, $title);
	    return response()->json($data->json(), $data->status());

	}


	public function getFolders(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getFolders($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function getMailBox(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getMailBox($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function postSendEmail(Request $request) {
		$user_uuid = auth()->userId();
		$bcc = $request->get('bcc');
		$body = $request->get('body');
		$cc = $request->get('cc');
		$email_address = $request->get('email_address');
		$files = $request->get('files');
		$schedule_at = $request->get('schedule_at');
		$subject = $request->get('subject');
		$type = $request->get('type');
	    $data = Mailbox::postSendEmail($user_uuid, $bcc, $body, $cc, $email_address, $files, $schedule_at, $subject, $type);
	    return response()->json($data->json(), $data->status());

	}


	public function postSent(Request $request) {
		$user_uuid = auth()->userId();
		$body = $request->get('body');
		$email_account_id = $request->get('email_account_id');
		$status = $request->get('status');
		$subject = $request->get('subject');
	    $data = Mailbox::postSent($user_uuid, $body, $email_account_id, $status, $subject);
	    return response()->json($data->json(), $data->status());

	}


	public function getSentEmailAddress(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getSentEmailAddress($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function postSentEmailAddress(Request $request) {
		$user_uuid = auth()->userId();
		$email_account_id = $request->get('email_account_id');
		$email_address = $request->get('email_address');
		$type = $request->get('type');
	    $data = Mailbox::postSentEmailAddress($user_uuid, $email_account_id, $email_address, $type);
	    return response()->json($data->json(), $data->status());

	}


	public function postSentdeleteSents(Request $request) {
		$user_uuid = auth()->userId();
		$ids = $request->get('ids');
	    $data = Mailbox::postSentdeleteSents($user_uuid, $ids);
	    return response()->json($data->json(), $data->status());

	}


	public function getSents(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getSents($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function getSetting(Request $request) {
		$user_uuid = auth()->userId();
		$per_page = $request->get('per_page');
		$page = $request->get('page');
		$search = $request->get('search');
		$filter = $request->get('filter');
		$data = Mailbox::getSetting($user_uuid, $per_page, $page, $search, $filter);
	    return response()->json($data->json(), $data->status());

	}


	public function postSetting(Request $request) {
		$user_uuid = auth()->userId();
		$value = $request->get('value');
	    $data = Mailbox::postSetting($user_uuid, $value);
	    return response()->json($data->json(), $data->status());

	}


	public function postTrashdelete(Request $request) {
		$user_uuid = auth()->userId();
		$objects = $request->get('objects');
	    $data = Mailbox::postTrashdelete($user_uuid, $objects);
	    return response()->json($data->json(), $data->status());

	}


	public function postTrashrestore(Request $request) {
		$user_uuid = auth()->userId();
		$objects = $request->get('objects');
	    $data = Mailbox::postTrashrestore($user_uuid, $objects);
	    return response()->json($data->json(), $data->status());

	}


	public function deleteAttachmentsdeleteid(Request $request, $id) {
		$user_uuid = auth()->userId();
	   	$data = Mailbox::deleteAttachmentsdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function getAttachmentsgetByEmailid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$data = Mailbox::getAttachmentsgetByEmailid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}


	public function getAttachmentsgetBySentid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$data = Mailbox::getAttachmentsgetBySentid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}


	public function deleteConfigid(Request $request, $id) {
		$user_uuid = auth()->userId();
	   	$data = Mailbox::deleteConfigid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function getConfigid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$data = Mailbox::getConfigid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}


	public function putConfigid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$default_value = $request->get('default_value');
		$key = $request->get('key');
		$value = $request->get('value');
	    $data = Mailbox::putConfigid($user_uuid, $default_value, $key, $value, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function getEmailgetEmailConversationid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$Page = $request->get('Page');
		$PerPage = $request->get('PerPage');
		$data = Mailbox::getEmailgetEmailConversationid($user_uuid, $id, $Page, $PerPage);
	    return response()->json($data->json(), $data->status());

	}


	public function deleteEmailid(Request $request, $id) {
		$user_uuid = auth()->userId();
	   	$data = Mailbox::deleteEmailid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function getEmailid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$data = Mailbox::getEmailid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}


	public function putEmailid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$star = $request->get('star');
	    $data = Mailbox::putEmailid($user_uuid, $star, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function deleteFolderid(Request $request, $id) {
		$user_uuid = auth()->userId();
	   	$data = Mailbox::deleteFolderid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function getFolderid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$data = Mailbox::getFolderid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}


	public function putFolderid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$title = $request->get('title');
	    $data = Mailbox::putFolderid($user_uuid, $title, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function deleteSentEmailAddressid(Request $request, $id) {
		$user_uuid = auth()->userId();
	   	$data = Mailbox::deleteSentEmailAddressid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function getSentEmailAddressid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$data = Mailbox::getSentEmailAddressid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}


	public function putSentEmailAddressid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$email_account_id = $request->get('email_account_id');
		$email_address = $request->get('email_address');
		$type = $request->get('type');
	    $data = Mailbox::putSentEmailAddressid($user_uuid, $email_account_id, $email_address, $type, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function deleteSentid(Request $request, $id) {
		$user_uuid = auth()->userId();
	   	$data = Mailbox::deleteSentid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	}


	public function getSentid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$data = Mailbox::getSentid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}


	public function putSentid(Request $request, $id) {
		$user_uuid = auth()->userId();
		$star = $request->get('star');
	    $data = Mailbox::putSentid($user_uuid, $star, $id);
	    return response()->json($data->json(), $data->status());
	}

}

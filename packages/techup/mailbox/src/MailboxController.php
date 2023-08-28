<?php

namespace Techup\Mailbox;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Techup\Mailbox\Facades\Mailbox;
class MailboxController  extends Controller
{

    
	public function postConfig(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$default_value = $request->get('default_value');
		$key = $request->get('key');
		$value = $request->get('value');
	    $data = Mailbox::postConfig($user_uuid, $default_value, $key, $value);
	    return response()->json($data->json(), $data->status());

	}   


	public function getConfigs(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getConfigs($user_uuid);
	    return response()->json($data->json(), $data->status());

	}  


	public function postEmailAccountcreate(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$email_address = $request->get('email_address');
		$password = $request->get('password');
	    $data = Mailbox::postEmailAccountcreate($user_uuid, $email_address, $password);
	    return response()->json($data->json(), $data->status());

	}   


	public function getEmails(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getEmails($user_uuid);
	    return response()->json($data->json(), $data->status());

	}  


	public function postFolder(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$title = $request->get('title');
	    $data = Mailbox::postFolder($user_uuid, $title);
	    return response()->json($data->json(), $data->status());

	}   


	public function getFolders(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getFolders($user_uuid);
	    return response()->json($data->json(), $data->status());

	}  


	public function getMailBox(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getMailBox($user_uuid);
	    return response()->json($data->json(), $data->status());

	}  


	public function postSendEmail(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$body = $request->get('body');
		$email_address = $request->get('email_address');
		$files = $request->get('files');
		$subject = $request->get('subject');
		$type = $request->get('type');
	    $data = Mailbox::postSendEmail($user_uuid, $body, $email_address, $files, $subject, $type);
	    return response()->json($data->json(), $data->status());

	}   


	public function getSents(Request $request) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getSents($user_uuid);
	    return response()->json($data->json(), $data->status());

	}  


	public function deleteAttachmentsdeleteid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteAttachmentsdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	} 


	public function getAttachmentsgetByEmailid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getAttachmentsgetByEmailid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}  


	public function getAttachmentsgetBySentid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getAttachmentsgetBySentid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}  


	public function deleteConfigid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteConfigid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	} 


	public function getConfigid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getConfigid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}  


	public function putConfigid(Request $request, $id) {	
		$user_uuid = auth()->user()->getkey();
		$default_value = $request->get('default_value');
		$key = $request->get('key');
		$value = $request->get('value');
	    $data = Mailbox::putConfigid($user_uuid, $default_value, $key, $value, $id);
	    return response()->json($data->json(), $data->status());
	}    


	public function deleteConversationdeleteid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteConversationdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	} 


	public function getConversationid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getConversationid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}  


	public function getEmailgetEmailConversationid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$Page = $request->get('Page');
		$PerPage = $request->get('PerPage');
		$data = Mailbox::getEmailgetEmailConversationid($user_uuid, $id, $Page, $PerPage);
	    return response()->json($data->json(), $data->status());

	}  


	public function deleteEmailid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteEmailid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	} 


	public function getEmailid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getEmailid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}  


	public function putEmailid(Request $request, $id) {	
		$user_uuid = auth()->user()->getkey();
		$star = $request->get('star');
	    $data = Mailbox::putEmailid($user_uuid, $star, $id);
	    return response()->json($data->json(), $data->status());
	}    


	public function deleteFolderid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteFolderid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	} 


	public function getFolderid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getFolderid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}  


	public function putFolderid(Request $request, $id) {	
		$user_uuid = auth()->user()->getkey();
		$title = $request->get('title');
	    $data = Mailbox::putFolderid($user_uuid, $title, $id);
	    return response()->json($data->json(), $data->status());
	}    


	public function deleteSendEmailAddressdeleteid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteSendEmailAddressdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	} 


	public function getSendEmailAddressid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
		$data = Mailbox::getSendEmailAddressid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());

	}  


	public function deleteSentsdeleteid(Request $request, $id) {
		$user_uuid = auth()->user()->getkey();
	   	$data = Mailbox::deleteSentsdeleteid($user_uuid, $id);
	    return response()->json($data->json(), $data->status());
	} 

}
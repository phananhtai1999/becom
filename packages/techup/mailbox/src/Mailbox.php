<?php

namespace Techup\Mailbox;
use Illuminate\Support\Facades\Http;

class Mailbox {
	private $baseUrl;
	public function __construct() {
        $this->baseUrl = rtrim(config('mailbox.base_url'), '/');
    }

    public function getRequestUrl($route){
    	return $this->baseUrl . '/' . ltrim($route, '/');
    }


	public function postConfig($user_uuid, $default_value, $key, $value) {
		$data = [
			'default_value' => $default_value,
			'key' => $key,
			'value' => $value,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('config'), $data);
	}   

	public function getConfigs($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('configs'), $data);
	}  

	public function postEmailAccountcreate($user_uuid, $email_address, $password) {
		$data = [
			'email_address' => $email_address,
			'password' => $password,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email_account/create'), $data);
	}   

	public function getEmails($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('emails'), $data);
	}  

	public function postFolder($user_uuid, $title) {
		$data = [
			'title' => $title,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('folder'), $data);
	}   

	public function getFolders($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('folders'), $data);
	}  

	public function getMailBox($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('mail-box/'), $data);
	}  

	public function postSendEmail($user_uuid, $body, $email_address, $files, $status, $subject, $type) {
		$data = [
			'body' => $body,
			'email_address' => $email_address,
			'files' => $files,
			'status' => $status,
			'subject' => $subject,
			'type' => $type,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('send-email/'), $data);
	}   

	public function getSents($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('sents/'), $data);
	}  

	public function deleteAttachmentsdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("attachments/delete/{$id}"));
	} 

	public function getAttachmentsgetByEmailid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("attachments/get-by-email/{$id}"), $data);
	}  

	public function getAttachmentsgetBySentid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("attachments/get-by-sent/{$id}"), $data);
	}  

	public function deleteConfigid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("config/{$id}"));
	} 

	public function getConfigid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("config/{$id}"), $data);
	}  

	public function putConfigid($user_uuid, $default_value, $key, $value, $id) {	
		$data = [
			'default_value' => $default_value,
			'key' => $key,
			'value' => $value,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->put($this->getRequestUrl("config/{$id}"), $data);
	}    

	public function deleteConversationdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("conversation/delete/{$id}"));
	} 

	public function getConversationid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("conversation/{$id}"), $data);
	}  

	public function getEmailgetEmailConversationid($user_uuid, $id, $Page, $PerPage) {
		$data = [
			'Page' => $Page,
			'PerPage' => $PerPage,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("email/get-email-conversation/{$id}"), $data);
	}  

	public function deleteEmailid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("email/{$id}"));
	} 

	public function getEmailid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("email/{$id}"), $data);
	}  

	public function putEmailid($user_uuid, $star, $id) {	
		$data = [
			'star' => $star,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->put($this->getRequestUrl("email/{$id}"), $data);
	}    

	public function deleteFolderid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("folder/{$id}"));
	} 

	public function getFolderid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("folder/{$id}"), $data);
	}  

	public function putFolderid($user_uuid, $title, $id) {	
		$data = [
			'title' => $title,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->put($this->getRequestUrl("folder/{$id}"), $data);
	}    

	public function deleteSendEmailAddressdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("send-email-address/delete/{$id}"));
	} 

	public function getSendEmailAddressid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("send-email-address/{$id}"), $data);
	}  

	public function deleteSentsdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("sents/delete/{$id}"));
	} 

}
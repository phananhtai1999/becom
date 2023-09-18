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

	public function getConfigs($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('configs'), $data);
	}  

	public function getEmailcountUnread($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('email/count-unread'), $data);
	}  

	public function postEmaildeleteEmails($user_uuid, $ids) {
		$data = [
			'ids' => $ids,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email/delete-emails'), $data);
	}   

	public function postEmailrestoreEmails($user_uuid, $ids) {
		$data = [
			'ids' => $ids,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email/restore-emails'), $data);
	}   

	public function postEmailupdateRead($user_uuid, $ids) {
		$data = [
			'ids' => $ids,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email/update-read'), $data);
	}   

	public function postEmailupdateUnread($user_uuid, $ids) {
		$data = [
			'ids' => $ids,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email/update-unread'), $data);
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

	public function getEmails($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('emails'), $data);
	}  

	public function getEmailstrash($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('emails/trash'), $data);
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

	public function getFolders($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('folders'), $data);
	}  

	public function getMailBox($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('mail-box/'), $data);
	}  

	public function postSendEmail($user_uuid, $bcc, $body, $cc, $email_address, $files, $subject, $type) {
		$data = [
			'bcc' => $bcc,
			'body' => $body,
			'cc' => $cc,
			'email_address' => $email_address,
			'files' => $files,
			'subject' => $subject,
			'type' => $type,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('send-email/'), $data);
	}   

	public function postSent($user_uuid, $body, $email_account_id, $status, $subject) {
		$data = [
			'body' => $body,
			'email_account_id' => $email_account_id,
			'status' => $status,
			'subject' => $subject,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('sent'), $data);
	}   

	public function getSentEmailAddress($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('sent-email-address'), $data);
	}  

	public function postSentEmailAddress($user_uuid, $email_account_id, $email_address, $type) {
		$data = [
			'email_account_id' => $email_account_id,
			'email_address' => $email_address,
			'type' => $type,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('sent-email-address'), $data);
	}   

	public function postSentdeleteSents($user_uuid, $ids) {
		$data = [
			'ids' => $ids,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('sent/delete-sents'), $data);
	}   

	public function getSents($user_uuid, $per_page, $page, $search, $filter) {
		$data = [
			'per_page' => $per_page,
			'page' => $page,
			'search' => $search,
			'filter' => $filter,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('sents'), $data);
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

	public function deleteSentEmailAddressid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("sent-email-address/{$id}"));
	} 

	public function getSentEmailAddressid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("sent-email-address/{$id}"), $data);
	}  

	public function putSentEmailAddressid($user_uuid, $email_account_id, $email_address, $type, $id) {	
		$data = [
			'email_account_id' => $email_account_id,
			'email_address' => $email_address,
			'type' => $type,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->put($this->getRequestUrl("sent-email-address/{$id}"), $data);
	}    

	public function deleteSentid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("sent/{$id}"));
	} 

	public function getSentid($user_uuid, $id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("sent/{$id}"), $data);
	}  

	public function putSentid($user_uuid, $body, $email_account_id, $status, $subject, $id) {	
		$data = [
			'body' => $body,
			'email_account_id' => $email_account_id,
			'status' => $status,
			'subject' => $subject,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->put($this->getRequestUrl("sent/{$id}"), $data);
	}    

}
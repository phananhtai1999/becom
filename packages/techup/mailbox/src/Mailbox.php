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
	public function send_request($route, $data = [], $type = 'post') {
		if ($type === 'post') {
			return Http::accept('application/json')->post($this->getRequestUrl($route), $data);
		} elseif ($type === 'get') {
			$data['client_id'] = config('mailbox.client_id');
			return Http::accept('application/json')->get(rtrim(config('mailbox.sending_url'), '/') . '/' . ltrim($route, '/'), $data);
		}

	}

	public function getEmail($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('email'), $data);
	}  

	public function postEmailaddToStar($user_uuid, $email_id) {
		$data = [
			'email_id' => $email_id,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email/add-to-star'), $data);
	}   

	public function postEmailremoveStar($user_uuid, $email_id) {
		$data = [
			'email_id' => $email_id,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email/remove-star'), $data);
	}   

	public function getSents($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('sents'), $data);
	}  

	public function postSendEmail($user_uuid, $subject, $body, $status, $email_address, $type, $files) {
		$data = [
			'subject' => $subject,
			'body' => $body,
			'status' => $status,
			'email_address' => $email_address,
			'type' => $type,
			'files' => $files,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('send-email'), $data);
	}   

	public function getMailBox($user_uuid) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl('mail-box'), $data);
	}  

	public function postEmailAccountcreate($user_uuid, $email_address, $password, $user_id, $app_id) {
		$data = [
			'email_address' => $email_address,
			'password' => $password,
			'user_id' => $user_id,
			'app_id' => $app_id,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('email_account/create'), $data);
	}   

	public function postConfigcreate($user_uuid, $expiration_date, $api_key) {
		$data = [
			'expiration_date' => $expiration_date,
			'api_key' => $api_key,
		];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->post($this->getRequestUrl('config/create'), $data);
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

	public function deleteEmaildeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("email/delete/{$id}"));
	} 

	public function getConversationemailId($user_uuid, $email_id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("conversation/{$email_id}"), $data);
	}  

	public function deleteConversationdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("conversation/delete/{$id}"));
	} 

	public function deleteSentsdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("sents/delete/{$id}"));
	} 

	public function getSendEmailAddressemailId($user_uuid, $email_id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("send-email-address/{$email_id}"), $data);
	}  

	public function deleteSendEmailAddressdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("send-email-address/delete/{$id}"));
	} 

	public function getAttachmentsgetByEmailemailId($user_uuid, $email_id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("attachments/get-by-email/{$email_id}"), $data);
	}  

	public function getAttachmentsgetBySentsentId($user_uuid, $sent_id) {
		$data = [];
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->get($this->getRequestUrl("attachments/get-by-sent/{$sent_id}"), $data);
	}  

	public function deleteAttachmentsdeleteid($user_uuid, $id) {
		return Http::accept('application/json')->withHeaders([
            'x-user-id' => $user_uuid,
            'Authorization' => 'Bearer ' . config('mailbox.access_token')
        ])->delete($this->getRequestUrl("attachments/delete/{$id}"));
	} 

}
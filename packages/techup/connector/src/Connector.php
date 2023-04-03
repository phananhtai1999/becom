<?php

namespace Techup\Connector;
use Illuminate\Support\Facades\Http;
class Connector
{
	public function send_request($route, $data, $type = 'post'){
		return Http::accept('application/json')->post(rtrim(config('connector.sending_url'), '/')  . '/' . ltrim($route, '/'), $data);

	}

    public function send_campaign($data){
 		return $this->send_request('campaign', $data);
    }
}
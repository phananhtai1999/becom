<?php

namespace Techup\Connector;
use Illuminate\Support\Facades\Http;

class Connector {
	public function send_request($route, $data = [], $type = 'post') {
		if ($type === 'post') {
			return Http::accept('application/json')->post(rtrim(config('connector.sending_url'), '/') . '/' . ltrim($route, '/'), $data);
		} elseif ($type === 'get') {
			$data['client_id'] = config('connector.client_id');
			return Http::accept('application/json')->get(rtrim(config('connector.sending_url'), '/') . '/' . ltrim($route, '/'), $data);
		}

	}

	public function send_campaign($data) {
		return $this->send_request('campaign', $data);
	}

	public function get_receivers() {
		return $this->send_request('processed-receivers', [], 'get');
	}

	public function get_all_receivers() {
		return $this->send_request('processed-receivers', ['get_all' => 1], 'get');
	}
}
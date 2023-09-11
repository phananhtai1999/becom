<?php

namespace Techup\SiteController;
use Illuminate\Support\Facades\Http;

class SiteController {
	private $baseUrl;
	public function __construct() {
        $this->baseUrl = rtrim(config('site_controller.base_url'), '/');
    }

    public function getRequestUrl($route){
    	return $this->baseUrl . '/' . ltrim($route, '/');
    }


	public function postDeployments($domain, $website_id) {
		$data = [
			'domain' => $domain,
			'website_id' => $website_id,
		];
		return Http::accept('application/json')->post($this->getRequestUrl('deployments'), $data);
	}   

	public function postDeploySsl($domain, $website_id) {
		$data = [
			'domain' => $domain,
			'website_id' => $website_id,
		];
		return Http::accept('application/json')->post($this->getRequestUrl('deploy-ssl'), $data);
	}   

}
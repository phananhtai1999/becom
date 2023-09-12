<?php

namespace Techup\SiteController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Techup\SiteController\Facades\SiteController;
class SiteControllerController  extends Controller
{

    
	public function postDeployments(Request $request) {
		$domain = $request->get('domain');
		$website_id = $request->get('website_id');
	    $data = SiteController::postDeployments($domain, $website_id);
	    return response()->json($data->json(), $data->status());

	}   


	public function postDeploySsl(Request $request) {
		$domain = $request->get('domain');
		$website_id = $request->get('website_id');
	    $data = SiteController::postDeploySsl($domain, $website_id);
	    return response()->json($data->json(), $data->status());

	}   

}
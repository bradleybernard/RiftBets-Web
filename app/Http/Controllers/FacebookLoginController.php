<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use DB;


class FacebookLoginController extends Controller
{

    public function test(Request $request)
    {
    	$fb = app(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk::class);
    	$accessToken = $request['facebook_access_token'];

    	$users = [];

    	try {
  			$response = $fb->get('/me?fields=id,name,email', $accessToken);
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
  			dd($e->getMessage());
		}

		$userNode = $response->getGraphUser();
		
		$users[] = [
            'facebook_id' => $userNode->getId(),
            'name'   	  => $userNode->getName(),
            'email'       => $userNode->getEmail(),
            'points'	  => 0
        ]; 

        $exists = DB::table('users')->where('facebook_id', $userNode->getId())->first();

		if(!$exists)
   		{
        	DB::table('users')->insert($users);
        }
        
    }
    
}

<?php

namespace App\Http\Controllers\Facebook;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;

use DB;
use App\User;
use JWTAuth;
use Log;

class FacebookController extends Controller
{
    public function facebook(Request $request)
    {
    	$fb = app(\SammyK\LaravelFacebookSdk\LaravelFacebookSdk::class);
    	$accessToken = $request['facebook_access_token'];

    	try {
  			$response = $fb->get('/me?fields=id,name,email', $accessToken);
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
            Log::error($e->getMessage());
  			dd($e->getMessage());
		}

		$userNode = $response->getGraphUser();

        if(!$user = User::where('facebook_id', $userNode->getId())->first()) {
            $user = User::create([
                'facebook_id' => $userNode->getId(),
                'name'        => $userNode->getName(),
                'email'       => $userNode->getEmail(),
                'credits'      => 0
            ]);

            DB::table('user_stats')->insert([
                'user_id'       => $user->id,
                'created_at'    => \Carbon\Carbon::now(),
                'updated_at'    => \Carbon\Carbon::now(),
            ]);
        }

        $token = JWTAuth::fromUser($user);

        return $this->response->array([
            'token' => $token,
            'user'  => $user,
        ]);
    }
}

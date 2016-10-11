<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;

use App\User;
use JWTAuth;
use DB;

class TestController extends Controller
{
    public function generate(Request $request)
    {
        if(!$user = User::where('id', 1)->first()) {
            $user = User::create([
                'facebook_id'   => '1',
                'name'          => 'travis',
                'credit'        => 1,
            ]);
        }

        $token = JWTAuth::fromUser($user);
        
        return $this->response->array([
            'token' => $token,
            'user'  => $user,
        ]);
    }

    public function test()
    {
    	$user = DB::table('leagues')->where('slug', 'worlds')->first();
    	return $this->response->array((array)$user);
    }
}

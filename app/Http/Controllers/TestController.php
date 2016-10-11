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
        if(!$user = User::where('id', 1)->first()) 
        {
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

    public function authenticate(Request $request)
    {
    	try 
    	{
	    	if (! $user = JWTAuth::parseToken()->authenticate()) 
	    	{
	            return response()->json(['user_not_found'], 404);
	        }
    	} 

    	catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) 
    	{
        	return response()->json(['token_expired'], $e->getStatusCode());
	    } 

	    catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) 
	    {
	        return response()->json(['token_invalid'], $e->getStatusCode());
	    }

	    catch (Tymon\JWTAuth\Exceptions\JWTException $e) 
	    {
	        return response()->json(['token_absent'], $e->getStatusCode());
	    }

	    // the token is valid and we have found the user via the sub claim
	    return response()->json(compact('user'));
    }

    public function test()
    {
    	$user = DB::table('leagues')->where('slug', 'worlds')->first();
    	return $this->response->array((array)$user);
    }
}

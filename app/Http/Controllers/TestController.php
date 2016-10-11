<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

use App\Http\Requests;
use DB;
use Dingo\Api\Routing\Helpers;


class TestController extends Controller implements AuthenticatableContract
{
	use Helpers;
	use Authenticatable;

	public function __construct()
	{
        $this->middleware('api.auth');

        // Only apply to a subset of methods.
        $this->middleware('api.auth', ['only' => ['test']]);
	}

    public function test()
    {
    	$user = DB::table('leagues')->where('slug', 'worlds')->first();
    	return $this->response->array((array)$user);
    }
}

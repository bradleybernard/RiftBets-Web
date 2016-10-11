<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use App\Http\Requests;
use DB;
use Dingo\Api\Routing\Helpers;


class TestController extends Controller
{
	use Helpers;

    public function test()
    {
    	$user = DB::table('leagues')->where('slug', 'worlds')->first();
    	return $this->response->array((array)$user);
    }
}

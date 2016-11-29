<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NewUserOutputsJWTTest extends TestCase
{
    use DatabaseMigrations;
    
    // Make sure user can generate a JWT token for API auth after creation
    public function test_new_user_outputs_jwt()
    {
        $accessToken = 'EAAKzu2L3NZCIBANqtTkEcqUqsOS0HaQAOOiTJaPSU2MlAV2ZBDSvSZCMpy6qAlUXTDKQK3UxKrFm5tZAmHofK2krZArEZBluCFo3lkZCbH437pi4DZBFFUmcHgZBQSmPPMfXZAkrgmPFOhjxZAYS7ro9ggPFIQKZA8PwoUZCwjI0jP3u9UwZDZD';

        $this->json('POST', '/api/auth/facebook', ['facebook_access_token' => $accessToken])
             ->seeJsonStructure([
                'token',
                'user' => [
                    'id', 
                    'facebook_id',
                    'name', 
                    'email', 
                    'credits',
                    'device_token',
                    'created_at',
                    'updated_at',
                ]
        ]);
    }
}

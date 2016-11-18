<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use DB;

use Sly\NotificationPusher\PushManager,
    Sly\NotificationPusher\Adapter\Apns as ApnsAdapter,
    Sly\NotificationPusher\Collection\DeviceCollection,
    Sly\NotificationPusher\Model\Device,
    Sly\NotificationPusher\Model\Message,
    Sly\NotificationPusher\Model\Push;

use Config;

class PushNotificationsForGradedBets implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $columns = ['bets.id as bet_id','bets.game_id', 'bets.credits_won', 'bets.details_placed',
                    'users.name', 'users.name', 'users.device_token'];

        $unpushedBets = DB::table('bets')->select($columns)
                            ->where('bets.is_complete', true)
                            ->where('bets.is_pushed', false)
                            ->join('users', 'users.id', '=', 'bets.user_id')
                            ->get()
                            ->keyBy('bet_id');

        foreach ($unpushedBets as $bet) 
        {
            $details = DB::table('bet_details')
                            ->where('bet_id', $bet->bet_id)
                            ->get();

            $detailsWon = $details->sum('won');

            $bet->details_won = $detailsWon;

            $matchId = DB::table('games')->select('api_match_id', 'name')
                            ->where('game_id', $bet->game_id)
                            ->get()
                            ->first();

            $bet->api_match_id = $matchId->api_match_id;

            $teams = DB::table('matches')->select(['api_resource_id_one', 'api_resource_id_two'])
                            ->where('api_id_long', $matchId->api_match_id)
                            ->get()
                            ->first();
            
            $teamOneName = DB::table('rosters')->select('name')
                            ->where('api_id_long', $teams->api_resource_id_one)
                            ->get()
                            ->first();

            $teamTwoName = DB::table('rosters')->select('name')
                            ->where('api_id_long', $teams->api_resource_id_two)
                            ->get()
                            ->first();

            $bet->game_matchup = $teamOneName->name .' vs '. $teamTwoName->name;

            $bet->game_name = 'Game ' .$matchId->name[1];
        }

        $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

        $apnsAdapter = new ApnsAdapter([
            'certificate' => Config::get('services.push_ios.certificate'),
            'passPhrase' => Config::get('services.push_ios.passphrase'),
        ]);

        foreach($unpushedBets as $bet)
        {
            $devices = new DeviceCollection([
                new Device($bet->device_token),
            ]);

            $message = new Message($this->formatMessage($bet));

            $push = new Push($apnsAdapter, $devices, $message);
            $pushManager->add($push);
        }

        $pushManager->push();
        
    }

    private function formatMessage($bet)
    {
        $message = strstr($bet->name, ' ', true) .', '. $bet->game_name .' of '. $bet->game_matchup 
                    .' has completed. You answered ' .$bet->details_won. ' out of ' .$bet->details_placed
                    .' questions correctly and won ' .number_format($bet->credits_won, 0, '', ','). ' credits!';

        return $message;
    }
}

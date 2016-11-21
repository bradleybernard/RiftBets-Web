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
                    'bets.credits_placed', 'users.name', 'users.name', 'users.device_token'];

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

            $teams = DB::table('matches')->select(['api_resource_id_one', 'api_resource_id_two',
                                                    'state', 'api_bracket_id', 'score_one', 'score_two'])
                            ->where('api_id_long', $matchId->api_match_id)
                            ->get()
                            ->first();

            $bet->match_state = $teams->state;
            
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

            $win = DB::table('game_team_stats')->select('win')
                        ->where('game_id', $bet->game_id)
                        ->where('team_id', 100)
                        ->get()
                        ->first();

            $bet->winner = ($win->win == 1 ? $teamOneName->name : $teamTwoName->name);
            $bet->loser = ($win->win == 1 ? $teamTwoName->name : $teamOneName->name);

            $bestof = DB::table('brackets')->select(['name as bracket_name', 'match_best_of'])
                        ->where('api_id_long', $teams->api_bracket_id)
                        ->get()
                        ->first();

            $bet->bracket_name = $bestof->bracket_name;
            $bet->match_best_of = $bestof->match_best_of;

            if (!$teams->score_one || !$teams->score_two)
            {
                $games = DB::table('games')
                            ->where('games.api_match_id', $matchId->api_match_id)
                            ->orderBy('name', 'asc')
                            ->join('game_team_stats', 'game_team_stats.game_id', '=', 'games.game_id')
                            ->get();

                $score1 = $games->where('team_id', 100)->sum('win');
                $score2 = $games->where('team_id', 200)->sum('win');

                $bet->score_one = $score1;
                $bet->score_two = $score2;

            } else
            {
                $bet->score_one = $teams->score_one;
                $bet->score_two = $teams->score_two;
            }



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

            DB::table('bets')->where('id', $bet->bet_id)
                ->update([
                    'is_pushed' => True,                    
                ]);
        }

        $pushManager->push();
        
    }

    private function formatMessage($bet)
    {
        if ($bet->match_best_of == 1)
        {
            $message = strstr($bet->name, ' ', true) .', '. $bet->winner .' has defeated '. $bet->loser 
                        .' in a match!';
        } else
        {
            if ($bet->match_state == 'resolved')
            {
                $message =  strstr($bet->name, ' ', true) .', '. $bet->winner .' has defeated '. $bet->loser 
                        .' '.$bet->score_one. '-' .$bet->score_two .' in a best of ' .$bet->match_best_of .'!';
            } else
            {
                $message = strstr($bet->name, ' ', true) .', '. $bet->winner .' has won '. $bet->game_name 
                            .' against '. $bet->loser .' in a best of ' .$bet->match_best_of .'!';
            }

        }


        if ($bet->details_won > 0)
        {
            $message = $message .' You answered ' .$bet->details_won. ' out of ' .$bet->details_placed
                        .' questions correctly and won ' .number_format($bet->credits_won, 0, '', ','). ' credits!';
        }
        else
        {
            $message = $message .' You answered ' .$bet->details_won. ' out of ' 
                        .$bet->details_placed. ' questions correctly and lost '
                        .number_format($bet->credits_placed, 0, '', ','). ' credits.';
        }

        return $message;
    }
}

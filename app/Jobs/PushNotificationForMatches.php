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

class PushNotificationForMatches implements ShouldQueue
{
	use InteractsWithQueue, Queueable, SerializesModels;

	protected $game;

	public function __construct($game)
	{
		$this->game = $game;
	}

	public function handle()
	{
		if(!$game)
			return;

		$users = DB::table('subscribed_users')
						->join('users', 'users.id', '=', 'subscribed_users.user_id')
						->where('api_match_id', $games->first->api_match_id)
						->get();

		$event = DB::table('matches')->select(['matches.name as teams_playing', 'state', 'games.name', 'match_best_of', 'api_match_id'])
						->join('games', 'games.api_id_long', '=', $games->first->api_game_id)
						->join('brackets', 'brackets.api_id_long', '=', 'matches.api_bracket_id')
						->where('matches.api_id_long', $games->first->api_match_id)
						->get();

		if($event->state != "resolved")
			$flag = true;
		else
			$flag = false;

		$message = new Message($this->formatMessage($event, $flag));

		$pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

		$apnsAdapter = new ApnsAdapter([
			'certificate' => Config::get('services.push_ios.certificate'),
			'passPhrase' => Config::get('services.push_ios.passphrase'),
		]);

		foreach ($users as $user) {
			$devices = new DeviceCollection([
				new Device($user->device_token),
			]);

			$push = new Push($apnsAdapter, $devices, $message);
			$pushManager->add($push);

			DB::table('bets')->where('id', $bet->bet_id)
				->update([
					'is_pushed' => True,                    
				]);

			$pushManager->push();
		}
	}

	private function formatMessage($event, $flag)
	{
		$games = DB::table('games')
                            ->where('games.api_match_id', $event->api_match_id)
                            ->orderBy('name', 'asc')
                            ->join('game_team_stats', 'game_team_stats.game_id', '=', 'games.game_id')
                            ->get();

		$score1 = $games->where('team_id', 100)->sum('win');
		$score2 = $games->where('team_id', 200)->sum('win');

		if($flag == true)
		{
			$message = 'Game ' . $event->name[1] . ' of ' . $event->match_best_of . 'for' . $event->teams_playing .'has completed' 
			. 'with a current match score of' . $event->score_one .  '-' . $event->score_two;
		}

		// else
		// {

		// }
	}
}
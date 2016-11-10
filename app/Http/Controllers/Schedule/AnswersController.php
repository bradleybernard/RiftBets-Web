<?php

namespace App\Http\Controllers\Schedule;

use App\Http\Controllers\Controller;
use App\Game;
use App\Jobs\InsertGameQuestionAnswers;

use DB;
use Log;

class AnswersController extends Controller
{
    protected $questions;

    public      $game;
    protected   $gameStats;
    protected   $gameTeamStats;
    protected   $gamePlayerStats;

    public function testJob()
    {
        dispatch(new InsertGameQuestionAnswers(Game::where('game_id', '1001890201')->first()));
    }

    public function insertAnswers()
    {
        $answers = [];

        $this->questions          = DB::table('questions')->select(['id', 'slug'])->get();
        $this->gameStats          = DB::table('game_stats')->where('game_id', $this->game->game_id)->first();
        $this->gameTeamStats      = DB::table('game_team_stats')->where('game_id', $this->game->game_id)->get();
        $this->gamePlayerStats    = DB::table('game_player_stats')->where('game_id', $this->game->game_id)->get();

        foreach($this->questions as $question) {
            $answers[] = [
                'question_id'   => $question->id,
                'game_id'       => $this->game->game_id,
                'answer'        => $this->{camel_case($question->slug)}(),
            ];
        }
        
        DB::table('question_answers')->insert($answers);
    }

    /********** Helper functions *******************/

    private function itemArrayToString($playerId)
    {
        $items = [];

        for ($i = 1; $i <= 6; $i++)
        {
            array_push($items, $this->gamePlayerStats->where('participant_id', $playerId)->pluck('item_' .$i)->first());
        }

        $items = array_filter($items, function($item){return !is_null($item);});

        asort($items);
        $items = implode(",", $items);

        return $items;
    }

    private function teamChampionsToString($teamId)
    {
        $offset = 0;

        if ($teamId == 200)
        {
            $offset += 5;
        }

        $champions = [];

        for($i = 1 + $offset; $i <= 5 + $offset; $i++)
        {
            array_push($champions, $this->gamePlayerStats->where('participant_id', $i)->pluck('champion_id')->first());
        }

        asort($champions);
        $champions = implode(",", $champions);

        return $champions;
    }

    private function playerSpells($playerId)
    {
        $spells = [];

        array_push($spells, $this->gamePlayerStats->where('participant_id', $playerId)->pluck('spell1_id')->first());
        array_push($spells, $this->gamePlayerStats->where('participant_id', $playerId)->pluck('spell2_id')->first());

        asort($spells);
        $spells = implode(",", $spells);

        return $spells;
    }

    private function teamBans($teamId)
    {
        $bans = [];

        for ($i = 1; $i <= 3; $i++)
        {
            array_push($bans, $this->gameTeamStats->where('team_id', $teamId)->pluck('ban_' .$i)->first());
        }

        asort($bans);
        $bans = implode(',', $bans);

        return $bans;
    }

    private function teamGold($teamId)
    {
        $offset = 0;

        if ($teamId == 200)
        {
            $offset += 5;
        }

        $totalGold = 0;

        for($i = 1 + $offset; $i <= 5 + $offset; $i++)
        {
            $totalGold += $this->gamePlayerStats->where('participant_id', $i)->pluck('gold_earned')->first();
        }

        return $totalGold;
    }

    /********** Game Stats *************************/

    private function gameDuration()
    {
        return $this->gameStats->game_duration;
    }


    /********* Game Team Stats *********************/

    private function teamWin()
    {
        return $this->gameTeamStats->where('win', true)->pluck('team_id')->first();
    }

    private function teamFirstBlood()
    {
        return $this->gameTeamStats->where('first_blood', true)->pluck('team_id')->first();
    }

    private function teamFirstInhibitor()
    {
        return $this->gameTeamStats->where('first_inhibitor', true)->pluck('team_id')->first();
    }

    private function teamOneFirstBaron()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('first_baron')->first();
    }

    private function teamTwoFirstBaron()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('first_baron')->first();
    }

    private function teamOneFirstDragon()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('first_dragon')->first();
    }

    private function teamTwoFirstDragon()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('first_dragon')->first();
    }

    private function teamOneFirstRiftHerald()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('first_rift_herald')->first();
    }

    private function teamTwoFirstRiftHerald()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('first_rift_herald')->first();
    }

    private function teamOneTowerKills()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('tower_kills')->first();
    }

    private function teamTwoTowerKills()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('tower_kills')->first();   
    }

    private function teamOneInhibitorKills()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('inhibitor_kills')->first();
    }

    private function teamTwoInhibitorKills()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('inhibitor_kills')->first();
    }

    private function teamOneBaronKills()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('baron_kills')->first();
    } 

    private function teamTwoBaronKills()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('baron_kills')->first();
    }

    private function teamOneDragonKills()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('dragon_kills')->first();  
    }

    private function teamTwoDragonKills()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('dragon_kills')->first();
    }

    private function teamOneRiftHeraldKills()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('rift_herald_kills')->first();
    }

    private function teamTwoRiftHeraldKills()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('rift_herald_kills')->first();
    }

    private function teamOneBanFirstChampion()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('ban_1')->first();
    }

    private function teamTwoBanFirstChampion()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('ban_1')->first();
    }

    private function teamOneBanSecondChampion()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('ban_2')->first();
    }

    private function teamTwoBanSecondChampion()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('ban_2')->first();
    }

    private function teamOneBanThirdChampion()
    {
        return $this->gameTeamStats->where('team_id', 100)->pluck('ban_3')->first();
    }

    private function teamTwoBanThirdChampion()
    {
        return $this->gameTeamStats->where('team_id', 200)->pluck('ban_3')->first();
    }

    private function teamOneChampionBans()
    {
        return $this->teamBans(100);
    }

    private function teamTwoChampionBans()
    {
        return $this->teamBans(200);
    }

    /********* Game Player Stats *******************/


    private function teamOnePlayerTopChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 1)->pluck('champion_id')->first();
    }

    private function teamTwoPlayerTopChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 6)->pluck('champion_id')->first();
    }

    private function teamOnePlayerJungleChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 2)->pluck('champion_id')->first();
    }

    private function teamTwoPlayerJungleChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 7)->pluck('champion_id')->first();
    }

    private function teamOnePlayerMidChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 3)->pluck('champion_id')->first();
    }
    private function teamTwoPlayerMidChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 8)->pluck('champion_id')->first();
    }

    private function teamOnePlayerAdcChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 4)->pluck('champion_id')->first();
    }

    private function teamTwoPlayerAdcChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 9)->pluck('champion_id')->first();
    }

    private function teamOnePlayerSupportChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 5    )->pluck('champion_id')->first();
    }

    private function teamTwoPlayerSupportChampion()
    {
        return $this->gamePlayerStats->where('participant_id', 10)->pluck('champion_id')->first();
    }

    private function teamOnePlayerTopKills()
    {
        return $this->gamePlayerStats->where('participant_id', 1)->pluck('kills')->first();
    }

    private function teamOnePlayerJungleKills()
    {
        return $this->gamePlayerStats->where('participant_id', 2)->pluck('kills')->first();
    }

    private function teamOnePlayerMidKills()
    {
        return $this->gamePlayerStats->where('participant_id', 3)->pluck('kills')->first();
    }

    private function teamOnePlayerAdcKills()
    {
        return $this->gamePlayerStats->where('participant_id', 4)->pluck('kills')->first();
    }

    private function teamOnePlayerSupportKills()
    {
        return $this->gamePlayerStats->where('participant_id', 5)->pluck('kills')->first();
    }

    private function teamTwoPlayerTopKills()
    {
        return $this->gamePlayerStats->where('participant_id', 6)->pluck('kills')->first();
    }

    private function teamTwoPlayerJungleKills()
    {
        return $this->gamePlayerStats->where('participant_id', 7)->pluck('kills')->first();
    }

    private function teamTwoPlayerMidKills()
    {
        return $this->gamePlayerStats->where('participant_id', 8)->pluck('kills')->first();
    }

    private function teamTwoPlayerAdcKills()
    {
        return $this->gamePlayerStats->where('participant_id', 9)->pluck('kills')->first();
    }

    private function teamTwoPlayerSupportKills()
    {
        return $this->gamePlayerStats->where('participant_id', 10)->pluck('kills')->first();
    }

    private function teamOnePlayerTopDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 1)->pluck('deaths')->first();
    }

    private function teamOnePlayerJungleDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 2)->pluck('deaths')->first();
    }

    private function teamOnePlayerMidDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 3)->pluck('deaths')->first();
    }

    private function teamOnePlayerAdcDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 4)->pluck('deaths')->first();
    }

    private function teamOnePlayerSupportDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 5)->pluck('deaths')->first();
    }

    private function teamTwoPlayerTopDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 6)->pluck('deaths')->first();
    }

    private function teamTwoPlayerJungleDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 7)->pluck('deaths')->first();
    }

    private function teamTwoPlayerMidDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 8)->pluck('deaths')->first();
    }

    private function teamTwoPlayerAdcDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 9)->pluck('deaths')->first();
    }

    private function teamTwoPlayerSupportDeaths()
    {
        return $this->gamePlayerStats->where('participant_id', 10)->pluck('deaths')->first();
    }

    private function teamOnePlayerTopAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 1)->pluck('assists')->first();
    }

    private function teamOnePlayerJungleAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 2)->pluck('assists')->first();
    }

    private function teamOnePlayerMidAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 3)->pluck('assists')->first();
    }

    private function teamOnePlayerAdcAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 4)->pluck('assists')->first();
    }

    private function teamOnePlayerSupportAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 5)->pluck('assists')->first();
    }

    private function teamTwoPlayerTopAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 6)->pluck('assists')->first();
    }

    private function teamTwoPlayerJungleAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 7)->pluck('assists')->first();
    }

    private function teamTwoPlayerMidAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 8)->pluck('assists')->first();
    }

    private function teamTwoPlayerAdcAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 9)->pluck('assists')->first();
    }

    private function teamTwoPlayerSupportAssists()
    {
        return $this->gamePlayerStats->where('participant_id', 10)->pluck('assists')->first();
    }

    private function teamOnePlayerTopGold()
    {
        return $this->gamePlayerStats->where('participant_id', 1)->pluck('gold_earned')->first();
    }

    private function teamOnePlayerJungleGold()
    {
        return $this->gamePlayerStats->where('participant_id', 2)->pluck('gold_earned')->first();
    }

    private function teamOnePlayerMidGold()
    {
        return $this->gamePlayerStats->where('participant_id', 3)->pluck('gold_earned')->first();
    }

    private function teamOnePlayerAdcGold()
    {
        return $this->gamePlayerStats->where('participant_id', 4)->pluck('gold_earned')->first();
    }

    private function teamOnePlayerSupportGold()
    {
        return $this->gamePlayerStats->where('participant_id', 5)->pluck('gold_earned')->first();
    }

    private function teamTwoPlayerTopGold()
    {
        return $this->gamePlayerStats->where('participant_id', 6)->pluck('gold_earned')->first();
    }

    private function teamTwoPlayerJungleGold()
    {
        return $this->gamePlayerStats->where('participant_id', 7)->pluck('gold_earned')->first();
    }

    private function teamTwoPlayerMidGold()
    {
        return $this->gamePlayerStats->where('participant_id', 8)->pluck('gold_earned')->first();
    }

    private function teamTwoPlayerAdcGold()
    {
        return $this->gamePlayerStats->where('participant_id', 9)->pluck('gold_earned')->first();
    }

    private function teamTwoPlayerSupportGold()
    {
        return $this->gamePlayerStats->where('participant_id', 10)->pluck('gold_earned')->first();
    }

    private function teamOnePlayerTopMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 1)->pluck('minions_killed')->first();
    }

    private function teamOnePlayerJungleMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 2)->pluck('minions_killed')->first();
    }

    private function teamOnePlayerMidMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 3)->pluck('minions_killed')->first();
    }

    private function teamOnePlayerAdcMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 4)->pluck('minions_killed')->first();
    }

    private function teamOnePlayerSupportMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 5)->pluck('minions_killed')->first();
    }

    private function teamTwoPlayerTopMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 6)->pluck('minions_killed')->first();
    }

    private function teamTwoPlayerJungleMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 7)->pluck('minions_killed')->first();
    }

    private function teamTwoPlayerMidMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 8)->pluck('minions_killed')->first();
    }

    private function teamTwoPlayerAdcMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 9)->pluck('minions_killed')->first();
    }

    private function teamTwoPlayerSupportMinionKills()
    {
        return $this->gamePlayerStats->where('participant_id', 10)->pluck('minions_killed')->first();
    }

    private function teamOnePlayerTopItems()
    {
        return $this->itemArrayToString(1);
    }

    private function teamOnePlayerJungleItems()
    {
        return $this->itemArrayToString(2);
    }

    private function teamOnePlayerMidItems()
    {
        return $this->itemArrayToString(3);
    }

    private function teamOnePlayerAdcItems()
    {
        return $this->itemArrayToString(4);
    }

    private function teamOnePlayerSupportItems()
    {
        return $this->itemArrayToString(5);
    }

    private function teamTwoPlayerTopItems()
    {
        return $this->itemArrayToString(6);
    }

    private function teamTwoPlayerJungleItems()
    {
        return $this->itemArrayToString(7);
    }

    private function teamTwoPlayerMidItems()
    {
        return $this->itemArrayToString(8);
    }

    private function teamTwoPlayerAdcItems()
    {
        return $this->itemArrayToString(9);
    }

    private function teamTwoPlayerSupportItems()
    {
        return $this->itemArrayToString(10);
    }

    private function teamOneChampions()
    {
        return $this->teamChampionsToString(100);
    }

    private function teamTwoChampions()
    {
        return $this->teamChampionsToString(200);
    }

    private function teamOnePlayerTopSpells()
    {
        return $this->playerSpells(1);
    }

    private function teamOnePlayerJungleSpells()
    {
        return $this->playerSpells(2);
    }

    private function teamOnePlayerMidSpells()
    {
        return $this->playerSpells(3);
    }

    private function teamOnePlayerAdcSpells()
    {
        return $this->playerSpells(4);
    }

    private function teamOnePlayerSupportSpells()
    {
        return $this->playerSpells(5);
    }

    private function teamTwoPlayerTopSpells()
    {
        return $this->playerSpells(6);
    }

    private function teamTwoPlayerJungleSpells()
    {
        return $this->playerSpells(7);
    }

    private function teamTwoPlayerMidSpells()
    {
        return $this->playerSpells(8);
    }

    private function teamTwoPlayerAdcSpells()
    {
        return $this->playerSpells(9);
    }

    private function teamTwoPlayerSupportSpells()
    {
        return $this->playerSpells(10);
    }

    private function teamOneTotalGold()
    {
        return $this->teamGold(100);
    }

    private function teamTwoTotalGold()
    {
        return $this->teamGold(200);
    }
}

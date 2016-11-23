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
        // $this->game = $game;
    }

    public function handle()
    {
        
    }
}
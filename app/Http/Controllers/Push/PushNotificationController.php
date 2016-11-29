<?php

namespace App\Http\Controllers\Push;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\PushNotificationsForGradedBets;

use Sly\NotificationPusher\PushManager,
    Sly\NotificationPusher\Adapter\Apns as ApnsAdapter,
    Sly\NotificationPusher\Collection\DeviceCollection,
    Sly\NotificationPusher\Model\Device,
    Sly\NotificationPusher\Model\Message,
    Sly\NotificationPusher\Model\Push;

use Config;

class PushNotificationController extends Controller
{
    // Sample push notifications code
    public function push()
    {
        $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

        $apnsAdapter = new ApnsAdapter([
            'certificate' => Config::get('services.push_ios.certificate'),
            'passPhrase' => Config::get('services.push_ios.passphrase'),
        ]);

        $devices = new DeviceCollection([
            new Device(\App\User::find(1)->device_token),
        ]);

        $message = new Message('Brad, SKT has defeated SSG 3-2 in a best of 5! You answered 3 out of 5 questions correctly and won 2584 credits!');

        $push = new Push($apnsAdapter, $devices, $message);
        $pushManager->add($push);
        $pushManager->push();
    }

    // Sample push notifications job
    public function test()
    {
        dispatch(new PushNotificationsForGradedBets());
    }
}


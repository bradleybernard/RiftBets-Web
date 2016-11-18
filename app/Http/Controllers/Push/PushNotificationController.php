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
    public function push()
    {
        $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

        $apnsAdapter = new ApnsAdapter([
            'certificate' => Config::get('services.push_ios.certificate'),
            'passPhrase' => Config::get('services.push_ios.passphrase'),
        ]);

        $devices = new DeviceCollection([
            new Device(\App\User::where('email', 'scorpiofrend@gmail.com')->first()->device_token),
        ]);

        $message = new Message('RiftBets life.');

        $push = new Push($apnsAdapter, $devices, $message);
        $pushManager->add($push);
        $pushManager->push();
    }

    public function test()
    {
        dispatch(new PushNotificationsForGradedBets());
    }
}

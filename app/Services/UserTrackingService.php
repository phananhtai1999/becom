<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\SendNotificationSystemEvent;
use App\Models\Notification;
use App\Models\UserTracking;

class UserTrackingService extends AbstractService
{
    protected $modelClass = UserTracking::class;

    public function checkAndSendUserLogin($user)
    {
        $ip = geoip()->getClientIP();
        // 92.38.148.61, 171.248.187.0
        $geoIp = geoip()->getLocation($ip);

        $country = $geoIp->country;

        $userTracking = $this->findOneWhere([
            'user_uuid' => $user->uuid,
        ]);

        if ($userTracking) {
            if ($userTracking->country != $country) {
                SendNotificationSystemEvent::dispatch($user, Notification::LOGIN_TYPE, $country);
            }
            $this->update($userTracking, [
                'ip' => $ip,
                'country' => $country,
                'postal_code' => $geoIp->postal_code
            ]);

        }else{
            $this->create([
                'ip' => $ip,
                'user_uuid' => $user->uuid,
                'country' => $country,
                'postal_code' => $geoIp->postal_code
            ]);
        }
    }
}

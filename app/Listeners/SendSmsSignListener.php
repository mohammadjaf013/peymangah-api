<?php

namespace App\Listeners;

use App\Libs\Helper\PersianHelper;
use App\Libs\Helper\UrlHelper;
use App\Libs\SmsCenter\SmsCenter;
use Cryptommer\Smsir\Smsir;
class SendSmsSignListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {





            $mobile = PersianHelper::normalize($event->user->mobile);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.sms.ir/v1/send/verify',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{
        "mobile": "'.$mobile.'",
        "templateId": "486465",
        "parameters": [
          {
            "name": "NAME",
            "value": "'.(string)$event->user->first_name . ' ' .$event->user->last_name.'"
          },
          {
              "name":"CONTRACT",
              "value":"'.$event->contract->title.'"
          },
          {
              "name":"USER_NAME",
              "value":"'.$event->singer->first_name . ' ' .$event->singer->last_name.'"
          }
        ]
      }',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Accept: text/plain',
                    'x-api-key: yGXY4YHLxNpqEOcM8vWDuyZBxUCy1chotH24dBPzvHljHt6LzWa5nBPKkZBWqD6h'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            report($response);




    }
}

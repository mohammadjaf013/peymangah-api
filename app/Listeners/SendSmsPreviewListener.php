<?php

namespace App\Listeners;

use App\Libs\Helper\PersianHelper;
use App\Libs\Helper\UrlHelper;
use App\Libs\SmsCenter\SmsCenter;
use Cryptommer\Smsir\Smsir;
use Illuminate\Support\Carbon;
use Morilog\Jalali\Jalalian;

class SendSmsPreviewListener
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

        foreach ($event->contract->users as $user){


//            $smsir = new Smsir("30007871", "yGXY4YHLxNpqEOcM8vWDuyZBxUCy1chotH24dBPzvHljHt6LzWa5nBPKkZBWqD6h");
//            $send = smsir::Send();
//            $parameter = new \Cryptommer\Smsir\Objects\Parameters("NAME", (string)$user->first_name . ' ' .$user->last_name );
//            $parameter2 = new \Cryptommer\Smsir\Objects\Parameters("CODE1", $event->contract->code);
//            $parameter3 = new \Cryptommer\Smsir\Objects\Parameters("CODE2", $user->code);
//            $parameters = [$parameter , $parameter2,$parameter3];
//            $data=  $send->Verify(PersianHelper::normalize($user->mobile), "200962", $parameters);
//dd($data);



            $mobile = PersianHelper::normalize($user->mobile);

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
        "templateId": "348546",
        "parameters": [
          {
            "name": "NAME",
            "value": "'.(string)$user->first_name . ' ' .$user->last_name.'"
          },
          {
              "name":"CODE1",
              "value":"'.$event->contract->code.'"
          },
          {
              "name":"CODE2",
              "value":"'.$user->code.'"
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
}

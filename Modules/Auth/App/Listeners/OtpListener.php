<?php

namespace Modules\Auth\App\Listeners;

use App\Libs\Helper\PersianHelper;
use App\Libs\SmsCenter\SmsCenter;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class OtpListener
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
    public function handle($event): void
    {

        $data = [
            "ParameterArray" => [
                [
                    "Parameter" => "CODE",
                    "ParameterValue" => (string)$event->otp
                ]
            ],
            "Mobile" => PersianHelper::normalize($event->mobile),
            "TemplateId" => "744450"
        ];
        $sms = (new SmsCenter())->UltraFastSend($data);

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
        "mobile": "'.$event->mobile.'",
        "templateId": "744450",
        "parameters": [

          {
              "name":"CODE",
              "value":"'.(string)$event->otp.'"
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
//        $log = new SmsLogModel();
//        $log->data = $data;
//        $log->result = $sms;
//        $log->number = PersianHelper::normalize($notifiable->mobile);
//        $log->save();
    }
}

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
                    "Parameter" => "code",
                    "ParameterValue" => (string)$event->otp
                ]
            ],
            "Mobile" => PersianHelper::normalize($event->mobile),
            "TemplateId" => "30271"
        ];
        $sms = (new SmsCenter())->UltraFastSend($data);
//        $log = new SmsLogModel();
//        $log->data = $data;
//        $log->result = $sms;
//        $log->number = PersianHelper::normalize($notifiable->mobile);
//        $log->save();
    }
}

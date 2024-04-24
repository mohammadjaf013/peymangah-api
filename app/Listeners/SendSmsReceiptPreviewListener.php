<?php

namespace App\Listeners;

use App\Libs\Helper\PersianHelper;
use App\Libs\SmsCenter\SmsCenter;

class SendSmsReceiptPreviewListener
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

        foreach ($event->receipt->users as $user){

            $data = [
                "ParameterArray" => [
                    [
                        "Parameter" => "code",
                        "ParameterValue" => (string)$event->receipt->code
                    ]
                ],
                "Mobile" => PersianHelper::normalize($user->mobile),
                "TemplateId" => "30271"
            ];
            $sms = (new SmsCenter())->UltraFastSend($data);

        }

    }
}

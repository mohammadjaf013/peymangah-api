<?php

namespace App\Libs\Helper;

use Illuminate\Support\Facades\URL;

class UrlHelper
{


    public static function frontUrl()
    {
        return 'http://localhost:3000/';
    }
    public static function Make($url)
    {
        if (env("SECURE_URL", false)) {
            return  Url($url);
        } else {
            return  URL::secure($url);
        }
    }
}

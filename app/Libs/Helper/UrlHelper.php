<?php

namespace App\Libs\Helper;

use Illuminate\Support\Facades\URL;

class UrlHelper
{


    public static function frontUrl()
    {
        return 'http://localhost:3000/';
    }

    public static function url($url=null){
        if($url == null){
            return 'http://localhost:3000/';
        }
        return 'http://localhost:3000/'.$url;
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

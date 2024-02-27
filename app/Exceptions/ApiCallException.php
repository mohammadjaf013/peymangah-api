<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class ApiCallException extends Exception
{
    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {

        Log::error($exception->message);
        Log::error($exception->getLine());
        Log::error($exception->getFile());
        Log::error($exception->getTraceAsString());
        parent::report($exception);
    }



    public function getJson()
    {
        return json_decode($this->getMessage(),true);
    }




}

<?php

namespace Modules\User\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Passport\HasApiTokens;

class UserValidationModel extends Authenticatable
{
    use HasApiTokens,  Notifiable;
    protected $table = "user_validation";



    protected $casts = [
        'shahkar_data' => 'encrypted:array',
        'inquery_data' => 'encrypted:array',
        'shahkar' => 'bool',
        'inquery' => 'bool',
        'image' => 'encrypted',
    ];
}

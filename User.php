<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    static public function get_single($id)
    {
        return self::find($id);
    }

    public function get_users_wallet_details(){
        return $this->hasMany(UsersWalletDetailsModel::class, "user_id");
    }
    
    
    public function get_users_mearchant_wallet_details(){
        return $this->hasMany(UsersWalletDetailsModel::class, "user_id")->where('money_type', '=', '1');
    }

    
}

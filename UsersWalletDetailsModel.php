<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UsersWalletDetailsModel extends Model {
	
	protected $table = 'users_wallet_details';

	static public function get_single($id)
	{
		return self::find($id);
	}
	
	public function user(){
		return $this->belongsTo(User::class, "user_id");
	}
}

?>
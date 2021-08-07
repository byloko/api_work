<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OrdersModel extends Model
{
    protected $table = 'orders';

	static public function get_single($id){
		return self::find($id);
	}

	public function user(){
		return $this->belongsTo(User::class, "user_id");
	}
   
   public function get_order_details(){
        return $this->hasMany(OrderDetailsModel::class, "order_id");
    }
   
}

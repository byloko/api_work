<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class OrderDetailsModel extends Model
{
    protected $table = 'order_details';

	static public function get_single($id){
		return self::find($id);
	}

	public function get_orders_name(){
		return $this->belongsTo(OrdersModel::class, "order_id");
	}
   
   
}

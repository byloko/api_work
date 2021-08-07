<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationStoreModel extends Model
{
    protected $table = 'notification_store';

    public function get_user_name(){
		return $this->belongsTo(User::class, "user_id");
	}

	public function get_mearchant_name(){
		return $this->belongsTo(User::class, "user_mearchant_id");
	}

	public function get_order_name(){
		return $this->belongsTo(OrdersModel::class, "order_id");
	}
}

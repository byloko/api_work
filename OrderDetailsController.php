<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\OrderDetailsModel;


class OrderDetailsController extends Controller
{
	public function order_details_list(Request $request)
	{
		$getrecord = OrderDetailsModel::orderBy('id', 'desc')->select('order_details.*');
		$getrecord = $getrecord->join('orders', 'order_details.order_id', '=', 'orders.id');

		//search box Start
		if(!empty($request->orders_name)){
           $getrecord = $getrecord->where('orders.orders_name', 'like', '%' . $request->orders_name . '%');
        }

	    if ($request->idsss) {
            $getrecord = $getrecord->where('order_details.id', '=', $request->idsss);
        }

        if ($request->item_name) {
            $getrecord = $getrecord->where('order_details.item_name', 'like', '%' . $request->item_name . '%');
        }

        if ($request->item_total_price) {
            $getrecord = $getrecord->where('order_details.item_total_price', 'like', '%' . $request->item_total_price . '%');
        }

        if ($request->item_price_per_kg) {
            $getrecord = $getrecord->where('order_details.item_price_per_kg', 'like', '%' . $request->item_price_per_kg . '%');
        }

        if ($request->item_weight) {
            $getrecord = $getrecord->where('order_details.item_weight', 'like', '%' . $request->item_weight . '%');
        }
		//search box end
		$getrecord = $getrecord->paginate(40);
        $data['getrecord'] = $getrecord;

		$data['meta_title'] = 'Order Details List';
		return view('backend.order_details.list', $data);
	}

	public function order_details_destroy($id, Request $request){
		$delete_re = OrderDetailsModel::get_single($id);
		$delete_re->delete();
        return redirect()->back()->with('error', 'Record successfully deleted!');

	}
}
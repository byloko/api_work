<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\OrdersModel;
use App\OrderDetailsModel;


class OrdersController extends Controller
{
   
    public function orders_index(Request $request)
    {
        $getrecord = OrdersModel::orderBy('id', 'desc')->select('orders.*');
        $getrecord = $getrecord->join('users', 'orders.user_id', '=', 'users.id');
        // Search Box Start
        if ($request->idsss) {
            $getrecord = $getrecord->where('orders.id', '=', $request->idsss);
        }
        if ($request->orders_name) {
            $getrecord = $getrecord->where('orders.orders_name', 'like', '%' . $request->orders_name . '%');
        }
    
        if ($request->orders_total_price) {
            $getrecord = $getrecord->where('orders.orders_total_price', 'like', '%' . $request->orders_total_price . '%');
        }
         if(!empty($request->name)){
           $getrecord = $getrecord->where('users.name', 'like', '%' . $request->name . '%');
       }
        
        // Search Box End

        $getrecord = $getrecord->paginate(40);
        $data['getrecord'] = $getrecord;

        $data['meta_title'] = 'Orders List';
        return view('backend.orders.list', $data);
    }

    public function orders_destroy($id, Request $request){
        $delete_recoard = OrdersModel::get_single($id);
        $delete_recoard->delete();
        return redirect()->back()->with('error', 'Record successfully deleted!');

    }

    public function orders_view($id, Request $request)
    {
        $data['getrecord'] = OrdersModel::get_single($id);

        $data['meta_title'] = 'View Orders';
        return view('backend.orders.view', $data);
    }

    public function orders_view_destroy($id, Request $request)
    {
        $delete_re = OrderDetailsModel::get_single($id);
        $delete_re->delete();
        return redirect()->back()->with('error', 'Record successfully deleted!');
    }
}
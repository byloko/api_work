<?php

namespace App\Http\Controllers\Backend;
// use App\Models\UsersModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\OrdersModel;
use App\OrderDetailsModel;

class DashboardController extends Controller
{
	public function dashboard_index(Request $request)
	{
		$data['TotalUser'] 		       = User::where('is_admin', '=', 0)->count();
		$data['TotalUserMearchant']    = User::where('is_type', '=', 2)->count();
		$data['TotalUserNormalUser']   = User::where('is_type', '=', 1)->count();
		$data['TotalOrders']           = OrdersModel::count();
        $data['TotalOrderDetails']     = OrderDetailsModel::count();

		$data['meta_title'] = 'Dashboard List';
		return view('backend.dashboard.list', $data);
	}

}

?>
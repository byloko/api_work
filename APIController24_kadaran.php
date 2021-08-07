<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\OrdersModel;
use App\OrderDetailsModel;
use App\VersionSettingModel;
use App\NotificationModel;
use App\NotificationStoreModel;
use App\Mail\ForgotPasswordMail;
use App\Mail\APIRegisterMail;
use Hash;
use Mail;

class APIController extends Controller {
	public $token;

	public function __construct(Request $request) {
		
		$this->token = !empty($request->header('token'))?$request->header('token'):'';
	}

	public function updateToken($user_id)
	{
		$randomStr = str_random(40).$user_id;
		$save_token = User::find($user_id);
		$save_token->user_token = $randomStr;
		$save_token->save();
	}
	
	public function app_mearchant_normal_user(Request $request)
	{
		if (!empty($request->is_type)){
			
			$record            = new User;
			$record->is_type   = trim($request->is_type);
			$record->token     = !empty($request->token)?$request->token:null;
			$record->save();
			$this->updateToken($record->id);
			$json['status'] = 1;
			$json['message'] = 'Account Successfully created';
			$json['result'] = $this->getProfileUser($record->id);
		}else{
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		echo json_encode($json);
	}

	public function app_social_login(Request $request)
	{
		if(!empty($request->social_id) && !empty($request->mobile)){
		
		$checksocialidMobile = User::where('mobile', '=', $request->mobile)->where('social_id', '=', $request->social_id)->where('is_type', '=', $request->is_type)->count();
		
		$statucheckmobile = User::where('mobile', '=', $request->mobile)->where('social_id', '=', $request->social_id)->first();

	
		$checkSocialid = User::where('social_id', '=', $request->social_id)->count();
		
		$checkSocmobileid = User::where('mobile', '=', $request->mobile)->count();

		if($checksocialidMobile == '0' && $checkSocialid == '0' && $checkSocmobileid == '0'){
			
			$record = new User;
			$record->email   = trim($request->email);

			if (!empty($request->file('user_profile'))) {
				$ext = 'jpg';
				$file = $request->file('user_profile');
				$randomStr = str_random(30);
				$filename = strtolower($randomStr) . '.' . $ext;
				$file->move('upload/profile/', $filename);
				$record->user_profile = $filename;
			}
			$record->token          = !empty($request->token)?$request->token:null;
			$record->mobile         = trim($request->mobile);
			$record->is_type        = trim($request->is_type);
			$record->name           = trim($request->name);				
			$record->social_type    = trim($request->social_type);
			$record->social_id      = trim($request->social_id);
			$record->save();
		
		    $this->updateToken($record->id);
		
			$json['status'] = 1;
			$json['message'] = 'Account Successfully created.';
			$json['result'] = $this->getProfileUser($record->id);
		}else if (!empty($statucheckmobile)){
			$json['status'] = 0;
			$json['message'] = 'Your social id  exist please login or try again.';
			$json['result'] = $this->getProfileUser($statucheckmobile->id);
		}else {
			$json['status'] = 0;
			$json['message'] = 'Your mobile number already exist please login or try again.';
		}

		}
		else {
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		echo json_encode($json);
	}

	public function getProfileUser($id)
	{
		$user 				    = User::find($id);
		$json['user_id']		= $user->id;
		$json['name'] 			= !empty($user->name) ? $user->name : '';
		$json['lastname'] 		= !empty($user->lastname) ? $user->lastname : '';
		$json['email'] 		    = !empty($user->email) ? $user->email : '';
		$json['mobile'] 	    = !empty($user->mobile) ? $user->mobile : '';
		$json['otp'] 		    = !empty($user->otp) ? $user->otp : '';
		$json['social_type'] 	= $user->social_type;
		$json['is_email_verify'] 	= $user->is_email_verify;
		$json['social_id'] 		= !empty($user->social_id) ? $user->social_id : '';
		$json['token']          = !empty($user->token) ? $user->token : '';
		$json['user_profile'] 	= !empty($user->user_profile) ? $user->user_profile : '';
		$json['address'] 	    = !empty($user->address) ? $user->address : '';
		$json['is_type'] 	    = $user->is_type;
		return $json;
	}

	public function app_register_old(Request $request)
	{
		$otp           = rand(1111,9999);
		if(!empty($request->email && !empty($request->password) && !empty($request->mobile) && !empty($request->name))){
			$check_email = User::where('email', '=', $request->email)->count();
			$check_mobile = User::where('mobile', '=', $request->mobile)->count();
// if(!empty($check_email)){
			if($check_email == '0' && $check_mobile == '0'){
	    	//$uprecord = User::find($request->user_id);
 
		//	if(!empty($uprecord)){
 				   $uprecord                     = new User;
 				   $uprecord->is_type            = trim($request->is_type);
			 	   $uprecord->token              = !empty($request->token)?$request->token:null;
				   $uprecord->email              = trim($request->email);
				   // $uprecord->otp             = 9999;
			       $uprecord->otp                = $otp;
			       $uprecord->name               = trim($request->name);
				   $uprecord->mobile             = trim($request->mobile);
				   $uprecord->password           = Hash::make($request->password);
				   $uprecord->remember_token     = !empty($request->token)?$request->token:null;
				   $uprecord->save();
				   $this->updateToken($uprecord->id);
				   $this->send_verification_mail($uprecord);
		 		$json['status'] = 1;
		  	 	$json['message'] = 'Account successfully created.';
		  	 	$json['result'] = $this->getProfileUser($uprecord->id);

		  	 

		  // 	 }else
		  //    {
			 //   	$json['status'] = 0;
				// $json['message'] = 'Invalid User.';
		  //   }
		    }else 
		   {

			$json['status'] = 0;
			// $json['message'] = 'Your email already exist please login or try again.';
			$json['message'] = 'Your account already exist please login or try again.';
		   }

		   }else 
		   {

			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		   }

		echo json_encode($json);
	}

	public function app_register(Request $request)
	{
		if(!empty($request->password) && !empty($request->mobile) && !empty($request->name)){
			$check_email = User::where('mobile', '=', $request->mobile)->count();
// if(!empty($check_email)){
			if($check_email == '0'){
	    	//$uprecord = User::find($request->user_id);
 
		//	if(!empty($uprecord)){
 				   $uprecord = new User;
 				   $uprecord->is_type   = trim($request->is_type);
			 	   $uprecord->token    = !empty($request->token)?$request->token:null;
				   //$uprecord->email    = trim($request->email);
			       $uprecord->name     = trim($request->name);
				   $uprecord->mobile   = trim($request->mobile);
				   $uprecord->password = Hash::make($request->password);
				   $uprecord->remember_token    = !empty($request->token)?$request->token:null;
				   $uprecord->save();
				   $this->updateToken($uprecord->id);
				  // $this->send_verification_mail($uprecord);
		 		$json['status'] = 1;
		  	 	$json['message'] = 'Account successfully created.';
		  	 	$json['result'] = $this->getProfileUser($uprecord->id);

		  	 

		  // 	 }else
		  //    {
			 //   	$json['status'] = 0;
				// $json['message'] = 'Invalid User.';
		  //   }
		    }else 
		   {

			$json['status'] = 0;
			$json['message'] = 'Your mobile already exist please login or try again.';
		   }

		   }else 
		   {

			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		   }

		echo json_encode($json);
	}


	public function send_verification_mail($uprecord){
		//dd($uprecord->email);
		// Mail::to($uprecord->email)->send(new APIRegisterMail($uprecord));
		 Mail::to($uprecord->mobile)->send(new APIRegisterMail($uprecord));
	}

	public function app_login(Request $request) {
		
		if (!empty($request->mobile) && !empty($request->password)) {

				$user = User::where('mobile', '=', $request->mobile)->where('is_type', '=', $request->is_type)->first();
				if (!empty($user)) {
				   if(!empty($user->otp_verify == 1)){
				
					$check = Hash::check($request->password, $user->password);
					if (!empty($check)) {

						// if(!empty($request->device_token))
						// {
							$datauser = User::find($user->id);	
							$datauser->token             = !empty($request->token)?$request->token:null;
							$datauser->remember_token    = !empty($request->token)?$request->token:null;
						//	$datauser->device_token = $request->device_token;
							$datauser->save();
						// }

						   $this->updateToken($datauser->id);

						$json['status'] = 1;
						$json['message'] = 'Record found.';
						$json['result'] = $this->getProfileUser($user->id);
					} else {
						$json['status'] = 0;
						$json['message'] = 'Your mobile or password is incorrect please try again.';
					}
				} else {
					$json['status'] = 2;
					$json['message'] = 'Mobile OTP not verified please try again.';
					$json['result'] = $this->getProfileUser($user->id);
				}
				}else
				{
					$json['status'] = 0;
					$json['message'] = 'You are trying to login with wrong user.';
				}
		} else {

			$json['status'] = 0;
			$json['message'] = 'Due to some error please try again.';
		}

		echo json_encode($json);
	}


	public function app_login_old(Request $request) {
		
		if (!empty($request->email) && !empty($request->password)) {

				$user = User::where('email', '=', $request->email)->where('is_type', '=', $request->is_type)->first();
				if (!empty($user)) {
				  if(!empty($user->is_email_verify == 1)){
				
					$check = Hash::check($request->password, $user->password);
					if (!empty($check)) {

						// if(!empty($request->device_token))
						// {
							$datauser = User::find($user->id);	
							$datauser->token             = !empty($request->token)?$request->token:null;
							$datauser->remember_token    = !empty($request->token)?$request->token:null;
						//	$datauser->device_token = $request->device_token;
							$datauser->save();
						// }

						   $this->updateToken($datauser->id);

						$json['status'] = 1;
						$json['message'] = 'Record found.';
						$json['result'] = $this->getProfileUser($user->id);
					} else {
						$json['status'] = 0;
						$json['message'] = 'Your email or password is incorrect please try again.';
					}
				} else {
					$json['status'] = 0;
					$json['message'] = 'Email not verified please try again.';
				}
				}else
				{
					$json['status'] = 0;
					$json['message'] = 'Your email or password is incorrect please try again.';
				}
		} else {

			$json['status'] = 0;
			$json['message'] = 'Due to some error please try again.';
		}

		echo json_encode($json);
	}


	public function app_orders_add(Request $request)
	{
		// dd($request->option);
		$getUser = User::where('id', '=', $request->user_id)->count();

		$update_rec = User::where('id', '=', $request->user_mearchant_id)->where('is_type', '=', 2)->first();

		// dd($update_rec);
		if(!empty($getUser)){
		
		$record_insert = new OrdersModel;
	
		if(!empty($record_insert) && !empty($update_rec)){

			$record_insert->user_id  		   = trim($request->user_id);
			$record_insert->orders_name 	   = trim($request->orders_name);

			if (!empty($request->file('order_image'))) {

			if (!empty($record_insert->order_image) && file_exists('upload/' . '/' . $record_insert->order_image)) {
				unlink('upload/' . $record_insert->order_image);
			}
			$ext = 'jpg';
			$file = $request->file('order_image');
			$randomStr = str_random(30);
			$filename = strtolower($randomStr) . '.' . $ext;
			$file->move('upload/', $filename);
	    	//$path = "http://localhost/laravel/bookfast/upload/".$filename;
	 		$record_insert->order_image = $filename;
			}
			// else
			// {
			// 		$record_insert->order_image = '';
			// }

			$record_insert->orders_total_price = trim($request->orders_total_price );
			$record_insert->order_date_time    = trim($request->order_date_time );
			$record_insert->user_mearchant_id  = trim($request->user_mearchant_id );
			$record_insert->is_flag  = 1;
			$record_insert->order_firebase_chat_id  = trim($request->order_firebase_chat_id);
			$record_insert->save();

			$this->normal_user_is_flag_update($record_insert->user_id);
			$this->mearchant_is_flag_update($record_insert->user_mearchant_id);

 			//dd($request->user_mearchant_id);
			if(!empty($request->order_details))
			{

				$option = json_decode($request->order_details);
				
				foreach ($option as  $value) {
// dd($value);
					// if(!empty($value->item_name) && !empty($value->item_total_price) && !empty($value->item_price_per_kg) && !empty($value->item_weight))
					// {
						//dd($value->item_name);
						$save_option = new OrderDetailsModel;
						$save_option->order_id 			 = $record_insert->id;
						$save_option->item_name 	     = $value->item_name;
						$save_option->firebase_chat_id   = $value->firebase_chat_id;
						$save_option->item_total_price   = !empty($value->item_total_price) ? $value->item_total_price : '';
						//$save_option->item_price_per_kg = $value->item_price_per_kg;
						$save_option->item_price_per_kg   = !empty($value->item_price_per_kg) ? $value->item_price_per_kg : '';
						$save_option->item_weight        = $value->item_weight;
						$save_option->unite   = !empty($value->unite) ? $value->unite : '0';
						$save_option->unite_name   = !empty($value->unite_name) ? $value->unite_name : '';
						$save_option->save();	
				//	}
				}
			}
//dd($save_option->order_id);
			//dd($record_insert->orders_name);
		    $json['status'] = 1;
			$json['message'] = 'Order successfully.';
//dd($update_rec->address);
			$json['result'] = $this->getOrdersList($record_insert->id);

			$title   = "Order Placed!";
		    //$message = "Received an order";
		    // dd($record_insert->user->name);
		    $message = " ".$record_insert->user->name." has placed new order ".$record_insert->orders_name." on ".date('d-m-Y h:i A', strtotime($record_insert->order_date_time) )."";
 //dd($message);
 //dd($record_insert->order_firebase_chat_id); 
 			$this->sendPushNotificationMearchantOrder($record_insert->order_firebase_chat_id,$record_insert->order_image,$record_insert->user_id,$title,$message,$request->user_mearchant_id,$save_option->order_id,$record_insert->orders_name,$record_insert->order_date_time,$update_rec->address);
			
				//Database Notification store Start
                $Nofi_insert =   new NotificationStoreModel;
                $Nofi_insert->user_id             = $record_insert->user_id;
                $Nofi_insert->orders_name         = $record_insert->orders_name;
                $Nofi_insert->title               = $title;
                $Nofi_insert->message             = $message;
                $Nofi_insert->user_mearchant_id   = $request->user_mearchant_id;
                $Nofi_insert->order_id            = $save_option->order_id;
                $Nofi_insert->order_date_time     = $record_insert->order_date_time;
                $Nofi_insert->address             = $update_rec->address;
                $Nofi_insert->order_firebase_chat_id  = $record_insert->order_firebase_chat_id;
                $Nofi_insert->save();
 
				//Database Notification End



			}else{
				$json['status'] = 0;
				$json['message'] = 'Mearchant ID Incorrect.';
			}
		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'User Id ';
		}

		echo json_encode($json);
	}

    public function mearchant_is_flag_update($user_mearchant_id)
	{
		$insert_user = User::where('id','=',$user_mearchant_id)->first();
		
			// if(empty($insert_wallet)) {
			// 	$insert_wallet = new WalletModel;
			// 	$insert_wallet->amount = 100;
			// }
			$insert_user->is_flag = 1;
	
			//$insert_wallet->amount  = trim($request->amount);
			
			$insert_user->save();
	}



	public function normal_user_is_flag_update($user_id)
	{
		$insert_user = User::where('id','=',$user_id)->first();
		
			// if(empty($insert_wallet)) {
			// 	$insert_wallet = new WalletModel;
			// 	$insert_wallet->amount = 100;
			// }
			$insert_user->is_flag = 1;
	
			//$insert_wallet->amount  = trim($request->amount);
			
			$insert_user->save();
	}

	public function sendPushNotificationMearchantOrder($order_firebase_chat_id,$order_image,$user_id,$title,$message,$user_mearchant_id,$order_id,$orders_name){
	//dd($user_mearchant_id);

		$user 	= User::find($user_mearchant_id);
		// dd($user->name);
 		
 		  	$result = NotificationModel::find(1);
 		  	$serverKey = $result->notification_key;
 		  	try {
			if(!empty($user->token)) {

				$token = $user->token;
				$body['price_status'] = "0";
				$body['payment_status'] = "0";
					// dd($body['payment_status']);
				$body['order_id']                = $order_id;
				$body['orders_name']             = $orders_name;
				$body['order_image']             = $order_image;
				$body['order_firebase_chat_id']  = $order_firebase_chat_id;
				// dd($body['order_firebase_chat_id']);
				$body['mearchant_id']          = $user_id;
				if(!empty($user))
				{
					
					$body['mearchant_name']         = $user->name;
					$body['mearchant_user_profile']  = $user->user_profile;
					$body['mearchant_address']      = $user->address;
					
				}
				//dd($body['mearchant_id']);

				$body['message'] = $message;
				//$body['body']    = $message;
				$body['body']    = $body;
				$body['title']   = $title;

				$url = "https://fcm.googleapis.com/fcm/send";

				$notification = array('title' => $title, 'body' => $message);

				$arrayToSend = array('to' => $token, 'notification' => $notification, 'data' => $body, 'priority' => 'high');

				$json1 = json_encode($arrayToSend);

				$headers = array();
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Authorization: key=' . $serverKey;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				$response = curl_exec($ch);
				//dd($response);
				curl_close($ch);
		// 	}

		}
		}
		catch (\Exception  $e) {

 	}

	}

	public function getOrdersList($id){
 		$user 						= OrdersModel::find($id);
		$json['id'] 			    = $user->id;
		$json['user_id'] 			= !empty($user->user_id) ? $user->user_id : '';
		$json['orders_name'] 		= !empty($user->orders_name) ? $user->orders_name : '';
		$json['order_date_time'] 		= !empty($user->order_date_time) ? $user->order_date_time : '';
		$json['order_image']        = $user->order_image;
		$json['orders_total_price'] = !empty($user->orders_total_price) ? $user->orders_total_price : '0';
		$json['payment_status'] 	= $user->payment_status;
		$json['price_status'] 	= $user->price_status;
		$json['user_mearchant_id'] 	= $user->user_mearchant_id;
		$json['order_firebase_chat_id'] = $user->order_firebase_chat_id;
		$option_main = array();
		foreach($user->get_order_details as $option)
		{
			$data = array();
			$data['id'] 	    = $option->id;
			$data['order_id'] 	= $option->order_id;
			$data['item_name'] = $option->item_name;
			$data['firebase_chat_id'] = $option->firebase_chat_id;
		//	$data['item_price_per_kg'] = $option->item_price_per_kg;
			$data['item_total_price'] = !empty($option->item_total_price) ? $option->item_total_price : '0';
			$data['item_price_per_kg'] = !empty($option->item_price_per_kg) ? $option->item_price_per_kg : '0';
			$data['item_weight'] = $option->item_weight;
			$data['unite']       = !empty($option->unite) ? $option->unite : '0';
			$data['unite_name']   = !empty($option->unite_name) ? $option->unite_name : '';

			
			$option_main[] = $data;
		}

		$json['order_details'] 		= $option_main;

		return $json;
 	}


 	public function app_orders_user_list(Request $request){
		
      $getresultCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getresultCount)){
		$result = array();
	    
	    $getresult = OrdersModel::where('user_id', '=', $request->user_id)->where('user_mearchant_id', '=', $request->user_mearchant_id)->orderBy('id', 'desc')->get();

		foreach ($getresult as $value) {
			$data['id']				      = $value->id;
			$data['user_id']		      = $value->user_id;
			$data['user_mearchant_id']	  = $value->user_mearchant_id;
			$data['order_image']	      = $value->order_image;
			$data['orders_name']		  = !empty($value->orders_name) ? $value->orders_name : '';
			$data['orders_total_price']	  = !empty($value->orders_total_price) ? $value->orders_total_price : '';
			$data['payment_status']		  = $value->payment_status;
			$data['price_status']		  = $value->price_status;
			$data['price_status']		  = $value->price_status;
			$data['is_flag']		      = $value->is_flag;
			$data['order_firebase_chat_id']	= $value->order_firebase_chat_id;
			$result[] = $data;
		}
		  
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
	     	$json['result']  = $result;
		}
		else
		{	
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}

		echo json_encode($json);
	}

	public function app_order_details_list(Request $request)
	{

	 $getresultCount = OrdersModel::where('id', '=', $request->order_id)->count();
		if(!empty($getresultCount)){
		$result = array();
	    
	    $getresult = OrderDetailsModel::where('order_id', '=', $request->order_id)->get();

		foreach ($getresult as $value) {
			$data['id']				    = $value->id;
			$data['order_id']		    = $value->order_id;
			$data['firebase_chat_id']	= !empty($value->firebase_chat_id) ? $value->firebase_chat_id : '';
			$data['item_name']		    = !empty($value->item_name) ? $value->item_name : '';
			$data['item_total_price']	= !empty($value->item_total_price) ? $value->item_total_price : '';
			$data['item_price_per_kg']  = !empty($value->item_price_per_kg) ? $value->item_price_per_kg : '';
			$data['item_weight']        = !empty($value->item_weight) ? $value->item_weight : '';
			$data['unite']              = !empty($value->unite) ? $value->unite : '';
			$data['unite_name']        = !empty($value->unite_name) ? $value->unite_name : '';
			$result[] = $data;
		}
		  
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
	     	$json['result']  = $result;
		}
		else
		{	
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}

		echo json_encode($json);
	}

	public function app_order_details_add(Request $request)
	{
		$getOrder = OrdersModel::where('id', '=', $request->order_id)->count();
		// dd($getOrder);
		if(!empty($getOrder)){
		
		$record_insert = new OrderDetailsModel;
	
		if(!empty($record_insert)){

			$record_insert->order_id  		   = trim($request->order_id);
			$record_insert->item_name 	       = trim($request->item_name);
			$record_insert->item_total_price         = trim($request->item_total_price );
			$record_insert->item_price_per_kg = trim($request->item_price_per_kg );
			$record_insert->item_weight        = trim($request->item_weight );
			$record_insert->unite        = trim($request->unite );
			$record_insert->unite_name        = trim($request->unite_name );
			$record_insert->save();

		    $json['status'] = 1;
			$json['message'] = 'Order details successfully.';

			$json['result'] = $this->getOrderDetailsList($record_insert->id);
			
			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid User.';
			}
		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Record not found!';
		}

		echo json_encode($json);
	}

	public function getOrderDetailsList($id){
 		$user 						= OrderDetailsModel::find($id);
		$json['id'] 			    = $user->id;
		$json['order_id'] 			= !empty($user->order_id) ? $user->order_id : '';
		$json['item_name'] 		    = !empty($user->item_name) ? $user->item_name : '';
		$json['item_total_price']         = !empty($user->item_total_price) ? $user->item_total_price : '';
		$json['item_price_per_kg'] = !empty($user->item_price_per_kg) ? $user->item_price_per_kg : '';
		$json['item_weight']        = !empty($user->item_weight) ? $user->item_weight : '';
		$json['unite']        = !empty($user->unite) ? $user->unite : '';
		$json['unite_name']        = !empty($user->unite_name) ? $user->unite_name : '';

		return $json;
 	}

 	public function app_update_profile(Request $request)
 	{
 		if (!empty($request->name) && !empty($request->user_id)) {
		$update_record = User::find($request->user_id);
		if(!empty($update_record)){
		$update_record->name = trim($request->name);
		
		if (!empty($request->file('user_profile'))) {

			if (!empty($update_record->user_profile) && file_exists('upload/profile/' . '/' . $update_record->user_profile)) {
				unlink('upload/profile/' . $update_record->user_profile);
			}
			$ext = 'jpg';
			$file = $request->file('user_profile');
			$randomStr = str_random(30);
			$filename = strtolower($randomStr) . '.' . $ext;
			$file->move('upload/profile/', $filename);
	    	//$path = "http://localhost/laravel/bookfast/upload/profile/".$filename;
	 		$update_record->user_profile = $filename;
			}
			// else
			// {
			// 		$update_record->user_profile = '';
			// }

	   //	$update_record->email = trim($request->email);
			$update_record->mobile = trim($request->mobile);
			$update_record->address  = !empty($request->address) ? $request->address : '';

			$update_record->save();

			$json['status'] = 1;
			$json['message'] = 'Profile updated successfully.';

			$json['user_data'] = $this->getProfileUser($update_record->id);
		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid User.';
		}

		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}

		echo json_encode($json);
 	}

 	public function app_forgot_email(Request $request)
 	{
 		if(!empty($request->email)) {
 			$rendome_password           = rand(111111,999999);
			$user =  User::where('email','=',$request->email)->first();	

			if(!empty($user))
			{
				// $user->remember_token = str_random(50);
			    // $user->save();
			    $user->password = Hash::make($rendome_password);
			   
			    $user->save();
			    $user->password_rendome   = $rendome_password;
			    Mail::to($user->email)->send(new ForgotPasswordMail($user));

			    $json['status'] = 1;
				$json['message'] = 'Password has been reset. and sent to you mailbox';
			}
			else
			{
				$json['status'] = 0;
				$json['message'] = 'Email not found in the system.';	
			}
		}
		else {
			$json['status'] = 0;
			$json['message'] = 'Due to some error please try again.';
		}
		echo json_encode($json);
 	}

 	public function app_version_setting_update(Request $request){
 		if(!empty($request->app_version)){
		$record = VersionSettingModel::find(1);
		if(!empty($record)){
			$record->app_version   = trim($request->app_version);
			$record->save();
			$json['status'] = 1;
			$json['message'] = 'Update App.';
			$json['user_data'] = $this->getVersionSetting($record->id);
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
	}else{

			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
	}
		echo json_encode($json);
 	}

 	public function getVersionSetting($id){
		$result   					= VersionSettingModel::find($id);
		//$json['id']				= $result->id;	
		$json['user_id']			= $result->id;	
		$json['app_version']        = !empty($result->app_version) ? $result->app_version : '';
	   	return $json;
	}

 	public function app_version_setting_list(Request $request){
 		//$result = array();
	   
		$getresult = VersionSettingModel::get();

		foreach ($getresult as $value) {
			$data['id'] 	       = $value->id;
			$data['app_version']    = !empty($value->app_version) ? $value->app_version : '';
			//$result[] = $data;
		}
		$json['status'] = 1;
		$json['message'] = 'All version setting loaded successfully.';
		// $json['result'] = $result;
		$json['result'] = $data;
	   
	   echo json_encode($json);
 	}

 	
 	public function app_mearchant_list_old_code(Request $request)
 	{

 		$result = array();
	   
		$getresult = User::where('is_admin','=', 0)->where('is_type','=', 2)->orderBy('id', 'desc');
		//$getresult = User::where('is_admin','=', 0)->where('is_type','=', 2)->get();
		$getresult = $getresult->paginate(40);

		foreach ($getresult as $value) {
			$data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address']     	= !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify'] = $value->is_email_verify;
			$data['is_flag'] = $value->is_flag;

			$result[] = $data;
		}
				
		$json['status'] = 1;
		$json['message'] = 'All mearchant loaded successfully.';
		$json['result'] = $result;
	   
	   echo json_encode($json);
 	}

 	public function app_mearchant_list_working(Request $request)
 	{
 		
    // dd($getssrsesult);
    if($request->user_id){
  		$getresultCount = OrdersModel::where('user_id', '=', $request->user_id)->count();
		if(!empty($getresultCount)){
		$result = array();
	    
	    $getresult = OrdersModel::where('user_id', '=', $request->user_id)->get();

	    foreach ($getresult as $value_row) {
			$single_recoard[] = $value_row->user_mearchant_id;
		
		}
		$single_user = User::whereIn('id',$single_recoard)->get();

		foreach ($single_user as $value) {
		    $data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address'] 	    = !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify']= $value->is_email_verify;
			$data['is_flag']        = $value->is_flag;
			$result[] = $data;

		}
			$json['status'] = 1;
			$json['message'] = 'All mearchant data loaded successfully.';
	     	$json['result']  = $result;
		}
		
		else {
			$json['status'] = 0;
			 $json['message'] = 'Record not found.';
		
		}

	 }
	else {
	// $getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 2)->get();
	$result = array();
	   
		$getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 2)->orderBy('id', 'desc');
		//$getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 2)->get();
		$getssrsesult = $getssrsesult->paginate(40);

		foreach ($getssrsesult as $value) {
			$data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address']     	= !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify'] = $value->is_email_verify;
			$data['is_flag'] = $value->is_flag;

			$result[] = $data;
		}
				
		$json['status'] = 2;
		$json['message'] = 'All mearchant loaded successfully.';
		$json['result'] = $result;

		}

		echo json_encode($json);
 	}


 	public function app_mearchant_list(Request $request)
 	{
 	// dd($getssrsesult);
    if($request->user_id){

    	$getresultCount = OrdersModel::where('user_id', '=', $request->user_id)->where('is_flag', '=', $request->is_flag)->count();
		if(!empty($getresultCount)){
		$result = array();
	    
	    $getresult = OrdersModel::where('user_id', '=', $request->user_id)->where('is_flag', '=', $request->is_flag)->get();
	  
	    foreach ($getresult as $value_row) {

			$single_recoard[] = $value_row->user_mearchant_id;
		
		}
		$single_user = User::whereIn('id',$single_recoard)->get();

		foreach ($single_user as $value) {
		    $data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address'] 	    = !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify']= $value->is_email_verify;
			$data['is_flag']        = $value->is_flag;
			$result[] = $data;

		}



			$json['status'] = 1;
			$json['message'] = 'All mearchant data loaded successfully.';
	     	$json['result']  = $result;
		}
		
		else {
			$json['status'] = 0;
			 $json['message'] = 'Record not found.';
		
		}

	 }
	else {
	// $getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 2)->get();
	$result = array();
	   
		$getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 2)->orderBy('id', 'desc');
		//$getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 2)->get();
		$getssrsesult = $getssrsesult->paginate(40);

		foreach ($getssrsesult as $value) {
			$data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address']     	= !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify'] = $value->is_email_verify;
			$data['is_flag'] = $value->is_flag;

			$result[] = $data;
		}
				
		$json['status'] = 2;
		$json['message'] = 'All mearchant loaded successfully.';
		$json['result'] = $result;

		}

		echo json_encode($json);
 	}

 	public function app_normal_user_list(Request $request){
 		// dd($getssrsesult);
    if($request->user_mearchant_id){
	 if ($request->is_flag==0) {
		    $result = array();
	 $getresultss = OrdersModel::where('user_mearchant_id', '=', $request->user_mearchant_id)->get();
	 foreach ($getresultss as $value_rowd) {

				$single_recoard[] = $value_rowd->user_id;
		
		}
		$single_userss = User::whereIn('id',$single_recoard)->get();
	foreach ($single_userss as $value) {
		    $data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address'] 	    = !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify']= $value->is_email_verify;
			$data['is_flag']        = $value->is_flag;
			$result[] = $data;

		}



			$json['status'] = 1;
			$json['message'] = 'All mearchant data loaded successfully.';
	     	$json['result']  = $result;

	    		 }else{
    	$getresultCount = OrdersModel::where('user_mearchant_id', '=', $request->user_mearchant_id)->where('is_flag', '=', $request->is_flag)->count();
		if(!empty($getresultCount)){
		$result = array();
	    
	    $getresult = OrdersModel::where('user_mearchant_id', '=', $request->user_mearchant_id)->where('is_flag', '=', $request->is_flag)->get();
	  //dd($request->is_flag);
	    foreach ($getresult as $value_row) {

			$single_recoard[] = $value_row->user_id;
		
		}
		$single_user = User::whereIn('id',$single_recoard)->get();

		foreach ($single_user as $value) {
		    $data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address'] 	    = !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify']= $value->is_email_verify;
			$data['is_flag']        = $value->is_flag;
			$result[] = $data;

		}



			$json['status'] = 1;
			$json['message'] = 'All mearchant data loaded successfully.';
	     	$json['result']  = $result;
		}
		
		else {
			$json['status'] = 0;
			 $json['message'] = 'Record not found.';
		
		}
	}

	 }
	else {
	// $getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 1)->get();
	$result = array();
	   
		$getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 1)->orderBy('id', 'desc');
		//$getssrsesult = User::where('is_admin','=', 0)->where('is_type','=', 1)->get();
		$getssrsesult = $getssrsesult->paginate(40);

		foreach ($getssrsesult as $value) {
			$data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address']     	= !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify'] = $value->is_email_verify;
			$data['is_flag'] = $value->is_flag;

			$result[] = $data;
		}
				
		$json['status'] = 2;
		$json['message'] = 'All mearchant loaded successfully.';
		$json['result'] = $result;

		}

		echo json_encode($json);
 	}


 	public function app_normal_user_list_old_workindse(Request $request)
 	{
 	 
 	 $getresultCount = OrdersModel::where('user_mearchant_id', '=', $request->user_mearchant_id)->count();
	 if(!empty($getresultCount)){
		$result = array();
	    
	    $getresult = OrdersModel::where('user_mearchant_id', '=', $request->user_mearchant_id)->get();

	    foreach ($getresult as $value_row) {
			$single_recoard[] = $value_row->user_id;
		 
		}
		$single_user = User::whereIn('id',$single_recoard)->get();

		foreach ($single_user as $value) {
		    $data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address'] 	    = !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify']= $value->is_email_verify;
			$data['is_flag']        = $value->is_flag;
			$result[] = $data;

		}


			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
	     	$json['result']  = $result;
		}
		else
		{	
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}

		echo json_encode($json);
 	}

 	public function app_normal_user_list_old(Request $request)
 	{
 		$result = array();
	   
		$getresult = User::where('is_admin','=', 0)->where('is_type','=', 1)->orderBy('id', 'desc');
		//$getresult = User::where('is_admin','=', 0)->where('is_type','=', 2)->get();
		$getresult = $getresult->paginate(40);

		foreach ($getresult as $value) {
			$data['user_id'] 	    = $value->id;
			$data['name']           = !empty($value->name) ? $value->name : '';
		    $data['lastname'] 		= !empty($value->lastname) ? $value->lastname : '';
			$data['email'] 		    = !empty($value->email) ? $value->email : '';
			$data['mobile'] 	    = !empty($value->mobile) ? $value->mobile : '';
			$data['social_type'] 	= $value->social_type;
			$data['social_id'] 		= !empty($value->social_id) ? $value->social_id : '';
			$data['user_profile'] 	= !empty($value->user_profile) ? $value->user_profile : '';
			$data['address'] 	    = !empty($value->address) ? $value->address : '';
			$data['is_type'] 	    = $value->is_type;
			$data['is_email_verify']= $value->is_email_verify;
			$data['is_flag']        = $value->is_flag;
			$result[] = $data;
		}
				
		$json['status'] = 1;
		$json['message'] = 'All normal user loaded successfully.';
		$json['result'] = $result;
	   
	   echo json_encode($json);
 	}


	public function app_orders_add_old(Request $request)
	{
		$getUser = User::where('id', '=', $request->user_id)->count();
		// dd($getUser);
		if(!empty($getUser)){
		
		$record_insert = new OrdersModel;
	
		if(!empty($record_insert)){

			$record_insert->user_id  		   = trim($request->user_id);
			$record_insert->orders_name 	   = trim($request->orders_name);
			$record_insert->orders_total_price = trim($request->orders_total_price );
			$record_insert->save();

		    $json['status'] = 1;
			$json['message'] = 'Order successfully.';

			$json['result'] = $this->getOrdersList($record_insert->id);
			
			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid User.';
			}
		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Record not found!';
		}

		echo json_encode($json);
	}

	public function getOrdersList_old($id){
 		$user 						= OrdersModel::find($id);
		$json['id'] 			    = $user->id;
		$json['user_id'] 			= !empty($user->user_id) ? $user->user_id : '';
		$json['orders_name'] 		= !empty($user->orders_name) ? $user->orders_name : '';
		$json['orders_total_price'] = !empty($user->orders_total_price) ? $user->orders_total_price : '';
	
		return $json;
 	}
 	

 	public function app_orders_update(Request $request)
 	{
 		
 		if (!empty($request->id)) {
		$update_record = OrdersModel::find($request->id);
		if(!empty($update_record)){
		    $update_record->orders_name        = $request->orders_name;
		    
		    // $AddAmount = $update_record->orders_total_price+$request->orders_total_price;
			// $update_record->orders_total_price = $AddAmount;
			$update_record->orders_total_price    = $request->orders_total_price;
			//$update_record->user_id               = $request->user_id;
			//$update_record->payment_status        = $request->payment_status;
	     	$update_record->price_status          = $request->price_status;
	     		
	     	if (!empty($request->file('order_image'))) {
  
			if (!empty($update_record->order_image) && file_exists('upload/' . '/' . $update_record->order_image)) {
				unlink('upload/' . $update_record->order_image);
			}
			$ext = 'jpg';
			$file = $request->file('order_image');
			$randomStr = str_random(30);
			$filename = strtolower($randomStr) . '.' . $ext;
			$file->move('upload/', $filename);
	    	//$path = "http://localhost/laravel/kadakaran/upload/".$filename;
	 		$update_record->order_image = $filename;
			}
			// else
			// {
			// 		$update_record->order_image = '';
			// }

	     	$update_record->order_date_time          = $request->order_date_time;
	     	$update_record->order_firebase_chat_id   = $request->order_firebase_chat_id;
			//dd($request->user_id_user);
			$update_record->save();



			if(!empty($request->order_details))
			{
				OrderDetailsModel::where('order_id', '=', $update_record->id)->delete();

				$option = json_decode($request->order_details);
				
				foreach ($option as  $value) {
				// dd($value);
				
						$save_option = new OrderDetailsModel;
						$save_option->order_id 			 = $update_record->id;
						$save_option->item_name 	     = $value->item_name;
						$save_option->firebase_chat_id 	     = $value->firebase_chat_id;
						//$save_option->item_total_price         = $value->item_total_price;
						//$save_option->item_price_per_kg = $value->item_price_per_kg;
						$save_option->item_total_price   = !empty($value->item_total_price) ? $value->item_total_price : '';
						$save_option->item_price_per_kg   = !empty($value->item_price_per_kg) ? $value->item_price_per_kg : '';
						$save_option->item_weight        = $value->item_weight;
						$save_option->unite        = !empty($value->unite) ? $value->unite : '0';
						$save_option->unite_name   = !empty($value->unite_name) ? $value->unite_name : '';
						$save_option->save();	
				
				}
			}

			$json['status'] = 1;
			$json['message'] = 'Order updated successfully.';

			$json['user_data'] = $this->getOrdersUpdate($update_record->id);
			$getord_ers = User::where('id', '=', $request->user_id)->first();
	
			$getord_ers_user_mearchant_id = User::where('id', '=', $update_record->user_mearchant_id)->first();
	
			$title   = "Order Updated!";
			$getorder = OrdersModel::where('id', '=', $request->id)->first();    
		   if($getord_ers->is_type == 1){
		   	
  			$message = "Order ".$update_record->orders_name." has been updated by ".$getord_ers->name."";
$this->sendPushNotificationUserOrder($update_record->order_firebase_chat_id,$getorder->user_mearchant_id,$title,$message,$getorder->user_mearchant_id,$update_record->orders_name,$update_record->orders_total_price,$request->id,$update_record->order_image);
		    }else {
 		
		    $message = "Order ".$update_record->orders_name." has been updated by ".$getord_ers_user_mearchant_id->name."";
		    $this->sendPushNotificationUserOrder($update_record->order_firebase_chat_id,$getorder->user_mearchant_id,$title,$message,$getorder->user_id,$update_record->orders_name,$update_record->orders_total_price,$request->id,$update_record->order_image);
		    }
			
		

			

		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid Order Id.';
		}

		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Payment not done.';
		}

		echo json_encode($json);
 	}


	public function sendPushNotificationUserOrder($order_firebase_chat_id,$user_mearchant_id,$title,$message,$id,$orders_name,$orders_total_price,$user_id,$order_image){
	
		$user 	= User::find($id);
		
 		  	$result = NotificationModel::find(1);
 		  	$serverKey = $result->notification_key;
 		  	try {
			if(!empty($user->token)) {

				$token = $user->token;
				$body['order_id']                = $user_id;
				$body['mearchant_id']            = $user_mearchant_id;
			    $body['price_status']            = "1";
				$body['payment_status']          = "0";
				$body['orders_name']             = $orders_name;
				$body['order_image']             = $order_image;
				$body['order_firebase_chat_id']  = $order_firebase_chat_id;
				
                if(!empty($user))
				{
					$body['mearchant_name']         = $user->name;
					$body['mearchant_user_profile'] = $user->user_profile;
					$body['mearchant_address']      = $user->address;
				}

				$body['message'] = $message;
				$body['body']    = $body;
				$body['title']   = $title;

				$url = "https://fcm.googleapis.com/fcm/send";

				$notification = array('title' => $title, 'body' => $message);

				$arrayToSend = array('to' => $token, 'notification' => $notification, 'data' => $body, 'priority' => 'high');

				$json1 = json_encode($arrayToSend);

				$headers = array();
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Authorization: key=' . $serverKey;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				$response = curl_exec($ch);
				//dd($response);
				curl_close($ch);
			}
		}
		catch (\Exception  $e) {

 	}

	}


 	public function getOrdersUpdate($id){
 		$user 						= OrdersModel::find($id);
		$json['id'] 			    = $user->id;
		$json['user_id'] 			= !empty($user->user_id) ? $user->user_id : '';
		$json['order_image'] 		= $user->order_image;
		$json['orders_name'] 		= !empty($user->orders_name) ? $user->orders_name : '';
		$json['order_date_time'] 		= !empty($user->order_date_time) ? $user->order_date_time : '';
		$json['orders_total_price'] = !empty($user->orders_total_price) ? $user->orders_total_price : '0';
		$json['payment_status'] 	= $user->payment_status;
		$json['price_status'] 	    = $user->price_status;
		$json['user_mearchant_id'] 	= $user->user_mearchant_id;
		$json['order_firebase_chat_id'] 	= $user->order_firebase_chat_id;
		$option_main = array();
		foreach($user->get_order_details as $option)
		{
			$data = array();
			$data['id'] 	    = $option->id;
			$data['order_id'] 	= $option->order_id;
			$data['item_name'] = $option->item_name;
			$data['firebase_chat_id'] = $option->firebase_chat_id;
//			$data['item_price_per_kg'] = $option->item_price_per_kg;
			$data['item_total_price'] = !empty($option->item_total_price) ? $option->item_total_price : '0';
			$data['item_price_per_kg'] = !empty($option->item_price_per_kg) ? $option->item_price_per_kg : '0';

			$data['item_weight'] = $option->item_weight;

			$data['unite']       = !empty($option->unite) ? $option->unite : '0';
			$data['unite_name']  = !empty($option->unite_name) ? $option->unite_name : '';

			
			$option_main[] = $data;
		}

		$json['order_details'] 		= $option_main;

		return $json;
 	}

 	public function app_select_mearchant(Request $request){
    	$update_record = User::find($request->id);
    	$update_rec = User::where('id', '=', $request->mearchant_id)->where('is_type', '=', 2)->first();
			// dd($update_rec);
 			if(!empty($update_rec) && !empty($update_record)){
 				$update_record->mearchant_id   = trim($request->mearchant_id);
		  		$update_record->save();
// dd($request->id);
 				$json['status'] = 1;
			    $json['message'] = 'ID successfully save';

			     $title   = "Mearchant";
		    	 $message = "Mearchant successfully!";

			     $this->sendPushNotificationMearchant($title,$message,$request->mearchant_id,$request->id);

 			}
			else
			{
				$json['status'] = 0;
				$json['message'] = 'Invalid user!.';
			}
		
		echo json_encode($json);
 	}

 	public function sendPushNotificationMearchant($title,$message,$mearchant_id,$id){
 		   //dd($title);
 			$user 	= User::find($mearchant_id);
 			$user_reco = User::find($id);
 			//dd($user_reco);
 			$order_re = OrdersModel::where('user_id', '=', $user_reco->id)->first();
 			// dd($order_re->orders_name);
 			$order_de_re = OrderDetailsModel::where('order_id', '=', $order_re->id)->first();
 		  	//dd($order_de_re->item_name);
 		  	$result = NotificationModel::find(1);
 		  	$serverKey = $result->notification_key;
 		  	try {
			if(!empty($user->token)) {

				$token = $user->token;
// dd($token);
				if(!empty($order_de_re))
				{
					$body['order_id']          = $order_de_re->order_id;
					$body['item_name']         = $order_de_re->item_name;
					$body['item_total_price']  = $order_de_re->item_total_price;
					$body['item_price_per_kg'] = $order_de_re->item_price_per_kg;
					$body['item_weight']       = $order_de_re->item_weight;
				}


				$body['message'] = $message;
				//$body['body']    = $message;
				$body['body']    = $body;
				$body['title']   = $title;

				$url = "https://fcm.googleapis.com/fcm/send";

				$notification = array('title' => $title, 'body' => $message);

				$arrayToSend = array('to' => $token, 'notification' => $notification, 'data' => $body, 'priority' => 'high');

				$json1 = json_encode($arrayToSend);

				$headers = array();
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Authorization: key=' . $serverKey;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				$response = curl_exec($ch);
				//dd($response);
				curl_close($ch);
		// 	}

		}
		}
		catch (\Exception  $e) {

 	}

 	}

	public function app_notification_store_list(Request $request)
	{

	 	$result = array();
	    
	    $getresult = NotificationStoreModel::where('user_mearchant_id', '=', $request->user_mearchant_id)->orderBy('id', 'desc')->get();

		foreach ($getresult as $value) {
			$data['id']				    = $value->id;
			$data['user_id']	= $value->user_id;
			$data['user_name']	=  !empty($value->get_user_name->name) ? $value->get_user_name->name : '';
			$data['user_profile']	= !empty($value->get_mearchant_name->user_profile) ? $value->get_mearchant_name->user_profile : '';
			$data['title']	= $value->title;
			$data['message']	= $value->message;
			$data['user_mearchant_id']	= $value->user_mearchant_id;
			$data['mearchant_name']	= !empty($value->get_mearchant_name->name) ? $value->get_mearchant_name->name : '';
			$data['mearchant_profile']	= !empty($value->get_mearchant_name->user_profile) ? $value->get_mearchant_name->user_profile : '';
			$data['order_id']	= $value->order_id;
			$data['orders_name']	= !empty($value->orders_name) ? $value->orders_name : '';
			$data['order_date_time']	= !empty($value->order_date_time) ? $value->order_date_time : '';
			$data['price_status']	= $value->get_order_name->price_status;
			$data['payment_status']	= $value->get_order_name->payment_status;
			$data['address']	= !empty($value->address) ? $value->address : '';
			$data['order_firebase_chat_id']	= $value->order_firebase_chat_id;
			$data['created_at']	    = date('d-m-Y h:i A', strtotime($value->created_at));
			$data['updated_at']	    = date('d-m-Y h:i A', strtotime($value->updated_at));
			$result[] = $data;
		}
		  
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
	     	$json['result']  = $result;
		
		echo json_encode($json);
	}

	public function app_payment_orders_update(Request $request){
		 if (!empty($request->id)) {
		$update_record = OrdersModel::find($request->id);
		//dd($update_record);
		if(!empty($update_record)){
		    // $update_record->payment_status        = $request->payment_status;
		    $update_record->payment_status        = "1";
		    $update_record->is_flag               = "2";
		  	$update_record->save();

		  	$this->normal_user_is_flag_update_payment($update_record->user_id);
			$this->mearchant_is_flag_update_payment($update_record->user_mearchant_id);


		 	$json['status'] = 1;
			$json['message'] = 'Payment successfully updated.';
			
			$getordername = OrdersModel::where('id', '=', $request->id)->first();

			$getusername = User::where('id', '=', $getordername->user_id)->first();
			//dd($getusername->name);
			//$title   = "Mearchant";
			$title = " ".$getusername->name." ";
		    $message = " ".$getordername->orders_name." payment successfully!";
			// dd($message);

			$getorder = OrdersModel::where('id', '=', $request->id)->first();
			 //dd($getorder->user_id);
			$this->sendPushNotificationMearchantPayment($title,$message,$getorder->user_mearchant_id,$getordername->orders_name,$request->id);

			$json['user_data'] = $this->getPaymentOrdersUpdate($update_record->id);

			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid Order Id.';
			}

		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Payment not change.';
		}

		echo json_encode($json);
	}

	 public function mearchant_is_flag_update_payment($user_mearchant_id)
	{
		$insert_user = User::where('id','=',$user_mearchant_id)->first();
		
			// if(empty($insert_wallet)) {
			// 	$insert_wallet = new WalletModel;
			// 	$insert_wallet->amount = 100;
			// }
			$insert_user->is_flag = 2;
	
			//$insert_wallet->amount  = trim($request->amount);
			
			$insert_user->save();
	}



	public function normal_user_is_flag_update_payment($user_id)
	{
		$insert_user = User::where('id','=',$user_id)->first();
		
			// if(empty($insert_wallet)) {
			// 	$insert_wallet = new WalletModel;
			// 	$insert_wallet->amount = 100;
			// }
			$insert_user->is_flag = 2;
	
			//$insert_wallet->amount  = trim($request->amount);
			
			$insert_user->save();
	}


	public function getPaymentOrdersUpdate($id){
 		$user 						= OrdersModel::find($id);
		$json['id'] 			    = $user->id;
		$json['user_id'] 			= !empty($user->user_id) ? $user->user_id : '';
		$json['orders_name'] 		= !empty($user->orders_name) ? $user->orders_name : '';
		$json['orders_total_price'] = !empty($user->orders_total_price) ? $user->orders_total_price : '';
		$json['payment_status'] 	= $user->payment_status;
		$json['price_status']    	= $user->price_status;
		return $json;
 	}

 	public function sendPushNotificationMearchantPayment($title,$message,$user_mearchant_id,$orders_name,$id){
 		//dd($user_mearchant_id);
 		//dd($id);
 			// $user 	= User::find($mearchant_id);
 			// $user_reco = User::find($id);
 			//dd($user_reco);
 			// $order_re = OrdersModel::where('user_id', '=', $user_reco->id)->first();
 			// dd($order_re->orders_name);
 			$user = User::where('id', '=', $user_mearchant_id)->first();
 		  	// dd($order_de_re->token);
 		  	$result = NotificationModel::find(1);
 		  	$serverKey = $result->notification_key;
 		  	try {
			if(!empty($user->token)) {

				$token = $user->token;


				$body['price_status'] = "0";
			//	$body['payment_status'] = "0";
					// dd($body['payment_status']);
				$body['order_id']             = $id;
				$body['orders_name']          = $orders_name;
				
				 // dd($body['orders_name']);
				if(!empty($user))
				{
					$body['mearchant_id']          = $user->id;
					$body['mearchant_name']         = $user->name;
					$body['mearchant_user_profile']  = $user->user_profile;
					$body['mearchant_address']      = $user->address;
					$body['is_flag']               = $user->is_flag;
					$body['payment_status']       = "1";
					//dd($body['mearchant_address']);
				}

				// dd($token);
				 //$body['price_status'] = "1";
				//$body['payment_status'] = "1";
				// dd($body['payment_status']);
				//$body['orders_name']             = $orders_name;
				// if(!empty($user))
				// {
				// 	$body['is_flag']          = $user->is_flag;
				// 	$body['is_payment_type']  = "2";
					

				// 	 // dd($body['is_payment_type']);
				// }

			
				$body['message'] = $message;
				//$body['body']    = $message;
				$body['body']    = $body;
				$body['title']   = $title;

				$url = "https://fcm.googleapis.com/fcm/send";

				$notification = array('title' => $title, 'body' => $message);

				$arrayToSend = array('to' => $token, 'notification' => $notification, 'data' => $body, 'priority' => 'high');

				$json1 = json_encode($arrayToSend);

				$headers = array();
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Authorization: key=' . $serverKey;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);

				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json1);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				$response = curl_exec($ch);
			    //dd($response);
				curl_close($ch);
		// 	}

		}
		}
		catch (\Exception  $e) {

 	}
 	}


 	public function app_notification_store_user_list(Request $request)
	{


	 	$result = array();
	    
	    $getresult = NotificationStoreModel::where('user_id', '=', $request->user_id)->orderBy('id', 'desc')->get();

		foreach ($getresult as $value) {
			$data['id']				    = $value->id;
			$data['user_id']	= $value->user_id;
			$data['user_name']	=  !empty($value->get_user_name->name) ? $value->get_user_name->name : '';
			$data['user_profile']	= !empty($value->get_mearchant_name->user_profile) ? $value->get_mearchant_name->user_profile : '';
			$data['title']	= $value->title;
			$data['message']	= $value->message;
			$data['user_mearchant_id']	= $value->user_mearchant_id;
			$data['mearchant_name']	= !empty($value->get_mearchant_name->name) ? $value->get_mearchant_name->name : '';
			$data['mearchant_profile']	= !empty($value->get_mearchant_name->user_profile) ? $value->get_mearchant_name->user_profile : '';
			$data['order_date_time']	= !empty($value->order_date_time) ? $value->order_date_time : '';
			$data['order_id']	= $value->order_id;
			$data['price_status']	= $value->get_order_name->price_status;
			$data['payment_status']	= $value->get_order_name->payment_status;
			$data['orders_name']	= !empty($value->orders_name) ? $value->orders_name : '';
			$data['address']	    = !empty($value->address) ? $value->address : '';
			$data['order_firebase_chat_id']	    = $value->order_firebase_chat_id;
			$data['created_at']	    = date('d-m-Y h:i A', strtotime($value->created_at));
			$data['updated_at']	    = date('d-m-Y h:i A', strtotime($value->updated_at));
			$result[] = $data;
		}
		  
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
	     	$json['result']  = $result;
		
		echo json_encode($json);
	}
	
// OTP Verify start
	public function app_verify_otp(Request $request){
		if (!empty($request->mobile) && !empty($request->otp) && !empty($request->is_type)) {
			$user = User::where('mobile', '=', $request->mobile)->where('otp', '=', $request->otp)->where('is_type', '=', $request->is_type)->first();
		if(!empty($user)){
			$check = $user->otp;
				if(!empty($check)){
				    $user->otp_verify = 1;
					$user->save();
					$this->updateToken($user->id);
					$json['status'] = 1;
					$json['message'] = 'Verified Successfully.';
					$json['result'] = $this->getProfileUser($user->id);
				}
				else 
				{
					$json['status'] = 0;
					$json['message'] = 'Invalid OTP entred.';
				}
			}else{
				
				$json['status'] = 0;
				$json['message'] = 'OTP is incorrect';
			}
		}
		else {
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}

		echo json_encode($json);
	}

	// OTP Verify End
	// Resend OTP Start
	public function app_resend_otp(Request $request)
	{
		if(!empty($request->mobile) && !empty($request->is_type)){
			$otp = rand(1111,9999);
			   $user = User::where('mobile', '=', trim($request->mobile))->where('is_type', '=', trim($request->is_type))->first();
			if (!empty($user)) {
				//$user->otp = 9998;

				$user->otp = $otp;
				$user->save();
				$this->updateToken($user->id);

			$json['status'] = 1;
			$json['message'] = 'OTP sent successfully.';
			$json['result'] = $this->getProfileUser($user->id);

		}
		 else {
				$json['status'] = 0;
				$json['message'] = 'Mobile number not found!';
			}
		} else {
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	}

// Resend OTP End

	// store OTP In database Start
	public function app_store_mobile_otp(Request $request){
		if (!empty($request->otp) && !empty($request->user_id)) {
		$update_record = User::find($request->user_id);
			if(!empty($update_record)){
		    $update_record->otp = trim($request->otp);
		    $update_record->otp_verify = 1;
			$update_record->save();

			$json['status'] = 1;
			$json['message'] = 'Mobile OTP updated successfully.';

			$json['user_data'] = $this->getProfileUser($update_record->id);
		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid User.';
		}
		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}

		echo json_encode($json);
	}
	// store OTP In database End
	// update password Strat
	public function app_update_password(Request $request)
	{
	  
	  $user = User::where('id','=',$request->user_id)->first();
   
	  if(trim($request->new_password) == trim($request->confirm_password))
        {
        	if(!empty($user)){
            
                $user->password = Hash::make($request->new_password);
                $user->save();
                    $json['status'] = 1;
					$json['message'] = 'Password successfully updated.';
				}	else
		       {
			   	$json['status'] = 0;
				$json['message'] = 'Invalid User.';
		      }
         }
        else
        {
        	$json['status'] = 0;
			$json['message'] = 'Confirm password does not updated.';
            
        }

		echo json_encode($json);
	}
	// update Passwork end

	
}
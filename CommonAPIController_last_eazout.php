<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Services;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Slider;
use App\Models\HelpFaqs;
use App\Models\UsersAddress;
use App\Models\Rating;
use App\Models\PaymentCardDetail;
use App\Models\NotificationStore;
use App\Models\UsersWalletDetails;
use App\Models\ServicesFlag;
use App\Models\OrderModel;
use App\Models\CartsOptionsModel;
use App\Models\OrderDetailModel;
use App\Models\OrderDetailsModel;
use App\Models\OrderDetailItemModel;
use App\Models\NotificationServerKeyModel;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Prettus\Validator\Exceptions\ValidatorException;
use Str;
use File;
use DB; 

 
class CommonAPIController extends Controller {

	public function getProfileUserCommon($id){
        $user                   = User::find($id);
        $json['user_id']        = $user->id;
        $json['name']           = !empty($user->name) ? $user->name : '';
        $json['email']          = !empty($user->email) ? $user->email : '';
        $json['mobile']         = !empty($user->mobile) ? $user->mobile : '';
        $json['otp']            = !empty($user->otp) ? $user->otp : '';
        $json['otp_verify']     = !empty($user->otp_verify) ? $user->otp_verify : '0';
        $json['lat']            = !empty($user->lat) ? $user->lat : '';
        $json['lang']           = !empty($user->lang) ? $user->lang : '';
        $json['user_profile']   = !empty($user->user_profile) ? $user->user_profile : '';
        $json['address']        = !empty($user->address) ? $user->address : '';
        $json['token']          = !empty($user->token) ? $user->token : ''; 
        $json['total_balance']  = !empty($user->total_balance) ? $user->total_balance : '0';
        $json['document_photo'] = !empty($user->document_photo) ? $user->document_photo : '';
        $json['social_id'] 		= !empty($user->social_id) ? $user->social_id : '';
		$json['social_type'] 	= $user->social_type;
		$json['online_offline_status'] 	= $user->online_offline_status;
		$json['account_number'] 	= !empty($user->account_number) ? $user->account_number : '';
		$json['bank_holder_name'] 	= !empty($user->bank_holder_name) ? $user->bank_holder_name : '';
		$json['ifsc_code'] 	        = !empty($user->ifsc_code) ? $user->ifsc_code : '';
		$json['branch_code'] 	    = !empty($user->address) ? $user->branch_code : '';
		$json['bank_name'] 	        = !empty($user->address) ? $user->bank_name : '';

		

        return $json;

    }

	public function app_verify_phone_old(Request $request)
	{
		// user_id
		// mobile

		if(!empty($request->user_id) && !empty($request->mobile)){
			//$otp = rand(1111,9999);
			$user = User::where('id', '=', $request->user_id)->where('mobile', '=', $request->mobile)->first();
			if (!empty($user)) {
				//$user->otp = 9998;
				//$user->otp = $otp;
				$user->otp = null;
				$user->save();

				$json['status'] = 1;
				$json['message'] = 'OTP sent successfully.';
				$json['result'] = $this->getProfileUserCommon($user->id);
				//return $this->sendResponse($user, 'OTP sent successfully.');
			

			}
			 else {
					$json['status'] = 0;
					$json['message'] = 'Mobile number not found!';
				}
		} else {
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		echo json_encode($json);

	}


	public function app_verify_phone(Request $request)
	{
		if(!empty($request->mobile)){
			//$otp = rand(1111,9999);
			$user = User::where('mobile', '=', $request->mobile)->first();
			if (!empty($user)) {
				//$user->otp = 9998;
				//$user->otp = $otp;
				$user->otp = null;
				$user->save();

				$json['status'] = 1;
				$json['message'] = 'OTP sent successfully.';
				$json['result'] = $this->getProfileUserCommon($user->id);
				//return $this->sendResponse($user, 'OTP sent successfully.');
			

			}
			 else {
					$json['status'] = 0;
					$json['message'] = 'Mobile number not found!';
				}
		} else {
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		echo json_encode($json);
	}

	public function app_store_mobile_otp(Request $request){
		if (!empty($request->otp) && !empty($request->user_id)) {
		$update_record = User::find($request->user_id);
			if(!empty($update_record)){
		    $update_record->otp = trim($request->otp);
		    $update_record->otp_verify = 1;
			$update_record->save();
			// return $this->sendResponse($update_record, 'Mobile OTP updated successfully.');
			    $json['status'] = 1;
				$json['message'] = 'Mobile OTP updated successfully.';
				$json['result'] = $this->getProfileUserCommon($update_record->id);
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


	public function app_verification_otp(Request $request)
	{
		if(!empty($request->user_id) && !empty($request->mobile) && !empty($request->otp)){
			$user = User::where('id', '=', $request->user_id)->where('mobile', '=', $request->mobile)->where('otp', '=', $request->otp)->first();

				if(!empty($user)){
		    	$check = $user->otp;
				if(!empty($check)){
				    $user->otp_verify = 1;
					$user->save();
					//return $this->sendResponse($user, 'Verified Successfully.');
					$json['status'] = 1;
					$json['message'] = 'Verified Successfully.';
					$json['result'] = $this->getProfileUserCommon($user->id);
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

		}else{
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		echo json_encode($json);
	}

	public function app_resend_otp(Request $request){
		if(!empty($request->mobile) && !empty($request->user_id)){
			$otp = rand(111111,999999);
			   $user = User::where('mobile', '=', trim($request->mobile))->where('id', '=', trim($request->user_id))->first();
			if (!empty($user)) {
				//$user->otp = 9998;

				$user->otp = $otp;
				$user->save();
				//return $this->sendResponse($user, 'OTP sent successfully.');

				$json['status'] = 1;
				$json['message'] = 'OTP sent successfully.';
				$json['result'] = $this->getProfileUserCommon($user->id);

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

// Update Profile Start
	public function app_update_profile(Request $request){

	   if(!empty($request->name) && !empty($request->user_id)){ 
		$update_record = User::find($request->user_id);
		//dd($update_record);
			if(!empty($update_record)){

			if (!empty($request->file('user_profile'))) {

				// if (!empty($update_record->user_profile) && file_exists('images/' . '/' . $update_record->user_profile)) {
				// 	unlink('images/' . $update_record->user_profile);
				// }
				$ext = 'jpg';
				$file = $request->file('user_profile');

				$randomStr = str_random(30);
				
				$filename = strtolower($randomStr) . '.' . $ext;

				$file->move('images/', $filename);
		    	//$path = "http://localhost/laravel/bookfast/upload/profile/".$filename;
		 		$update_record->user_profile = $filename;
		 		//dd($update_record->user_profile);
				}
				// else
				// {
				// 		$update_record->user_profile = '';
				// }


				$update_record->name    = trim($request->name);
				$update_record->mobile  = trim($request->mobile);
				$update_record->address = !empty($request->address) ? $request->address : '';
				$update_record->save();

				//return $this->sendResponse($update_record, 'Profile updated successfully.');

				$json['status'] = 1;
				$json['message'] = 'Profile updated successfully.';
				$json['result'] = $this->getProfileUserCommon($update_record->id);

			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid User.';
			}
		}else{
				$json['status'] = 0;
				$json['message'] = 'Parameter missing!';
			}
			echo json_encode($json);
	}
// Update Profile End

// Profile Start
	public function app_profile_list(Request $request){
		$getcount = User::where('id', '=', $request->user_id)->count();
			if(!empty($getcount)){
			// $result = array();
			$getresult = User::where('id', '=', $request->user_id)->orderBy('id', 'desc')->get();

			//	foreach($getresult as $value) {
			//	$data['user_id']    = $value->id;
				return $this->sendResponse($getresult, 'All Profile data loaded successfully.');
				// $json['status'] = 1;
				// $json['message'] = 'All Profile data loaded successfully.';
				// $json['result'] = $this->getProfileUserCommon($getresult->id);

			// 	$result[] = $data;
			// }
				// $json['status'] = 1;
				// $json['message'] = 'All data loaded successfully.';
		     	// $json['result']  = $result;
		    }else{
		    	$json['status'] = 0;
				$json['message'] = 'Record not found.';
		    }
	    echo json_encode($json);

	}
// Profile End
// Categories Start
	public function app_categories_list(Request $request){
		$result = array();
		$getresult = Category::orderBy('id', 'desc')->get();
			foreach ($getresult as $value) {
				$data['id']				= $value->id;
				$data['name']			= $value->name;
				$data['description']	= strip_tags($value->description);
				$data['categories_img']	= $value->categories_img;
				$result[] = $data;
				// http://localhost/laravel/new_eazout/public/images/123.jpg
			}
		    $json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
	     	$json['result']  = $result;
	    echo json_encode($json);
	}

// Categories End

// Services Start
	public function app_services_list(Request $request){
		// categories_id
		$getRecordCount = Category::where('id', '=', $request->categories_id)->count();
		if(!empty($getRecordCount)){

		$result = array();
		$getRecord = Services::where('categories_id', '=', $request->categories_id)->orderBy('id', 'desc')->get();
		
		foreach ($getRecord as $value) {
			$data['id']				      = $value->id;
			$data['store_name']		      = !empty($value->store_name) ? $value->store_name : '';
			$data['services_description'] = !empty($value->services_description) ? $value->services_description : '';
			$data['services_image'] = !empty($value->services_image) ? $value->services_image : '';
			$data['latitude']       = !empty($value->latitude) ? $value->latitude : '';
			$data['longitude']      = !empty($value->longitude) ? $value->longitude : '';
	    	$data['services_flag_status'] = $value->flag;
			$result[] = $data;
		}
		$json['status'] = 1;
		$json['message'] = 'All services loaded successfully.';
		$json['result'] = $result;
	   }else{
	   		$json['status'] = 0;
			$json['message'] = 'Record not found.';
	   }
	   echo json_encode($json);
	}

	public function app_services_list_old(Request $request){
		// categories_id
		// user_id
		$getRecordCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getRecordCount)){
		$getRecordUser = User::where('id', '=', $request->user_id)->orderBy('id', 'desc')->get();

		$result = array();
  		foreach ($getRecordUser as $get_Location) {

  		if(!empty($get_Location->lat) && !empty($get_Location->lang)){

		$get_User = Services::selectRaw("services.*, (SELECT 111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(services.latitude)) * COS(RADIANS(".$get_Location->lat.")) * COS(RADIANS(services.longitude - ".$get_Location->lang.")) + SIN(RADIANS(services.latitude)) * SIN(RADIANS(".$get_Location->lat.")))))) * 0.621371 as distance")
								->where(DB::raw("(111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(services.latitude)) * COS(RADIANS(".$get_Location->lat.")) * COS(RADIANS(services.longitude - ".$get_Location->lang.")) + SIN(RADIANS(services.latitude)) * SIN(RADIANS(".$get_Location->lat."))))) * 0.621371)"), "<=", 5)->where('categories_id', '=', $request->categories_id)->get();
									
	
					
			foreach($get_User as $value){
				$data['id']				      = $value->id;
				$data['store_name']		      = !empty($value->store_name) ? $value->store_name : '';
				$data['services_description'] = !empty($value->services_description) ? $value->services_description : '';
				$data['services_image']       = !empty($value->services_image) ? $value->services_image : '';
				$data['latitude']             = !empty($value->latitude) ? $value->latitude : '';
				$data['longitude']            = !empty($value->longitude) ? $value->longitude : '';
		    	$data['flag'] = $value->flag;
				$result[] = $data;
			}
			$json['status'] = 1;
			$json['message'] = 'All services loaded successfully.';
			$json['result'] = $result;

			}else{
				$json['status'] = 0;
		 		$json['message'] = 'Record not found.';
			}
		
		}
		}else{
		    $json['status'] = 0;
		 	$json['message'] = 'User ID not found.';
	}
	

		   echo json_encode($json);
		
		
	}



	public function app_user_lat_lang_update(Request $request){
		  if(!empty($request->lat) && !empty($request->lang) && !empty($request->user_id)){ 
		$update_record = User::find($request->user_id);
		//dd($update_record);
			if(!empty($update_record)){

		    	$update_record->lat    = trim($request->lat);
				$update_record->lang    = trim($request->lang);
				$update_record->save();

				//return $this->sendResponse($update_record, 'Profile updated successfully.');

				$json['status'] = 1;
				$json['message'] = 'Record updated successfully.';
				$json['result'] = $this->getProfileUserCommon($update_record->id);

			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid User.';
			}
		}else{
				$json['status'] = 0;
				$json['message'] = 'Parameter missing!';
			}
			echo json_encode($json);
	}

	public function app_near_by_store_search_list(Request $request){
	
		$getRecordCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getRecordCount)){
		$getRecordUser = User::where('id', '=', $request->user_id)->orderBy('id', 'desc')->get();

		$result = array();
  		foreach ($getRecordUser as $get_Location) {

  		if(!empty($get_Location->lat) && !empty($get_Location->lang)){

		$get_User = Services::selectRaw("services.*, (SELECT 111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(services.latitude)) * COS(RADIANS(".$get_Location->lat.")) * COS(RADIANS(services.longitude - ".$get_Location->lang.")) + SIN(RADIANS(services.latitude)) * SIN(RADIANS(".$get_Location->lat.")))))) * 0.621371 as distance")
								->where(DB::raw("(111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(services.latitude)) * COS(RADIANS(".$get_Location->lat.")) * COS(RADIANS(services.longitude - ".$get_Location->lang.")) + SIN(RADIANS(services.latitude)) * SIN(RADIANS(".$get_Location->lat."))))) * 0.621371)"), "<=", 5)->get();
									
	    
					
			foreach($get_User as $value){
				$data['id']				      = $value->id;
				$data['store_name']		      = !empty($value->store_name) ? $value->store_name : '';
				$data['services_description'] = !empty($value->services_description) ? $value->services_description : '';
				$data['services_image']       = !empty($value->services_image) ? $value->services_image : '';
				$data['latitude']             = !empty($value->latitude) ? $value->latitude : '';
				$data['longitude']            = !empty($value->longitude) ? $value->longitude : '';
		    	$data['services_flag_status'] = $value->flag;
				$result[] = $data;
			}
			$json['status'] = 1;
			$json['message'] = 'All services loaded successfully.';
			$json['result'] = $result;

			}else{
				$json['status'] = 0;
		 		$json['message'] = 'Record not found.';
			}
		
		}
		}else{
			$json['status'] = 0;
			$json['message'] = 'User ID not found.';
		}
	

		   echo json_encode($json);		
			
	}

	public function app_near_by_store_search_list_old(Request $request){
 		if(!empty($request->lat) && !empty($request->lang)){ 

		// $checkLang = User::where('lat', '=', $request->lang)->count();
		
		// $checkLang = User::where('lang', '=', $request->lang)->count();
		// if($checkLang == '1' && $checkLang == '1'){

		$get_recoard = User::where('lat', '=', $request->lat)->where('lang', '=', $request->lang)->orderBy('id', 'desc')->get();
			//dd($get_recoard);

		    foreach ($get_recoard as $get_Location) {

			  	// dd($get_Location->lang);
				$get_User = Services::selectRaw("services.*, (SELECT 111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(services.latitude)) * COS(RADIANS(".$get_Location->lat.")) * COS(RADIANS(services.longitude - ".$get_Location->lang.")) + SIN(RADIANS(services.latitude)) * SIN(RADIANS(".$get_Location->lat.")))))) * 0.621371 as distance")
								->where(DB::raw("(111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(services.latitude)) * COS(RADIANS(".$get_Location->lat.")) * COS(RADIANS(services.longitude - ".$get_Location->lang.")) + SIN(RADIANS(services.latitude)) * SIN(RADIANS(".$get_Location->lat."))))) * 0.621371)"), "<=", 5)->get();
									//dd($get_User);
			    }
		
	  //dd($get_User);
		$result = array();
			foreach ($get_User as $value) {
				$data['id']				      = $value->id;
				$data['store_name']		      = !empty($value->store_name) ? $value->store_name : '';
				$data['services_description'] = !empty($value->services_description) ? $value->services_description : '';
				$data['services_image'] = !empty($value->services_image) ? $value->services_image : '';
				$data['latitude']       = !empty($value->latitude) ? $value->latitude : '';
				$data['longitude']      = !empty($value->longitude) ? $value->longitude : '';
		    	$data['flag'] = $value->flag;
				$result[] = $data;
			}
			$json['status'] = 1;
			$json['message'] = 'All services loaded successfully.';
			$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!.';
		}

		// }else {
		// 		$json['status'] = 0;
		// 		$json['message'] = 'Not valid.';
		// 	}
		echo json_encode($json);
		

	}


// Services End
// Products Start
	public function app_products_list(Request $request){
		
		// services_id
		// dd($request->category_id);

		$result = array();
		$getRecord = Product::where('services_id', '=', $request->services_id)->get();
		// dd($getRecord);
		foreach ($getRecord as $value){
			$data['id']        = $value->id;
			$data['name']	   = !empty($value->name) ? $value->name : '';
			$data['price']     = !empty($value->price)? $value->price : '0';
			$data['discount_price'] = !empty($value->discount_price) ? $value->discount_price : '0';
			$data['description']    = !empty($value->description) ? $value->description : '';
			$data['capacity']       = !empty($value->capacity) ? $value->capacity : '0';
			$data['package_items_count'] = !empty($value->package_items_count) ? $value->package_items_count : '0';
			$data['unit']        = !empty($value->unit) ? $value->unit : '';
			$data['featured']    = !empty($value->featured) ? $value->featured : '0';
			$data['deliverable'] = !empty($value->deliverable) ? $value->deliverable : '0';
			
			$data['old_price']       = !empty($value->old_price)? $value->old_price : '0';
			$data['products_image']  = !empty($value->products_image)? $value->products_image : '';
			$data['delivery_charge'] = $value->delivery_charge;
			$data['offer_available'] = $value->offer_available;
			$data['product_active']  = $value->product_active;
			$data['add_to_cart']     = $value->add_to_cart;
			$data['products_type']   = !empty($value->products_type)? $value->products_type : '';
			$data['product_tax']     = !empty($value->product_tax)? $value->product_tax : '';
			$data['cart_count']      = !empty($value->cart_count)? $value->cart_count : '';

			
			$result[] = $data;
		}
		$json['status'] = 1;
		$json['message'] = 'All Product loaded successfully';
		$json['result'] = $result;
		echo json_encode($json);
	}

	public function app_products_list_old(Request $request){
		// category_id
		// services_id
		// dd($request->category_id);

		$result = array();
		$getRecord = Product::where('category_id', '=', $request->category_id)->where('services_id', '=', $request->services_id)->get();
		// dd($getRecord);
		foreach ($getRecord as $value){
			$data['id']        = $value->id;
			$data['name']	   = !empty($value->name) ? $value->name : '';
			$data['price']     = !empty($value->price)? $value->price : '0';
			$data['discount_price'] = !empty($value->discount_price) ? $value->discount_price : '0';
			$data['description']    = !empty($value->description) ? $value->description : '';
			$data['capacity']       = !empty($value->capacity) ? $value->capacity : '0';
			$data['package_items_count'] = !empty($value->package_items_count) ? $value->package_items_count : '0';
			$data['unit']        = !empty($value->unit) ? $value->unit : '';
			$data['featured']    = !empty($value->featured) ? $value->featured : '0';
			$data['deliverable'] = !empty($value->deliverable) ? $value->deliverable : '0';
			
			$data['old_price']       = !empty($value->old_price)? $value->old_price : '0';
			$data['products_image']  = !empty($value->products_image)? $value->products_image : '';
			$data['delivery_charge'] = $value->delivery_charge;
			$data['offer_available'] = $value->offer_available;
			$data['product_active']  = $value->product_active;
			$data['add_to_cart']     = $value->add_to_cart;
			$data['products_type']   = !empty($value->products_type)? $value->products_type : '';
			$data['product_tax']     = !empty($value->product_tax)? $value->product_tax : '';
			$data['cart_count']      = !empty($value->cart_count)? $value->cart_count : '';

			
			$result[] = $data;
		}
		$json['status'] = 1;
		$json['message'] = 'All Product loaded successfully';
		$json['result'] = $result;
		echo json_encode($json);
	}
// Products End
// Add To Cart Start
	public function app_add_to_cart(Request $request){

		$getCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getCount)){
			$getRecord =  Product::where('id', '=', $request->product_id)->first();


			// dd($getRecord);
			
			 // $GetResult = Cart::where('product_id', '=', $request->product_id)->where('user_id', '=', $request->user_id)->first();
			 // if(empty($GetResult)){
	
	 		$record_insert = new Cart;
	 
	 		if(!empty($record_insert)){
		 		$record_insert->user_id    = trim($request->user_id);
		 		$record_insert->product_id = trim($request->product_id);
		 		$record_insert->quantity   = trim($request->quantity);
		 		
		 		$AddTotalPrice = $getRecord->price * $request->quantity;
		 		$record_insert->total_price   = $AddTotalPrice;
		 		
		 		$record_insert->save();

		 		$json['status']  = 1;
		 		$json['message'] = 'Add to cart successfully.';

		 		$json['result'] = $this->getAddToCartList($record_insert->id);

		 	}else{
		 		$json['status'] = 0;
				$json['message'] = 'Record not found!';
		 	}
		 	// }else{
		 	// 	$json['status'] = 0;
				// $json['message'] = 'Product already available!';
		 	// }

	 	} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Invalid User.';
		}

		echo json_encode($json);
	}
// Add To Cart End

	public function getAddToCartList($id){
		$user 						= Cart::find($id);
		$json['id'] 			    = $user->id;
		$json['user_id'] 			= !empty($user->user_id) ? $user->user_id : '';
		$json['user_name']         = !empty($user->user->name) ? $user->user->name : '';
		$json['product_id'] 		= !empty($user->product_id) ? $user->product_id : '';
		$json['product_price']      = !empty($user->product->price) ? $user->product->price : '0';
		$json['product_discount_price'] = !empty($user->product->discount_price) ? $user->product->discount_price : '0';
		$json['product_description'] = !empty($user->product->description) ? $user->product->description : '';
		$json['product_capacity']        = !empty($user->product->capacity) ? $user->product->capacity : '';
		$json['product_package_items_count']  = !empty($user->product->package_items_count) ? $user->product->package_items_count : '';
		$json['product_unit']        = !empty($user->product->unit) ? $user->product->unit : '';
		$json['product_featured']    = !empty($user->product->featured) ? $user->product->featured : '';
		$json['product_deliverable'] = !empty($user->product->deliverable) ? $user->product->deliverable : '';

		$json['product_old_price'] = !empty($user->product->old_price) ? $user->product->old_price : '0';
		$json['product_products_image'] = !empty($user->product->products_image) ? $user->product->products_image : '';
		$json['delivery_charge'] = $user->product->delivery_charge;
		$json['offer_available'] = $user->product->offer_available;
		$json['product_active']  = $user->product->product_active;
		$json['add_to_cart']     = $user->product->add_to_cart;
		$json['products_type']   = !empty($user->product->products_type)? $user->product->products_type : '';
		$json['product_tax']     = !empty($user->product->product_tax)? $user->product->product_tax : '';
		$json['cart_count']      = !empty($user->product->cart_count)? $user->product->cart_count : '';

		$json['quantity'] 		     = !empty($user->quantity) ? $user->quantity : '';
		$json['total_price'] 		     = $user->total_price;
		return $json;
	}


	public function app_checkout_cart_list(Request $request){
		   $getresultCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getresultCount)){

			 $GetResult = Cart::where('product_id', '=', $request->product_id)->where('user_id', '=', $request->user_id)->first();
			 if(empty($GetResult)){

		$result = array();
	    
		$getresult = Cart::where('user_id', '=', $request->user_id)->where('status', '=', 1)->get();
		foreach ($getresult as $value) {
			$data['id']				   = $value->id;
			$data['product_id']        = $value->product_id;
			$data['product_name']      = !empty($value->product->name) ? $value->product->name : '';
			$data['product_price']     = !empty($value->product->price) ? $value->product->price : '';
			$data['total_price']       = $value->total_price;
			
			$data['product_discount_price']  = !empty($value->product->discount_price) ? $value->product->discount_price : '';
			$data['product_description']     = !empty($value->product->description) ? $value->product->description : '';
			$data['product_capacity']        = !empty($value->product->capacity) ? $value->product->capacity : '';
			$data['product_package_items_count']  = !empty($value->product->package_items_count) ? $value->product->package_items_count : '';
			$data['product_unit']        = !empty($value->product->unit) ? $value->product->unit : '';
			$data['product_featured']    = !empty($value->product->featured) ? $value->product->featured : '';
			$data['product_deliverable'] = !empty($value->product->deliverable) ? $value->product->deliverable : '';

			$data['product_old_price'] = !empty($value->product->old_price) ? $value->product->old_price : '0';
			$data['product_products_image'] = !empty($value->product->products_image) ? $value->product->products_image : '';
			$data['delivery_charge'] = $value->product->delivery_charge;
			$data['offer_available'] = $value->product->offer_available;
			$data['product_active']  = $value->product->product_active;
			$data['add_to_cart']     = $value->product->add_to_cart;
			$data['products_type']   = !empty($value->product->products_type)? $value->product->products_type : '';
			$data['product_tax']     = !empty($value->product->product_tax)? $value->product->product_tax : '';
			$data['cart_count']      = !empty($value->product->cart_count)? $value->product->cart_count : '';


			$data['user_id']           = $value->user_id;
			$data['user_name']         = !empty($value->user->name) ? $value->user->name : '';
			$data['quantity']          = $value->quantity;

			$result[] = $data;
		}

		}else{
		 		$json['status'] = 0;
				$json['message'] = 'Product already available!';
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
   
    public function app_checkout_cart_empty(Request $request){
		$getresultCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getresultCount)){
    	Cart::where('user_id', $request->user_id)
            ->update(['status' => 0]);
			$json['status'] = 1;
		 	$json['message'] = 'Cart Empty Successfully.';

		}
		else
		{	
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}

	 echo json_encode($json);
	}



	public function app_checkout_cart_delete_single_item(Request $request){
		$record_delete = Cart::find($request->id);
			if(!empty($record_delete)){
			 	$record_delete->delete();
			 	$json['status'] = 1;
			 	$json['message'] = 'Cart Deleted Successfully.';
			}else{
			 	$json['status'] = 0;
			 	$json['message'] = 'Record not found.';
			} 
		echo json_encode($json);
	}
// Update Check out start
	public function app_update_check_out_cart(Request $request){

		if(!empty($request->id)){ 
		$update_record = Cart::find($request->id);
		//dd($update_record);
		$getRecord = Product::where('id', '=', $request->product_id)->first();
		//dd($getRecord->price);
			if(!empty($update_record)){
				$update_record->product_id   = trim($request->product_id);
				$update_record->quantity     = trim($request->quantity);

				$AddTotalPrice = $getRecord->price * $request->quantity;
				$update_record->total_price = $AddTotalPrice;

				$update_record->save();
				
				$json['status'] = 1;
				$json['message'] = 'Cart updated successfully.';
				//return $this->sendResponse($update_record, 'Profile updated successfully.');
				$json['update_result'] = $this->getAddToCartList($update_record->id);

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

// Update Check out end
	
// Home Page Silder Start
	public function app_home_slider_list(Request $request){
		$result = array();
		$getRecord = Slider::orderBy('id', 'desc')->get();
		//dd($getRecord);
		foreach ($getRecord as $key => $value) {

			$data['id'] = $value->id;
			$data['slider_image'] = !empty($value->slider_image) ? $value->slider_image : '';
			$data['slider_type']  = $value->slider_type;
			$data['slider_title'] = !empty($value->slider_title) ? $value->slider_title : '';
			$data['slider_description'] = !empty($value->slider_description) ? $value->slider_description : '';
			$data['slider_offer']       = !empty($value->slider_offer) ? $value->slider_offer : '0';

			$result[]  = $data;
		}
		$json['status']  = 1;
		$json['message'] = 'All data loaded successfully.';
		$json['result']  = $result; 

		echo json_encode($json);
	}

// Home Page Silder End

	public function app_social_login(Request $request)
	{

		if(!empty($request->social_id)){
		
		$statucheckmobile = User::where('social_id', '=', $request->social_id)->first();

		$checkSocialid = User::where('social_id', '=', $request->social_id)->count();
		
		//$checkSocmobileid = User::where('mobile', '=', $request->mobile)->count();

		if($checkSocialid == '0'){
			
			$record = new User;
			$record->email   = trim($request->email);

			if (!empty($request->file('user_profile'))) {
				$ext = 'jpg';
				$file = $request->file('user_profile');
				$randomStr = str_random(30);
				$filename = strtolower($randomStr) . '.' . $ext;
				$file->move('images/', $filename);
				$record->user_profile = $filename;
			}
			$record->token          = !empty($request->token)?$request->token:null;
			//$record->mobile         = trim($request->mobile);

			$record->mobile         = !empty($request->mobile) ? $request->mobile : '';			
			$record->name           = trim($request->name);				
			$record->social_type    = trim($request->social_type);
			$record->social_id      = trim($request->social_id);
			$record->save();
		
		    //$this->updateToken($record->id);
		
			$json['status'] = 1;
			$json['message'] = 'Account Successfully created.';
			$json['result'] = $this->getProfileUserCommon($record->id);
		}
		else if (!empty($statucheckmobile)){
			$json['status'] = 0;
			$json['message'] = 'Your social id  exist please login or try again.';
			$json['result'] = $this->getProfileUserCommon($statucheckmobile->id);
		}
		else {
			$json['status'] = 0;
			$json['message'] = 'Your social id already exist please login or try again.';
		}

		}
		else {
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		echo json_encode($json);
	}

// Update Password Start
	public function app_update_password(Request $request){
		$user = User::where('id', '=', $request->user_id)->first();
		if(trim($request->new_password) == trim($request->confirm_password)){
			if(!empty($user)){
				$user->password = Hash::make($request->new_password);
				$user->save();
				$json['status'] = 1;
				$json['message'] = 'Password successfully updated.';
			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid User.';
			}
		}else{
			$json['status'] = 0;
			$json['message'] = 'Confirm password does not updated.';
		}
		echo json_encode($json);
	}

// Update Password End

// Driver Document Start 
	public function app_driver_document_upload(Request $request){
	
	   if(!empty($request->user_id)){ 
		$update_record = User::find($request->user_id);
		//dd($update_record);
			if(!empty($update_record)){

			if (!empty($request->file('document_photo'))) {

				if (!empty($update_record->document_photo) && file_exists('images/' . '/' . $update_record->document_photo)) {
					unlink('images/' . $update_record->document_photo);
				}
				$ext = 'jpg';
				$file = $request->file('document_photo');

				$randomStr = str_random(30);
				
				$filename = strtolower($randomStr) . '.' . $ext;

				$file->move('images/', $filename);
		    	//$path = "http://localhost/laravel/bookfast/upload/profile/".$filename;
		 		$update_record->document_photo = $filename;
		 		//dd($update_record->document_photo);
				}
				// else
				// {
				// 		$update_record->document_photo = '';
				// }


			
				$update_record->save();

				//return $this->sendResponse($update_record, 'Profile updated successfully.');

				$json['status'] = 1;
				$json['message'] = 'Document updated successfully.';
				$json['result'] = $this->getProfileUserCommon($update_record->id);

			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid User.';
			}
		}else{
				$json['status'] = 0;
				$json['message'] = 'Parameter missing!';
			}
			echo json_encode($json);
	}	
// Driver Document End

// Help Faqs Start
	public function app_help_faqs_list(Request $request){
		$getresult = HelpFaqs::orderBy('id', 'desc')->get();
		// dd($getresult);
		foreach ($getresult as $key => $value) {
			$data['id']              = $value->id;
			$data['title_one']       = !empty($value->title_one) ? $value->title_one : '';
			$data['description_one'] = !empty($value->description_one) ? $value->description_one : '';
			$result[] = $data;
		}
		$json['status']  = 1;
		$json['message'] = 'All data loaded successfully.';
		$json['result'] = $result;

		echo json_encode($json);
	}
// Help Faqs End

// Search Store Start
	public function app_search_store_update(Request $request){
		if(!empty($request->user_id)){ 
		$update_record = User::find($request->user_id);
		// dd($update_record);
			if(!empty($update_record)){
				$update_record->lat    = trim($request->lat);
				$update_record->lang   = trim($request->lang);
				$update_record->save();

				$json['status'] = 1;
				$json['message'] = 'Location updated successfully.';
				$json['result'] = $this->getProfileUserCommon($update_record->id);
			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid User.';
			}
		}else{
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		echo json_encode($json);
	}
// Search Store End
	
// Add Address Start

	public function app_address_add(Request $request){
		$getUserCount = User::where('id', '=', $request->user_id)->count();
		
		if(!empty($getUserCount)){
			$record_insert = new UsersAddress;
			$record_insert->user_id = trim($request->user_id);
			$record_insert->latitude = trim($request->latitude);
			$record_insert->longitude = trim($request->longitude);
			$record_insert->full_address = trim($request->full_address);
			$record_insert->save();

			$json['status']  = 1;
			$json['message'] = 'Address successfully';
			$json['result']  = $this->getUsersAddress($record_insert->id);
		}else{
			$json['status']  = 0;
			$json['message'] = 'Invalid User Id';
		}
		echo json_encode($json);
 	}

// Add Address End

 	public function getUsersAddress($id){
 		$user = UsersAddress::find($id);
 		$json['id'] = $user->id;
 		$json['user_id'] = $user->user_id;
 		$json['latitude'] = $user->latitude;
 		$json['longitude'] = $user->longitude;
 		$json['full_address'] = $user->full_address;
 		$json['is_default'] = $user->is_default;
 		return $json;
 	}

 	// Rating Add
 	public function app_rating_add(Request $request){
 		$getUserCount = User::where('id', '=', $request->user_id)->count();
 		$getDriverCount = User::where('id', '=', $request->driver_id)->count();

 		if(!empty($getUserCount) && !empty($getDriverCount)){

	 		$record_insert = new Rating;
	 		$record_insert->user_id            = trim($request->user_id);
	 		$record_insert->driver_id          = trim($request->driver_id);
	 		$record_insert->rating_title       = !empty($request->rating_title) ? $request->rating_title: '';
	 		$record_insert->rating_description = !empty($request->rating_description) ? $request->rating_description: '';
	 		$record_insert->rating_avg         = trim($request->rating_avg);
	 		$record_insert->save();

	 		$json['status']  = 1;
	 		$json['message'] = 'Rating updated successfully.';
	 		$json['result'] = $this->getRating($record_insert->id);
		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid Id';
		}
 		echo json_encode($json);

 	}
 	// Rating End

 	public function getRating($id){
 		$user = Rating::find($id);
 		$json['id']      = $user->id;
 		$json['user_id'] = $user->user_id;
 		$json['driver_id'] = $user->driver_id;
 		$json['rating_title'] = $user->rating_title;
 		$json['rating_description'] = $user->rating_description;
 		$json['rating_avg'] = $user->rating_avg;
 		return $json;
 	}

 	// Address List
 	public function app_address_list(Request $request){
		$getCount = User::where('id', '=', $request->user_id)->count();
			if(!empty($getCount)){
			$result = array();
 		$getRecord = UsersAddress::where('user_id', '=', $request->user_id)->orderBy('id', 'desc')->get();

 		foreach ($getRecord as $key => $value) {
 			$data['id'] = $value->id;
 			$data['user_id'] = $value->user_id;
 			$data['latitude'] = $value->latitude;
 			$data['longitude'] = $value->longitude;
 			$data['full_address'] = $value->full_address;
 			$data['is_default'] = $value->is_default;
 			$result[] = $data;


 		}
	 		$json['status'] = 1;
	 		$json['message'] = 'All data loaded successfully.';
	 		$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
 		echo json_encode($json);
 	}
// Rating
 	public function app_rating_user_list(Request $request){
 		$getCount = User::where('id', '=', $request->user_id)->count();
			if(!empty($getCount)){
			$result = array();
 		$getRecord = Rating::where('user_id', '=', $request->user_id)->orderBy('id', 'desc')->get();

 		foreach ($getRecord as $key => $value) {
 			$data['id'] = $value->id;
 			$data['user_id'] = $value->user_id;
 			$data['driver_id'] = $value->driver_id;
 			$data['driver_name'] = !empty($value->get_driver_name->name) ? $value->get_driver_name->name : '';
 			$data['driver_image'] = !empty($value->get_driver_name->user_profile) ? $value->get_driver_name->user_profile : '';
 			$data['rating_title'] = $value->rating_title;
 			$data['rating_description'] = $value->rating_description;
 			$data['rating_avg'] = $value->rating_avg;
 			$result[] = $data;

 
 		}
	 		$json['status'] = 1;
	 		$json['message'] = 'All data loaded successfully.';
	 		$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
 		echo json_encode($json);
 	}

	public function app_rating_driver_list(Request $request)
	{
		$getCount = User::where('id', '=', $request->driver_id)->count();
			if(!empty($getCount)){
			$result = array();
 		$getRecord = Rating::where('driver_id', '=', $request->driver_id)->orderBy('id', 'desc')->get();

 		foreach ($getRecord as $key => $value) {
 			$data['id'] = $value->id;
 			$data['user_id'] = $value->user_id;
 			$data['driver_id'] = $value->driver_id;
 			$data['user_image'] = !empty($value->get_user_name->user_profile) ? $value->get_user_name->user_profile : '';
 			$data['user_name'] = !empty($value->get_user_name->name) ? $value->get_user_name->name : '';
 			$data['rating_title'] = $value->rating_title;
 			$data['rating_description'] = $value->rating_description;
 			$data['rating_avg'] = $value->rating_avg;
 			 // $data['created_at'] = date('d-m-Y h:i A', strtotime($value->created_at));
 			 // $data['updated_at'] = date('d-m-Y h:i A', strtotime($value->updated_at));
 			 $data['created_at'] = date('Y-m-d h:i:s', strtotime($value->created_at));
 			 $data['updated_at'] = date('Y-m-d h:i:s', strtotime($value->updated_at));
 			//$data['created_at'] = $value->created_at;
 			//$data['updated_at'] = $value->updated_at;
 			$result[] = $data;
 		}   
	 		$json['status'] = 1;
	 		$json['message'] = 'All data loaded successfully.';
	 		$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
 		echo json_encode($json);
	}
	
	// Notification Store list

	public function notification_store_user_list(Request $request){
		$getCount = User::where('id', '=', $request->user_id)->count();
			if(!empty($getCount)){
		$getRecord = NotificationStore::where('user_id', '=', $request->user_id)->orderBy('id', 'desc')->get();

		$result = array();
		foreach ($getRecord as $key => $value) {
			$data['id']              = $value->id;
			$data['user_id']         = $value->user_id;
			$data['title']           = $value->title;
			$data['message']         = $value->message;
			$data['order_date_time'] = $value->order_date_time;
		 // $data['order_date_time'] = date('d-m-Y h:i A', strtotime($value->order_date_time));
			$result[] = $data;
		}
			$json['status'] = 1;
	 		$json['message'] = 'All data loaded successfully.';
	 		$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	}

	public function app_services_search_list(Request $request){
			$result = array();
        $users = Services::where('categories_id', '=', $request->categories_id)->orderBy('id', 'desc');
        
        if(!empty($request->categories_id)){
           $users = $users->where('services.categories_id', 'like', '%' . $request->categories_id . '%');
        }

        if(!empty($request->store_name)){
           $users = $users->where('services.store_name', 'like', '%' . $request->store_name . '%');
        }
        $users = $users->paginate(80);
     
        foreach ($users as $value) {

            $data['id']				      = $value->id;
			$data['store_name']		      = !empty($value->store_name) ? $value->store_name : '';
			$data['services_description'] = !empty($value->services_description) ? $value->services_description : '';
			$data['services_image'] = !empty($value->services_image) ? $value->services_image : '';
			$data['latitude']       = !empty($value->latitude) ? $value->latitude : '';
			$data['longitude']      = !empty($value->longitude) ? $value->longitude : '';
	    	$data['flag'] = $value->flag;

            $result[] = $data;
        }
        $json['success'] = 1;
        $json['message'] = 'All loaded successfully.';
        $json['result'] = $result;
        
        echo json_encode($json);
	}

	public function app_address_edit(Request $request){
		
		if (!empty($request->full_address) && !empty($request->id)) {
		$update_record = UsersAddress::find($request->id);

		if(!empty($update_record)){
			$update_record->latitude = trim($request->latitude);
			$update_record->longitude = trim($request->longitude);
			$update_record->full_address = trim($request->full_address);
			$update_record->save();
			
			$json['status'] = 1;
			$json['message'] = 'Address updated successfully.';

			$json['result']  = $this->getUsersAddress($update_record->id);
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

	public function app_payment_card_detail_add(Request $request)
 	{
 		$userRecordCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($userRecordCount)){
		$addrecord                   = new PaymentCardDetail;
		$addrecord->user_id          = trim($request->user_id);
		$addrecord->card_id          = trim($request->card_id);
		$addrecord->card_number	     = trim($request->card_number);
		$addrecord->card_holder	     = trim($request->card_holder);
		$addrecord->card_expiry_date = trim($request->card_expiry_date);
		$addrecord->card_cvv_number  = trim($request->card_cvv_number);
		$addrecord->save();

		$json['status']  = 1;
		$json['message'] = 'Record created successfully.';

		$json['result'] = $this->getPaymentCardDetail($addrecord->id);
		}else{
			$json['status'] = 0;
				$json['message'] = 'User Invalid.';
			
		}
		echo json_encode($json);

 	}

 	public function getPaymentCardDetail($id){
 		$getRecord 				        = PaymentCardDetail::find($id);
		$json['id']    		            = $getRecord->id;
		$json['user_id']    		    = $getRecord->user_id;
		$json['card_id']			    = !empty($getRecord->card_id) ? $getRecord->card_id : '';
		$json['card_number']			= !empty($getRecord->card_number) ? $getRecord->card_number : '';
		$json['card_holder']			= !empty($getRecord->card_holder) ? $getRecord->card_holder : '';
		$json['card_expiry_date']		= !empty($getRecord->card_expiry_date) ? $getRecord->card_expiry_date : '';
		$json['card_cvv_number']		= !empty($getRecord->card_cvv_number) ? $getRecord->card_cvv_number : '';
		return $json;	
 	}

 	public function app_payment_card_detail_list(Request $request){
 		
 		$recodeCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($recodeCount)){
		$result = array();
 		$getRecord = PaymentCardDetail::where('user_id', '=', $request->user_id)->get();
 		foreach ($getRecord as $value) {
 			$data['id']                 = $value->id;
			$data['user_id'] 	        = !empty($value->user_id) ? $value->user_id : '';
			$data['card_id']			= !empty($value->card_id) ? $value->card_id : '';
			$data['card_number']		= !empty($value->card_number) ? $value->card_number : '';
			$data['card_holder']		= !empty($value->card_holder) ? $value->card_holder : '';
			$data['card_expiry_date']	= !empty($value->card_expiry_date) ? $value->card_expiry_date : '';
			$data['card_cvv_number']    = !empty($value->card_cvv_number) ? $value->card_cvv_number : ''; 
			$result[] = $data; 

 		}
 			$json['status']  = 1;
			$json['message'] = 'All Payment card detail loaded successfully.';
			$json['result'] = $result;
		} else{
			$json['status'] = 0;
			$json['message'] = 'User Invalid.';
		}
	
		echo json_encode($json);

 	}

 	public function app_services_update_flag(Request $request){
 		if(!empty($request->id)){ 
		$update_record = Services::find($request->id);
		//dd($update_record);
			if(!empty($update_record)){

				$update_record->flag    = $request->flag;
				$update_record->save();

				$json['status'] = 1;
				$json['message'] = 'Flag updated successfully.';
				$json['result'] = $this->getServicesCommon($update_record->id);

			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid ID.';
			}
		}else{
				$json['status'] = 0;
				$json['message'] = 'Parameter missing!';
			}
			echo json_encode($json);
 	}

 	public function getServicesCommon($id){
 		$getRecord 			    = Services::find($id);
 		
		$json['id']    		    = $getRecord->id;
	
		$json['store_name']		= !empty($getRecord->store_name) ? $getRecord->store_name : '';
			
		$json['services_description'] = !empty($getRecord->services_description) ? $getRecord->services_description : '';
		 // dd($json['services_description']);
		$json['services_image']	= !empty($getRecord->services_image) ? $getRecord->services_image : '';
		$json['latitude']		= !empty($getRecord->latitude) ? $getRecord->latitude : '';
		$json['longitude']		= !empty($getRecord->longitude) ? $getRecord->longitude : '';
		$json['flag']    		= $getRecord->flag;
		return $json;	
 	}

 	public function app_online_offline_status(Request $request){
		if (!empty($request->user_id)) {
		 $update_record = User::find($request->user_id);
			if(!empty($update_record)){
		
				$update_record->online_offline_status = trim($request->online_offline_status);
				$update_record->save();

				$json['status'] = 1;
				$json['message'] = 'Status updated successfully.';

				$json['result'] = $this->getProfileUserCommon($update_record->id);
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

	public function app_add_money_wallet(Request $request){
	    
	  if (!empty($request->total_balance) && !empty($request->user_id)) {
		$update_record = User::find($request->user_id);
		if(!empty($update_record)){
	       
	        if($request->money_type == 0){	
    	    	$AddAmount = $update_record->total_balance + $request->total_balance;
    			$update_record->total_balance = $AddAmount;
    	        $update_record->save();   
    	    $json['status'] = 1;
    		$json['message'] = 'Add Money wallet successfully.';
    	        
	        }
		 
    		if($update_record->total_balance >= $request->total_balance){
        		if($request->money_type == 1){
        		    $LessAmount = $update_record->total_balance - $request->total_balance;
        			$update_record->total_balance = $LessAmount;
        	        // $update_record->total_balance = $request->total_balance;
        	        $update_record->save();
        	        $json['status']  = 1;
        	    	$json['message'] = 'Withdraw Money wallet successfully.';
        		}
    		
    	    $json['result'] = $this->getProfileUserCommon($update_record->id);

    	    $user_insert                   = new UsersWalletDetails;
            $user_insert->user_id          = $request->user_id;
            $user_insert->amount_transfer  = $request->total_balance;
            $user_insert->money_type       = $request->money_type;
            $user_insert->money_date       = $request->money_date;
            $user_insert->money_status     = 0;
            $user_insert->save();
 
            }
    		else{
    		    $json['status'] = 0;
    	       	$json['message'] = 'No balance bank account.';
    		}
    		

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
	

	public function app_wallet_details_list(Request $request){
	  $getresultCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getresultCount)){
		$result = array();
	    
	    $getresult = UsersWalletDetails::where('user_id', '=', $request->user_id)->orderBy('id', 'DESC')->get();
	    $getTotalBalance = User::where('id', '=', $request->user_id)->first();
	   
		foreach ($getresult as $value) {
			$data['id']				    = $value->id;
			$data['user_id']		    = $value->user_id;
			$data['amount_transfer']	= $value->amount_transfer;
			$data['money_type']		    = $value->money_type;
			$data['money_date']		    = $value->money_date;
			$data['money_status']		= $value->money_status;
		//	$data['unite_name']        = !empty($value->unite_name) ? $value->unite_name : '';
			$result[] = $data;
		}
		  	
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['wallet_amount'] = $getTotalBalance->total_balance;
	     	$json['result']  = $result;
		}
		else
		{	
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}

		echo json_encode($json);
	    
	}

	public function app_bank_detail_add(Request $request){
	    if (!empty($request->bank_holder_name) && !empty($request->user_id)) {
		$update_record = User::find($request->user_id);
		if(!empty($update_record)){
		    $update_record->bank_holder_name   = trim($request->bank_holder_name);
			$update_record->account_number     = !empty($request->account_number) ? $request->account_number : '';
			$update_record->ifsc_code          = !empty($request->ifsc_code) ? $request->ifsc_code : '';
			$update_record->branch_code        = !empty($request->branch_code) ? $request->branch_code : '';
			$update_record->bank_name          = !empty($request->bank_name) ? $request->bank_name : '';

			$update_record->save();

			$json['status'] = 1;
			$json['message'] = 'Bank detail updated successfully.';

			$json['user_data'] = $this->getProfileUserCommon($update_record->id);
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


	public function app_services_flag_user_update_old(Request $request){
		
		$getUser = User::where('id', '=', $request->user_id)->count();
		// dd($getOrder);

		$update_rec = Services::where('id', '=', $request->services_id)->first();

		if(!empty($getUser)){
		 
			$record_insert = new ServicesFlag;
		 
		if(!empty($record_insert) && !empty($update_rec)){

			$record_insert->user_id  		   = trim($request->user_id);
			$record_insert->services_id  	   = trim($request->services_id);
			$record_insert->services_flag_status  	   = $request->services_flag_status;
			$record_insert->save();

		    $json['status'] = 1;
			$json['message'] = 'Flag successfully.';

			$json['result'] = $this->getServicesFlagList($record_insert->id);
			
			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid Services .';
			}
		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Invalid User.';
		}

		echo json_encode($json);
	}

	public function app_services_flag_user_update_oodddd(Request $request){
		$getUserCount = User::where('id', '=', $request->user_id)->count();
		$getServiceCount = Services::where('id', '=', $request->services_id)->count();
		

// dd($getUserCount);
		if(!empty($getUserCount) && !empty($getServiceCount)){
// if(!empty($getServiceCount)){
		$record = ServicesFlag::where('user_id','=',$request->user_id)->first();
			
				if(empty($record)) {
					$record = new ServicesFlag;
				}
				$record->user_id       			= trim($request->user_id);
				$record->services_id            = trim($request->services_id);
				$record->services_flag_status   = $request->services_flag_status;
			
				$record->save();

				$json['status'] = 1;
				$json['message'] = 'Flag update successfully.';

	       		$json['result'] = $this->getServicesFlagList($record->id);
		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Invalid ID.';
		}

	    echo json_encode($json);
	}

 	public function app_services_flag_user_update(Request $request){
		// user_id
		// services_id
		// services_flag_status
		$getUserCount = User::where('id', '=', $request->user_id)->count();
		$getServiceCount = Services::where('id', '=', $request->services_id)->count();
		

// dd($getUserCount);
		if(!empty($getUserCount) && !empty($getServiceCount)){
// if(!empty($getServiceCount)){
		$record = ServicesFlag::where('user_id','=',$request->user_id)->where('services_id','=',$request->services_id)->first();
			//$records = ServicesFlag::where('user_id','=',$request->user_id)->first();
				if(empty($record)) {
					$record = new ServicesFlag;
					$record->user_id       			= trim($request->user_id);
				    $record->services_id            = trim($request->services_id);
			    }	
				
				$record->services_flag_status   = $request->services_flag_status;
			
				$record->save();

				$json['status'] = 1;
				$json['message'] = 'Flag update successfully.';

	       		$json['result'] = $this->getServicesFlagList($record->id);
		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Invalid ID.';
		}

	    echo json_encode($json);
	}


	
	public function app_services_flag_user_delete(Request $request){
		$record_delete = ServicesFlag::find($request->services_flag_id);
			if(!empty($record_delete)){
			 	$record_delete->delete();
			 	$json['status'] = 1;
			 	$json['message'] = 'Services Flag Deleted Successfully.';
			}else{
			 	$json['status'] = 0;
			 	$json['message'] = 'Record not found.';
			} 
		echo json_encode($json);
	}

	public function getServicesFlagList($id){
		$user 				        = ServicesFlag::find($id);
		$json['id']    		        = $user->id;
		$json['user_id']            = $user->user_id;
		$json['services_id']    	    = $user->services_id;
	 //   $json['services_flag_status'] 		    = !empty($user->services_flag_status) ? $user->services_flag_status : '';
		$json['services_flag_status'] 		    =$user->services_flag_status;

		return $json;
	}

	public function app_carts_options_add_old(Request $request){
		// user_id
		// carts_id
		// checkout_total
		$getUserCount = User::where('id', '=', $request->user_id)->count();
		$getCartsCount = Cart::where('id', '=', $request->carts_id)->count();

		if(!empty($getUserCount) && !empty($getCartsCount)){
			$record_insert = new CartsOptionsModel;
			if(!empty($record_insert)){
				$record_insert->user_id  		   = trim($request->user_id);
				$record_insert->carts_id  	       = trim($request->carts_id);
				$record_insert->checkout_total 	   = trim($request->checkout_total);
				
				$record_insert->save();

				$json['status'] = 1;
				$json['message'] = 'Carts Successfully.';
			    $json['result'] = $this->getCartsOptionsListComm($record_insert->id);

			}else{
				$json['status'] = 0;
				$json['message'] = 'Id Incorrect.';
			}

		}else{
			$json['status'] = 0;
			$json['message'] = 'Id Incorrect.';
		}
		echo json_encode($json);

	}

	


	public function getCartsOptionsListComm($id){
		$user = CartsOptionsModel::find($id);
		$json['id']      = $user->id;
		//user start
		$json['user_id']        = $user->user_id;
		$json['user_name']      = !empty($user->get_user->name) ? $user->get_user->name : '';
		
		//user end
		//cart start
	
		 $json['product_id']      = !empty($user->get_carts->product_id) ? $user->get_carts->product_id : '';

		$option_main = array();
		foreach($user->get_user->get_carts as $option)
		{
			$data = array();
			$data['id'] 	        = $option->id;
			
			$data['product_id'] 	= $option->product_id;
			// product start
			$data['product_name']      = !empty($option->get_product_name->name) ? $option->get_product_name->name : '';
			$data['product_price']     = !empty($option->get_product_name->price) ? $option->get_product_name->price : '';
			
			
			$data['product_discount_price']  = !empty($option->get_product_name->discount_price) ? $option->get_product_name->discount_price : '';
			$data['product_description']     = !empty($option->get_product_name->description) ? $option->get_product_name->description : '';
			$data['product_capacity']        = !empty($option->get_product_name->capacity) ? $option->get_product_name->capacity : '';
			$data['product_package_items_count']  = !empty($option->get_product_name->package_items_count) ? $option->get_product_name->package_items_count : '';
			$data['product_unit']        = !empty($option->get_product_name->unit) ? $option->get_product_name->unit : '';
			$data['product_featured']    = !empty($option->get_product_name->featured) ? $option->get_product_name->featured : '';
			$data['product_deliverable'] = !empty($option->get_product_name->deliverable) ? $option->get_product_name->deliverable : '';

			$data['product_old_price'] = !empty($option->get_product_name->old_price) ? $option->get_product_name->old_price : '0';
			$data['product_products_image'] = !empty($option->get_product_name->products_image) ? $option->get_product_name->products_image : '';
			$data['delivery_charge'] = $option->get_product_name->delivery_charge;
			$data['offer_available'] = $option->get_product_name->offer_available;
			$data['product_active']  = $option->get_product_name->product_active;
			$data['add_to_cart']     = $option->get_product_name->add_to_cart;
			$data['products_type']   = !empty($option->get_product_name->products_type)? $option->get_product_name->products_type : '';
			$data['product_tax']     = !empty($option->get_product_name->product_tax)? $option->get_product_name->product_tax : '';
			$data['cart_count']      = !empty($option->get_product_name->cart_count)? $option->get_product_name->cart_count : '';

// product end 

			$data['user_id'] 	    = $option->user_id;
			$data['quantity'] 	    = $option->quantity;
			$data['total_price'] 	= $option->total_price;
			$option_main[] = $data;
		}
 
		$json['cart_details'] 		= $option_main;

		$json['quantity']        = !empty($user->get_carts->quantity) ? $user->get_carts->quantity : '';
		$json['total_price']     = !empty($user->get_carts->total_price) ? $user->get_carts->total_price : '0';
		//cart End
		$json['checkout_total'] = $user->checkout_total;
		return $json;

	}
// order add
	public function app_order_add(Request $request){
		$getUserCount = User::where('id', '=', $request->user_id)->count();
		$getProductCount = Product::where('id', '=', $request->products_id)->count();
		
		if(!empty($getUserCount) && !empty($getProductCount)){
		    $record_insert = new OrderModel;
		    if(!empty($record_insert)){
				$record_insert->user_id  		   = trim($request->user_id);
				$record_insert->products_id  	   = trim($request->products_id);
				$record_insert->orders_name 	   = trim($request->orders_name);
				$record_insert->order_date_time    = trim($request->order_date_time );
				$record_insert->order_status	   = trim($request->order_status);
				$record_insert->save();

				$json['status'] = 1;
				$json['message'] = 'Order Successfully.';
				$json['result'] = $this->getOrderListComm($record_insert->id);
			}else{
				$json['status'] = 0;
				$json['message'] = 'Id Incorrect.';
			}
		}else{
			$json['status'] = 0;
			$json['message'] = 'Id Incorrect.';
		}	
		echo json_encode($json);
 	}

 	public function getOrderListComm($id){
 		$user  = OrderModel::find($id);
	 		$json['id'] 			 = $user->id;
	 		// user start
	 		$json['user_id'] 		 = !empty($user->user_id) ? $user->user_id : '';
	 		$json['user_name'] 		 = !empty($user->get_user->name) ? $user->get_user->name : '';
	 		// user end
	 		// product start
			$json['products_id'] 	 = !empty($user->products_id) ? $user->products_id : '';
			$json['product_name']      = !empty($user->get_product->name) ? $user->get_product->name : '';
			$json['product_price']      = !empty($user->get_product->price) ? $user->get_product->price : '';
			$json['product_discount_price']  = !empty($user->get_product->discount_price) ? $user->get_product->discount_price : '';
			$json['product_description']     = !empty($user->get_product->description) ? $user->get_product->description : '';
			$json['product_capacity']        = !empty($user->get_product->capacity) ? $user->get_product->capacity : '';
			$json['product_package_items_count']  = !empty($user->get_product->package_items_count) ? $user->get_product->package_items_count : '';
			$json['product_unit']        = !empty($user->get_product->unit) ? $user->get_product->unit : '';
			$json['product_featured']    = !empty($user->get_product->featured) ? $user->get_product->featured : '';
			$json['product_deliverable'] = !empty($user->get_product->deliverable) ? $user->get_product->deliverable : '';
			$json['product_old_price'] = !empty($user->get_product->old_price) ? $user->get_product->old_price : '0';
			$json['product_products_image'] = !empty($user->get_product->products_image) ? $user->get_product->products_image : '';
			 $json['offer_available'] = !empty($user->get_product->offer_available) ? $user->get_product->offer_available : '0';
			 $json['product_active'] = !empty($user->get_product->product_active) ? $user->get_product->product_active : '0';
			 $json['add_to_cart'] = !empty($user->get_product->add_to_cart) ? $user->get_product->add_to_cart : '0';
			  $json['delivery_charge'] = !empty($user->get_product->delivery_charge) ? $user->get_product->delivery_charge : '0';
			//$json['delivery_charge'] = $user->get_product->delivery_charge;
			//$json['offer_available'] = $user->get_product->offer_available;
			//$json['product_active'] = $user->get_product->product_active;
			//$json['add_to_cart']    = $user->get_product->add_to_cart;
			$json['products_type']   = !empty($user->get_product->products_type)? $user->get_product->products_type : '';
			$json['product_tax']     = !empty($user->get_product->product_tax)? $user->get_product->product_tax : '';
			$json['cart_count']      = !empty($user->get_product->cart_count)? $user->get_product->cart_count : '';
			// product end
			$json['orders_name'] 	 = !empty($user->orders_name) ? $user->orders_name : '';
			$json['order_date_time'] = !empty($user->order_date_time) ? $user->order_date_time : '';
			$json['order_status'] 	 = $user->order_status;

			$json['problem_name'] 	 = !empty($user->problem_name) ? $user->problem_name : '';
			$json['problem_image'] = !empty($user->problem_image) ? $user->problem_image : '';
		return $json;
 	}
	
	public function app_order_update(Request $request){

		if (!empty($request->id)) {

			$update_record = OrderModel::find($request->id);
			// dd($update_record);
			$update_record->order_date_time    = !empty($request->order_date_time) ? $request->order_date_time : '';
	
			//dd($request->order_date_time);


  			 $new_date = date('Y-m-d h:i:s', strtotime($request->order_date_time. '+3 day'));
  			 $update_record->expected_delivery_time  		   = $new_date;

			//	$update_record->expected_delivery_time    = !empty($request->expected_delivery_time) ? $request->expected_delivery_time : '';


			$update_record->delivery_charge = !empty($request->delivery_charge) ? $request->delivery_charge : '0';

			$update_record->order_total = !empty($request->order_total) ? $request->order_total : '0';
			$update_record->grand_total = !empty($request->grand_total) ? $request->grand_total : '0';
			
			
			$update_record->order_status  = trim($request->order_status );
			$update_record->delivery_status  = trim($request->delivery_status );
	
			$update_record->save();


			if(!empty($request->order_detail))
			{
				OrderDetailModel::where('order_id', '=', $update_record->id)->delete();

				$option = json_decode($request->order_detail);
				
				foreach ($option as  $value) {
				// dd($value);
				
						$save_option = new OrderDetailModel;
						$save_option->order_id 			 = $update_record->id;
						$save_option->products_id 	     = $value->product_id;
						$save_option->driver_id   = !empty($value->driver_id) ? $value->driver_id : '';
						$save_option->quantity   = !empty($value->quantity) ? $value->quantity : '';
						$save_option->save();	
				
				}
			} 
  
			$json['status'] = 1;
			$json['message'] = 'Order updated successfully.';

			$json['user_data'] = $this->getOrdersList($update_record->id);

		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid Order Id.';
		}
		echo json_encode($json);
	}

	public function app_order_update_old(Request $request){
		// id
		// products_id
		// orders_name
		// order_date_time  2021-05-28 06:20:22   2021-05-28 06:20:22 YYYY-MM-DD HH:MM:SS
		// order_status  0    0:Pending,1:Processing, 2:Complete
 		if(!empty($request->products_id) && !empty($request->id)){ 
 			$getProductCount = Product::where('id', '=', $request->products_id)->count();
			$update_record = OrderModel::find($request->id);
		    if(!empty($update_record) && !empty($getProductCount)){
		    	$update_record->products_id  	   = trim($request->products_id);
				$update_record->orders_name 	   = trim($request->orders_name);
				$update_record->order_date_time    = trim($request->order_date_time );
				$update_record->order_status	   = trim($request->order_status);
				$update_record->save();
				$json['status'] = 1;
				$json['message'] = 'Order Update Successfully.';
				$json['result'] = $this->getOrderListComm($update_record->id);
		
			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid ID.';
			}
		}else{
				$json['status'] = 0;
				$json['message'] = 'Parameter missing!';
			}
		echo json_encode($json);
	}

	public function app_order_list(Request $request){
		$result = array();

		$getresult = OrderModel::where('user_id', '=', $request->user_id)->orderBy('id', 'desc')->get();



		
		foreach ($getresult as $value) {
			
			// dd($getRecordcart);
			// dd($value->id);
			$data['id']              = $value->id;
			// $data['order_id']              = !empty($value->get_order_details->order_id) ? $value->get_order_details->order_id : '';
			$data['user_id']         = $value->user_id;	
			$data['order_date_time'] = $value->order_date_time;	
			$data['order_status']    = $value->order_status;
			$data['problem_name']     = !empty($value->problem_name)? $value->problem_name : '';
			$data['problem_image']    = !empty($value->problem_image)? $value->problem_image : '';
			
			$data['transaction_id']        = $value->transaction_id;
			$data['users_address_id']        = $value->users_address_id;
		
 
			// $data['delivery_charge']    = $value->delivery_charge;
			// $data['order_total']    = $value->order_total;
			// $data['grand_total']    = $value->grand_total;
			$data['expected_delivery_time']    = $value->expected_delivery_time;

			$data['delivery_charge']  = !empty($value->delivery_charge) ? $value->delivery_charge : '0';

			$data['order_total']  = !empty($value->order_total) ? $value->order_total : '0';
				$data['grand_total']    = !empty($value->grand_total) ? $value->grand_total : '0';


			// cart

			// $option_main_cart = array();

			// $getRecordcart = Cart::where('user_id', '=', $value->user_id)->get();

			// foreach ($getRecordcart as $kvalue) {
			// 	$data_xz['id']               = $kvalue->id;
			// 	$data_xz['product_id']       = $kvalue->product_id;
			// 	$data_xz['user_id']          = $kvalue->user_id;
			// 	$data_xz['quantity']         = $kvalue->quantity;
			// 	$data_xz['total_price']      = $kvalue->total_price;
			// 	$option_main_cart[] = $data_xz;
			// }

			// $data['cart_list']    = $option_main_cart;

			//  order_detail start

			$option_main = array();
		
			$getresultsss = OrderDetailModel::where('order_id', '=', $value->id)->orderBy('id', 'desc')->get();
			foreach ($getresultsss as $valuse_x) {
				$data_x['id']              = $valuse_x->id;
				$data_x['order_id']        = $valuse_x->order_id;
				$data_x['quantity']        = $valuse_x->quantity;
 
				$data_x['product_id']     = $valuse_x->products_id;
				$data_x['product_name']    =  !empty($valuse_x->get_product_name->name) ? $valuse_x->get_product_name->name : '';

				$data_x['product_price']      = !empty($valuse_x->get_product_name->price) ? $valuse_x->get_product_name->price : '';
				$data_x['product_discount_price']  = !empty($valuse_x->get_product_name->discount_price) ? $valuse_x->get_product_name->discount_price : '';
				$data_x['product_description']     = !empty($valuse_x->get_product_name->description) ? $valuse_x->get_product_name->description : '';
				$data_x['product_capacity']        = !empty($valuse_x->get_product_name->capacity) ? $valuse_x->get_product_name->capacity : '';
				$data_x['product_package_items_count']  = !empty($valuse_x->get_product_name->package_items_count) ? $valuse_x->get_product_name->package_items_count : '';
				$data_x['product_unit']        = !empty($valuse_x->get_product_name->unit) ? $valuse_x->get_product_name->unit : '';
				$data_x['product_featured']    = !empty($valuse_x->get_product_name->featured) ? $valuse_x->get_product_name->featured : '';
				$data_x['product_deliverable'] = !empty($valuse_x->get_product_name->deliverable) ? $valuse_x->get_product_name->deliverable : '';
				$data_x['product_old_price'] = !empty($valuse_x->get_product_name->old_price) ? $valuse_x->get_product_name->old_price : '0';
				$data_x['product_products_image'] = !empty($valuse_x->get_product_name->products_image) ? $valuse_x->get_product_name->products_image : '';
				$data_x['delivery_charge'] = $valuse_x->get_product_name->delivery_charge;
				$data_x['offer_available'] = $valuse_x->get_product_name->offer_available;
				$data_x['product_active'] = $valuse_x->get_product_name->product_active;
				$data_x['add_to_cart']    = $valuse_x->get_product_name->add_to_cart;
				$data_x['products_type']   = !empty($valuse_x->get_product_name->products_type) ? $valuse_x->get_product_name->products_type : '';
				$data_x['product_tax']     = !empty($valuse_x->get_product_name->product_tax)? $valuse_x->get_product_name->product_tax : '';
				$data_x['cart_count']      = !empty($valuse_x->get_product_name->cart_count)? $valuse_x->get_product_name->cart_count : '';

				$data_x['driver_id']       = $valuse_x->driver_id;
				$option_main[] = $data_x;
			}

		    $data['product_list']    = $option_main;

		    //  order_detail end

		    $option_main_item = array();
		
			$getresult_item = OrderDetailItemModel::where('order_id', '=', $value->id)->orderBy('id', 'desc')->get();
			foreach ($getresult_item as $valuse_xy) {
				$data_xy['id']              = $valuse_xy->id;
				$data_xy['order_id']        = $valuse_xy->order_id;
				$data_xy['product_name']    =  !empty($valuse_xy->product_name) ? $valuse_xy->product_name : '';
				$data_xy['product_unit']    =  !empty($valuse_xy->product_unit) ? $valuse_xy->product_unit : '';
				$data_xy['product_price'] = !empty($valuse_xy->product_price) ? $valuse_xy->product_price : '0';
				$data_xy['quantity'] = !empty($valuse_xy->quantity) ? $valuse_xy->quantity : '0';
			
				$option_main_item[] = $data_xy;
			}

		    $data['product_item_list']    = $option_main_item;
	
			$result[] = $data;	
		}




		$json['status'] = 1;
		$json['message'] = 'All data loaded successfully.';
		$json['result'] = $result;
		
	   
	   echo json_encode($json);
	}

	public function app_carts_options_list(Request $request){
		$result = array();
		$getresult =  CartsOptionsModel::where('user_id', '=', $request->user_id)->orderBy('id', 'desc')->get();
		foreach ($getresult as $value) {
			$data['id'] = $value->id;
			$data['user_id'] = $value->user_id;
			$data['carts_id'] = $value->carts_id;
			$data['checkout_total'] = $value->checkout_total;
			$result[] = $data;
		}
		$json['status'] = 1;
		$json['message'] = 'All data loaded successfully.';
		$json['result'] = $result;
	   
	   echo json_encode($json);
	}


	// File a Dispute

	public function app_file_a_dispute_update(Request $request){
		if(!empty($request->problem_name) && !empty($request->order_id) && !empty($request->user_id)){ 

		$update_record = OrderModel::where('id', '=', $request->order_id)->where('user_id', '=', $request->user_id)->first();
		 // dd($update_record->user_id);
		if(!empty($update_record)){

			if (!empty($request->file('problem_image'))) {
			// if (!empty($update_record->problem_image) && file_exists('images/' . '/' . $update_record->problem_image)) {
			// 	unlink('images/' . $update_record->problem_image);
			// }
			$ext = 'jpg';
			$file = $request->file('problem_image');
			$randomStr = str_random(30);
			$filename = strtolower($randomStr) . '.' . $ext;
			$file->move('images/', $filename);
	    	//$path = "http://localhost/laravel/bookfast/upload/profile/".$filename;
	 		$update_record->problem_image = $filename;
	 		//dd($update_record->problem_image);
			}
			// else
			// {
			// 		$update_record->problem_image = '';
			// }
			$update_record->problem_name = !empty($request->problem_name) ? $request->problem_name : '';
			$update_record->save();

			$json['status'] = 1;
			$json['message'] = 'Dispute updated successfully.';
			$json['result'] = $this->getOrderListComm($update_record->id);

		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid ID.';
		}
		}else{
				$json['status'] = 0;
				$json['message'] = 'Parameter missing!';
			}
		echo json_encode($json);
	}


	public function app_money_wallet_list(Request $request){
		$result = array();
		if(!empty($request->user_id)){
			$getRecord = User::where('id', '=', $request->user_id)->get();
			foreach ($getRecord as $user) {

				$data['user_id']        = $user->id;
		        $data['name']           = !empty($user->name) ? $user->name : '';
		        $data['email']          = !empty($user->email) ? $user->email : '';
		        $data['mobile']         = !empty($user->mobile) ? $user->mobile : '';
		        $data['otp']            = !empty($user->otp) ? $user->otp : '';
		        $data['otp_verify']     = !empty($user->otp_verify) ? $user->otp_verify : '0';
		        $data['lat']            = !empty($user->lat) ? $user->lat : '';
		        $data['lang']           = !empty($user->lang) ? $user->lang : '';
		        $data['user_profile']   = !empty($user->user_profile) ? $user->user_profile : '';
		        $data['address']        = !empty($user->address) ? $user->address : '';
		        $data['token']          = !empty($user->token) ? $user->token : ''; 
		        $data['total_balance']  = !empty($user->total_balance) ? $user->total_balance : '0';
		        $data['document_photo'] = !empty($user->document_photo) ? $user->document_photo : '';
		        $data['social_id'] 		= !empty($user->social_id) ? $user->social_id : '';
				$data['social_type'] 	= $user->social_type;
				$data['online_offline_status'] 	= $user->online_offline_status;
				$data['account_number'] 	= !empty($user->account_number) ? $user->account_number : '';
				$data['bank_holder_name'] 	= !empty($user->bank_holder_name) ? $user->bank_holder_name : '';
				$data['ifsc_code'] 	        = !empty($user->ifsc_code) ? $user->ifsc_code : '';
				$data['branch_code'] 	    = !empty($user->address) ? $user->branch_code : '';
				$data['bank_name'] 	        = !empty($user->address) ? $user->bank_name : '';

				$result[] = $data;
			}
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['result'] = $result;

		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	}

	public function app_driver_document_list(Request $request){
		$result = array();
		if(!empty($request->user_id)){
		$getresult = User::where('id', '=', $request->user_id)->get();

			foreach ($getresult as $user) {
				$data['user_id']        = $user->id;
				$data['document_photo'] = !empty($user->document_photo) ? $user->document_photo : '';
				$result[] = $data;
			}
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	}

	public function app_order_status_list(Request $request){
		//dd($request->order_id);
		$result = array();
		if(!empty($request->user_id)){
		$update_record = OrderModel::where('id', '=', $request->order_id)->where('user_id', '=', $request->user_id)->get();
		// dd($update_record);
			foreach ($update_record as $user) {
				$data['id'] = $user->id;
				$data['user_id'] = $user->user_id;
			
				$data['order_date_time'] = $user->order_date_time;
				$data['delivery_status'] = $user->delivery_status;
				$result[] = $data;
			} 
				$json['status'] = 1;
				$json['message'] = 'All data loaded successfully.';
				$json['result'] = $result;
			}else{
			
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	
	}

	public function app_order_accept_reject(Request $request){
		$getUserCount = User::where('id','=', $request->drivers_id)->count();
		if(!empty($getUserCount)){
		$update_accept_reject = OrderModel::find($request->order_id);
		if(!empty($update_accept_reject)){
			
			$update_accept_reject->accept_reject_order = trim($request->accept_reject_order);
			//	$update_accept_reject->trip_id             = trim($request->trip_id);
			$update_accept_reject->drivers_id = trim($request->drivers_id);
			$update_accept_reject->save();

			if($update_accept_reject->accept_reject_order == 1){
				$title   = "Order Accept!";
				$message = "Received an order";
				$this->sendPushNotificationUser($title,$message,$update_accept_reject->user_id,$update_accept_reject->accept_reject_order);
				$json['status']  = 1;
				$json['message'] = 'Your order successfully accept.';
			}else{
				$title   = "Order Reject!";
				$message = "Received an order";
				$this->sendPushNotificationUser($title,$message,$update_accept_reject->user_id,$update_accept_reject->accept_reject_order);
				$json['status']  = 1;
				$json['message'] = 'Your order successfully reject.';
			}
		}else{ 
			$json['status']  = 0;
			$json['message'] = 'Record not found.';
		}
	}else{
		$json['status']  = 0;
		$json['message'] = 'Driver ID not available.';
	}
		echo json_encode($json);
	
	}

	public function sendPushNotificationUser($title,$message,$user_id,$accept_reject_order){
		// echo $user_id;
		// die();
		//dd($title);
		$user    = User::find($user_id);
		$result = NotificationServerKeyModel::find(1);
		$serverKey = $result->notification_key;	
			// try {
		//	if(!empty($user->token)) {

			$token = $user->token;
			// dd($token); 
			$body['message'] = $message;
		
			$body['title']   = $title;

			$body['driver_name']         = $user->name;

			$body['driver_mobile']       = $user->mobile;

			$body['driver_lat']          = $user->lat;

			$body['driver_lang']         = $user->lang;

			$body['driver_image']        = $user->user_profile;
 
			$body['notification_type']   = $accept_reject_order;
			// dd($body['notification_type']);


			$body['body']    = $body;
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
// }
		// 	}
		// catch (\Exception  $e) {

 	// }
	}
	
 
	

	public function getOrderList($id){
		$user = OrderModel::find($id);
			$json['id'] 			 = $user->id;
			$json['user_id'] 		 = $user->user_id;

			$json['user_name']       = !empty($user->get_user->name) ? $user->get_user->name : '';

				

			//$json['product_id']      = !empty($user->get_carts->product_id) ? $user->get_carts->product_id : '';

			$option_main = array();
			foreach($user->get_user->get_carts as $option)
			{
				$data = array();
				$data['id'] 	        = $option->id;
				$data['product_id'] 	= $option->product_id;
			// product start
				$data['product_name']      = !empty($option->get_product_name->name) ? $option->get_product_name->name : '';
				$data['product_price']     = !empty($option->get_product_name->price) ? $option->get_product_name->price : '';
				$data['product_discount_price']  = !empty($option->get_product_name->discount_price) ? $option->get_product_name->discount_price : '';
				$data['product_description']     = !empty($option->get_product_name->description) ? $option->get_product_name->description : '';
				$data['product_capacity']        = !empty($option->get_product_name->capacity) ? $option->get_product_name->capacity : '';
				$data['product_package_items_count']  = !empty($option->get_product_name->package_items_count) ? $option->get_product_name->package_items_count : '';
				$data['product_unit']        = !empty($option->get_product_name->unit) ? $option->get_product_name->unit : '';
				$data['product_featured']    = !empty($option->get_product_name->featured) ? $option->get_product_name->featured : '';
				$data['product_deliverable'] = !empty($option->get_product_name->deliverable) ? $option->get_product_name->deliverable : '';

				$data['product_old_price'] = !empty($option->get_product_name->old_price) ? $option->get_product_name->old_price : '0';
				$data['product_products_image'] = !empty($option->get_product_name->products_image) ? $option->get_product_name->products_image : '';
				$data['delivery_charge'] = $option->get_product_name->delivery_charge;
				$data['offer_available'] = $option->get_product_name->offer_available;
				$data['product_active']  = $option->get_product_name->product_active;
				$data['add_to_cart']     = $option->get_product_name->add_to_cart;
				$data['products_type']   = !empty($option->get_product_name->products_type)? $option->get_product_name->products_type : '';
				$data['product_tax']     = !empty($option->get_product_name->product_tax)? $option->get_product_name->product_tax : '';
				$data['cart_count']      = !empty($option->get_product_name->cart_count)? $option->get_product_name->cart_count : '';
				// product end 
				$data['user_id'] 	    = $option->user_id;
				$data['quantity'] 	    = $option->quantity;
				$data['total_price'] 	= $option->total_price;

				$option_main[] = $data;
			}
	 
			$json['cart_details'] 		= $option_main;

			$json['checkout_total']  = $user->checkout_total_all;
			$json['orders_name']  = $user->orders_name;
			$json['order_date_time']  = $user->order_date_time;
			$json['order_status']  = $user->order_status;
		return $json;
	}


	public function app_carts_options_add(Request $request){

		$getUser = User::where('id', '=', $request->user_id)->count();

		$update_rec = User::where('id', '=', $request->user_id)->first();

		if(!empty($getUser)){
		
		$record_insert = new OrderModel;
	
		if(!empty($record_insert) && !empty($update_rec)){

			$record_insert->user_id  		   = trim($request->user_id);
			$record_insert->drivers_id 	       = !empty($request->drivers_id) ? $request->drivers_id : '';
			$record_insert->order_date_time    = !empty($request->order_date_time) ? $request->order_date_time : '';
	
			//dd($request->order_date_time);


  			 $new_date = date('Y-m-d h:i:s', strtotime($request->order_date_time. '+3 day'));
  			 $record_insert->expected_delivery_time  		   = $new_date;

			//	$record_insert->expected_delivery_time    = !empty($request->expected_delivery_time) ? $request->expected_delivery_time : '';


			$record_insert->delivery_charge = !empty($request->delivery_charge) ? $request->delivery_charge : '0';

			$record_insert->order_total = !empty($request->order_total) ? $request->order_total : '0';
			$record_insert->grand_total = !empty($request->grand_total) ? $request->grand_total : '0';
			
			
			$record_insert->order_status  = trim($request->order_status );
			$record_insert->delivery_status  = trim($request->delivery_status );
	
			$record_insert->save();

			if(!empty($request->order_detail))
			{
				$option = json_decode($request->order_detail);
				
				foreach ($option as  $value) {
						$save_option = new OrderDetailModel;
						$save_option->order_id 			 = $record_insert->id;
						$save_option->products_id 	     = $value->product_id;
						$save_option->driver_id   = !empty($value->driver_id) ? $value->driver_id : '';
						$save_option->quantity   = !empty($value->quantity) ? $value->quantity : '';
						$save_option->save();	
				} 
			}
		    $json['status'] = 1;
			$json['message'] = 'Order successfully.';
			$json['result'] = $this->getOrdersList($record_insert->id);
		
			}else{
				$json['status'] = 0;
				$json['message'] = 'User ID Incorrect.';
			}
		} 
		else   
		{

			$json['status'] = 0;
			$json['message'] = 'User Id';
		}

		echo json_encode($json);
	}


	public function getOrdersList($id){
		$user 						= OrderModel::find($id);
		$json['id'] 			    = $user->id;
		$json['user_id'] 			= !empty($user->user_id) ? $user->user_id : '';
		$json['user_name'] 			= !empty($user->get_user->name) ? $user->get_user->name : '';
		$json['drivers_id'] 		= !empty($user->drivers_id) ? $user->drivers_id : '';
		$json['order_date_time'] 	= !empty($user->order_date_time) ? $user->order_date_time : '';
		$json['expected_delivery_time']  = !empty($user->expected_delivery_time) ? $user->expected_delivery_time : '';
		$json['delivery_charge'] = !empty($user->delivery_charge) ? $user->delivery_charge : '0';
		$json['order_total']     = $user->order_total;
		$json['grand_total']     = $user->grand_total;
		$json['order_status']    = $user->order_status;
		$json['delivery_status']       = $user->delivery_status;
		$json['transaction_id']        = $user->transaction_id;
		$json['users_address_id']        = $user->users_address_id;
		
		
		$json['problem_name']     = !empty($user->problem_name)? $user->problem_name : '';
		$json['problem_image']    = !empty($user->problem_image)? $user->problem_image : '';
			
		
		$option_main = array();
		foreach($user->get_order_details as $option)
		{
			$data = array();
			$data['id'] 	    = $option->id;
			$data['order_id'] 	= $option->order_id;
			$data['driver_id']   = !empty($option->driver_id) ? $option->driver_id : '';


		 	$data['product_id'] = !empty($option->products_id) ? $option->products_id : '';
	// product		 	
		 	$data['product_name']      = !empty($option->get_product_name->name) ? $option->get_product_name->name : '';
	 		$data['product_price']     = !empty($option->get_product_name->price) ? $option->get_product_name->price : '';
			$data['product_discount_price']  = !empty($option->get_product_name->discount_price) ? $option->get_product_name->discount_price : '';
			$data['product_description']     = !empty($option->get_product_name->description) ? $option->get_product_name->description : '';
			$data['product_capacity']        = !empty($option->get_product_name->capacity) ? $option->get_product_name->capacity : '';
			$data['product_package_items_count']  = !empty($option->get_product_name->package_items_count) ? $option->get_product_name->package_items_count : '';
			$data['product_unit']        = !empty($option->get_product_name->unit) ? $option->get_product_name->unit : '';
			$data['product_featured']    = !empty($option->get_product_name->featured) ? $option->get_product_name->featured : '';
			$data['product_deliverable'] = !empty($option->get_product_name->deliverable) ? $option->get_product_name->deliverable : '';

			$data['product_old_price'] = !empty($option->get_product_name->old_price) ? $option->get_product_name->old_price : '0';
			$data['product_products_image'] = !empty($option->get_product_name->products_image) ? $option->get_product_name->products_image : '';
			$data['delivery_charge'] = $option->get_product_name->delivery_charge;
			$data['offer_available'] = $option->get_product_name->offer_available;
			$data['product_active']  = $option->get_product_name->product_active;
			$data['add_to_cart']     = $option->get_product_name->add_to_cart;
			$data['products_type']   = !empty($option->get_product_name->products_type)? $option->get_product_name->products_type : '';
			$data['product_tax']     = !empty($option->get_product_name->product_tax)? $option->get_product_name->product_tax : '';
			$data['cart_count']      = !empty($option->get_product_name->cart_count)? $option->get_product_name->cart_count : '';

	// productend

			
			$option_main[] = $data;
		}

		$json['order_details'] 		= $option_main;

		return $json;
	}


	public function app_place_order_add(Request $request){

	   if(!empty($request->order_id) && !empty($request->transaction_id) && !empty($request->users_address_id)){ 
	   	$UsersAddressCount = UsersAddress::where('id', '=', $request->users_address_id)->count();
		$update_record = OrderModel::find($request->order_id);
		//dd($update_record->users_address_id);
			if(!empty($update_record)  && !empty($UsersAddressCount)){

				$update_record->transaction_id    = trim($request->transaction_id);
				$update_record->users_address_id    = trim($request->users_address_id);
				
				$update_record->save();

				$json['status'] = 1;
				$json['message'] = 'Payment updated successfully.';
				$json['result'] = $this->getOrdersList($update_record->id);

				$title   = "Order Placed!";
				$message = "Received an order";

				$getUsersAddress = UsersAddress::where('id', '=', $update_record->users_address_id)->first();
				//dd($getUsersAddress);

					$get_User = User::selectRaw("users.*, (SELECT 111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(users.lat)) * COS(RADIANS(".$getUsersAddress->latitude.")) * COS(RADIANS(users.lang - ".$getUsersAddress->longitude.")) + SIN(RADIANS(users.lat)) * SIN(RADIANS(".$getUsersAddress->latitude.")))))) * 0.621371 as distance")
					->where(DB::raw("(111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(users.lat)) * COS(RADIANS(".$getUsersAddress->latitude.")) * COS(RADIANS(users.lang - ".$getUsersAddress->longitude.")) + SIN(RADIANS(users.lat)) * SIN(RADIANS(".$getUsersAddress->latitude."))))) * 0.621371)"), "<=", 500000)
					->where('online_offline_status', '=', '1')->get();
 

					
// dd($get_User);

					foreach ($get_User as $value) {
							//$xyz = User::where('id', '=', $value->id)->first();
						/*dd($xyz->id);*/
						$this->sendPushNotificationDriver($title,$message,$value->id);

					}


					// Database Notification store start
					$Nofi_insert = new NotificationStore;
					$Nofi_insert->user_id             = $update_record->user_id;
					$Nofi_insert->title               = $title;
                    $Nofi_insert->message             = $message;
                    $Nofi_insert->order_date_time     = $update_record->order_date_time;
					$Nofi_insert->save();
					// Database Notification store end
	


			}else{
				$json['status'] = 0;
				$json['message'] = 'Invalid ID.';
			}
		}else{
				$json['status'] = 0;
				$json['message'] = 'Parameter missing!';
			}
			echo json_encode($json);
	}

	public function sendPushNotificationDriver($title,$message,$user_id){
	
 		//$user    	  = User::find($user_id);
 		$user    	  = User::find($user_id);
 	// print_r($user);

 	//dd($user);
		// End
		$result = NotificationServerKeyModel::find(1);
		$serverKey = $result->notification_key;	   
 		 
		// if(!empty($user))
		// {
// 			foreach ($user as $value) {
// // dd($user);
// 			 if(!empty($value->token)) {

				$token = $user->token;
// dd($token);
				$body['message'] = $message;
				
				$body['body']    = $body;
				$body['title']   = $title;
//dd($body['body']);
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
		// } 
		//}

	}
  	  
	
	public function app_driver_user_list(Request $request){

		$getcount = User::where('id', '=', $request->driver_id)->count();


		if(!empty($getcount)){
		
			$value = User::where('id', '=', $request->driver_id)->orderBy('id', 'desc')->first();
		
			$get_User = UsersAddress::selectRaw("users_address.*, (SELECT 111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(users_address.latitude)) * COS(RADIANS(".$value->lat.")) * COS(RADIANS(users_address.longitude - ".$value->lang.")) + SIN(RADIANS(users_address.latitude)) * SIN(RADIANS(".$value->lat.")))))) * 0.621371 as distance")
					->where(DB::raw("(111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(users_address.latitude)) * COS(RADIANS(".$value->lat.")) * COS(RADIANS(users_address.longitude - ".$value->lang.")) + SIN(RADIANS(users_address.latitude)) * SIN(RADIANS(".$value->lat."))))) * 0.621371)"), "<=", 5)
					->orderBy('id', 'desc')->get();
				
				$result = array();
					 

					foreach ($get_User as $key_value) {

						
						$getresultsss = OrderModel::where('users_address_id', '=', $key_value->id)->where('accept_reject_order', '=', '0')->orderBy('id', 'desc')->get();
						

						foreach ($getresultsss as $valuse_x) {
							$data_x['id']              = $valuse_x->id;
							$data_x['user_id']         = $valuse_x->user_id;
							$data_x['user_name']         = !empty($valuse_x->get_user->name) ? $valuse_x->get_user->name : '';
							$data_x['user_profile']         = !empty($valuse_x->get_user->user_profile) ? $valuse_x->get_user->user_profile : '';
							$data_x['user_address']         = !empty($valuse_x->get_users_address->full_address) ? $valuse_x->get_users_address->full_address : '';

						

							$data_x['latitude']         = !empty($valuse_x->get_users_address->latitude) ? $valuse_x->get_users_address->latitude : '';

							$data_x['longitude']         = !empty($valuse_x->get_users_address->longitude) ? $valuse_x->get_users_address->longitude : '';



							$data_x['drivers_id']      = $valuse_x->drivers_id;
							$data_x['driver_name']         = !empty($valuse_x->get_drivers->name) ? $valuse_x->get_drivers->name : '';
							//	$data_x['driver_address']         = !empty($valuse_x->get_users_address->full_address) ? $valuse_x->get_users_address->full_address : '';

							$data_x['order_date_time']      = $valuse_x->order_date_time;
							$data_x['expected_delivery_time']      = $valuse_x->expected_delivery_time;
							$data_x['delivery_charge']      = $valuse_x->delivery_charge;
							$data_x['order_total']      = $valuse_x->order_total;
							$data_x['grand_total']      = $valuse_x->grand_total;
							$data_x['order_status']      = $valuse_x->order_status;
							$data_x['problem_name']      = $valuse_x->problem_name;
							$data_x['problem_image']      = $valuse_x->problem_image;
							$data_x['delivery_status']      = $valuse_x->delivery_status;
							$data_x['transaction_id']      = $valuse_x->transaction_id;
							$data_x['accept_reject_order']      = $valuse_x->accept_reject_order;


							
							$option_main_sub = array();

							$getresultorder = OrderDetailModel::where('order_id', '=', $valuse_x->id)->orderBy('id', 'desc')->get();

							foreach ($getresultorder as $val) {
									$data_xy['id']              = $val->id;

									$data_xy['order_id']        = $val->order_id;
									$data_xy['quantity']        = $val->quantity;
									$data_xy['product_id']     = $val->products_id;



							$data_xy['product_name']    =  !empty($val->get_product_name->name) ? $val->get_product_name->name : '';
							$data_xy['product_price']      = !empty($val->get_product_name->price) ? $val->get_product_name->price : '';
							$data_xy['product_discount_price']  = !empty($val->get_product_name->discount_price) ? $val->get_product_name->discount_price : '';
							$data_xy['product_description']     = !empty($val->get_product_name->description) ? $val->get_product_name->description : '';
							$data_xy['product_capacity']        = !empty($val->get_product_name->capacity) ? $val->get_product_name->capacity : '';
							$data_xy['product_package_items_count']  = !empty($val->get_product_name->package_items_count) ? $val->get_product_name->package_items_count : '';
							$data_xy['product_unit']        = !empty($val->get_product_name->unit) ? $val->get_product_name->unit : '';
							$data_xy['product_featured']    = !empty($val->get_product_name->featured) ? $val->get_product_name->featured : '';
							$data_xy['product_deliverable'] = !empty($val->get_product_name->deliverable) ? $val->get_product_name->deliverable : '';
							$data_xy['product_old_price'] = !empty($val->get_product_name->old_price) ? $val->get_product_name->old_price : '0';
							$data_xy['product_products_image'] = !empty($val->get_product_name->products_image) ? $val->get_product_name->products_image : '';
							$data_xy['delivery_charge'] = $val->get_product_name->delivery_charge;
							$data_xy['offer_available'] = $val->get_product_name->offer_available;
							$data_xy['product_active'] = $val->get_product_name->product_active;
							$data_xy['add_to_cart']    = $val->get_product_name->add_to_cart;
							$data_xy['products_type']   = !empty($val->get_product_name->products_type) ? $val->get_product_name->products_type : '';
							$data_xy['product_tax']     = !empty($val->get_product_name->product_tax)? $val->get_product_name->product_tax : '';
							$data_xy['cart_count']      = !empty($val->get_product_name->cart_count)? $val->get_product_name->cart_count : '';


									$data_xy['driver_id']       = $val->driver_id;

									$option_main_sub[] = $data_xy;

							}
							 $data_x['product_list']    = $option_main_sub;


						 $result[] = $data_x;

						}

					



					
					}




					$json['status'] = 1;
				    $json['message'] = 'All data loaded successfully.';

					$json['result'] = $result;



			
		
			
			}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);

	}


	public function app_add_item_add(Request $request){
		

		$getUser = User::where('id', '=', $request->user_id)->count();

		$update_rec = User::where('id', '=', $request->user_id)->first();

		if(!empty($getUser)){
		
		$record_insert = new OrderModel;
	
		if(!empty($record_insert) && !empty($update_rec)){

			$record_insert->user_id  		   = trim($request->user_id);
			$record_insert->drivers_id 	       = !empty($request->drivers_id) ? $request->drivers_id : '';
			$record_insert->order_date_time    = !empty($request->order_date_time) ? $request->order_date_time : '';
	
			//dd($request->order_date_time);


  			 $new_date = date('Y-m-d h:i:s', strtotime($request->order_date_time. '+3 day'));
  			 $record_insert->expected_delivery_time  		   = $new_date;

			//	$record_insert->expected_delivery_time    = !empty($request->expected_delivery_time) ? $request->expected_delivery_time : '';


			$record_insert->delivery_charge = !empty($request->delivery_charge) ? $request->delivery_charge : '0';

			$record_insert->order_total = !empty($request->order_total) ? $request->order_total : '0';
			$record_insert->grand_total = !empty($request->grand_total) ? $request->grand_total : '0';
			
			
			$record_insert->order_status  = trim($request->order_status );
			$record_insert->delivery_status  = trim($request->delivery_status );
	
			$record_insert->save();

			if(!empty($request->order_detail_item))
			{
				$option = json_decode($request->order_detail_item);
				
				foreach ($option as  $value) {
						$save_option = new OrderDetailItemModel;
						$save_option->order_id 			 = $record_insert->id;
						$save_option->product_name   = !empty($value->product_name) ? $value->product_name : '';
						$save_option->quantity   = !empty($value->quantity) ? $value->quantity : '0';
						$save_option->product_unit   = !empty($value->product_unit) ? $value->product_unit : '';
						
						$save_option->save();	
				} 
			}
		    $json['status'] = 1;
			$json['message'] = 'Order successfully.';
			$json['result'] = $this->getOrdersItmeList($record_insert->id);
		// Notification work		
			$title   = "Order Placed!";
			$message = "Received an order";
		//	dd($record_insert->id);
			$this->sendPushNotificationNearByDriver($title,$message,$record_insert->id);
			
			}else{
				$json['status'] = 0;
				$json['message'] = 'User ID Incorrect.';
			}
		} 
		else   
		{

			$json['status'] = 0;
			$json['message'] = 'User Id';
		}

		echo json_encode($json);
	} 
 
	public function sendPushNotificationNearByDriver($title,$message,$user_id){
		$user = User::where('online_offline_status', '=', '1')->get();
	//	dd($user);
		$result = NotificationServerKeyModel::find(1);
		$serverKey = $result->notification_key;	  
		// dd($result);
 		if(!empty($user))
		 {
 			foreach ($user as $value) {
// // dd($user);
			 if(!empty($value->token)) {

				$token = $value->token;
// dd($token);
				$body['message'] = $message;
				
				$body['body']    = $body;
				$body['title']   = $title;
//dd($body['body']);
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

		}
	}

	public function getOrdersItmeList($id){
		$user 						= OrderModel::find($id);
		$json['id'] 			    = $user->id;
		$json['user_id'] 			= !empty($user->user_id) ? $user->user_id : '';
		$json['user_name'] 			= !empty($user->get_user->name) ? $user->get_user->name : '';

		$json['drivers_id'] 		= !empty($user->drivers_id) ? $user->drivers_id : '';
			$json['drivers_name'] 			= !empty($user->get_drivers->name) ? $user->get_drivers->name : '';
		$json['order_date_time'] 	= !empty($user->order_date_time) ? $user->order_date_time : '';
		$json['expected_delivery_time']  = !empty($user->expected_delivery_time) ? $user->expected_delivery_time : '';
		$json['delivery_charge'] = !empty($user->delivery_charge) ? $user->delivery_charge : '0';
		$json['order_total']     = $user->order_total;
		$json['grand_total']     = $user->grand_total;
		$json['order_status']    = $user->order_status;
		$json['delivery_status']       = $user->delivery_status;
		$json['transaction_id']        = $user->transaction_id;
		$json['users_address_id']        = $user->users_address_id;
		
		
		$json['problem_name']     = !empty($user->problem_name)? $user->problem_name : '';
		$json['problem_image']    = !empty($user->problem_image)? $user->problem_image : '';
			
		 
		$option_main = array();
		foreach($user->get_order_details_item as $option)
		{
			$data = array();
			$data['id'] 	        = $option->id;
			$data['order_id'] 	    = $option->order_id;
			$data['product_name']   = !empty($option->product_name) ? $option->product_name : '';
			$data['quantity']       = !empty($option->quantity) ? $option->quantity : '0';
			$data['product_unit']   = !empty($option->product_unit) ? $option->product_unit : '';
			$data['product_price']  = !empty($option->product_price) ? $option->product_price : '0';
			$option_main[] = $data;
		}

		$json['product_list'] 		= $option_main;
  
		return $json;
	}


	public function app_driver_user_ongoing_list(Request $request){

// drivers_id                   order table drivers_id
// accept_reject_order         0=Reject, Pending, 1=Accept , Ongoing
		$getcount = User::where('id', '=', $request->drivers_id)->count();
		if(!empty($getcount)){

		$result = array();
		$getresult = OrderModel::where('drivers_id', '=', $request->drivers_id)->where('accept_reject_order', '=', '1')->orderBy('id', 'desc')->get();

			foreach ($getresult as $user) {
			 		
				$getAddress = UsersAddress::where('id', '=', $user->users_address_id)->orderBy('id', 'desc')->first();
 //dd($getAddress->full_address);
				$data['id']        = $user->id;
				$data['user_id'] = !empty($user->user_id) ? $user->user_id : '';
				$data['user_name'] = !empty($user->get_user->name) ? $user->get_user->name : '';
				$data['user_profile'] = !empty($user->get_user->user_profile) ? $user->get_user->user_profile : '';
				$data['driver_name'] = !empty($user->get_drivers->name) ? $user->get_drivers->name : '';
				$data['drivers_id'] = !empty($user->drivers_id) ? $user->drivers_id : '';
				$data['order_status'] = $user->order_status;
				$data['users_address_id'] = !empty($user->users_address_id) ? $user->users_address_id : '';
				$data['order_date_time'] = !empty($user->order_date_time) ? $user->order_date_time : '';
				

				$data['expected_delivery_time']    = $user->expected_delivery_time;

			    $data['delivery_charge']  = !empty($user->delivery_charge) ? $user->delivery_charge : '0';

			    $data['order_total']  = !empty($user->order_total) ? $user->order_total : '0';
				$data['grand_total']    = !empty($user->grand_total) ? $user->grand_total : '0';
				$data['delivery_status']    = !empty($user->delivery_status) ? $user->delivery_status : '0';

				$data['transaction_id']      = $user->transaction_id;
				$data['accept_reject_order']      = $user->accept_reject_order;

				$data['user_address'] = !empty($getAddress->full_address) ? $getAddress->full_address : '';
				$data['latitude'] = !empty($getAddress->latitude) ? $getAddress->latitude : '';
				$data['longitude'] = !empty($getAddress->longitude) ? $getAddress->longitude : '';


				$option_main = array();
		
				$getresultsss = OrderDetailModel::where('order_id', '=', $user->id)->orderBy('id', 'desc')->get();
				foreach ($getresultsss as $valuse_x) {
					$data_x['id']              = $valuse_x->id;
					$data_x['order_id']        = $valuse_x->order_id;
					// $data_x['product_name']        = $valuse_x->product_name;
					// $data_x['quantity']        = $valuse_x->quantity;
					// $data_x['product_unit']        = $valuse_x->product_unit;
					
					$data_x['quantity']        = $valuse_x->quantity;
	 
					$data_x['product_id']     = $valuse_x->products_id;
					$data_x['product_name']    =  !empty($valuse_x->get_product_name->name) ? $valuse_x->get_product_name->name : '';

					$data_x['product_price']      = !empty($valuse_x->get_product_name->price) ? $valuse_x->get_product_name->price : '';
					$data_x['product_discount_price']  = !empty($valuse_x->get_product_name->discount_price) ? $valuse_x->get_product_name->discount_price : '';
					$data_x['product_description']     = !empty($valuse_x->get_product_name->description) ? $valuse_x->get_product_name->description : '';
					$data_x['product_capacity']        = !empty($valuse_x->get_product_name->capacity) ? $valuse_x->get_product_name->capacity : '';
					$data_x['product_package_items_count']  = !empty($valuse_x->get_product_name->package_items_count) ? $valuse_x->get_product_name->package_items_count : '';
					$data_x['product_unit']        = !empty($valuse_x->get_product_name->unit) ? $valuse_x->get_product_name->unit : '';
					$data_x['product_featured']    = !empty($valuse_x->get_product_name->featured) ? $valuse_x->get_product_name->featured : '';
					$data_x['product_deliverable'] = !empty($valuse_x->get_product_name->deliverable) ? $valuse_x->get_product_name->deliverable : '';
					$data_x['product_old_price'] = !empty($valuse_x->get_product_name->old_price) ? $valuse_x->get_product_name->old_price : '0';
					$data_x['product_products_image'] = !empty($valuse_x->get_product_name->products_image) ? $valuse_x->get_product_name->products_image : '';
					$data_x['delivery_charge'] = $valuse_x->get_product_name->delivery_charge;
					$data_x['offer_available'] = $valuse_x->get_product_name->offer_available;
					$data_x['product_active'] = $valuse_x->get_product_name->product_active;
					$data_x['add_to_cart']    = $valuse_x->get_product_name->add_to_cart;
					$data_x['products_type']   = !empty($valuse_x->get_product_name->products_type) ? $valuse_x->get_product_name->products_type : '';
					$data_x['product_tax']     = !empty($valuse_x->get_product_name->product_tax)? $valuse_x->get_product_name->product_tax : '';
					$data_x['cart_count']      = !empty($valuse_x->get_product_name->cart_count)? $valuse_x->get_product_name->cart_count : '';

					$data_x['driver_id']       = $valuse_x->driver_id;
					$option_main[] = $data_x;
				}

		    $data['product_list']    = $option_main;


		  //   $address_main = array();

		  //   $getAddress = UsersAddress::where('id', '=', $user->users_address_id)->orderBy('id', 'desc')->get();

				// foreach ($getAddress as $valuse_x) {
				// 	$data_xyz['id']              = $valuse_x->id;
				
				// 	$address_main[] = $data_xyz;
				// }

		  //   $data['address_list']    = $address_main;


 
			$result[] = $data;
			}


			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	}


	public function app_driver_area_list(Request $request){
		$getDriverCount = User::where('id', '=', $request->drivers_id)->count();
		if(!empty($getDriverCount) && !empty($request->kilometer)){
			$result = array();
			$getresult = OrderModel::where('drivers_id', '=', $request->drivers_id)->where('accept_reject_order', '=', '1')->orderBy('id', 'desc')->get();
// dd($getresult);
				foreach ($getresult as $value) {
					$getUsersAddress = UsersAddress::where('id', '=', $value->users_address_id)->first();
					

					$get_User = User::selectRaw("users.*, (SELECT 111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(users.lat)) * COS(RADIANS(".$getUsersAddress->latitude.")) * COS(RADIANS(users.lang - ".$getUsersAddress->longitude.")) + SIN(RADIANS(users.lat)) * SIN(RADIANS(".$getUsersAddress->latitude.")))))) * 0.621371 as distance")
					->where(DB::raw("(111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(users.lat)) * COS(RADIANS(".$getUsersAddress->latitude.")) * COS(RADIANS(users.lang - ".$getUsersAddress->longitude.")) + SIN(RADIANS(users.lat)) * SIN(RADIANS(".$getUsersAddress->latitude."))))) * 0.621371)"), "<=", $request->kilometer)
					->where('online_offline_status', '=', '1')->get();

					//dd($get_User);

						foreach ($get_User as $key_value) {
						//dd($key_value->id);
					  //  $data_xyz['id']         = $value->id;
						$data_xyz['user_id']         = $key_value->id;
						$data_xyz['name']            = $key_value->name;
						$data_xyz['address']         = $key_value->address;
						$data_xyz['user_profile']    = !empty($key_value->user_profile) ? $key_value->user_profile : '';
						$data_xyz['full_address']    = $getUsersAddress->full_address;
						$data_xyz['grand_total']    = $value->grand_total;
						$data_xyz['order_total']    = $value->order_total;
						$data_xyz['delivery_charge'] = $value->delivery_charge;
						$data_xyz['order_status']    = $value->order_status;
						$data_xyz['delivery_status']    = $value->delivery_status;
						$data_xyz['order_date_time']    = $value->order_date_time;
						$data_xyz['accept_reject_order']    = $value->accept_reject_order;
						$data_xyz['expected_delivery_time'] = $value->expected_delivery_time;
						// $data_xyz['full_address']    = !empty($key_value->get_users_address->full_address) ? $key_value->get_users_address->full_address : '';
						// dd($value->id);
						$option_main = array();
		
						$getresultsss = OrderDetailModel::where('order_id', '=', $value->id)->orderBy('id', 'desc')->get();
						// dd($getresultsss);
						foreach ($getresultsss as $valuse_x) {
							$data_x['id']              = $valuse_x->id;
							// dd($data_x['id']);
							$data_x['order_id']        = $valuse_x->order_id;
							$data_x['quantity']        = $valuse_x->quantity;
							$data_x['product_id']     = $valuse_x->products_id;

							$data_x['product_name']    =  !empty($valuse_x->get_product_name->name) ? $valuse_x->get_product_name->name : '';
							$data_x['product_price']      = !empty($valuse_x->get_product_name->price) ? $valuse_x->get_product_name->price : '';
							$data_x['product_discount_price']  = !empty($valuse_x->get_product_name->discount_price) ? $valuse_x->get_product_name->discount_price : '';
							$data_x['product_description']     = !empty($valuse_x->get_product_name->description) ? $valuse_x->get_product_name->description : '';
							$data_x['product_capacity']        = !empty($valuse_x->get_product_name->capacity) ? $valuse_x->get_product_name->capacity : '';
							$data_x['product_package_items_count']  = !empty($valuse_x->get_product_name->package_items_count) ? $valuse_x->get_product_name->package_items_count : '';
							$data_x['product_unit']        = !empty($valuse_x->get_product_name->unit) ? $valuse_x->get_product_name->unit : '';
							$data_x['product_featured']    = !empty($valuse_x->get_product_name->featured) ? $valuse_x->get_product_name->featured : '';
							$data_x['product_deliverable'] = !empty($valuse_x->get_product_name->deliverable) ? $valuse_x->get_product_name->deliverable : '';
							$data_x['product_old_price'] = !empty($valuse_x->get_product_name->old_price) ? $valuse_x->get_product_name->old_price : '0';
							$data_x['product_products_image'] = !empty($valuse_x->get_product_name->products_image) ? $valuse_x->get_product_name->products_image : '';
							$data_x['delivery_charge'] = $valuse_x->get_product_name->delivery_charge;
							$data_x['offer_available'] = $valuse_x->get_product_name->offer_available;
							$data_x['product_active'] = $valuse_x->get_product_name->product_active;
							$data_x['add_to_cart']    = $valuse_x->get_product_name->add_to_cart;
							$data_x['products_type']   = !empty($valuse_x->get_product_name->products_type) ? $valuse_x->get_product_name->products_type : '';
							$data_x['product_tax']     = !empty($valuse_x->get_product_name->product_tax)? $valuse_x->get_product_name->product_tax : '';
							$data_x['cart_count']      = !empty($valuse_x->get_product_name->cart_count)? $valuse_x->get_product_name->cart_count : '';

							$data_x['driver_id']       = $valuse_x->driver_id;
							
							$option_main[] = $data_x;
						}
						
					    $data_xyz['product_list']    = $option_main;

						// dd($data_xyz['product_list']);
						$result[] = $data_xyz;
					}
				}

			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	}
 
	public function app_driver_earning_list_oldsss(Request $request){
		$getDriverCount = User::where('id', '=', $request->driver_id)->count();
		if (!empty($getDriverCount)) {
			$result = array();

		    $getResult = User::where('id', '=', $request->driver_id)->first();
		
			$result['driver_id'] = $getResult->id;
			$result['total_balance'] = $getResult->total_balance;
			 
			$get_Total_Trips   = OrderModel::where('user_id', '=', $request->driver_id)->count();
		    $result['total_trips'] = $get_Total_Trips;

		    $get_Today_Trips  = OrderModel::where('user_id', '=', $request->driver_id)->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),"=", date('Y-m-d'))->count();
		
			$result['today_trips'] = $get_Today_Trips;

			$get_count   = OrderModel::where('drivers_id', '=', $request->driver_id)->count();
			if(!empty($get_count)){
			$get_User   = OrderModel::where('drivers_id', '=', $request->driver_id)->get();

			foreach ($get_User as $key_value) {
						//dd($key_value->id);	
				$data['id'] = $key_value->id;
				$data['user_id'] = $key_value->user_id;
				$data['drivers_id'] = $key_value->drivers_id;
				$data['order_date_time'] = date('d-m-Y h:i A', strtotime($key_value->order_date_time));
				$data['order_total'] = $key_value->order_total;
				$data['grand_total'] = $key_value->grand_total;
				$data['user_name']   = !empty($key_value->get_user->name) ? $key_value->get_user->name: '';
				$data['driver_name'] = !empty($key_value->get_drivers->name) ? $key_value->get_drivers->name: '';
				$data['user_profile'] = !empty($key_value->get_user->user_profile) ? $key_value->get_user->user_profile: '';
				$data['full_address'] = !empty($key_value->get_users_address->full_address) ? $key_value->get_users_address->full_address: '';

				$User_Array[] = $data;
			}
			$result['user_list'] = $User_Array;
		}else{
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
		}
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
			echo json_encode($json);
	}



public function app_driver_earning_list(Request $request){
		$getDriverCount = User::where('id', '=', $request->driver_id)->count();
		if (!empty($getDriverCount)) {
			$result = array();

		    $getResult = User::where('id', '=', $request->driver_id)->first();
		
			$result['driver_id'] = $getResult->id;
			$result['total_balance'] = $getResult->total_balance;
			 
			$get_Total_Trips   = OrderModel::where('user_id', '=', $request->driver_id)->count();
		    $result['total_trips'] = $get_Total_Trips;

		    $get_Today_Trips  = OrderModel::where('user_id', '=', $request->driver_id)->where(DB::raw("(DATE_FORMAT(created_at,'%Y-%m-%d'))"),"=", date('Y-m-d'))->count();
		
			$result['today_trips'] = $get_Today_Trips;

			//$get_count   = OrderModel::where('drivers_id', '=', $request->driver_id)->count();

			 $User_Array = array();
			//if(!empty($get_count)){
			$get_User   = OrderModel::where('drivers_id', '=', $request->driver_id)->get();

			foreach ($get_User as $key_value) {
						//dd($key_value->id);	
				$data['id'] = $key_value->id;
				$data['user_id'] = $key_value->user_id;
				$data['drivers_id'] = $key_value->drivers_id;
				$data['order_date_time'] = date('d-m-Y h:i A', strtotime($key_value->order_date_time));
				$data['order_total'] = $key_value->order_total;
				$data['grand_total'] = $key_value->grand_total;
				$data['user_name']   = !empty($key_value->get_user->name) ? $key_value->get_user->name: '';
				$data['driver_name'] = !empty($key_value->get_drivers->name) ? $key_value->get_drivers->name: '';
				$data['user_profile'] = !empty($key_value->get_user->user_profile) ? $key_value->get_user->user_profile: '';
				$data['full_address'] = !empty($key_value->get_users_address->full_address) ? $key_value->get_users_address->full_address: '';

				$User_Array[] = $data;
			}
			$result['user_list'] = $User_Array;
		// }else{
		// 	$json['status'] = 1;
		// 	$json['message'] = 'All data loaded successfully.';
		// }
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
			echo json_encode($json);
	}


	public function app_driver_order_list(Request $request){
		
		$getCount = OrderModel::where('id', '=', $request->order_id)->count();
		if(!empty($getCount)){
			$result = array();
			$getOrder = OrderModel::where('id', '=', $request->order_id)->get();
			foreach ($getOrder as $keyvalue) {
				$data['id'] = $keyvalue->id;
				$data['user_id'] = $keyvalue->user_id;
				$data['users_address_id'] = $keyvalue->users_address_id;
				$data['order_date_time'] = $keyvalue->order_date_time;
				$data['expected_delivery_time'] = $keyvalue->expected_delivery_time;
				$data['delivery_charge'] = $keyvalue->delivery_charge;
				$data['order_total'] = $keyvalue->order_total;
				$data['grand_total'] = $keyvalue->grand_total;
				$data['order_status'] = $keyvalue->order_status;
				$data['problem_name'] = $keyvalue->problem_name;
				$data['problem_image'] = $keyvalue->problem_image;
				$data['delivery_status'] = $keyvalue->delivery_status;
				$data['transaction_id'] = $keyvalue->transaction_id;
				$data['accept_reject_order'] = $keyvalue->accept_reject_order;
				$data['user_profile'] = !empty($keyvalue->get_user->user_profile) ? $keyvalue->get_user->user_profile: '';
				$data['user_name'] = !empty($keyvalue->get_user->name) ? $keyvalue->get_user->name: '';
				$data['user_address'] = !empty($keyvalue->get_users_address->full_address) ? $keyvalue->get_users_address->full_address: '';
				$data['user_mobile'] = !empty($keyvalue->get_user->mobile) ? $keyvalue->get_user->mobile: '';
				$data['user_lat'] = !empty($keyvalue->get_user->lat) ? $keyvalue->get_user->lat: '';
				$data['user_lang'] = !empty($keyvalue->get_user->lang) ? $keyvalue->get_user->lang: '';
			 //order start
				$option_main = array();

				$getresultsss = OrderDetailModel::where('order_id', '=', $keyvalue->id)->orderBy('id', 'desc')->get();
				foreach ($getresultsss as $valuse_x) {
							$data_x['id']              = $valuse_x->id;

							// $data_x['order_id']        = $valuse_x->order_id;
							// $data_x['product_name']        = $valuse_x->product_name;
							// $data_x['quantity']     = $valuse_x->quantity;
				 			// $data_x['product_unit']     = $valuse_x->product_unit;
							// dd($data_x['id']);
							$data_x['order_id']        = $valuse_x->order_id;
							$data_x['quantity']        = $valuse_x->quantity;
							$data_x['product_id']     = $valuse_x->products_id;

							$data_x['product_name']    =  !empty($valuse_x->get_product_name->name) ? $valuse_x->get_product_name->name : '';
							$data_x['product_price']      = !empty($valuse_x->get_product_name->price) ? $valuse_x->get_product_name->price : '';
							$data_x['product_discount_price']  = !empty($valuse_x->get_product_name->discount_price) ? $valuse_x->get_product_name->discount_price : '';
							$data_x['product_description']     = !empty($valuse_x->get_product_name->description) ? $valuse_x->get_product_name->description : '';
							$data_x['product_capacity']        = !empty($valuse_x->get_product_name->capacity) ? $valuse_x->get_product_name->capacity : '';
							$data_x['product_package_items_count']  = !empty($valuse_x->get_product_name->package_items_count) ? $valuse_x->get_product_name->package_items_count : '';
							$data_x['product_unit']        = !empty($valuse_x->get_product_name->unit) ? $valuse_x->get_product_name->unit : '';
							$data_x['product_featured']    = !empty($valuse_x->get_product_name->featured) ? $valuse_x->get_product_name->featured : '';
							$data_x['product_deliverable'] = !empty($valuse_x->get_product_name->deliverable) ? $valuse_x->get_product_name->deliverable : '';
							$data_x['product_old_price'] = !empty($valuse_x->get_product_name->old_price) ? $valuse_x->get_product_name->old_price : '0';
							$data_x['product_products_image'] = !empty($valuse_x->get_product_name->products_image) ? $valuse_x->get_product_name->products_image : '';
							$data_x['delivery_charge'] = $valuse_x->get_product_name->delivery_charge;
							$data_x['offer_available'] = $valuse_x->get_product_name->offer_available;
							$data_x['product_active'] = $valuse_x->get_product_name->product_active;
							$data_x['add_to_cart']    = $valuse_x->get_product_name->add_to_cart;
							$data_x['products_type']   = !empty($valuse_x->get_product_name->products_type) ? $valuse_x->get_product_name->products_type : '';
							$data_x['product_tax']     = !empty($valuse_x->get_product_name->product_tax)? $valuse_x->get_product_name->product_tax : '';
							$data_x['cart_count']      = !empty($valuse_x->get_product_name->cart_count)? $valuse_x->get_product_name->cart_count : '';

							$data_x['driver_id']       = $valuse_x->driver_id;
							
							$option_main[] = $data_x;
						}
						

				$data['product_list']    = $option_main;

				 //order detail end
				// order detail item start
				$option_main_item = array();
		
		
				$getresult_item = OrderDetailItemModel::where('order_id', '=', $keyvalue->id)->orderBy('id', 'desc')->get();
				foreach ($getresult_item as $valuse_xy) {
					$data_xy['id']              = $valuse_xy->id;
					$data_xy['order_id']        = $valuse_xy->order_id;
					$data_xy['product_name']    =  !empty($valuse_xy->product_name) ? $valuse_xy->product_name : '';
					$data_xy['product_unit']    =  !empty($valuse_xy->product_unit) ? $valuse_xy->product_unit : '';
					$data_xy['product_price'] = !empty($valuse_xy->product_price) ? $valuse_xy->product_price : '0';
					$data_xy['quantity'] = !empty($valuse_xy->quantity) ? $valuse_xy->quantity : '0';
				
					$option_main_item[] = $data_xy;
				}

			    $data['product_item_list']    = $option_main_item;

			    // order detail item end

			    $result[] = $data;
			}
			$json['status'] = 1;
			$json['message'] = 'All data loaded successfully.';
			$json['result'] = $result;
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}

		echo json_encode($json);
	}
	
	public function app_driver_delivery_status_change(Request $request){
		$OrderCount = OrderModel::where('id', '=', $request->order_id)->count();
		if(!empty($OrderCount)){

		if(!empty($request->order_id) && !empty($request->delivery_status)){
			$getresult = OrderModel::find($request->order_id);
			$getresult->delivery_status = $request->delivery_status;
			$getresult->save();

			$json['status'] = 1;
			$json['message'] = 'Status successfully chang';
			$json['result'] = $this->getOrdersItmeList($getresult->id);
			
			if($getresult->delivery_status == 1){
				$title   = "Order Confirmed!";
				$message = "Received an order";
				$this->sendPushNotificationUserDeliveryStatus($title,$message,$getresult->user_id,$getresult->delivery_status);
				$json['status']  = 1;
				$json['message'] = 'Status successfully chang';
			}else if($getresult->delivery_status == 2){
				$title   = "Order in Preparation!";
				$message = "Received an order";
				$this->sendPushNotificationUserDeliveryStatus($title,$message,$getresult->user_id,$getresult->delivery_status);
				$json['status']  = 1;
				$json['message'] = 'Status successfully chang';
			}else if($getresult->delivery_status == 3){
				$title   = "Order in Delivery!";
				$message = "Received an order";
				$this->sendPushNotificationUserDeliveryStatus($title,$message,$getresult->user_id,$getresult->delivery_status);
				$json['status']  = 1;
				$json['message'] = 'Status successfully chang';
			}else if($getresult->delivery_status == 4){
				$title   = "Order Received!";
				$message = "Received an order";
				$this->sendPushNotificationUserDeliveryStatus($title,$message,$getresult->user_id,$getresult->delivery_status);
				$json['status']  = 1;
				$json['message'] = 'Status successfully chang';
			}else{
				$title   = "Order Grocery!";
				$message = "Received an order";
				$this->sendPushNotificationUserDeliveryStatus($title,$message,$getresult->user_id,$getresult->delivery_status);
				$json['status']  = 1;
				$json['message'] = 'Status successfully chang';
			}

			

			
		}else{
			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}
		}else{
			$json['status'] = 0;
			$json['message'] = 'Record not found.';
		}
		echo json_encode($json);
	}


	public function sendPushNotificationUserDeliveryStatus($title,$message,$user_id,$delivery_status){
		// echo $user_id;
		// die();
		//dd($title);
		$user    = User::find($user_id);
		$result = NotificationServerKeyModel::find(1);
		$serverKey = $result->notification_key;	   
			$token = $user->token;
// dd($token);
			$body['message'] = $message;
			
			$body['title']   = $title;

			$body['driver_name']         = $user->name;

			$body['driver_mobile']       = $user->mobile;

			$body['driver_lat']          = $user->lat;

			$body['driver_lang']         = $user->lang;

			$body['notification_type']   = $delivery_status;
			//dd($body['notification_type']);

 
			$body['body']    = $body;
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

   
	public function app_add_item_update(Request $request){
		  
		if (!empty($request->id)) {

			$update_record = OrderModel::find($request->id);
			// dd($update_record);
			$update_record->order_date_time    = !empty($request->order_date_time) ? $request->order_date_time : '';
	
			//dd($request->order_date_time);


  			 $new_date = date('Y-m-d h:i:s', strtotime($request->order_date_time. '+3 day'));
  			 $update_record->expected_delivery_time  		   = $new_date;

			//	$update_record->expected_delivery_time    = !empty($request->expected_delivery_time) ? $request->expected_delivery_time : '';


			$update_record->delivery_charge = !empty($request->delivery_charge) ? $request->delivery_charge : '0';

			$update_record->order_total = !empty($request->order_total) ? $request->order_total : '0';
			$update_record->grand_total = !empty($request->grand_total) ? $request->grand_total : '0';
			
			
			$update_record->order_status  = trim($request->order_status );
			$update_record->delivery_status  = trim($request->delivery_status );
	
			$update_record->save();


			if(!empty($request->order_detail_item))
			{
				OrderDetailItemModel::where('order_id', '=', $update_record->id)->delete();

				$option = json_decode($request->order_detail_item);
				
				foreach ($option as  $value) {
				// dd($value);
				
						$save_option = new OrderDetailItemModel;
						$save_option->order_id 			 = $update_record->id;
						$save_option->product_name   = !empty($value->product_name) ? $value->product_name : '';
						$save_option->quantity   = !empty($value->quantity) ? $value->quantity : '0';
						$save_option->product_unit   = !empty($value->product_unit) ? $value->product_unit : '';
						$save_option->product_price   = !empty($value->product_price) ? $value->product_price : '0';

						$save_option->save();	
				
				}
			} 
  
			$json['status'] = 1;
			$json['message'] = 'Order updated successfully.';

			$json['result'] = $this->getOrdersItmeList($update_record->id);

		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid Order Id.';
		}
		echo json_encode($json);
	}


	public function app_order_rating_add(Request $request){
		$getUserCount = OrderModel::where('id','=', $request->order_id)->count();
		if(!empty($getUserCount)){
		$update_rating = OrderModel::find($request->order_id);
		if(!empty($update_rating)){
			   
			$update_rating->order_rating = $request->order_rating;
			
			$update_rating->save();
  
			$json['status'] = 1;
			$json['message'] = 'Order rating successfully submit.';
 
		}else{ 
			$json['status']  = 0;
			$json['message'] = 'Record not found.';
		}
	}else{
		$json['status']  = 0;
		$json['message'] = 'Order ID not available.';
	}
		echo json_encode($json);
	}


    public function app_services_flag_user_list(Request $request){
		$getRecordCount = User::where('id', '=', $request->user_id)->count();
		if(!empty($getRecordCount)){

		$result = array();
		$getRecord = ServicesFlag::where('user_id', '=', $request->user_id)->where('services_flag_status','=', 1)->orderBy('id', 'desc')->get();
		
		foreach ($getRecord as $value) {
			$data['id']				      = $value->id;
			$data['store_name']		      = !empty($value->get_services->store_name) ? $value->get_services->store_name : '';
			$data['services_description'] = !empty($value->get_services->services_description) ? $value->get_services->services_description : '';
			$data['services_image']       = !empty($value->get_services->services_image) ? $value->get_services->services_image : '';
			$data['latitude']             = !empty($value->get_services->latitude) ? $value->get_services->latitude : '';
			$data['longitude']            = !empty($value->get_services->longitude) ? $value->get_services->longitude : '';
	    	$data['flag']                 = $value->get_services->flag;
	    	$data['services_flag_status'] = $value->services_flag_status;
			$result[] = $data;
		}
		$json['status'] = 1;
		$json['message'] = 'All Services Flag loaded successfully.';
		$json['result'] = $result;
	   }else{
	   		$json['status'] = 0;
			$json['message'] = 'Record not found.';
	   }
	   echo json_encode($json);
	}
	
	public function app_register_update(Request $request){
		if (!empty($request->user_id)) {
			//$check_email  = User::where('email', '=', $request->email)->count();
          //  $check_mobile = User::where('mobile', '=', $request->mobile)->count();
            //$check_social_id = User::where('social_id', '=', $request->social_id)->count();
		// if($check_mobile == '0'){
		$update_record = User::find($request->user_id);
			if(!empty($update_record)){
		  
		    
		    $update_record->name    = !empty($request->name)?$request->name: '';
		    // $update_record->email  = !empty($request->email)?$request->email: '';
		    $update_record->mobile  = trim($request->mobile);
		   // $update_record->password  = Hash::make($request->password);
			$update_record->save();
		
			    $json['status'] = 1;
				$json['message'] = 'Mobile number updated successfully.';
			    $json['result'] = $this->getProfileUserCommon($update_record->id);
		}else{
			$json['status'] = 0;
			$json['message'] = 'Invalid User.';
		}

		  // }else 
    //        {

    //         $json['status'] = 0;
    //         $json['message'] = 'Your account already exist please login or try again.';
    //        }

		} 
		else 
		{

			$json['status'] = 0;
			$json['message'] = 'Parameter missing!';
		}

		echo json_encode($json);
	}
	
	
}


 
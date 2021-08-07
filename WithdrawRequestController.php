<?php

namespace App\Http\Controllers\Backend;
// use App\Models\UsersModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UsersWalletDetailsModel;
use Razorpay\Api\Api;


class WithdrawRequestController extends Controller
{
	public function withdraw_request_list(Request $request)
	{	
		$getrecord = UsersWalletDetailsModel::orderBy('id', 'desc')->select('users_wallet_details.*');
		$getrecord = $getrecord->join('users', 'users_wallet_details.user_id', '=', 'users.id');
		// Search Box Start

		if ($request->idsss) {
            $getrecord = $getrecord->where('users_wallet_details.id', '=', $request->idsss);
        }

        if(!empty($request->name)){
           $getrecord = $getrecord->where('users.name', 'like', '%' . $request->name . '%');
        }

     
        if ($request->money_status) {
            $money_status = $request->money_status;
            if($request->money_status == 100)
            {
                $money_status = 0;
            }

            $getrecord = $getrecord->where('money_status','=',$money_status);
        }
		// Search Box End
		
		$getrecord = $getrecord->paginate(40);
		$data['getrecord'] = $getrecord;
		$data['meta_title'] = 'Withdraw Request List';
		return view('backend.withdraw_request.list', $data);
	}

// Key Id
//    rzp_test_T9c5xyx85UfW0b
// Key Secret  
// 4z0bfAXroxtxsvtEB3GIHREm
	public function pay_now_old($id){

        $getrecord = UsersWalletDetailsModel::get_single($id);
        // dd($getrecord->amount_transfer);

		// $api = new Api('rzp_test_T9c5xyx85UfW0b', '4z0bfAXroxtxsvtEB3GIHREm');
		// dd($api);

		$getrecord->money_status = '1';
		$getrecord->save();

        return redirect()->back()->with('success', 'Payment successfull');
	}

	public function pay_now($id){

         $getrecord = UsersWalletDetailsModel::get_single($id);
        // dd($getrecord->amount_transfer);


        $curl = curl_init();
        $request = '{
          "name":"VB Indus",
          "email":"vb@gmail.com",
          "contact":"8866786154",
          "type":"employee",
          "reference_id":"Acme Contact ID 12345",
          "notes":{
            "notes_key_1":"Tea, Earl Grey, Hot",
            "notes_key_2":"Tea, Earl Grey… decaf."
          }
        }';
        curl_setopt($curl, CURLOPT_URL, 'https://api.razorpay.com/v1/contacts/');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['content-type: application/json']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        $err = curl_error($curl);
        if($err) {
            echo 'Curl Error: ' . $err;
        } else {

            //$response = json_decode($result, true);
            // dd($response);
            //$token = $response['resonse']['result']['token'];
            curl_close($curl);

            $curl = curl_init();
            $request = '{
                  "name":"VB Indus",
                  "email":"vb@gmail.com",
                  "contact":"8866786154",
                  "type":"employee",
                  "reference_id":"Acme Contact ID 12345",
                  "notes":{
                    "notes_key_1":"Tea, Earl Grey, Hot",
                    "notes_key_2":"Tea, Earl Grey… decaf."
                  }
                }';
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.razorpay.com/v1/contacts',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $request,
              CURLOPT_HTTPHEADER => array(
               'Authorization: Basic cnpwX3Rlc3RfOWlJbjZjZGZMck5IMUs6bGFzOHF6eWJGMXBoRnJnSEQwUGRacEth',
               // 'Authorization: Basic $token',
                'Content-Type: application/json'
              ),
            ));

            $response = curl_exec($curl);
            
            $err = curl_error($curl);
            // dd($err);
            if($err){
                echo "cURL Error #:" . $err;
            }else{
                //echo $response;
                //dd($getrecord);
              //  dd($response);
                $this->fund_accounts_api($response, $getrecord);

            }
            curl_close($curl);
          //  echo $response;
        }

        return redirect()->back()->with('success', 'Payment successfull');
    }



    public function fund_accounts_api($response, $getrecord)
    {
        $contact_details = json_decode($response);
      // dd($contact_details->id);
        $curl = curl_init();
        $request_all = 
                     '"contact_id":"'.$contact_details->id.'",
                      "account_type":"bank_account",
                      "bank_account":{
                            "name":"'.$getrecord->user->name.'",
                            "ifsc":"'.$getrecord->user->ifsc_code.'",
                            "account_number":"'.$getrecord->user->account_number.'"
                      }';
                   //   dd($request_all);
                   //dd('{'.$request_all.'}');
            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://api.razorpay.com/v1/fund_accounts',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => '{'.$request_all.'}',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Basic cnpwX3Rlc3RfOWlJbjZjZGZMck5IMUs6bGFzOHF6eWJGMXBoRnJnSEQwUGRacEth',
                'Content-Type: application/json'
              ),
            ));

            $payouts_response = curl_exec($curl);
            $err = curl_error($curl);
               if($err){
                echo "cURL Error #:" . $err;
            }else{
                // dd($getrecord);
             // dd($payouts_response);
                $this->payouts_api($payouts_response, $getrecord);

            }

            curl_close($curl);
            return redirect()->back()->with('success', 'Payment successfull');
         
    }
    

    public function payouts_api($payouts_response, $getrecord){
         $fund_accounts = json_decode($payouts_response);
     // dd($fund_accounts);  


         $Gunkar = $getrecord->amount_transfer * 100;
            // dd($Gunkar);
        $curl = curl_init();
            $all_request = '{
                      "account_number": "2323230027517028",
                      "fund_account_id": "'.$fund_accounts->id.'",
                      "amount": '.$Gunkar.',
                      "currency": "INR",
                      "mode": "IMPS",
                      "purpose": "payout",
                      "queue_if_low_balance": true,
                      "reference_id": "",
                      "narration": ""
                    }';
          //dd($all_request);  
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://api.razorpay.com/v1/payouts',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS => $all_request,
                  CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic cnpwX3Rlc3RfOWlJbjZjZGZMck5IMUs6bGFzOHF6eWJGMXBoRnJnSEQwUGRacEth',
                    'Content-Type: application/json'
                  ),
                ));

            $response_last = curl_exec($curl);
          
            $err = curl_error($curl);
            
            if($err){
               // echo "cURL Error #:" . $err;
                  return redirect()->back()->with('error', 'Payment Failed');
            }else{
             
                  $getrecord->money_status = '1';
                  $getrecord->save();
                  return redirect()->back()->with('success', 'Payment successfull');
            }

            curl_close($curl);
           // return redirect()->back()->with('success', 'Payment successfull');





       //  $response_last = curl_exec($curl);
       // // dd($response_last);
       //  curl_close($curl);
       // // echo $response;

       //  $getrecord->money_status = '1';
       //  $getrecord->save();
       //  return redirect()->back()->with('success', 'Payment successfull');
            


        
       
    }


}

?>
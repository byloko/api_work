<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UsersWalletDetailsModel;
use Hash;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_index(Request $request)
    {
        $getrecord = User::where('is_admin','=', 0)->where('is_type','=', 1)->orderBy('id', 'desc');
        // Search Box Start
        if ($request->idsss) {
            $getrecord = $getrecord->where('users.id', '=', $request->idsss);
        }
        if ($request->name) {
            $getrecord = $getrecord->where('users.name', 'like', '%' . $request->name . '%');
        }
    
        if ($request->email) {
            $getrecord = $getrecord->where('users.email', 'like', '%' . $request->email . '%');
        }
        if ($request->mobile) {
            $getrecord = $getrecord->where('users.mobile', 'like', '%' . $request->mobile . '%');
        }

        // Search Box End

        $getrecord = $getrecord->paginate(40);
        $data['getrecord'] = $getrecord;

        $data['meta_title'] = 'User List';
        return view('backend.user.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function user_create(Request $request)
    {
        $data['meta_title'] = 'Add User';
        return view('backend.user.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function user_store(Request $request)
    {
       $insert_r = request()->validate([
          //'username'            => 'required|alpha_dash|unique:users',
          'email'         => 'required|unique:users',
          //'mobile'                => 'required|unique:users|digits:10|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            //'mobile' => 'required|unique:users|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
          'mobile' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
          //  'alternate_number' => 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
       ]);


      $insert_r                   = new User;
      $insert_r->name             = trim($request->name);
      
      $insert_r->email            = trim($request->email);
      $insert_r->mobile           = trim($request->mobile);
      if (!empty($request->file('user_profile'))) {
            $ext = 'jpg';
            $file = $request->file('user_profile');
            $randomStr = str_random(30);
            $filename = strtolower($randomStr) . '.' . $ext;
            $file->move('upload/profile/', $filename);
            $insert_r->user_profile = $filename;
        }
      $insert_r->password = Hash::make($request->password);
      $insert_r->is_type           = 1; 
      $insert_r->save();
      return redirect('admin/user')->with('success', 'Record successfully register.');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_show($id)
    {
        $data['getrecord'] = User::get_single($id);
        $data['meta_title'] = 'View User';
        return view('backend.user.view', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_edit($id)
    {
        $data['getrecord'] = User::get_single($id);
        $data['meta_title'] = 'Edit User';
        return view('backend.user.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_update(Request $request, $id)
    {
        //$user_update = request()->validate([
          //'username'            => 'required|alpha_dash|unique:users',
          //'mobile'                => 'required|unique:users|digits:10|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
           // 'mobile' => 'required|unique:users|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
       //      'alternate_number' => 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
       // ]);


        $user_update = User::get_single($id);
        $user_update->name = $request->name;
        if(!empty($request->password)){
          $user_update->password = Hash::make($request->password);
        }
        //$user_update->email = $request->email;
        $user_update->mobile = $request->mobile;
         if (!empty($request->file('user_profile')))
        {
            if (!empty($user_update->user_profile) && file_exists('upload/profile/'.$user_update->user_profile))
            {
                unlink('upload/profile/'.$user_update->user_profile);
            }
            $ext = 'jpg';
            $file = $request->file('user_profile');
            $randomStr = str_random(30);
            $filename = strtolower($randomStr) . '.' . $ext;
            $file->move('upload/profile/', $filename);
            $user_update->user_profile = $filename;
        }

        $user_update->save();
        return redirect('admin/user')->with('success', 'Record successfully update.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function user_destroy($id)
    {
        $deleteRecord = User::get_single($id);
        $deleteRecord->delete();

     
        UsersWalletDetailsModel::where('user_id','=',$id)->delete();
     
        return redirect()->back()->with('error', 'Record successfully deleted!');

        //$delete_record = User::get_single($id);
        //$delete_record->delete();
        //return redirect()->back()->with('error', 'Record successfully deleted!');
    }


    public function user_change_status(Request $request){
        $order = UsersWalletDetailsModel::find($request->order_id);
        $order->money_status = $request->status_id;
        $order->save();

   //   dd($order->amount_transfer);

         if($order->money_status == 1){
             $update_record = User::find($order->user_id);
             $LessAmount    = $update_record->amount - $order->amount_transfer;
             $update_record->amount = $LessAmount;

             $update_record->save();
         }

        $json['success'] = true;
        echo json_encode($json);
    }

    public function user_view_delete($id){
      $deleteR  = UsersWalletDetailsModel::get_single($id);
      $deleteR->delete();
      return redirect()->back()->with('error', 'Record successfully deleted!');
    }

    // user stop

    public function mearchant_index(Request $request){
        $getrecord = User::where('is_admin','=', 0)->where('is_type','=', 2)->orderBy('id', 'desc');
        // Search Box Start
        if ($request->idsss) {
            $getrecord = $getrecord->where('users.id', '=', $request->idsss);
        }
        if ($request->name) {
            $getrecord = $getrecord->where('users.name', 'like', '%' . $request->name . '%');
        }
    
        if ($request->email) {
            $getrecord = $getrecord->where('users.email', 'like', '%' . $request->email . '%');
        }
        if ($request->mobile) {
            $getrecord = $getrecord->where('users.mobile', 'like', '%' . $request->mobile . '%');
        }

        // Search Box End

        $getrecord = $getrecord->paginate(40);
        $data['getrecord'] = $getrecord;

        $data['meta_title'] = 'Mearchant List';
        return view('backend.mearchant.list', $data);
    }

    public function mearchant_create(Request $request){
        $data['meta_title'] = 'Add Mearchant';
        return view('backend.mearchant.add', $data);
    }

    public function mearchant_store(Request $request){
         $insert_r = request()->validate([
          //'username'            => 'required|alpha_dash|unique:users',
           'email'         => 'required|unique:users',
          //'mobile'                => 'required|unique:users|digits:10|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            //'mobile' => 'required|unique:users|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
           'mobile' => 'required|regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
          //  'alternate_number' => 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/',
       ]);


      $insert_r                   = new User;
      $insert_r->name             = trim($request->name);
      
      $insert_r->email            = trim($request->email);
      $insert_r->mobile           = trim($request->mobile);
      if (!empty($request->file('user_profile'))) {
            $ext = 'jpg';
            $file = $request->file('user_profile');
            $randomStr = str_random(30);
            $filename = strtolower($randomStr) . '.' . $ext;
            $file->move('upload/profile/', $filename);
            $insert_r->user_profile = $filename;
        }
      $insert_r->password = Hash::make($request->password);
      $insert_r->is_type           = 2;
      $insert_r->save();
      return redirect('admin/mearchant')->with('success', 'Record successfully register.');
    }

    public function mearchant_show($id, Request $request){
        $data['getrecord'] = User::get_single($id);
        $data['meta_title'] = 'View Mearchant';
        return view('backend.mearchant.view', $data);
    }

    public function mearchant_edit($id, Request $request)
    {
        $data['getrecord'] = User::get_single($id);
        $data['meta_title'] = 'Edit Mearchant';
        return view('backend.mearchant.edit', $data);   
    }

    public function mearchant_update($id, Request $request){
        $user_update = User::get_single($id);
        $user_update->name = $request->name;
        if(!empty($request->password)){
          $user_update->password = Hash::make($request->password);
        }

       // $user_update->email = $request->email;
        $user_update->mobile = $request->mobile;
         if (!empty($request->file('user_profile')))
        {
            if (!empty($user_update->user_profile) && file_exists('upload/profile/'.$user_update->user_profile))
            {
                unlink('upload/profile/'.$user_update->user_profile);
            }
            $ext = 'jpg';
            $file = $request->file('user_profile');
            $randomStr = str_random(30);
            $filename = strtolower($randomStr) . '.' . $ext;
            $file->move('upload/profile/', $filename);
            $user_update->user_profile = $filename;
        }

        $user_update->save();
        return redirect('admin/mearchant')->with('success', 'Record successfully update.');
    }


    public function mearchant_destroy($id)
    {
        $deleteRecord = User::get_single($id);
        $deleteRecord->delete();

        UsersWalletDetailsModel::where('user_id','=',$id)->delete();

        return redirect()->back()->with('error', 'Record successfully deleted!');

        //$delete_record = User::get_single($id);
        //$delete_record->delete();
        //return redirect()->back()->with('error', 'Record successfully deleted!');
    }
    
    public function mearchant_delete($id, Request $request){
        $deleteRecord = User::get_single($id);
        $deleteRecord->user_profile = $request->user_profile;
        $deleteRecord->save();
        return redirect()->back()->with('error', 'Record successfully deleted!');

    }

    public function mearchant_change_status(Request $request){
        $order = UsersWalletDetailsModel::find($request->order_id);
        $order->money_status = $request->status_id;
        $order->save();

        $json['success'] = true;
        echo json_encode($json);
    }

    public function mearchant_view_delete($id){
      $deleteR  = UsersWalletDetailsModel::get_single($id);
      $deleteR->delete();
      return redirect()->back()->with('error', 'Record successfully deleted!');
    }



    
}

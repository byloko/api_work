<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\CustomFieldRepository;
use App\Repositories\RoleRepository;
use App\Repositories\UploadRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Prettus\Validator\Exceptions\ValidatorException;

class UserAPIController extends Controller
{
    private $userRepository;
    private $uploadRepository;
    private $roleRepository;
    private $customFieldRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, UploadRepository $uploadRepository, RoleRepository $roleRepository, CustomFieldRepository $customFieldRepo)
    {
        $this->userRepository = $userRepository;
        $this->uploadRepository = $uploadRepository;
        $this->roleRepository = $roleRepository;
        $this->customFieldRepository = $customFieldRepo;
    }

    function login_old(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {
                // Authentication passed...
                $user = auth()->user();
                if(!empty($user->otp_verify == 1)){
                $check = Hash::check($request->password, $user->password);
                if(!empty($check)){
                $user->device_token = $request->input('device_token', '');
                $user->save();
                return $this->sendResponse($user, 'User retrieved successfully');
            }

        }else{
            $json['status'] = false;
            $json['message'] = 'Mobile OTP not verified please try again.';
             //return $this->sendResponse($user, 'Mobile OTP not verified please try again.');
        }

            }else {
              return $this->sendResponse($user, 'User retrieved successfully');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }
    echo json_encode($json);
    }

    public function login(Request $request){
            if (!empty($request->email) && !empty($request->password)) {

                $user = User::where('email', '=', $request->email)->first();
                if (!empty($user)) {
                   if(!empty($user->otp_verify == 1)){
                
                    $check = Hash::check($request->password, $user->password);
                    if (!empty($check)) {

                        // if(!empty($request->device_token))
                        // {
                            $datauser = User::find($user->id);  
                            $datauser->token             = !empty($request->token)?$request->token:null;
                            // $datauser->remember_token    = !empty($request->token)?$request->token:null;
                        //  $datauser->device_token = $request->device_token;
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

public function updateToken($user_id)
    {
        // $randomStr = str_random(40).$user_id;
        // $save_token = User::find($user_id);
        // $save_token->token = $randomStr;
        // $save_token->save();

        $randomStr = str_random(40).$user_id;
        $save_token = User::find($user_id);
        $save_token->user_token = $randomStr;
        $save_token->save();
    }

    public function register(Request $request){
        if(!empty($request->password) && !empty($request->mobile) && !empty($request->name) && !empty($request->email)){
            $check_email  = User::where('email', '=', $request->email)->count();
            $check_mobile = User::where('mobile', '=', $request->mobile)->count();
// if(!empty($check_email)){
            if($check_email == '0' && $check_mobile == '0'){
            //$uprecord = User::find($request->user_id);
 
        //  if(!empty($uprecord)){
                   $uprecord = new User;
               //    $uprecord->is_type   = trim($request->is_type);
                   $uprecord->is_type   = 1;
                   $uprecord->token    = !empty($request->token)?$request->token:null;
                   $uprecord->email    = trim($request->email);
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

             

          //     }else
          //    {
             //     $json['status'] = 0;
                // $json['message'] = 'Invalid User.';
          //   }
            }else 
           {

            $json['status'] = 0;
            $json['message'] = 'Your account already exist please login or try again.';
           }

           }else 
           {

            $json['status'] = 0;
            $json['message'] = 'Parameter missing!';
           }

        echo json_encode($json);
    }

    public function getProfileUser($id){
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
        $json['social_id']      = !empty($user->social_id) ? $user->social_id : '';
        $json['social_type']    = $user->social_type;
        $json['document_photo'] = !empty($user->document_photo) ? $user->document_photo : '';
        $json['online_offline_status']  = $user->online_offline_status;

        $json['account_number']     = !empty($user->account_number) ? $user->account_number : '';
        $json['bank_holder_name']   = !empty($user->bank_holder_name) ? $user->bank_holder_name : '';
        $json['ifsc_code']          = !empty($user->ifsc_code) ? $user->ifsc_code : '';
        $json['branch_code']        = !empty($user->address) ? $user->branch_code : '';
        $json['bank_name']          = !empty($user->address) ? $user->bank_name : '';

       
        return $json;
    }
     


    function register_old(Request $request)
    {

        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|unique:users|email',
                'password' => 'required',
                'mobile' => 'required',
            ]);
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->mobile = $request->input('mobile');
            $user->device_token = $request->input('device_token', '');
            $user->password = Hash::make($request->input('password'));
            $user->api_token = str_random(60);
            $user->save();

              // $user->assignRole('both');

        //     event(new UserRoleChangedEvent($user));
        // } catch (\Exception $e) {
        //     return $this->sendError($e->getMessage(), 401);
        // }

            $defaultRoles = $this->roleRepository->findByField('default', 'both');
            $defaultRoles = $defaultRoles->pluck('name')->toArray();
           // $user->assignRole($defaultRoles);
            $user->assignRole('both');

            if (copy(public_path('images/avatar_default.png'), public_path('images/avatar_default_temp.png'))) {
                $user->addMedia(public_path('images/avatar_default_temp.png'))
                    ->withCustomProperties(['uuid' => bcrypt(str_random())])
                    ->toMediaCollection('avatar');
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 401);
        }


        return $this->sendResponse($user, 'User retrieved successfully');
    }

    function logout(Request $request)
    {
       
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();
        if (!$user) {
            return $this->sendError('User not found', 401);
        }
        try {
            auth()->logout();
        } catch (\Exception $e) {
            $this->sendError($e->getMessage(), 401);
        }
        return $this->sendResponse($user['name'], 'User logout successfully');

    }

    function user(Request $request)
    {
        $user = $this->userRepository->findByField('api_token', $request->input('api_token'))->first();

        if (!$user) {
            return $this->sendError('User not found', 401);
        }

        return $this->sendResponse($user, 'User retrieved successfully');
    }

    function settings(Request $request)
    {
        $settings = setting()->all();
        $settings = array_intersect_key($settings,
            [
                'default_tax' => '',
                'default_currency' => '',
                'default_currency_decimal_digits' => '',
                'app_name' => '',
                'currency_right' => '',
                'enable_paypal' => '',
                'enable_stripe' => '',
                'enable_razorpay' => '',
                'main_color' => '',
                'main_dark_color' => '',
                'second_color' => '',
                'second_dark_color' => '',
                'accent_color' => '',
                'accent_dark_color' => '',
                'scaffold_dark_color' => '',
                'scaffold_color' => '',
                'google_maps_key' => '',
                'fcm_key' => '',
                'mobile_language' => '',
                'app_version' => '',
                'enable_version' => '',
                'distance_unit' => '',
                'home_section_1'=> '',
                'home_section_2'=> '',
                'home_section_3'=> '',
                'home_section_4'=> '',
                'home_section_5'=> '',
                'home_section_6'=> '',
                'home_section_7'=> '',
                'home_section_8'=> '',
                'home_section_9'=> '',
                'home_section_10'=> '',
                'home_section_11'=> '',
                'home_section_12'=> '',
            ]
        );

        if (!$settings) {
            return $this->sendError('Settings not found', 401);
        }

        return $this->sendResponse($settings, 'Settings retrieved successfully');
    }

    /**
     * Update the specified User in storage.
     *
     * @param int $id
     * @param Request $request
     *
     */
    public function update($id, Request $request)
    {
        $user = $this->userRepository->findWithoutFail($id);

        if (empty($user)) {
            return $this->sendResponse([
                'error' => true,
                'code' => 404,
            ], 'User not found');
        }
        $input = $request->except(['password', 'api_token']);
        try {
            if ($request->has('device_token')) {
                $user = $this->userRepository->update($request->only('device_token'), $id);
            } else {
                $customFields = $this->customFieldRepository->findByField('custom_field_model', $this->userRepository->model());
                $user = $this->userRepository->update($input, $id);

                foreach (getCustomFieldsValues($customFields, $request) as $value) {
                    $user->customFieldsValues()
                        ->updateOrCreate(['custom_field_id' => $value['custom_field_id']], $value);
                }
            }
        } catch (ValidatorException $e) {
            return $this->sendError($e->getMessage(), 401);
        }

        return $this->sendResponse($user, __('lang.updated_successfully', ['operator' => __('lang.user')]));
    }

    function sendResetLinkEmail(Request $request)
    {
        
       
        $this->validate($request, ['email' => 'required|email']);

        $response = Password::broker()->sendResetLink(
            $request->only('email')
        );

        if ($response == Password::RESET_LINK_SENT) {
            return $this->sendResponse(true, 'Reset link was sent successfully');
        } else {
            return $this->sendError('Reset link not sent', 401);
        }

    }
}

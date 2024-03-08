<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTraits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    use GeneralTraits;


    public function register(Request $request){
        try {
            // validation
            $rules = [
                'name_ar'=>'required',
                'name_en'=>'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required',
                'com_password' => 'required',
                'age' => 'required',
                'gender' => 'required',
//                'photo'=>'required|mimes:jpg,png,jpeg'

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            //  register

            if ($request->password==$request->com_password){
                if ($request->hasFile('photo')) {
                    $file_path = uploadImage('users', $request->photo);
                    User::create([
                        'name_ar' => $request->name_ar,
                        'name_en' => $request->name_en,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
//                        'com_password' => bcrypt($request->com_password),
                        'age' => $request->age,
                        'gender' => $request->gender,
                        'photo' => $file_path
                    ]);
                    return $this->ReturnSuccess('S000', __('messages.added'));
                }
                else{
                    User::create([
                        'name_ar' => $request->name_ar,
                        'name_en' => $request->name_en,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
//                        'com_password' => bcrypt($request->com_password),
                        'age' => $request->age,
                        'gender' => $request->gender,
                        'photo' => null ]);
                    return $this->ReturnSuccess('S000', __('messages.added'));
                }
            }else{
                return $this->ReturnError('E001',__('messages.password'));
            }


            //return token
        }catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }


    public function login(Request $request)
    {
        try {
            //validation
            $rules = [
                'email' => 'required',
                'password' => 'required',

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }


            /// Login
            $incremental=$request->only(['email','password']);
            $token=Auth::guard('user-api')->attempt($incremental);
            if (!$token)
            {
                return $this->ReturnError('E001',__('messages.information'));
            }
            $user=Auth::guard('user-api')->user();
            $user->api_token=$token;
            return $this->ReturnData('user',$user,__('messages.enter'));


        }
        catch (\Exception $ex)
            {
                return $this->ReturnError($ex->getCode(),$ex->getMessage());
            }
    }


    public function logout(Request $request){
        $token=$request->header('auth-token');
        if($token){
            try {
                JWTAuth::setToken($token)->invalidate();
            }
            catch (TokenInvalidException $ex)
            {
                return $this->ReturnError('E001',__('messages.something'));
            }
            return $this->ReturnSuccess('S001',__('messages.logout'));
        }else{
            return $this->ReturnError('E001',__('messages.something'));
        }
    }





}

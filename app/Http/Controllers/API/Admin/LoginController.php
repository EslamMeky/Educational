<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTraits;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function Symfony\Component\Translation\t;

class LoginController extends Controller
{
    use GeneralTraits;
    public function register(Request $request){
        try {
            // validation
            $rules = [
                'name'=>'required',
                'email' => 'required|email|unique:admins,email',
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
                    $file_path = uploadImage('admins', $request->photo);
                    Admin::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'age' => $request->age,
                        'gender' => $request->gender,
                        'photo' => $file_path
                    ]);
                    return $this->ReturnSuccess('S000', __('messages.added'));
                }
                else{
                    Admin::create([
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
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
            $rules=[
                'email' => 'required',
                'password' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            /// login
            $incremental=$request->only(['email','password']);
            $token=Auth::guard('admin')->attempt($incremental);
            if (!$token){
                return  $this->ReturnError('E00',__('messages.information'));
            }
            $admin=Auth::guard('admin')->user();
            $admin->api_token=$token;
            return $this->ReturnData('admin',$admin,__('messages.enter'));



        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }




}

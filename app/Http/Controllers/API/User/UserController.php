<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTraits;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use GeneralTraits;
    public function show()
    {
        try {
            $users=User::selection()->paginate(PAGINATE);
            return $this->ReturnData('users',$users,'success');
        }catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }

    public function edit($id)
    {
        try
        {
            $user=User::find($id);
            if (!$user){
                return $this->ReturnError('E00',__('messages.not found this user'));
            }
            $user->where('id',$id)->get();
            return $this->ReturnData('user',$user,'S00');
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }

    public function update(Request $request ,$id)
    {
        try {
            $rules = [
                'name_ar'=>'required',
                'name_en'=>'required',
                'email' => 'required|email',
                'age' => 'required',
                'gender' => 'required',

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
/////////////// update  //////////////
            $user=User::find($id);
            if (!$user)
                return $this->ReturnError('E001',__('messages.not found this user'));
            User::where('id',$id)->update([
                'name_ar'=>$request->name_ar,
                'name_en'=>$request->name_en,
                'email'=>$request->email,
                'age'=>$request->age,
                'gender'=>$request->gender,
            ]);
            if ($request->hasFile('photo'))
            {
                $file_path=uploadImage('users',$request->photo);
                User::where('id',$id)->update([
                    'photo'=>$file_path
                ]);
            }
            return $this->ReturnSuccess('S001',__('messages.update'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }


    public function delete(Request $request ,$id)
    {
        try {
           $user=User::find($id);
           if (!$user){
               return $this->ReturnError('E001',__('messages.not found this user'));
           }
           if ($user->photo !=null){
               $image=Str::after($user->photo,'assets/');
               $image=base_path('public/assets/'.$image);
               unlink($image);
               $user->delete();
           }
           else
              $user->delete();

           return $this->ReturnSuccess('S001',__('messages.delete'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }


    public function forgetPassword(Request $request)
    {
        try {
            ///validate///
            $rules = [
                'email' => 'required|exists:users,email',
                'password'=>'required'

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            //////// update password ///
            User::where('email',$request->email)->update([
                'password'=>bcrypt($request->password),
//                'com_password'=>bcrypt($request->password),
            ]);
            return $this->ReturnSuccess('S001',__('messages.change password'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }




    }
}

<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTraits;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    use GeneralTraits;

    public function show()
    {
        try
        {
           $admins= Admin::selection()->paginate(PAGINATE);
            return $this->ReturnData('Admins',$admins,'S00');
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }

    public function edit($id)
    {
        try
        {
         $admin=Admin::find($id);
         if (!$admin){
             return $this->ReturnError('E00',__('messages.not found this user'));
         }
         $admin->where('id',$id)->get();
         return $this->ReturnData('admin',$admin,'S00');
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }


    public function update(Request $request ,$id)
    {
        try
        {
            $rules = [
                'name'=>'required',
                'email' => 'required|email',
                'age' => 'required',
                'gender' => 'required',

            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            // update
            $admin=Admin::find($id);
            if (!$admin){
                return $this->ReturnError('E00',__('messages.not found this user'));
            }
            Admin::where('id',$id)->update([
                'name'=>$request->name,
                'email'=>$request->email,
                'age'=>$request->age,
                'gender'=>$request->gender,
//                'photo'=>null,
            ]);
            if ($request->hasFile('photo'))
            {
                $path_file=uploadImage('admins',$request->photo);
                Admin::where('id',$id)->update([
                   'photo'=>$path_file,
                ]);
            }
            return $this->ReturnSuccess('S00',__('messages.update'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function delete($id){
        try
        {
            $admin=Admin::find($id);
            if (!$admin){
                return $this->ReturnError('E00',__('messages.not found this user'));
            }
            if ($admin->photo != null){
                $image=Str::after($admin->photo,'assets/');
                $image=base_path('public/assets/'.$image);
                unlink($image);
                $admin->delete();
            }
            else
            $admin->delete();
            return $this->ReturnSuccess('S00',__('messages.delete'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function forgetPassword(Request $request)
    {
        try
        {
            ///validate///
            $rules = [
                'email' => 'required|exists:admins,email',
                'password'=>'required'
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            /// update
            $admin=Admin::where('email',$request->email)->update([
               'password'=>bcrypt($request->password),
            ]);
            return $this->ReturnSuccess('S00',__('messages.change password'));
        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

}

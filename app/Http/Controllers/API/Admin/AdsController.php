<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTraits;
use App\Models\Ads;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdsController extends Controller
{
    use GeneralTraits;
    public function add(Request $request)
    {
        try
        {
            $rules = [
                'name'=>'required',
                'category'=>'required',
                'description' => 'required',
                'photo'=>'required|mimes:jpg,png,jpeg'


            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            /// add
            $path_file=uploadImage('ads',$request->photo);

            Ads::create([
                'name'=>$request->name,
                'category_id'=>$request->category,
                'description'=>$request->description,
                'photo'=>$path_file,
            ]);
            return $this->ReturnSuccess('S000', __('messages.added'));


        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function show()
    {
        try {

            $ads=Ads::selection()->paginate(PAGINATE);
            return $this->ReturnData('Ads',$ads,'S00');
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function edit($id){
        try {
            $ads=Ads::find($id);
            if (!$ads){
                return  $this->ReturnError('E00',__('messages.not found this category'));
            }
            $ads->where('id',$id)->get();
            return $this->ReturnData('Ads',$ads,'S00');
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
                'category'=>'required',
                'description' => 'required',
//                'photo'=>'required|mimes:jpg,png,jpeg'


            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            /// update
            $ads=Ads::find($id);
            if (!$ads){
                return $this->ReturnError('E00',__('messages.not found this category'));
            }

            Ads::where('id',$id)->update([
                'name'=>$request->name,
                'description'=>$request->description,
                'category_id'=>$request->category,
            ]);
            if ($request->hasFile('photo'))
            {
                $path_file=uploadImage('ads',$request->photo);
                Category::where('id',$id)->update([
                    'photo'=>$path_file,

                ]);
            }
            return $this->ReturnSuccess('S000', __('messages.update'));


        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }

    public function delete($id){
        try {
            $ads=Ads::find($id);
            if (!$ads){
                return  $this->ReturnError('E00',__('messages.not found this category'));
            }
            $image=Str::after($ads->photo,'assets/');
            $image=base_path('public/assets/'.$image);
            unlink($image);
            $ads->delete();
            return $this->ReturnSuccess('S00',__('messages.delete'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }



}



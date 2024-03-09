<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTraits;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
use GeneralTraits;
    public function add(Request $request)
    {
        try
        {
            $rules = [
                'name_ar'=>'required',
                'name_en'=>'required',
                'description_ar' => 'required',
                'description_en' => 'required',
                'photo'=>'required|mimes:jpg,png,jpeg'


            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            /// add
            $path_file=uploadImage('categories',$request->photo);

            Category::create([
                'name_ar'=>$request->name_ar,
                'name_en'=>$request->name_en,
                'description_ar'=>$request->description_ar,
                'description_en'=>$request->description_en,
                'photo'=>$path_file,
            ]);
            return $this->ReturnSuccess('S000', __('messages.added'));


        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function delete($id){
        try {
            $category=Category::find($id);
            if (!$category){
                return  $this->ReturnError('E00',__('messages.not found this category'));
            }
            $image=Str::after($category->photo,'assets/');
            $image=base_path('public/assets/'.$image);
            unlink($image);
            $category->delete();
            return $this->ReturnSuccess('S00',__('messages.delete'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function show()
    {
        try {

            $category=Category::selection()->paginate(PAGINATE);
            return $this->ReturnData('categories',$category,'S00');
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function edit($id){
        try {
            $category=Category::find($id);
            if (!$category){
                return  $this->ReturnError('E00',__('messages.not found this category'));
            }
           $category->where('id',$id)->get();
            return $this->ReturnData('category',$category,'S00');
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
                'name_ar'=>'required',
                'name_en'=>'required',
                'description_ar' => 'required',
                'description_en' => 'required',
                'photo'=>'required|mimes:jpg,png,jpeg'


            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            /// update
            $category=Category::find($id);
            if (!$category){
                return $this->ReturnError('E00',__('messages.not found this category'));
            }
            $path_file=uploadImage('categories',$request->photo);

            Category::where('id',$id)->update([
                'name_ar'=>$request->name_ar,
                'name_en'=>$request->name_en,
                'description_ar'=>$request->description_ar,
                'description_en'=>$request->description_en,
                'photo'=>$path_file,
            ]);
            return $this->ReturnSuccess('S000', __('messages.update'));


        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }

}

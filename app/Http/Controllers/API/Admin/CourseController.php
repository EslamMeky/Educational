<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTraits;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
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
                'category'=>'required',
                'sub_category'=>'required',
                'photo'=>'required|mimes:jpg,png,jpeg',
                'video'=>'required',


            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            /// add
            ///
            ///
            $photo=uploadImage('courses',$request->photo);

            $fileNames=[];

            foreach ($request->file('video') as $video)
            {
                $path_video = uploadVideo('video', $video);
                $fileNames[]=$path_video;
            }
            foreach ($fileNames as  $fileName){

                         Course::create([
                            'name_ar' => $request->name_ar,
                            'name_en' => $request->name_en,
                            'description_ar' => $request->description_ar,
                            'description_en' => $request->description_en,
                            'category_id' => $request->category,
                            'sub_category_id' => $request->sub_category,
                            'photo' => $photo,
                            'video' => $fileName,

                        ]);
            }
                return $this->ReturnSuccess('S00', __('messages.added'));

        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }



    public function test(Request $request)
    {
        $fileNames=[];

        foreach ($request->file('video') as $video)
        {
            $path_video = uploadVideo('video', $video);
            $fileNames[]=$path_video;
        }
    foreach ($fileNames as  $fileName){
         Course::create([
                'name_ar' => 'اسلام',
                'name_en' => 'eslam',
                'description_ar' => 'eslam',
                'description_en' => 'eslam',
                'category_id' => 1,
                'sub_category_id' => 5,
                'photo' => 'images/courses/0vljaBGk25DqlHNk9cWXn58y6U0i6n5DbZG6s9N6.jpg',
                'video' => $fileName,

        ]);
}
         return back();

    }


    public function delete($id){
        try {
            $course=Course::find($id);
            if (!$course){
                return  $this->ReturnError('E00',__('messages.not found this category'));
            }
            $image=Str::after($course->photo,'assets/');
            $image=base_path('public/assets/'.$image);
            unlink($image);

            $video=Str::after($course->video,'assets/');
            $video=base_path('public/assets/'.$video);
            unlink($video);

            $course->delete();
            return $this->ReturnSuccess('S00',__('messages.delete'));
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function show()
    {
        try {

            $courses=Course::with(['category','subCategory'])->selection()->paginate(PAGINATE);
            return $this->ReturnData('Courses',$courses,'S00');
        }
        catch (\Exception $ex){
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }
    }

    public function edit($id){
        try {
            $course=Course::find($id);
            if (!$course){
                return  $this->ReturnError('E00',__('messages.not found this category'));
            }
            $course->where('id',$id)->get();
            return $this->ReturnData('Course',$course,'S00');
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
                'category'=>'required',
                'sub_category'=>'required',
//                'photo'=>'required|mimes:jpg,png,jpeg'


            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            /// update
            $course=Course::find($id);
            if (!$course){
                return $this->ReturnError('E00',__('messages.not found this category'));
            }

            Course::where('id',$id)->update([
                'name_ar'=>$request->name_ar,
                'name_en'=>$request->name_en,
                'description_ar'=>$request->description_ar,
                'description_en'=>$request->description_en,
                'category_id'=>$request->category,
                'sub_category_id'=>$request->sub_category,
            ]);
            if ($request->hasFile('photo'))
            {
                $path_file=uploadImage('courses',$request->photo);
                Course::where('id',$id)->update([
                    'photo'=>$path_file,

                ]);
            }
            if ($request->hasFile('video'))
            {
                $path_file=uploadImage('video',$request->video);
                Course::where('id',$id)->update([
                    'video'=>$path_file,

                ]);
            }
            return $this->ReturnSuccess('S000', __('messages.update'));


        }
        catch (\Exception $ex)
        {
            return $this->ReturnError($ex->getCode(),$ex->getMessage());
        }

    }




}

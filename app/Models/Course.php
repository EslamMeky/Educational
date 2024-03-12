<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $table='courses';
    protected $fillable = [
        'name_ar',
        'name_en',
        'category_id',
        'sub_category_id',
        'description_ar',
        'description_en',
        'photo',
        'video',
        'created_at',
        'updated_at',
    ];

    protected $timestamp=true;


    public function scopeSelection($q){
        return $q->select(
            'id',
            'name_'.app() -> getLocale() .' as name',
            'category_id',
            'sub_category_id',
            'description_'.app()->getLocale().' as description',
            'photo',
            'video',
            'created_at',
            'updated_at',
        );
    }
    public function getPhotoAttribute($val)
    {
        return ($val!=null)? asset('assets/'.$val):"";
    }
    public function getVideoAttribute($val)
    {
        return ($val!=null)? asset('assets/'.$val):"";
    }


    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
    public function subCategory()
    {
        return $this->belongsTo(subCategory::class,'sub_category_id','id');
    }
}

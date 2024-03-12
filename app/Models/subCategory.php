<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subCategory extends Model
{
    use HasFactory;
    protected $table='sub_categories';
    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'category_id',
        'photo',
        'created_at',
        'updated_at',
    ];

    protected $timestamp=true;


    public function scopeSelection($q){
        return $q->select(
            'id',
            'name_'.app() -> getLocale() .' as name',
            'description_'.app()->getLocale().' as description',
            'photo',
            'category_id',
            'created_at',
            'updated_at',
        );
    }
    public function getPhotoAttribute($val)
    {
        return ($val!=null)? asset('assets/'.$val):"";
    }

    public function categories(){
        return $this->belongsTo(Category::class,'category_id','id');
    }
}

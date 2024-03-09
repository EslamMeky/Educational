<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table='categories';
    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'photo',
        'created_at',
        'updated_at',
    ];

    protected $timestamp=true;


    public function scopeSelection($q){
        return $q->select(
            'id',
            'name_'.app()->getLocale().' as name',
            'description_'.app()->getLocale().' as description',
            'photo',
            'created_at',
            'updated_at',
        );
    }
    public function getPhotoAttribute($val)
    {
        return ($val!=null)? asset('assets/'.$val):"";
    }
}

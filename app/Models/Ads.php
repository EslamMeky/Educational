<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use HasFactory;
    protected $table='ads';
    protected $fillable = [
        'name',
        'category_id',
        'description',
        'photo',
        'created_at',
        'updated_at',
    ];

    protected $timestamp=true;


    public function scopeSelection($q){
        return $q->select(
            'id',
            'name',
            'category_id',
            'description',
            'photo',
            'created_at',
            'updated_at',
        );
    }
    public function getPhotoAttribute($val)
    {
        return ($val!=null)? asset('assets/'.$val):"";
    }

    public function category()
    {
        return $this->belongsTo(Category::class,'category_id','id');
    }
}

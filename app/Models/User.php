<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table='users';
    protected $fillable = [
        'name_ar',
        'name_en',
        'email',
        'password',
        'photo',
        'age',
        'gender',
        'created_at',
        'updated_at',
    ];

    protected $timestamp=true;


    public function scopeSelection($q){
        return $q->select('id','name_'.app()->getLocale().' as name',
            'email',
            'password',
            'photo',
            'age',
            'gender',
            'created_at',
            'updated_at',
            );
    }
    public function getPhotoAttribute($val)
    {
        return ($val!=null)? asset('assets/'.$val):"";
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}

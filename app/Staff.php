<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Str;
class Staff extends Authenticatable
{
    use Notifiable;
    protected $table = 'staffs';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $primaryKey = 'id';
    protected static function boot()
    {
        parent::boot();
        // static::deleted(function($booking) {
        //     foreach(['plans', 'payment'] as $relation) {
        //         foreach($booking->$relation()->get() as $child){
        //             $child->delete();
        //         }
        //     }
        // });
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::orderedUuid();
        });
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password','shop_id', 'admin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function shop()
    {
        return $this->hasOne('App\Shop', 'id', 'shop_id');
    }
}

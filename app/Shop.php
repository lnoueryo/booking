<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'staff_id', 'password'];

    protected $table = 'shops';
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
}

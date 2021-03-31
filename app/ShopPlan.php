<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopPlan extends Model
{
    protected $fillable = ['shop_id', 'title', 'types', 'price', 'duration', 'description', 'image'];
    // public function shop_types()
    // {
    //     return $this->hasMany('App\PlanTypes', 'shop_plan_id', 'id');
    // }
}

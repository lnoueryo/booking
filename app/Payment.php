<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['price', 'paid', 'method', 'booking_id'];

    protected $table = 'payments';
    protected $primaryKey = 'id';

}

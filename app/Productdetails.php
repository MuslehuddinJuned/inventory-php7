<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productdetails extends Model
{
    protected $fillable = ['quantity', 'remarks', 'producthead_id', 'inventory_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

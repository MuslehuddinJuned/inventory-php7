<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Producthead extends Model
{
    protected $fillable = ['product_category', 'buyer', 'product_style', 'product_code', 'specification', 'remarks', 'product_image'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

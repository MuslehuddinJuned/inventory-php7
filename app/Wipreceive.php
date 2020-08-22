<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wipreceive extends Model
{
    protected $fillable = ['receive_qty', 'etd', 'remarks', 'user_id', 'product_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['store_name', 'item', 'item_code', 'specification', 'unit', 'unit_price', 'item_image'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

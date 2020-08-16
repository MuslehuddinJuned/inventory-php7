<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = ['store_id', 'item', 'item_code', 'specification', 'unit', 'cann_per_sheet', 'unit_price', 'item_image'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

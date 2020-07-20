<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventoryreceive extends Model
{
    protected $fillable = ['quantity', 'remarks', 'stock_type', 'price', 'description', 'supplier_name', 'challan_no', 'storeReceive_id', 'inventory_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

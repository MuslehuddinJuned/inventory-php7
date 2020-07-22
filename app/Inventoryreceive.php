<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventoryreceive extends Model
{
    protected $fillable = ['remarks', 'stock_type', 'challan_date', 'supplier_name', 'challan_no', 'storeReceive_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

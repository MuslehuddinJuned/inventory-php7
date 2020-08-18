<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invenrecall extends Model
{
    protected $fillable = ['quantity', 'master_sheet', 'remarks', 'price', 'inventory_id', 'inventoryreceive_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

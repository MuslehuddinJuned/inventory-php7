<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productdetails extends Model
{
    protected $fillable = ['quantity', 'sn', 'material_number', 'material_name', 'description', 'material_name_ch', 'description_ch', 'unit_weight', 'unit', 'remarks', 'producthead_id', 'inventory_id', 'store_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

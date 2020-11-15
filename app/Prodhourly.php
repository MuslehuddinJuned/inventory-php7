<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prodhourly extends Model
{
    protected $fillable = ['line', 'section', 'department', 'leader', 'item', 'remarks', 'prod_date', 'quantity', 'hourly_target', 'qty_1', 'ng_1', 'qty_2', 'ng_2', 'qty_3', 'ng_3', 'qty_4', 
    'ng_4', 'qty_5', 'ng_5', 'qty_6', 'ng_6', 'qty_7', 'ng_7', 'qty_8', 'ng_8', 'qty_9', 'ng_9', 'qty_10', 'ng_10', 'qty_11', 'ng_11', 
    'qty_12', 'ng_12', 'qty_13', 'ng_13', 'qty_14', 'ng_14', 'qty_15', 'ng_15', 'qty_16', 'ng_16', 'qty_17', 'ng_17', 'qty_18', 
    'ng_18', 'qty_19', 'ng_19', 'qty_20', 'ng_20', 'qty_21', 'ng_21', 'qty_22', 'ng_22', 'qty_23', 'ng_23', 'qty_24', 'ng_24', 
    'polist_id'];

    public function user(){
        return $this->belongsToMany(User::class);
    }
}

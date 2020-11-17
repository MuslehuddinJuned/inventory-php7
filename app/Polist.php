<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Polist extends Model
{
    protected $fillable = ['lot', 'container', 'ctn', 'pcs_per_ctn', 'cbm', 'shipment_booking', 'loading_date', 'inspection_date', 'quantity', 'remarks', 'po_date', 'po_no', 'etd', 'producthead_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recdetails extends Model
{
    protected $fillable = ['quantity', 'polist_id', 'po_qty', 'master_sheet', 'issue_etd', 'remarks', 'accept', 'inventory_id', 'rechead_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

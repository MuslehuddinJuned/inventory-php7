<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recdetails extends Model
{
    protected $fillable = ['quantity', 'remarks', 'inventory_id', 'rechead_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

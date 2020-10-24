<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    protected $fillable = ['prod_date', 'prod_qty', 'remarks', 'polist_id', 'producthead_id', 'prodstore_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

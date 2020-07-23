<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventoryissue extends Model
{
    protected $fillable = ['rechead_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

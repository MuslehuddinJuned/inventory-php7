<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inventoryissue extends Model
{
    protected $fillable = ['quantity', 'remarks', 'issue_price', 'user_id', 'inventory_id'];
    public function user(){
        return $this->belongsTo(User::class);
    }
}

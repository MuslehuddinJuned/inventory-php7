<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Proditem extends Model
{
    protected $fillable = ['transfer_qty', 'from_channel_id', 'to_channel_id', 'polist_id', 'producthead_id'];
    public function user(){
        return $this->belongsToMany(User::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prodparts extends Model
{
    protected $fillable = ['quantity', 'remarks', 'producthead_id', 'subpart_id', 'polist_id', 'prodstore_id'];
    public function user(){
        return $this->belongsToMany(User::class);
    }
}

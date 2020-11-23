<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prodparts extends Model
{
    protected $fillable = ['quantity', 'prod_date', 'department', 'remarks', 'producthead_id', 'subpart_id', 'polist_id'];
    public function user(){
        return $this->belongsToMany(User::class);
    }
}

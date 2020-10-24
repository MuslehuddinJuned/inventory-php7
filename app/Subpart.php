<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subpart extends Model
{
    protected $fillable = ['parts_name', 'parts_description', 'parts_qty', 'unit', 'remarks', 'producthead_id'];
    public function user(){
        return $this->belongsToMany(User::class);
    }
}
